@extends('layouts.app')

@section('title', 'Quản lý Điểm danh - CTXH')
@section('page-title', 'Dữ Liệu Điểm Danh')

@section('content')
<div class="container-fluid px-4 pb-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2b3674;">Điểm danh Sự kiện</h4>
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-clock me-1"></i> Cập nhật lần cuối lúc <span id="lastUpdateTime" class="fw-semibold">15:30</span>
            </p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #18ff93; border: none; border-radius: 8px;">
            <i class="fa-solid fa-file-excel me-2"></i> Xuất Excel
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small mb-1">Chọn ngày</label>
                    <input type="date" class="form-control form-control-lg bg-light border-0" id="filter-date" value="2026-03-13">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold text-muted small mb-1">Chọn sự kiện</label>
                    <select class="form-select form-select-lg bg-light border-0 fw-semibold" id="filter-event" style="color: #79ec96;">
                        <option value="1" selected>Mùa Hè Xanh 2026</option>
                        <option value="2">Hiến máu tình nguyện</option>
                        <option value="3">Hỗ trợ tân sinh viên</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #4318ff !important;">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: #4318ff;">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-bold small mb-0">TỔNG SỐ SINH VIÊN</p>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;" id="stat-total">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #10b981 !important;">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: #10b981;">
                        <i class="fa-solid fa-user-check fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-bold small mb-0">CÓ MẶT</p>
                        <h3 class="fw-bold mb-0 text-success" id="stat-present">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #ef4444 !important;">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; color: #ef4444;">
                        <i class="fa-solid fa-user-xmark fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-bold small mb-0">VẮNG MẶT</p>
                        <h3 class="fw-bold mb-0 text-danger" id="stat-absent">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-4 bg-light rounded" style="border: 1px dashed #cbd5e1;">
            <form onsubmit="event.preventDefault(); quickCheckIn();" class="d-flex gap-3 align-items-center flex-wrap">
                <div class="flex-grow-1">
                    <input type="text" id="checkin-mssv" class="form-control form-control-lg border-0 shadow-sm" placeholder="Nhập MSSV ..." required autofocus style="font-family: monospace;">
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm" id="btnCheckIn" style="background-color: #29dd83; border: none; border-radius: 8px;">
                    <i class="fa-solid fa-qrcode me-2"></i> Điểm danh ngay
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="text-center text-muted py-3 fw-bold small text-uppercase" style="width: 80px;">STT</th>
                        <th class="text-muted py-3 fw-bold small text-uppercase">Mã Sinh Viên</th>
                        <th class="text-muted py-3 fw-bold small text-uppercase">Họ và Tên</th>
                        <th class="text-muted py-3 fw-bold small text-uppercase">Khoa / Lớp</th>
                        <th class="text-muted py-3 fw-bold small text-uppercase">Thời gian Check-in</th>
                        <th class="text-center text-muted py-3 fw-bold small text-uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="attendanceListContainer" class="border-top-0">
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Đang tải dữ liệu điểm danh...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = '{{ url("/api") }}';

    async function loadAttendance() {
        try {
            // Khi có API thật: const response = await fetch(`${API_URL}/attendance`); ...
            
            // Dữ liệu mẫu
            const data = [
                { stt: 1, mssv: "DH52200837", name: "Lê Duy Khang", class: "CNTT - DH52200837", time: "07:15:22 - 13/03/2026", status: "Hợp lệ" },
                { stt: 2, mssv: "DH52200123", name: "Lê Duy Khánh", class: "CNTT - DH52200123", time: "07:45:10 - 13/03/2026", status: "Trễ giờ" },
                { stt: 3, mssv: "DH52200456", name: "Nguyễn Trọng Dương", class: "CNTT - DH52200456", time: "-- : -- : --", status: "Vắng mặt" },
                { stt: 4, mssv: "DH52200789", name: "Trần Văn B", class: "CNTT - DH52200789", time: "-- : -- : --", status: "Vắng mặt" }
            ];

            const tbody = document.getElementById('attendanceListContainer');
            
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">Chưa có sinh viên nào trong danh sách.</td></tr>`;
                updateStats(0, 0, 0);
                return;
            }

            let presentCount = 0;
            let absentCount = 0;

            tbody.innerHTML = data.map(item => {
                let badgeClass = 'bg-danger bg-opacity-10 text-danger';
                let iconClass = 'fa-xmark';
                
                // Cập nhật thống kê và màu sắc UI
                if (item.status === 'Hợp lệ') {
                    badgeClass = 'bg-success bg-opacity-10 text-success';
                    iconClass = 'fa-check';
                    presentCount++;
                } else if (item.status === 'Trễ giờ') {
                    badgeClass = 'bg-warning bg-opacity-10 text-warning';
                    iconClass = 'fa-clock';
                    presentCount++; // Trễ giờ vẫn tính là có mặt
                } else {
                    absentCount++;
                }

                return `
                <tr>
                    <td class="text-center fw-semibold text-muted py-3">${item.stt}</td>
                    <td class="fw-bold" style="font-family: monospace; font-size: 1.05rem; color: #2b3674;">${item.mssv}</td>
                    <td class="fw-bold" style="color: #2b3674;">${item.name}</td>
                    <td class="text-muted">${item.class}</td>
                    <td class="text-muted" style="font-family: monospace;">${item.time}</td>
                    <td class="text-center">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold ${badgeClass}">
                            <i class="fa-solid ${iconClass} me-1"></i> ${item.status}
                        </span>
                    </td>
                </tr>
                `;
            }).join('');

            // Gọi hàm cập nhật thẻ thống kê
            updateStats(data.length, presentCount, absentCount);

            // Cập nhật giờ lấy dữ liệu
            const now = new Date();
            document.getElementById('lastUpdateTime').innerText = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        } catch (error) {
            console.error('Lỗi load danh sách:', error);
            document.getElementById('attendanceListContainer').innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger fw-bold">Lỗi kết nối API!</td></tr>`;
        }
    }

    // Cập nhật số liệu trên 3 thẻ Card
    function updateStats(total, present, absent) {
        document.getElementById('stat-total').innerText = total;
        document.getElementById('stat-present').innerText = present;
        document.getElementById('stat-absent').innerText = absent;
    }

    // Hàm Xử lý Điểm danh nhanh
    async function quickCheckIn() {
        const input = document.getElementById('checkin-mssv');
        const mssv = input.value.trim();
        const btn = document.getElementById('btnCheckIn');

        if (!mssv) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý...';

        try {
            await new Promise(resolve => setTimeout(resolve, 500)); // Giả lập mạng
            input.value = '';
            input.focus();
            alert(`Điểm danh thành công cho MSSV: ${mssv}`);
            loadAttendance();
        } catch (error) {
            alert('Điểm danh thất bại!');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-qrcode me-2"></i> Điểm danh ngay';
        }
    }

    document.addEventListener('DOMContentLoaded', loadAttendance);
</script>
@endsection