@extends('layouts.app')

@section('title', 'Quản lý Sự kiện - CTXH')
@section('page-title', 'Sự Kiện')

@section('content')
<div class="container-fluid px-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #2b3674; font-size: 2rem;">Danh sách Sự Kiện</h2>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addEventModal" style="background-color: #314dff; border: none; border-radius: 8px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm Sự Kiện
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="py-4 px-4 fw-bold" style="color: #475569;">Tên sự kiện</th>
                        <th class="py-4 fw-bold" style="color: #475569;">Mã SK</th>
                        <th class="py-4 fw-bold text-center" style="color: #475569;">Phương thức điểm danh</th>
                        <th class="py-4 fw-bold" style="color: #475569;">Nội dung</th>
                        <th class="py-4 fw-bold" style="color: #475569;">Đơn vị tổ chức</th>
                        <th class="py-4 fw-bold text-center" style="color: #475569;">Số lượng</th>
                        <th class="py-4 fw-bold text-center" style="color: #475569;">Số giờ CTXH</th>
                        <th class="py-4 fw-bold" style="color: #475569;">Địa điểm</th>
                        <th class="py-4 fw-bold text-center" style="color: #475569;">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="eventListContainer">
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div> Đang kết nối máy chủ AI...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h4 class="modal-title fw-bold" style="color: #2b3674;">Thêm Sự Kiện Mới</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addEventForm" onsubmit="event.preventDefault(); saveEvent();">
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold text-muted small">Tên sự kiện</label>
                            <input type="text" id="event-name" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold text-muted small">Nội dung sự kiện</label>
                            <textarea id="event-content" class="form-control bg-light border-0 py-2" rows="3" placeholder="Mô tả ngắn nội dung sự kiện..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Đơn vị tổ chức</label>
                            <input type="text" id="event-org" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Địa điểm</label>
                            <input type="text" id="event-loc" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Thời gian tổ chức</label>
                            <input type="datetime-local" id="event-time" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Phương thức điểm danh</label>
                            <select id="event-method" class="form-select bg-light border-0 py-2" style="border-radius: 8px;">
                                <option value="qr">Quét mã QR sinh viên</option>
                                <option value="anh">Quét khuôn mặt sinh viên</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-muted small">Số lượng (SV)</label>
                            <input type="number" id="event-qty" class="form-control bg-light border-0 py-2" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-muted small">Số giờ CTXH</label>
                            <input type="number" id="event-hours" class="form-control bg-light border-0 py-2" min="0" step="0.5" value="0" style="border-radius: 8px;">
                            <small class="text-muted">Nếu có nhập thì tối thiểu 0.5</small>
                        </div>
                    </div>
                    <button type="submit" id="btnSubmitEvent" class="btn w-100 fw-bold text-white py-3 shadow-sm" style="background-color: #05cd99; border: none; border-radius: 8px; font-size: 1.05rem;">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu sự kiện
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="attendanceModalTitle">Điểm danh sự kiện</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="attendance-mssv">
                <div class="mb-3">
                    <div class="btn-group w-100" role="group">
                        <button type="button" id="btn-mode-qr" class="btn btn-outline-primary active">Quét QR</button>
                        <button type="button" id="btn-mode-face" class="btn btn-outline-primary">Quét khuôn mặt</button>
                    </div>
                </div>
                <input type="hidden" id="attendance-hoten">
                <div class="mb-2 small text-muted" id="attendance-detected-student">Chưa nhận diện mã sinh viên.</div>
                <div class="mb-3 position-relative">
                    <video id="attendance-video" class="w-100 rounded border d-none" autoplay playsinline style="max-height: 260px; object-fit: cover;"></video>
                    <canvas id="attendance-overlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
                    <canvas id="attendance-canvas" class="d-none"></canvas>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-primary btn-sm" type="button" id="btn-open-camera">
                        <i class="fa-solid fa-camera me-1"></i>Mở camera
                    </button>
                </div>
                <div class="small text-muted" id="attendance-note"></div>
                <div class="small mt-2" id="attendance-result"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="attendanceListTitle">Danh sách điểm danh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="attendance-list-body" class="small text-muted">Đang tải dữ liệu...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    let selectedEvent = null;
    let currentAttendanceMethod = 'qr';
    let attendanceStream = null;
    let attendanceQrTimer = null;
    let attendanceCapturedBlob = null;
    let attendanceFaceAutoTimer = null;
    let isSubmittingAttendance = false;
    let lastQrValue = '';
    let lastQrAt = 0;
    let attendanceQrFallbackTimer = null;

    function formatMethod(method) {
        return method === 'anh' ? 'Quét khuôn mặt' : 'Quét mã QR';
    }

    function isTodayEvent(eventTime) {
        if (!eventTime) return false;
        const normalized = String(eventTime).trim().replace(' ', 'T');
        const eventDateObj = new Date(normalized);
        if (Number.isNaN(eventDateObj.getTime())) {
            const ngaySuKienRaw = String(eventTime).slice(0, 10);
            const now = new Date();
            const ngayHienTaiRaw = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            return ngaySuKienRaw === ngayHienTaiRaw;
        }
        const ngaySuKien = `${eventDateObj.getFullYear()}-${String(eventDateObj.getMonth() + 1).padStart(2, '0')}-${String(eventDateObj.getDate()).padStart(2, '0')}`;
        const now = new Date();
        const ngayHienTai = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        return ngaySuKien === ngayHienTai;
    }

    function updateAttendanceModeUI() {
        const isFace = currentAttendanceMethod === 'anh';
        document.getElementById('attendance-note').innerText = isFace
            ? 'Phương thức áp dụng: Quét khuôn mặt. Bấm Mở camera để hệ thống tự chụp.'
            : 'Phương thức áp dụng: Quét mã QR.';
        document.getElementById('btn-mode-qr').classList.toggle('active', !isFace);
        document.getElementById('btn-mode-face').classList.toggle('active', isFace);
    }

    function renderAttendanceResult(ok, message, svInfo) {
        const box = document.getElementById('attendance-result');
        const sv = svInfo || {};
        const maSV = sv.maSV || 'Không xác định';
        const hoTen = sv.hoTen || 'Không xác định';
        const lop = sv.lop || 'Không xác định';
        const khoa = sv.khoa || 'Không xác định';
        box.className = `small mt-2 p-2 rounded ${ok ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle'}`;
        box.innerHTML = `
            <div class="fw-bold mb-1">${message || (ok ? 'Điểm danh thành công' : 'Điểm danh thất bại')}</div>
            <div>Tên: <b>${hoTen}</b></div>
            <div>MSSV: <b>${maSV}</b></div>
            <div>Lớp: <b>${lop}</b></div>
            <div>Ngành/Khoa: <b>${khoa}</b></div>
        `;
    }

    function normalizeSvInfo(sv) {
        return {
            maSV: sv?.maSV || sv?.ma_sv || '',
            hoTen: sv?.hoTen || sv?.ho_ten || '',
            lop: sv?.lop || sv?.lopHoc || '',
            khoa: sv?.khoa || sv?.nganhHoc || ''
        };
    }

    function shouldEnrichStudentInfo(sv) {
        const name = (sv?.hoTen || '').trim().toLowerCase();
        const lop = (sv?.lop || '').trim();
        const khoa = (sv?.khoa || '').trim();
        return !name || name === 'sinh viên' || !lop || !khoa;
    }

    async function fetchLaravelStudentInfo(maSV) {
        const mssv = String(maSV || '').trim().toUpperCase();
        if (!mssv) return null;
        try {
            const response = await fetch(`/api/thong-tin-sinh-vien?ma_sv=${encodeURIComponent(mssv)}&t=${Date.now()}`);
            const result = await response.json();
            if (!response.ok || !result.success) return null;
            return normalizeSvInfo(result.data || {});
        } catch (_) {
            return null;
        }
    }

    function isMissingStudentInfo(row) {
        const hoTen = String(row?.hoTen || '').trim().toLowerCase();
        const lop = String(row?.lop || '').trim().toLowerCase();
        const khoa = String(row?.khoa || '').trim().toLowerCase();
        return (
            !hoTen || hoTen === 'chưa đồng bộ' || hoTen === 'sinh viên' ||
            !lop || lop === 'chưa cập nhật' ||
            !khoa || khoa === 'chưa cập nhật'
        );
    }

    async function enrichAttendanceRowsFromLaravel(rows) {
        const list = Array.isArray(rows) ? rows : [];
        const jobs = list.map(async (row) => {
            if (!isMissingStudentInfo(row)) return row;
            const extra = await fetchLaravelStudentInfo(row.maSV);
            if (!extra) return row;
            return {
                ...row,
                hoTen: (!row.hoTen || String(row.hoTen).trim().toLowerCase() === 'chưa đồng bộ' || String(row.hoTen).trim().toLowerCase() === 'sinh viên')
                    ? (extra.hoTen || row.hoTen)
                    : row.hoTen,
                lop: (!row.lop || String(row.lop).trim().toLowerCase() === 'chưa cập nhật')
                    ? (extra.lop || row.lop)
                    : row.lop,
                khoa: (!row.khoa || String(row.khoa).trim().toLowerCase() === 'chưa cập nhật')
                    ? (extra.khoa || row.khoa)
                    : row.khoa
            };
        });
        return Promise.all(jobs);
    }

    function stopAttendanceCamera() {
        if (attendanceQrTimer) {
            clearInterval(attendanceQrTimer);
            attendanceQrTimer = null;
        }
        if (attendanceQrFallbackTimer) {
            clearInterval(attendanceQrFallbackTimer);
            attendanceQrFallbackTimer = null;
        }
        if (attendanceFaceAutoTimer) {
            clearTimeout(attendanceFaceAutoTimer);
            attendanceFaceAutoTimer = null;
        }
        if (attendanceStream) {
            attendanceStream.getTracks().forEach(track => track.stop());
            attendanceStream = null;
        }
        const video = document.getElementById('attendance-video');
        video.classList.add('d-none');
        video.srcObject = null;
        clearAttendanceOverlay();
    }

    function clearAttendanceOverlay() {
        const overlay = document.getElementById('attendance-overlay');
        if (!overlay) return;
        if (!overlay.width) overlay.width = overlay.clientWidth || 0;
        if (!overlay.height) overlay.height = overlay.clientHeight || 0;
        const ctx = overlay.getContext('2d');
        ctx.clearRect(0, 0, overlay.width, overlay.height);
    }

    function drawAttendanceFaceBoxes(boxes) {
        const list = Array.isArray(boxes) ? boxes : [];
        const video = document.getElementById('attendance-video');
        const overlay = document.getElementById('attendance-overlay');
        if (!video || !overlay || video.classList.contains('d-none')) return;

        const w = video.clientWidth || 0;
        const h = video.clientHeight || 0;
        if (!w || !h) return;
        overlay.width = w;
        overlay.height = h;

        const ctx = overlay.getContext('2d');
        ctx.clearRect(0, 0, w, h);
        if (list.length === 0) return;

        ctx.strokeStyle = '#00e676';
        ctx.lineWidth = 2;
        for (const k of list) {
            const x = Number(k?.x || 0) * w;
            const y = Number(k?.y || 0) * h;
            const bw = Number(k?.w || 0) * w;
            const bh = Number(k?.h || 0) * h;
            if (bw > 0 && bh > 0) {
                ctx.strokeRect(x, y, bw, bh);
            }
        }
    }

    function handleDetectedQrRaw(raw) {
        const qrValue = (raw || '').trim();
        if (!qrValue) return;
        const now = Date.now();
        if (qrValue === lastQrValue && (now - lastQrAt) < 3000) {
            return;
        }
        lastQrValue = qrValue;
        lastQrAt = now;
        document.getElementById('attendance-mssv').value = qrValue;
        document.getElementById('attendance-detected-student').innerText = `Đã nhận mã: ${qrValue}`;
        submitAttendance();
    }

    function scheduleFaceAttendanceCapture() {
        if (!selectedEvent || currentAttendanceMethod !== 'anh') return;
        if (attendanceFaceAutoTimer) {
            clearTimeout(attendanceFaceAutoTimer);
            attendanceFaceAutoTimer = null;
        }
        attendanceFaceAutoTimer = setTimeout(async () => {
            try {
                const video = document.getElementById('attendance-video');
                if (!video || !video.videoWidth || !video.videoHeight) {
                    scheduleFaceAttendanceCapture();
                    return;
                }
                const canvas = document.getElementById('attendance-canvas');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                attendanceCapturedBlob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.9));
                if (!attendanceCapturedBlob) {
                    document.getElementById('attendance-detected-student').innerText = 'Không chụp được ảnh khuôn mặt. Đang thử lại...';
                    scheduleFaceAttendanceCapture();
                    return;
                }
                document.getElementById('attendance-detected-student').innerText = 'Đã chụp khuôn mặt, đang điểm danh...';
                await submitAttendance();
            } catch (_) {}
        }, 1200);
    }

    async function startAttendanceCamera() {
        try {
            stopAttendanceCamera();
            const video = document.getElementById('attendance-video');
            attendanceStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = attendanceStream;
            video.classList.remove('d-none');

            // QR mode: tự quét và điền MSSV từ camera
            if (selectedEvent && currentAttendanceMethod === 'qr' && 'BarcodeDetector' in window) {
                const detector = new BarcodeDetector({ formats: ['qr_code'] });
                attendanceQrTimer = setInterval(async () => {
                    try {
                        const codes = await detector.detect(video);
                        if (codes && codes.length > 0) {
                            handleDetectedQrRaw(codes[0].rawValue || '');
                        }
                    } catch (_) {}
                }, 700);
            } else if (selectedEvent && currentAttendanceMethod === 'qr') {
                if (typeof window.jsQR === 'function') {
                    const canvas = document.getElementById('attendance-canvas');
                    const ctx = canvas.getContext('2d', { willReadFrequently: true });
                    document.getElementById('attendance-detected-student').innerText = 'Đang quét QR bằng chế độ tương thích...';
                    attendanceQrFallbackTimer = setInterval(() => {
                        try {
                            if (!video.videoWidth || !video.videoHeight) return;
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = window.jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "dontInvert"
                            });
                            if (code && code.data) {
                                handleDetectedQrRaw(code.data);
                            }
                        } catch (_) {}
                    }, 500);
                } else {
                    document.getElementById('attendance-detected-student').innerText = 'Trình duyệt chưa hỗ trợ quét QR tự động. Vui lòng dùng Chrome/Edge bản mới.';
                }
            }
            if (selectedEvent && currentAttendanceMethod === 'anh') {
                scheduleFaceAttendanceCapture();
            }
        } catch (err) {
            alert('Không thể mở camera. Hãy kiểm tra quyền truy cập camera trên trình duyệt.');
        }
    }

    async function loadEvents() {
        const tbody = document.getElementById('eventListContainer');
        try {
            const response = await fetch('http://127.0.0.1:5000/api/danh_sach_su_kien?t=' + new Date().getTime());
            const events = await response.json();

            if(events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5 text-muted">Chưa có sự kiện nào trong hệ thống.</td></tr>';
                return;
            }

            tbody.innerHTML = events.map((event, idx) => `
                <tr class="border-bottom">
                    <td class="py-4 px-4"><span class="fw-bold" style="color: #2b3674;">${event.tenSK}</span></td>
                    <td class="py-4"><span class="badge bg-light text-muted border">${event.maSK}</span></td>
                    <td class="py-4 text-center"><span class="badge bg-info-subtle text-info">${formatMethod(event.phuongThucDiemDanh)}</span></td>
                    <td class="py-4 text-muted">${event.noiDung || ''}</td>
                    <td class="py-4">${event.donVi}</td>
                    <td class="py-4 text-center"><b>${event.soLuong}</b></td>
                    <td class="py-4 text-center"><span class="badge bg-primary-subtle text-primary">${event.gioCTXH} giờ</span></td>
                    <td class="py-4 text-muted"><i class="fa-solid fa-location-dot me-1"></i>${event.diaDiem}</td>
                    <td class="py-4 text-center">
                        ${isTodayEvent(event.thoiGianToChuc)
                            ? `<button class="btn btn-sm btn-outline-success me-1" onclick="openAttendance(${idx})">Điểm danh</button>`
                            : ``}
                        <button class="btn btn-sm btn-outline-primary" onclick="openAttendanceList('${event.maSK}', '${(event.tenSK || '').replace(/'/g, "\\'")}')">Xem DS</button>
                    </td>
                </tr>
            `).join('');
            window._eventsAdmin = events;
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5 text-danger">Lỗi kết nối máy chủ Python! Hãy đảm bảo ung_dung.py đang chạy.</td></tr>';
        }
    }

    function openAttendance(index) {
        const events = window._eventsAdmin || [];
        const event = events[index];
        if (!event) return;

        if (!isTodayEvent(event.thoiGianToChuc)) {
            alert('Chỉ điểm danh khi đến đúng ngày diễn ra sự kiện.');
            return;
        }

        selectedEvent = event;
        document.getElementById('attendanceModalTitle').innerText = `Điểm danh: ${event.tenSK}`;
        currentAttendanceMethod = event.phuongThucDiemDanh || 'qr';
        updateAttendanceModeUI();
        document.getElementById('attendance-mssv').value = '';
        document.getElementById('attendance-hoten').value = '';
        document.getElementById('attendance-detected-student').innerText = 'Chưa nhận diện mã sinh viên.';
        document.getElementById('attendance-result').innerHTML = '';
        document.getElementById('attendance-result').className = 'small mt-2';
        clearAttendanceOverlay();
        attendanceCapturedBlob = null;
        lastQrValue = '';
        lastQrAt = 0;

        const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        modal.show();
    }

    document.getElementById('btn-mode-qr').addEventListener('click', () => {
        currentAttendanceMethod = 'qr';
        updateAttendanceModeUI();
        if (attendanceStream) startAttendanceCamera();
    });

    document.getElementById('btn-mode-face').addEventListener('click', () => {
        currentAttendanceMethod = 'anh';
        updateAttendanceModeUI();
        if (attendanceStream) startAttendanceCamera();
    });

    document.getElementById('btn-open-camera').addEventListener('click', startAttendanceCamera);

    async function submitAttendance() {
        if (!selectedEvent) return;
        if (isSubmittingAttendance) return;

        const mssv = (document.getElementById('attendance-mssv').value || '').trim().toUpperCase();
        const hoTen = (document.getElementById('attendance-hoten').value || '').trim();
        if (currentAttendanceMethod === 'qr' && !mssv) {
            renderAttendanceResult(false, 'Chưa có mã QR hợp lệ.', { maSV: '', hoTen: '', lop: '', khoa: '' });
            return;
        }
        if (currentAttendanceMethod === 'anh' && !attendanceCapturedBlob) {
            renderAttendanceResult(false, 'Chưa có ảnh khuôn mặt để nhận diện.', { maSV: '', hoTen: '', lop: '', khoa: '' });
            return;
        }

        const formData = new FormData();
        formData.append('maSK', selectedEvent.maSK);
        formData.append('maSV', mssv);
        formData.append('hoTen', hoTen);
        formData.append('phuongThuc', currentAttendanceMethod);
        if (attendanceCapturedBlob) formData.append('anh', attendanceCapturedBlob, 'face_capture.jpg');

        try {
            isSubmittingAttendance = true;
            const response = await fetch('http://127.0.0.1:5000/api/diem_danh_su_kien_admin', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (currentAttendanceMethod === 'anh') {
                drawAttendanceFaceBoxes(result?.khung_mat || []);
                const soKhuonMat = Number(result?.so_khuon_mat || 0);
                if (soKhuonMat > 1) {
                    document.getElementById('attendance-detected-student').innerText = `Phát hiện ${soKhuonMat} khuôn mặt. Vui lòng chỉ để 1 người trong khung hình.`;
                }
            }
            let svInfo = normalizeSvInfo(result?.sinh_vien || {});
            if (currentAttendanceMethod === 'qr' && shouldEnrichStudentInfo(svInfo)) {
                const enriched = await fetchLaravelStudentInfo(svInfo.maSV || mssv);
                if (enriched) {
                    svInfo = {
                        maSV: svInfo.maSV || enriched.maSV,
                        hoTen: svInfo.hoTen && svInfo.hoTen.toLowerCase() !== 'sinh viên' ? svInfo.hoTen : enriched.hoTen,
                        lop: svInfo.lop || enriched.lop,
                        khoa: svInfo.khoa || enriched.khoa
                    };
                }
            }
            if (response.ok && result.ok) {
                renderAttendanceResult(true, result.thong_diep || 'Điểm danh thành công.', svInfo);
                if (currentAttendanceMethod === 'anh') {
                    attendanceCapturedBlob = null;
                    scheduleFaceAttendanceCapture();
                }
            }
            else {
                renderAttendanceResult(false, result.thong_diep || 'Điểm danh thất bại.', svInfo);
                if (currentAttendanceMethod === 'anh') {
                    attendanceCapturedBlob = null;
                    scheduleFaceAttendanceCapture();
                }
            }
        } catch (error) {
            renderAttendanceResult(false, 'Không thể kết nối máy chủ điểm danh.', { maSV: '', hoTen: '', lop: '', khoa: '' });
        } finally {
            isSubmittingAttendance = false;
        }
    }

    document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', () => {
        stopAttendanceCamera();
        attendanceCapturedBlob = null;
        clearAttendanceOverlay();
        lastQrValue = '';
        lastQrAt = 0;
    });

    async function openAttendanceList(maSK, tenSK) {
        const modal = new bootstrap.Modal(document.getElementById('attendanceListModal'));
        const body = document.getElementById('attendance-list-body');
        document.getElementById('attendanceListTitle').innerText = `Danh sách điểm danh: ${tenSK}`;
        body.innerHTML = 'Đang tải dữ liệu...';
        modal.show();

        try {
            const response = await fetch(`http://127.0.0.1:5000/api/danh_sach_diem_danh_su_kien?maSK=${encodeURIComponent(maSK)}`);
            const result = await response.json();
            if (!response.ok || !result.ok) {
                body.innerHTML = `<div class="text-danger">${result.thong_diep || 'Không tải được dữ liệu.'}</div>`;
                return;
            }
            const rows = await enrichAttendanceRowsFromLaravel(result.data || []);
            const tongThamGia = rows.filter(r => ['Đã điểm danh', 'Điểm danh không đăng ký'].includes(r.trangThai)).length;
            const renderRows = (list) => (
                list.length === 0
                    ? '<tr><td colspan="5" class="text-center text-muted">Không có dữ liệu theo bộ lọc.</td></tr>'
                    : list.map(r => `
                        <tr>
                            <td>${r.maSV}</td>
                            <td>${r.hoTen}</td>
                            <td>${r.lop}</td>
                            <td>${r.khoa}</td>
                            <td>${r.trangThai}</td>
                        </tr>
                    `).join('')
            );
            body.innerHTML = `
                <div class="d-flex justify-content-end mb-2">
                    <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">
                        Tổng sinh viên tham gia: <b>${tongThamGia}</b>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="small text-muted mb-0">Lọc trạng thái:</label>
                    <select id="attendance-status-filter" class="form-select form-select-sm" style="max-width: 240px;">
                        <option value="all">Tất cả</option>
                        <option value="tham_gia">Tham gia</option>
                        <option value="khong_dang_ky">Không đăng ký</option>
                        <option value="vang">Vắng</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Khoa</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-list-tbody">
                            ${renderRows(rows)}
                        </tbody>
                    </table>
                </div>
            `;
            const filterEl = document.getElementById('attendance-status-filter');
            const tbodyEl = document.getElementById('attendance-list-tbody');
            if (filterEl && tbodyEl) {
                filterEl.addEventListener('change', () => {
                    const value = filterEl.value;
                    let filtered = rows;
                    if (value === 'tham_gia') {
                        filtered = rows.filter(r => r.trangThai === 'Đã điểm danh');
                    } else if (value === 'khong_dang_ky') {
                        filtered = rows.filter(r => r.trangThai === 'Điểm danh không đăng ký');
                    } else if (value === 'vang') {
                        filtered = rows.filter(r => r.trangThai === 'Vắng');
                    }
                    tbodyEl.innerHTML = renderRows(filtered);
                });
            }
        } catch (error) {
            body.innerHTML = '<div class="text-danger">Lỗi kết nối máy chủ.</div>';
        }
    }

    async function saveEvent() {
        const btn = document.getElementById('btnSubmitEvent');
        const gioCtxh = parseFloat(document.getElementById('event-hours').value) || 0;

        if (gioCtxh > 0 && gioCtxh < 0.5) {
            alert('Số giờ CTXH nếu có nhập thì tối thiểu là 0.5');
            return;
        }
        if (gioCtxh <= 0) {
            alert('Phải nhập số giờ CTXH lớn hơn 0');
            return;
        }

        const payload = {
            tenSK: document.getElementById('event-name').value.trim(),
            noiDung: document.getElementById('event-content').value.trim(),
            donVi: document.getElementById('event-org').value.trim(),
            diaDiem: document.getElementById('event-loc').value.trim(),
            thoiGianToChuc: document.getElementById('event-time').value,
            phuongThucDiemDanh: document.getElementById('event-method').value,
            soLuong: parseInt(document.getElementById('event-qty').value) || 0,
            gioCTXH: gioCtxh
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

        try {
            const response = await fetch('http://127.0.0.1:5000/api/them_su_kien', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.ok) {
                alert('Thêm sự kiện mới thành công! Mã SK: ' + result.maSK);
                const modalElement = document.getElementById('addEventModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if(modalInstance) modalInstance.hide();
                document.getElementById('addEventForm').reset();
                loadEvents();
            } else {
                alert('Lỗi: ' + (result.thong_diep || 'Mã sự kiện đã tồn tại!'));
            }
        } catch (error) {
            alert('Lỗi hệ thống: Không thể kết nối với máy chủ AI (Cổng 5000)!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Lưu sự kiện';
        }
    }

    document.addEventListener('DOMContentLoaded', loadEvents);
</script>
@endsection
