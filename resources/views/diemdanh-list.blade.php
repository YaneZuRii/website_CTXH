@extends('layouts.app')

@section('title', 'Lịch sử Điểm danh - CTXH')
@section('page-title', 'Dữ liệu Điểm danh')

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2b3674;">Điểm danh Sự kiện</h4>
            <p class="text-muted small mb-0"><i class="fa-regular fa-clock me-1"></i>Cập nhật lần cuối lúc <span id="last-update">...</span></p>
        </div>
        <button class="btn text-white fw-bold shadow-sm" style="background-color: #05cd99; border-radius: 10px;">
            <i class="fa-solid fa-file-excel me-2"></i>Xuất Excel
        </button>
    </div>

    {{-- Bảng Điều Khiển --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Chọn ngày</label>
                    <input type="date" class="form-control bg-light border-0" id="filter-date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-9">
                    <label class="form-label text-muted small fw-bold">Chọn sự kiện đang diễn ra</label>
                    <select class="form-select bg-light border-0 fw-bold text-primary" id="event-select" onchange="loadAttendance()">
                        <option value="">-- Đang tải sự kiện... --</option>
                    </select>
                </div>
            </div>

            <hr class="my-4" style="border-color: #e2e8f0; border-style: dashed;">

            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <form id="form-manual-checkin" onsubmit="return false;" class="d-flex gap-2">
                        <input type="text" id="manual-mssv" class="form-control bg-light border-0" placeholder="Quét mã QR (ví dụ: CTXH-DH52...) hoặc nhập MSSV..." required>
                        <button type="submit" class="btn text-white fw-bold px-4" style="background-color: #2b3674; border-radius: 8px; white-space: nowrap;">
                            <i class="fa-solid fa-qrcode me-2"></i>Xác nhận
                        </button>
                    </form>
                </div>
                <div class="col-md-1 text-center text-muted fw-bold small">HOẶC</div>
                <div class="col-md-3">
                    <a href="/diem-danh" class="btn w-100 fw-bold text-white shadow-sm" style="background-color: #05cd99; border-radius: 8px;">
                        Bật Camera Quét AI
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Thẻ Thống Kê --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 5px solid #4318ff !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(67, 24, 255, 0.1); color: #4318ff; font-size: 1.5rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-1">TỔNG SINH VIÊN</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-total">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 5px solid #05cd99 !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(5, 205, 153, 0.1); color: #05cd99; font-size: 1.5rem;">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-1">ĐÃ CÓ MẶT</p>
                        <h4 class="fw-bold mb-0" style="color: #05cd99;" id="stat-present">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: 5px solid #ef4444 !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.5rem;">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-1">CHƯA CÓ MẶT</p>
                        <h4 class="fw-bold mb-0" style="color: #ef4444;" id="stat-absent">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng Dữ Liệu --}}
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center py-3 text-muted small">STT</th>
                        <th class="py-3 text-muted small">MÃ SINH VIÊN</th>
                        <th class="py-3 text-muted small">HỌ VÀ TÊN</th>
                        <th class="py-3 text-muted small">KHOA / LỚP</th>
                        <th class="py-3 text-muted small">THỜI GIAN CHECK-IN</th>
                        <th class="text-center py-3 text-muted small">TRẠNG THÁI</th>
                    </tr>
                </thead>
                <tbody id="attendance-tbody">
                    <tr><td colspan="6" class="text-center py-5 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Đang tải dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    document.getElementById('last-update').innerText = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

    // --- BỘ TỪ ĐIỂN TÊN TỪ LARAVEL ---
    const laravelStudents = @json($sinhviens ?? []);
    const studentDict = {};
    laravelStudents.forEach(s => {
        studentDict[s.maSV.toUpperCase()] = s.name; // Lưu mã và tên để tra cứu chéo
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadEvents();
        await loadAttendance(); 
    });

    function parseStudentCode(rawValue) {
        const raw = (rawValue || '').trim().toUpperCase();
        if (!raw) return '';

        // Nếu quét đúng định dạng QR nội bộ: CTXH-MSSV
        if (raw.startsWith('CTXH-')) {
            return raw.replace('CTXH-', '').trim();
        }

        // Nếu quét MSSV thường
        return raw;
    }

    async function loadEvents() {
        const select = document.getElementById('event-select');
        try {
            const cacheBuster = new Date().getTime();
            const response = await fetch('http://127.0.0.1:5000/api/danh_sach_su_kien?t=' + cacheBuster);
            if (response.ok) {
                const events = await response.json();
                select.innerHTML = '<option value="">-- Tất cả điểm danh --</option>';
                events.forEach(ev => {
                    select.innerHTML += `<option value="${ev.tenSK}">${ev.tenSK}</option>`;
                });
            }
        } catch (error) {
            select.innerHTML = '<option value="">Lỗi kết nối máy chủ AI</option>';
        }
    }

    async function loadAttendance() {
        const eventName = document.getElementById('event-select').value;
        const tbody = document.getElementById('attendance-tbody');
        
        try {
            const cacheBuster = new Date().getTime();
            const response = await fetch(`http://127.0.0.1:5000/api/lich_su_diem_danh?su_kien=${encodeURIComponent(eventName)}&t=${cacheBuster}`);
            
            if (response.ok) {
                const dataObj = await response.json();
                const list = dataObj.data || [];
                const totalSys = dataObj.total_system || 0;
                const present = list.length;
                const absent = totalSys > present ? (totalSys - present) : 0;

                document.getElementById('stat-total').innerText = totalSys;
                document.getElementById('stat-present').innerText = present;
                document.getElementById('stat-absent').innerText = absent;

                tbody.innerHTML = '';
                if(list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Chưa có ai điểm danh cho sự kiện này.</td></tr>`;
                    return;
                }

                list.forEach((sv, idx) => {
                    const mssvKey = sv.mssv.toUpperCase();
                    // PHÉP THUẬT Ở ĐÂY: Nếu tên bị lỗi hoặc tự động thêm, lấy tên thật đắp vào!
                    let realName = sv.name;
                    if (realName.includes("Tự động thêm") && studentDict[mssvKey]) {
                        realName = studentDict[mssvKey];
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center text-muted fw-bold">${idx + 1}</td>
                            <td class="fw-bold" style="color: #2b3674;">${sv.mssv}</td>
                            <td class="fw-bold" style="color: #4318ff;">${realName}</td>
                            <td class="text-muted small">${sv.class}</td>
                            <td class="text-muted small">${sv.time.replace('T', ' ')}</td>
                            <td class="text-center"><span class="badge" style="background-color: #dcfce7; color: #16a34a; border: 1px solid #86efac;"><i class="fa-solid fa-check me-1"></i>Hợp lệ</span></td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Không thể kết nối với máy chủ AI (Cổng 5000)</td></tr>`;
        }
    }

    // XỬ LÝ QUÉT BẰNG TAY (Gửi kèm Tên)
    document.getElementById('form-manual-checkin').addEventListener('submit', async function(e) {
        e.preventDefault();
        const mssvInput = document.getElementById('manual-mssv');
        const mssv = parseStudentCode(mssvInput.value);
        const eventName = document.getElementById('event-select').value;

        if(!eventName) {
            alert("Vui lòng chọn sự kiện ở mục thả xuống trước khi điểm danh!");
            return;
        }

        // Tự động tìm tên của sinh viên này để gửi cho Python
        const hoTenGuiDi = studentDict[mssv] || ""; 

        try {
            const response = await fetch('http://127.0.0.1:5000/api/diem_danh_qr', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mssv: mssv, ho_ten: hoTenGuiDi, ten_su_kien: eventName })
            });
            const result = await response.json();
            
            if(response.ok && result.ok) {
                mssvInput.value = ''; 
                loadAttendance(); 
            } else {
                alert("LỖI: " + result.thong_diep);
            }
        } catch (error) {
            alert("Lỗi kết nối đến máy chủ AI!");
        }
    });
</script>
@endsection