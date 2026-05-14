# -*- coding: utf-8 -*-
import re
import sqlite3
import unicodedata
from collections import defaultdict
from datetime import datetime
from pathlib import Path
from typing import Dict, List, Optional, Tuple, Any

import cv2
import numpy as np
from flask import Flask, abort, jsonify, request, send_from_directory, render_template, redirect, url_for, session, flash
from flask_cors import CORS

# --- CẤU HÌNH THƯ MỤC ---
THAMUC_GOC = Path(__file__).resolve().parent
DUONG_DU_LIEU = THAMUC_GOC / "du_lieu"
THU_MAT_KHUON = DUONG_DU_LIEU / "mat_khuon"
THU_LICHSU = DUONG_DU_LIEU / "lich_su_quet"
FILE_DB = DUONG_DU_LIEU / "csdl_sinh_vien.db"

app = Flask(__name__)
app.secret_key = "chuoi_bi_mat_khang_ctxh_2026"

CORS(app, resources={r"/*": {"origins": "*"}}) 

for p in (DUONG_DU_LIEU, THU_MAT_KHUON, THU_LICHSU):
    p.mkdir(parents=True, exist_ok=True)

CAC_KE_HAAR = cv2.CascadeClassifier(cv2.data.haarcascades + "haarcascade_frontalface_default.xml")
NGUONG_NHAN_DIEN_TOI_DA = 90.0
NGUONG_TUONG_QUAN_TOI_THIEU = 0.25
NGUONG_TUONG_QUAN_DA_VUNG_TOI_THIEU = 0.25

# --- XỬ LÝ DATABASE ---
def ket_noi_db():
    conn = sqlite3.connect(FILE_DB)
    conn.row_factory = sqlite3.Row
    return conn

def khoi_tao_bang():
    db = ket_noi_db()
    db.execute("""
        CREATE TABLE IF NOT EXISTS sinh_vien (
            ma_sv TEXT PRIMARY KEY, ho_ten TEXT NOT NULL,
            ngay_sinh TEXT, lop TEXT, khoa TEXT, thoi_gian_tao TEXT NOT NULL
        )
    """)
    db.execute("""
        CREATE TABLE IF NOT EXISTS su_kien (
            ma_sk TEXT PRIMARY KEY, ten_sk TEXT NOT NULL,
            don_vi TEXT, dia_diem TEXT, so_luong INTEGER, gio_ctxh REAL, diem_ren_luyen INTEGER, noi_dung TEXT, thoi_gian_to_chuc TEXT, phuong_thuc_diem_danh TEXT DEFAULT 'qr'
        )
    """)
    db.execute("""
        CREATE TABLE IF NOT EXISTS diem_danh (
            id INTEGER PRIMARY KEY AUTOINCREMENT, ma_sv TEXT NOT NULL,
            ten_su_kien TEXT NOT NULL, thoi_gian TEXT NOT NULL, duong_anh TEXT NOT NULL,
            FOREIGN KEY (ma_sv) REFERENCES sinh_vien(ma_sv)
        )
    """)
    db.execute("""
        CREATE TABLE IF NOT EXISTS dang_ky_su_kien (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ma_sv TEXT NOT NULL,
            ma_sk TEXT NOT NULL,
            cam_ket TEXT NOT NULL,
            thoi_gian_dang_ky TEXT NOT NULL,
            UNIQUE(ma_sv, ma_sk)
        )
    """)
    cols_sv = [row[1] for row in db.execute("PRAGMA table_info(sinh_vien)").fetchall()]
    cols_su_kien = [row[1] for row in db.execute("PRAGMA table_info(su_kien)").fetchall()]
    if "ngay_sinh" not in cols_sv: db.execute("ALTER TABLE sinh_vien ADD COLUMN ngay_sinh TEXT")
    if "lop" not in cols_sv: db.execute("ALTER TABLE sinh_vien ADD COLUMN lop TEXT")
    if "khoa" not in cols_sv: db.execute("ALTER TABLE sinh_vien ADD COLUMN khoa TEXT")
    if "thoi_gian_to_chuc" not in cols_su_kien: db.execute("ALTER TABLE su_kien ADD COLUMN thoi_gian_to_chuc TEXT")
    if "diem_ren_luyen" not in cols_su_kien: db.execute("ALTER TABLE su_kien ADD COLUMN diem_ren_luyen INTEGER DEFAULT 0")
    if "noi_dung" not in cols_su_kien: db.execute("ALTER TABLE su_kien ADD COLUMN noi_dung TEXT")
    if "phuong_thuc_diem_danh" not in cols_su_kien: db.execute("ALTER TABLE su_kien ADD COLUMN phuong_thuc_diem_danh TEXT DEFAULT 'qr'")
    db.commit()
    db.close()

def lay_anh_tu_bytes(buf: bytes) -> Optional[np.ndarray]:
    arr = np.frombuffer(buf, dtype=np.uint8)
    return cv2.imdecode(arr, cv2.IMREAD_COLOR)

def tim_tat_ca_mat_va_khung(img_bgr: np.ndarray):
    if img_bgr is None or img_bgr.size == 0: return [], []
    xam = cv2.cvtColor(img_bgr, cv2.COLOR_BGR2GRAY)
    mats = CAC_KE_HAAR.detectMultiScale(xam, 1.2, 5, minSize=(60, 60))
    if len(mats) == 0: return [], []
    mats = sorted(mats.tolist(), key=lambda m: -(m[2] * m[3]))
    h_img, w_img = xam.shape[:2]
    khung, cap = [], []
    for x, y, w, h in mats:
        khung.append({"x": float(x)/w_img, "y": float(y)/h_img, "w": float(w)/w_img, "h": float(h)/h_img})
        mat200 = cv2.resize(xam[y : y + h, x : x + w], (200, 200))
        cap.append((mat200, (int(x), int(y), int(w), int(h))))
    return khung, cap

def chuan_hoa_anh_mat(gray_face: np.ndarray) -> np.ndarray:
    face = cv2.resize(gray_face, (200, 200))
    return cv2.equalizeHist(face)

def tai_du_lieu_khuon_mat() -> Tuple[List[np.ndarray], List[int], Dict[int, str]]:
    face_images: List[np.ndarray] = []
    labels: List[int] = []
    label_to_mssv: Dict[int, str] = {}
    idx = 0
    for p in THU_MAT_KHUON.glob("*.png"):
        try:
            ref = cv2.imread(str(p), cv2.IMREAD_GRAYSCALE)
            if ref is None:
                continue
            face_images.append(chuan_hoa_anh_mat(ref))
            labels.append(idx)
            label_to_mssv[idx] = p.stem.upper()
            idx += 1
        except Exception:
            continue
    return face_images, labels, label_to_mssv

def do_tuong_quan_mat(a: np.ndarray, b: np.ndarray) -> float:
    hist_a = cv2.calcHist([a], [0], None, [64], [0, 256])
    hist_b = cv2.calcHist([b], [0], None, [64], [0, 256])
    cv2.normalize(hist_a, hist_a)
    cv2.normalize(hist_b, hist_b)
    hist_score = float(cv2.compareHist(hist_a, hist_b, cv2.HISTCMP_CORREL))
    tpl_score = float(cv2.matchTemplate(a, b, cv2.TM_CCOEFF_NORMED)[0][0])
    return 0.6 * hist_score + 0.4 * tpl_score

def do_tuong_quan_mat_da_vung(a: np.ndarray, b: np.ndarray) -> float:
    """
    So khớp đa vùng để tăng độ bền khi bị che một phần mặt (khẩu trang/kính):
    - full: toàn khuôn mặt
    - upper: nửa trên (mắt, trán) -> hữu ích khi đeo khẩu trang
    - lower: nửa dưới (mũi, miệng, cằm) -> hữu ích khi mắt bị che/kính phản quang
    """
    h = min(a.shape[0], b.shape[0])
    if h < 20:
        return do_tuong_quan_mat(a, b)

    cut_top = int(h * 0.58)
    cut_bottom = int(h * 0.42)
    a_upper, b_upper = a[:cut_top, :], b[:cut_top, :]
    a_lower, b_lower = a[cut_bottom:, :], b[cut_bottom:, :]

    s_full = do_tuong_quan_mat(a, b)
    s_upper = do_tuong_quan_mat(a_upper, b_upper)
    s_lower = do_tuong_quan_mat(a_lower, b_lower)

    # Điểm tổng hợp: ưu tiên full nhưng vẫn "cứu" khi một phần bị che.
    return max(
        0.50 * s_full + 0.50 * s_upper,
        0.50 * s_full + 0.50 * s_lower,
        0.40 * s_full + 0.30 * s_upper + 0.30 * s_lower
    )

def nhan_dien_sinh_vien_tu_anh(uploaded_bytes: bytes) -> Tuple[Optional[str], Optional[float], str, List[Dict[str, float]], int]:
    img = lay_anh_tu_bytes(uploaded_bytes)
    if img is None:
        return None, None, "Không đọc được ảnh tải lên.", [], 0

    khung_mat, caps = tim_tat_ca_mat_va_khung(img)
    if not caps:
        return None, None, "Không phát hiện khuôn mặt trong ảnh.", [], 0
    if len(caps) > 1:
        return None, None, "Phát hiện nhiều khuôn mặt. Vui lòng chỉ để 1 người trong khung hình.", khung_mat, len(caps)

    query_face = chuan_hoa_anh_mat(caps[0][0])  # grayscale 200x200
    face_images, labels, label_to_mssv = tai_du_lieu_khuon_mat()

    if len(face_images) == 0:
        return None, None, "Chưa có dữ liệu khuôn mặt mẫu trong hệ thống.", khung_mat, len(caps)

    if hasattr(cv2, "face") and hasattr(cv2.face, "LBPHFaceRecognizer_create"):
        recognizer = cv2.face.LBPHFaceRecognizer_create()
        recognizer.train(face_images, np.array(labels))
        pred_label, confidence = recognizer.predict(query_face)

        if confidence > NGUONG_NHAN_DIEN_TOI_DA:
            return None, float(confidence), "Không khớp khuôn mặt sinh viên nào trong dữ liệu.", khung_mat, len(caps)

        # Chặn nhận diện nhầm: LBPH đúng label nhưng ảnh tương quan thấp vẫn loại.
        ref_face = face_images[int(pred_label)]
        corr = do_tuong_quan_mat_da_vung(query_face, ref_face)
        if corr < NGUONG_TUONG_QUAN_DA_VUNG_TOI_THIEU:
            return None, float(confidence), "Ảnh khuôn mặt chưa đủ rõ hoặc không khớp dữ liệu mẫu.", khung_mat, len(caps)

        mssv = label_to_mssv.get(pred_label)
        return mssv, float(confidence), "", khung_mat, len(caps)

    # Fallback nếu OpenCV thiếu cv2.face trên máy người dùng.
    best_idx = -1
    best_score = -1.0
    for i, ref_face in enumerate(face_images):
        score = do_tuong_quan_mat_da_vung(query_face, ref_face)
        if score > best_score:
            best_score = score
            best_idx = i

    if best_idx < 0 or best_score < NGUONG_TUONG_QUAN_DA_VUNG_TOI_THIEU:
        return None, None, "Không khớp khuôn mặt sinh viên nào trong dữ liệu.", khung_mat, len(caps)
    return label_to_mssv.get(best_idx), None, "", khung_mat, len(caps)

def tach_mssv_tu_qr(raw_value: str) -> str:
    s = (raw_value or "").strip().upper()
    if not s:
        return ""
    if s.startswith("CTXH-"):
        return s.replace("CTXH-", "", 1).strip()
    # Cho phép định dạng QR kiểu: CTXH|MSSV|...
    if "|" in s:
        parts = [p.strip() for p in s.split("|") if p.strip()]
        for p in parts:
            if p.startswith("DH") or p.startswith("SV"):
                return p
    # Tách MSSV từ chuỗi có URL hoặc ký tự thừa: ưu tiên mẫu DH..., SV..., B...
    m = re.search(r"\b((?:DH|SV|B)[A-Z0-9]{5,})\b", s)
    if m:
        return m.group(1)
    return s

@app.route("/")
def goc():
    return redirect(url_for("diem_danh"))

@app.route("/diem_danh", methods=["GET", "POST"])
def diem_danh():
    if request.method == "POST":
        sk = (request.form.get("ten_su_kien") or "").strip()
        session["ten_su_kien"] = sk
        return redirect(url_for("quet_mat"))
    return render_template("diem_danh.html")

@app.route("/quet_mat")
def quet_mat():
    sk = session.get("ten_su_kien", "Sự kiện chưa đặt tên")
    return render_template("quet_mat.html", ten_su_kien=sk)


# --- API CHO LARAVEL ---

@app.route("/api/danh_sach", methods=["GET"])
def api_danh_sach():
    db = ket_noi_db()
    sv = db.execute("SELECT * FROM sinh_vien ORDER BY thoi_gian_tao DESC").fetchall()
    db.close()
    return jsonify([{"maSV": r["ma_sv"], "hoTen": r["ho_ten"], "ngaySinh": r["ngay_sinh"], "email": f"{r['ma_sv']}@stu.edu.vn", "trangThai": "Active", "khoa": r["khoa"], "lop": r["lop"]} for r in sv])

@app.route("/api/danh_sach_su_kien", methods=["GET"])
def api_danh_sach_su_kien():
    try:
        ma_sv = (request.args.get("maSV") or "").strip().upper()
        db = ket_noi_db()
        rows = db.execute("""
            SELECT sk.*,
                   IFNULL(dk.so_dang_ky, 0) AS so_dang_ky
            FROM su_kien sk
            LEFT JOIN (
                SELECT UPPER(ma_sk) AS ma_sk, COUNT(*) AS so_dang_ky
                FROM dang_ky_su_kien
                GROUP BY UPPER(ma_sk)
            ) dk ON UPPER(sk.ma_sk) = dk.ma_sk
        """).fetchall()
        now = datetime.now()
        events = []
        for r in rows:
            so_luong = int(r["so_luong"] or 0)
            so_dang_ky = int(r["so_dang_ky"] or 0)
            con_slot = (so_luong <= 0) or (so_dang_ky < so_luong)
            cho_con_lai = (so_luong - so_dang_ky) if so_luong > 0 else -1

            tg_tc = (r["thoi_gian_to_chuc"] or "").strip()
            con_thoi_gian = True
            if tg_tc:
                try:
                    tg_obj = datetime.fromisoformat(tg_tc.replace(" ", "T"))
                    con_thoi_gian = tg_obj >= now
                except Exception:
                    con_thoi_gian = True

            mo_dang_ky = con_slot and con_thoi_gian
            da_dang_ky = False
            da_diem_danh = False
            if ma_sv:
                da_dang_ky = db.execute(
                    "SELECT 1 FROM dang_ky_su_kien WHERE ma_sv = ? AND UPPER(ma_sk) = UPPER(?)",
                    (ma_sv, r["ma_sk"])
                ).fetchone() is not None
                da_diem_danh = db.execute(
                    "SELECT 1 FROM diem_danh WHERE ma_sv = ? AND ten_su_kien = ?",
                    (ma_sv, r["ten_sk"])
                ).fetchone() is not None
            events.append({
                "tenSK": r["ten_sk"],
                "maSK": r["ma_sk"],
                "donVi": r["don_vi"],
                "soLuong": so_luong,
                "soDangKy": so_dang_ky,
                "choConLai": cho_con_lai,
                "moDangKy": mo_dang_ky,
                "conSlot": con_slot,
                "conThoiGian": con_thoi_gian,
                "daDangKy": da_dang_ky,
                "daDiemDanh": da_diem_danh,
                "coTheDangKy": mo_dang_ky and (not da_dang_ky) and (not da_diem_danh),
                "gioCTXH": r["gio_ctxh"],
                "noiDung": r["noi_dung"],
                "diaDiem": r["dia_diem"],
                "thoiGianToChuc": r["thoi_gian_to_chuc"],
                "phuongThucDiemDanh": (r["phuong_thuc_diem_danh"] or "qr"),
            })

        db.close()
        events.sort(key=lambda e: (not e["moDangKy"], not e["conSlot"], not e["conThoiGian"], e["tenSK"] or ""))
        return jsonify(events)
    except Exception as e:
        return jsonify([]) 

@app.route("/api/them_su_kien", methods=["POST", "OPTIONS"])
def api_them_su_kien():
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200 
    try:
        data = request.get_json(silent=True) or request.form
        if not data or not str(data.get('tenSK', '')).strip():
            return jsonify({"ok": False, "thong_diep": "Thiếu tên sự kiện!"}), 400
        sl_str = str(data.get('soLuong', '0')).strip()
        so_luong = int(sl_str) if sl_str else 0
        gio_ctxh = float(str(data.get('gioCTXH', '0')).strip() or 0)
        phuong_thuc = str(data.get('phuongThucDiemDanh', 'qr')).strip().lower()

        if gio_ctxh < 0:
            return jsonify({"ok": False, "thong_diep": "Số giờ CTXH không được âm!"}), 400
        if gio_ctxh > 0 and gio_ctxh < 0.5:
            return jsonify({"ok": False, "thong_diep": "Số giờ CTXH tối thiểu là 0.5 nếu có nhập!"}), 400
        if gio_ctxh <= 0:
            return jsonify({"ok": False, "thong_diep": "Phải nhập số giờ CTXH lớn hơn 0!"}), 400
        if phuong_thuc not in ("qr", "anh"):
            return jsonify({"ok": False, "thong_diep": "Phương thức điểm danh không hợp lệ!"}), 400

        db = ket_noi_db()
        stt = db.execute("SELECT COUNT(*) FROM su_kien").fetchone()[0] + 1
        ma_sk = f"SK{datetime.now().strftime('%Y%m%d')}{stt:03d}"
        db.execute("INSERT INTO su_kien (ma_sk, ten_sk, don_vi, dia_diem, so_luong, gio_ctxh, diem_ren_luyen, noi_dung, thoi_gian_to_chuc, phuong_thuc_diem_danh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                   (ma_sk, data.get('tenSK'), data.get('donVi', ''), data.get('diaDiem', ''), so_luong, gio_ctxh, 0, data.get('noiDung', ''), data.get('thoiGianToChuc', ''), phuong_thuc))
        db.commit()
        db.close()
        return jsonify({"ok": True, "thong_diep": "Lưu sự kiện thành công!", "maSK": ma_sk})
    except sqlite3.IntegrityError:
        return jsonify({"ok": False, "thong_diep": "Mã Sự kiện đã tồn tại trong hệ thống!"}), 400
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/diem_danh_su_kien_admin", methods=["POST", "OPTIONS"])
def api_diem_danh_su_kien_admin():
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200
    try:
        data = request.form if request.form else (request.get_json(silent=True) or {})
        ma_sk = str(data.get("maSK", "")).strip().upper()
        mssv_raw = str(data.get("maSV", "")).strip()
        mssv = tach_mssv_tu_qr(mssv_raw)
        ho_ten = str(data.get("hoTen", "")).strip()
        phuong_thuc = str(data.get("phuongThuc", "qr")).strip().lower()
        anh = request.files.get("anh")

        if not ma_sk:
            return jsonify({"ok": False, "thong_diep": "Thiếu mã sự kiện!"}), 400
        if phuong_thuc not in ("qr", "anh"):
            return jsonify({"ok": False, "thong_diep": "Phương thức điểm danh không hợp lệ!"}), 400
        if phuong_thuc == "anh" and not anh:
            return jsonify({"ok": False, "thong_diep": "Điểm danh khuôn mặt cần tải ảnh khuôn mặt lên!"}), 400
        if phuong_thuc == "qr" and not mssv:
            return jsonify({"ok": False, "thong_diep": "Điểm danh QR cần mã sinh viên từ QR."}), 400

        db = ket_noi_db()
        sk = db.execute("SELECT ten_sk, thoi_gian_to_chuc, phuong_thuc_diem_danh FROM su_kien WHERE UPPER(ma_sk) = ?", (ma_sk,)).fetchone()
        if not sk:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Không tìm thấy sự kiện!"}), 404
        ngay_to_chuc = (sk["thoi_gian_to_chuc"] or "")[:10]
        ngay_hien_tai = datetime.now().strftime("%Y-%m-%d")
        if ngay_to_chuc and ngay_to_chuc != ngay_hien_tai:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Chỉ được điểm danh đúng ngày diễn ra sự kiện!"}), 400

        # Nhận diện khuôn mặt để suy ra MSSV nếu điểm danh bằng ảnh
        khung_mat: List[Dict[str, float]] = []
        so_khuon_mat = 0
        if phuong_thuc == "anh":
            mssv_nhan_dien, confidence, error_msg, khung_mat, so_khuon_mat = nhan_dien_sinh_vien_tu_anh(anh.read())
            if not mssv_nhan_dien:
                db.close()
                return jsonify({
                    "ok": False,
                    "thong_diep": error_msg or "Không nhận diện được khuôn mặt.",
                    "khung_mat": khung_mat,
                    "so_khuon_mat": so_khuon_mat
                }), 400
            mssv = str(mssv_nhan_dien).strip().upper()
        else:
            mssv = str(mssv).strip().upper()

        sv = db.execute("SELECT ho_ten, lop, khoa FROM sinh_vien WHERE UPPER(ma_sv) = UPPER(?)", (mssv,)).fetchone()
        ten_hien_thi = ho_ten if ho_ten else "Sinh viên"
        if not sv:
            db.execute(
                "INSERT INTO sinh_vien (ma_sv, ho_ten, ngay_sinh, lop, khoa, thoi_gian_tao) VALUES (?, ?, ?, ?, ?, ?)",
                (mssv, ten_hien_thi, "", "", "", datetime.now().isoformat(timespec="seconds"))
            )
            db.commit()
        else:
            ten_hien_thi = sv["ho_ten"] or ten_hien_thi

        da_diem_danh = db.execute(
            "SELECT 1 FROM diem_danh WHERE UPPER(ma_sv) = UPPER(?) AND ten_su_kien = ?",
            (mssv, sk["ten_sk"])
        ).fetchone()
        if da_diem_danh:
            db.close()
            return jsonify({"ok": False, "thong_diep": f"Sinh viên {ten_hien_thi} đã điểm danh sự kiện này rồi!"}), 400

        duong_anh = "QUET_QR_ADMIN"
        if phuong_thuc == "anh":
            duong_anh = f"FACE_MATCH:{mssv}"

        db.execute(
            "INSERT INTO diem_danh (ma_sv, ten_su_kien, thoi_gian, duong_anh) VALUES (?, ?, ?, ?)",
            (mssv, sk["ten_sk"], datetime.now().isoformat(timespec="seconds"), duong_anh)
        )
        db.commit()
        db.close()
        lop_val = "Không xác định"
        khoa_val = "Không xác định"
        if sv:
            lop_val = sv["lop"] if sv["lop"] else "Không xác định"
            khoa_val = sv["khoa"] if sv["khoa"] else "Không xác định"

        return jsonify({
            "ok": True,
            "thong_diep": f"Điểm danh thành công: {ten_hien_thi}",
            "khung_mat": khung_mat,
            "so_khuon_mat": so_khuon_mat,
            "sinh_vien": {
                "maSV": mssv or "Không xác định",
                "hoTen": ten_hien_thi or "Không xác định",
                "lop": lop_val,
                "khoa": khoa_val
            }
        })
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/danh_sach_diem_danh_su_kien", methods=["GET"])
def api_danh_sach_diem_danh_su_kien():
    try:
        ma_sk = (request.args.get("maSK") or "").strip().upper()
        if not ma_sk:
            return jsonify({"ok": False, "thong_diep": "Thiếu mã sự kiện!"}), 400

        db = ket_noi_db()
        sk = db.execute("SELECT ten_sk FROM su_kien WHERE UPPER(ma_sk) = ?", (ma_sk,)).fetchone()
        if not sk:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Không tìm thấy sự kiện!"}), 404

        rows = db.execute("""
            WITH reg AS (
                SELECT ma_sv FROM dang_ky_su_kien WHERE UPPER(ma_sk) = UPPER(?)
            ),
            chk AS (
                SELECT ma_sv, thoi_gian FROM diem_danh WHERE ten_su_kien = ?
            ),
            all_sv AS (
                SELECT ma_sv FROM reg
                UNION
                SELECT ma_sv FROM chk
            )
            SELECT
                a.ma_sv,
                IFNULL(sv.ho_ten, 'Chưa đồng bộ') AS ho_ten,
                IFNULL(sv.lop, 'Chưa cập nhật') AS lop,
                IFNULL(sv.khoa, 'Chưa cập nhật') AS khoa,
                chk.thoi_gian,
                CASE
                    WHEN reg.ma_sv IS NOT NULL AND chk.ma_sv IS NOT NULL THEN 'Đã điểm danh'
                    WHEN reg.ma_sv IS NULL AND chk.ma_sv IS NOT NULL THEN 'Điểm danh không đăng ký'
                    WHEN reg.ma_sv IS NOT NULL AND chk.ma_sv IS NULL THEN 'Vắng'
                    ELSE 'Không xác định'
                END AS trang_thai
            FROM all_sv a
            LEFT JOIN reg ON reg.ma_sv = a.ma_sv
            LEFT JOIN chk ON chk.ma_sv = a.ma_sv
            LEFT JOIN sinh_vien sv ON sv.ma_sv = a.ma_sv
            ORDER BY
                CASE
                    WHEN reg.ma_sv IS NOT NULL AND chk.ma_sv IS NOT NULL THEN 1
                    WHEN reg.ma_sv IS NULL AND chk.ma_sv IS NOT NULL THEN 2
                    WHEN reg.ma_sv IS NOT NULL AND chk.ma_sv IS NULL THEN 3
                    ELSE 4
                END,
                a.ma_sv
        """, (ma_sk, sk["ten_sk"])).fetchall()
        db.close()

        return jsonify({
            "ok": True,
            "tenSK": sk["ten_sk"],
            "data": [{
                "maSV": r["ma_sv"],
                "hoTen": r["ho_ten"],
                "lop": r["lop"],
                "khoa": r["khoa"],
                "thoiGian": r["thoi_gian"],
                "trangThai": r["trang_thai"],
            } for r in rows]
        })
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/dang_ky_su_kien", methods=["POST", "OPTIONS"])
def api_dang_ky_su_kien():
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200
    try:
        data = request.get_json(silent=True) or request.form
        ma_sv = str(data.get("maSV", "")).strip().upper()
        ma_sk = str(data.get("maSK", "")).strip().upper()
        cam_ket = str(data.get("camKet", "")).strip()

        if not ma_sv or not ma_sk:
            return jsonify({"ok": False, "thong_diep": "Thiếu mã sinh viên hoặc mã sự kiện!"}), 400
        if not cam_ket:
            return jsonify({"ok": False, "thong_diep": "Bạn cần xác nhận cam kết tham gia đầy đủ."}), 400

        db = ket_noi_db()
        su_kien = db.execute("SELECT ten_sk FROM su_kien WHERE UPPER(ma_sk) = ?", (ma_sk,)).fetchone()
        if not su_kien:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Sự kiện không tồn tại!"}), 404

        da_diem_danh = db.execute(
            "SELECT 1 FROM diem_danh WHERE ma_sv = ? AND ten_su_kien = ?",
            (ma_sv, su_kien["ten_sk"])
        ).fetchone()
        if da_diem_danh:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Bạn đã điểm danh sự kiện này, không thể đăng ký lại."}), 400

        da_dang_ky = db.execute(
            "SELECT 1 FROM dang_ky_su_kien WHERE ma_sv = ? AND UPPER(ma_sk) = ?",
            (ma_sv, ma_sk)
        ).fetchone()
        if da_dang_ky:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Bạn đã đăng ký sự kiện này rồi!"}), 400

        db.execute(
            "INSERT INTO dang_ky_su_kien (ma_sv, ma_sk, cam_ket, thoi_gian_dang_ky) VALUES (?, ?, ?, ?)",
            (ma_sv, ma_sk, cam_ket, datetime.now().isoformat(timespec="seconds"))
        )
        db.commit()
        db.close()
        return jsonify({"ok": True, "thong_diep": f"Đăng ký thành công sự kiện: {su_kien['ten_sk']}"})
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/diem_danh_qr", methods=["POST", "OPTIONS"])
def api_diem_danh_qr():
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200

    data = request.get_json(silent=True) or request.form
    if not data:
        return jsonify({"ok": False, "thong_diep": "Không nhận được dữ liệu!"}), 400

    mssv = str(data.get('mssv', '')).strip().upper()
    ten_sk = str(data.get('ten_su_kien', '')).strip()
    ho_ten = str(data.get('ho_ten', '')).strip()

    if not mssv or not ten_sk:
        return jsonify({"ok": False, "thong_diep": "Thiếu MSSV hoặc Tên sự kiện!"}), 400

    db = ket_noi_db()
    try:
        sv = db.execute("SELECT ho_ten FROM sinh_vien WHERE ma_sv = ?", (mssv,)).fetchone()
        
        # --- ĐÃ SỬA: ÉP LƯU BẤT CHẤP ---
        # Nếu chưa có mã sinh viên này trong Python, TỰ ĐỘNG THÊM VÀO không cần hỏi nhiều!
        if not sv:
            ten_hien_thi = ho_ten if ho_ten else "Sinh viên (Tự động thêm)"
            tg = datetime.now().isoformat(timespec="seconds")
            db.execute("INSERT INTO sinh_vien (ma_sv, ho_ten, ngay_sinh, lop, khoa, thoi_gian_tao) VALUES (?, ?, ?, ?, ?, ?)",
                       (mssv, ten_hien_thi, "", "", "", tg))
            db.commit()
        else:
            ten_hien_thi = sv['ho_ten']

        da_diem_danh = db.execute("SELECT 1 FROM diem_danh WHERE ma_sv = ? AND ten_su_kien = ?", (mssv, ten_sk)).fetchone()
        if da_diem_danh:
            return jsonify({"ok": False, "thong_diep": f"Sinh viên {ten_hien_thi} đã điểm danh sự kiện này rồi!"}), 400

        tg = datetime.now().isoformat(timespec="seconds")
        db.execute("INSERT INTO diem_danh (ma_sv, ten_su_kien, thoi_gian, duong_anh) VALUES (?, ?, ?, ?)",
                   (mssv, ten_sk, tg, "QUET_THU_CONG"))
        db.commit()
        
        return jsonify({"ok": True, "thong_diep": f"Điểm danh thành công: {ten_hien_thi}"})
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500
    finally:
        db.close()

@app.route("/api/lich_su_diem_danh", methods=["GET"])
def api_lich_su_diem_danh():
    try:
        ten_sk = request.args.get("su_kien")
        db = ket_noi_db()
        query = "SELECT d.ma_sv, s.ho_ten, s.lop, s.khoa, d.thoi_gian FROM diem_danh d JOIN sinh_vien s ON s.ma_sv = d.ma_sv"
        params = []
        if ten_sk:
            query += " WHERE d.ten_su_kien = ?"
            params.append(ten_sk)
        query += " ORDER BY d.thoi_gian DESC"
        rows = db.execute(query, params).fetchall()
        tong_sv = db.execute("SELECT COUNT(*) FROM sinh_vien").fetchone()[0]
        db.close()
        
        data_list = []
        for r in rows:
            khoa = r['khoa'] if r['khoa'] else "N/A"
            lop = r['lop'] if r['lop'] else "N/A"
            data_list.append({
                "mssv": r["ma_sv"], 
                "name": r["ho_ten"], 
                "class": f"{khoa} - {lop}", 
                "time": r["thoi_gian"], 
                "status": "Hợp lệ"
            })
            
        return jsonify({"data": data_list, "total_system": tong_sv})
    except Exception as e:
        print("LỖI API LỊCH SỬ:", str(e))
        return jsonify({"data": [], "total_system": 0, "error": str(e)})

@app.route("/dang_ky", methods=["POST", "OPTIONS"])
def dang_ky():
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200
    ma_sv, ho_ten = (request.form.get("ma_sv") or "").strip().upper(), (request.form.get("ho_ten") or "").strip()
    f = request.files.get("anh")
    if not ma_sv or not ho_ten: return jsonify({"ok": False, "thong_diep": "Thiếu dữ liệu đăng ký"}), 400
    if f:
        img = lay_anh_tu_bytes(f.read())
        if img is not None:
            xam = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
            _, caps = tim_tat_ca_mat_va_khung(img)
            mat_xam = caps[0][0] if caps else cv2.resize(xam, (200, 200))
            cv2.imwrite(str(THU_MAT_KHUON / f"{ma_sv}.png"), mat_xam)
    
    db = ket_noi_db()
    try:
        db.execute("INSERT INTO sinh_vien (ma_sv, ho_ten, ngay_sinh, lop, khoa, thoi_gian_tao) VALUES (?, ?, ?, ?, ?, ?)",
                   (ma_sv, ho_ten, request.form.get("ngay_sinh", ""), request.form.get("lop", ""), request.form.get("khoa", ""), datetime.now().isoformat(timespec="seconds")))
        db.commit()
        return jsonify({"ok": True, "thong_diep": "Thêm khuôn mặt thành công!"})
    except sqlite3.IntegrityError:
        return jsonify({"ok": False, "thong_diep": "Mã SV đã tồn tại bên Python!"}), 400
    finally:
        db.close()

@app.route("/sinh_vien/<ma_sv>/sua", methods=["POST", "OPTIONS"])
def sua_sinh_vien(ma_sv):
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200
    ma_sv = (ma_sv or "").strip().upper()
    db = ket_noi_db()
    ho_ten = (request.form.get("ho_ten") or "").strip()
    f = request.files.get("anh")
    if f:
        img = lay_anh_tu_bytes(f.read())
        if img is not None:
            xam = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
            _, caps = tim_tat_ca_mat_va_khung(img)
            mat_xam = caps[0][0] if caps else cv2.resize(xam, (200, 200))
            cv2.imwrite(str(THU_MAT_KHUON / f"{ma_sv}.png"), mat_xam)
    db.execute("UPDATE sinh_vien SET ho_ten=?, ngay_sinh=?, lop=?, khoa=? WHERE ma_sv = ?",
               (ho_ten, request.form.get("ngay_sinh", ""), request.form.get("lop", ""), request.form.get("khoa", ""), ma_sv))
    db.commit()
    db.close()
    return jsonify({"ok": True, "thong_diep": "Sửa thành công!"})

@app.route("/sinh_vien/<ma_sv>/xoa", methods=["POST", "OPTIONS"])
def xoa_sinh_vien(ma_sv):
    if request.method == "OPTIONS":
        return jsonify({"ok": True}), 200
    ma_sv = (ma_sv or "").strip().upper()
    db = ket_noi_db()
    db.execute("DELETE FROM diem_danh WHERE UPPER(ma_sv) = ?", (ma_sv,))
    db.execute("DELETE FROM sinh_vien WHERE UPPER(ma_sv) = ?", (ma_sv,))
    db.commit()
    db.close()
    return jsonify({"ok": True, "thong_diep": "Xóa thành công!"})

@app.route("/api/thong_ke_tong_ket", methods=["GET"])
def api_thong_ke_tong_ket():
    db = ket_noi_db()
    students = db.execute("SELECT ma_sv, ho_ten, khoa, lop FROM sinh_vien").fetchall()
    result = []
    for sv in students:
        lich_su = db.execute("""
            SELECT
                IFNULL(sk.gio_ctxh, 0) AS gio_ctxh,
                d.id AS diem_danh_id,
                dk.id AS dang_ky_id
            FROM su_kien sk
            LEFT JOIN diem_danh d
                ON d.ten_su_kien = sk.ten_sk
               AND UPPER(d.ma_sv) = UPPER(?)
            LEFT JOIN dang_ky_su_kien dk
                ON UPPER(dk.ma_sk) = UPPER(sk.ma_sk)
               AND UPPER(dk.ma_sv) = UPPER(?)
            WHERE d.id IS NOT NULL OR dk.id IS NOT NULL
        """, (sv["ma_sv"], sv["ma_sv"])).fetchall()

        tong_ctxh = 0.0
        for row in lich_su:
            gio_ctxh = float(row["gio_ctxh"] or 0)
            is_present = row["diem_danh_id"] is not None
            is_registered = row["dang_ky_id"] is not None

            if is_present:
                tong_ctxh += gio_ctxh
            elif is_registered and gio_ctxh > 0:
                tong_ctxh -= 0.5

        result.append({
            "hoTen": sv["ho_ten"],
            "maSV": sv["ma_sv"],
            "khoa": sv["khoa"],
            "gioTichLuy": round(tong_ctxh, 2),
            "gioYeuCau": 15
        })
    db.close()
    return jsonify(result)

@app.route("/api/thong_ke_sinh_vien", methods=["GET"])
def api_thong_ke_sinh_vien():
    try:
        ma_sv = (request.args.get("ma_sv") or "").strip().upper()
        if not ma_sv:
            return jsonify({"ok": False, "thong_diep": "Thiếu mã sinh viên!"}), 400

        db = ket_noi_db()
        lich_su = db.execute("""
            SELECT
                sk.ten_sk AS ten_su_kien,
                IFNULL(sk.dia_diem, 'Chưa cập nhật') AS dia_diem,
                IFNULL(sk.gio_ctxh, 0) AS so_ngay_ctxh,
                IFNULL(sk.thoi_gian_to_chuc, '') AS thoi_gian_to_chuc,
                d.thoi_gian AS thoi_gian_diem_danh,
                CASE WHEN d.id IS NULL THEN 'Vắng' ELSE 'Có mặt' END AS trang_thai
            FROM su_kien sk
            LEFT JOIN diem_danh d
                ON d.ten_su_kien = sk.ten_sk
               AND d.ma_sv = ?
            LEFT JOIN dang_ky_su_kien dk
                ON UPPER(dk.ma_sk) = UPPER(sk.ma_sk)
               AND dk.ma_sv = ?
            WHERE d.id IS NOT NULL OR dk.id IS NOT NULL
            ORDER BY sk.ten_sk ASC
        """, (ma_sv, ma_sv)).fetchall()
        db.close()

        tong_su_kien = len(lich_su)
        co_mat = sum(1 for row in lich_su if row["trang_thai"] == "Có mặt")
        vang = tong_su_kien - co_mat
        tong_ngay_ctxh = 0.0
        for row in lich_su:
            is_present = row["trang_thai"] == "Có mặt"
            so_ngay_ctxh = float(row["so_ngay_ctxh"] or 0)
            if is_present:
                tong_ngay_ctxh += so_ngay_ctxh
            else:
                # Đăng ký nhưng vắng: trừ 0.5 CTXH nếu sự kiện có tính giờ CTXH.
                if so_ngay_ctxh > 0:
                    tong_ngay_ctxh -= 0.5

        return jsonify({
            "ok": True,
            "tong_su_kien": tong_su_kien,
            "co_mat": co_mat,
            "vang": vang,
            "tong_ngay_ctxh": round(tong_ngay_ctxh, 2),
            "lich_su": [{
                "ten_su_kien": row["ten_su_kien"],
                "dia_diem": row["dia_diem"],
                "thoi_gian_to_chuc": row["thoi_gian_to_chuc"],
                "thoi_gian_diem_danh": row["thoi_gian_diem_danh"],
                "trang_thai": row["trang_thai"],
                "so_ngay_ctxh": float(row["so_ngay_ctxh"] or 0)
            } for row in lich_su]
        })
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/su_kien_moi_nhat_chi_tiet", methods=["GET"])
def api_su_kien_moi_nhat_chi_tiet():
    try:
        db = ket_noi_db()
        su_kien = db.execute("""
            SELECT *
            FROM su_kien
            ORDER BY
                CASE WHEN IFNULL(thoi_gian_to_chuc, '') = '' THEN 1 ELSE 0 END,
                thoi_gian_to_chuc DESC,
                ma_sk DESC
            LIMIT 1
        """).fetchone()

        if not su_kien:
            db.close()
            return jsonify({"ok": True, "event": None})

        ds_dang_ky = db.execute("""
            SELECT dk.ma_sv, IFNULL(sv.ho_ten, '') AS ho_ten, IFNULL(sv.lop, 'Chưa cập nhật') AS lop, IFNULL(sv.khoa, 'Chưa cập nhật') AS khoa
            FROM dang_ky_su_kien dk
            LEFT JOIN sinh_vien sv ON sv.ma_sv = dk.ma_sv
            WHERE UPPER(dk.ma_sk) = UPPER(?)
            ORDER BY dk.thoi_gian_dang_ky DESC
        """, (su_kien["ma_sk"],)).fetchall()
        db.close()

        so_luong = int(su_kien["so_luong"] or 0)
        so_da_dang_ky = len(ds_dang_ky)
        slot_con_lai = (so_luong - so_da_dang_ky) if so_luong > 0 else -1

        return jsonify({
            "ok": True,
            "event": {
                "maSK": su_kien["ma_sk"],
                "tenSK": su_kien["ten_sk"],
                "diaDiem": su_kien["dia_diem"],
                "thoiGianToChuc": su_kien["thoi_gian_to_chuc"],
                "soLuong": so_luong,
                "soDangKy": so_da_dang_ky,
                "slotConLai": slot_con_lai,
                "sinhVienDangKy": [{
                    "maSV": row["ma_sv"],
                    "hoTen": row["ho_ten"] if row["ho_ten"] else "Chưa đồng bộ",
                    "lop": row["lop"],
                    "khoa": row["khoa"],
                } for row in ds_dang_ky]
            }
        })
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

@app.route("/api/chi_tiet_dang_ky_su_kien", methods=["GET"])
def api_chi_tiet_dang_ky_su_kien():
    try:
        ma_sk = (request.args.get("maSK") or "").strip().upper()
        if not ma_sk:
            return jsonify({"ok": False, "thong_diep": "Thiếu mã sự kiện!"}), 400

        db = ket_noi_db()
        su_kien = db.execute("SELECT ma_sk, ten_sk FROM su_kien WHERE UPPER(ma_sk) = ?", (ma_sk,)).fetchone()
        if not su_kien:
            db.close()
            return jsonify({"ok": False, "thong_diep": "Không tìm thấy sự kiện!"}), 404

        ds_dang_ky = db.execute("""
            SELECT dk.ma_sv, IFNULL(sv.ho_ten, '') AS ho_ten, IFNULL(sv.lop, 'Chưa cập nhật') AS lop, IFNULL(sv.khoa, 'Chưa cập nhật') AS khoa
            FROM dang_ky_su_kien dk
            LEFT JOIN sinh_vien sv ON sv.ma_sv = dk.ma_sv
            WHERE UPPER(dk.ma_sk) = ?
            ORDER BY dk.thoi_gian_dang_ky DESC
        """, (ma_sk,)).fetchall()
        db.close()

        return jsonify({
            "ok": True,
            "maSK": su_kien["ma_sk"],
            "tenSK": su_kien["ten_sk"],
            "sinhVienDangKy": [{
                "maSV": row["ma_sv"],
                "hoTen": row["ho_ten"] if row["ho_ten"] else "Chưa đồng bộ",
                "lop": row["lop"],
                "khoa": row["khoa"],
            } for row in ds_dang_ky]
        })
    except Exception as e:
        return jsonify({"ok": False, "thong_diep": f"Lỗi hệ thống: {str(e)}"}), 500

if __name__ == "__main__":
    khoi_tao_bang()
    app.run(debug=True, host="127.0.0.1", port=5000)