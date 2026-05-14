@extends('layouts.app')

@section('title', 'Tổng kết - CTXH')
@section('page-title', 'Tổng kết')

@section('content')
<div class="container-fluid px-4 pb-5">
    @php
        $sv = auth()->user();
        $maQr = $sv?->maQR ?: ('CTXH-' . strtoupper($sv?->maSV ?? ''));
        $qrPayload = $maQr;
        $qrImageUrl = 'https://quickchart.io/qr?size=220&text=' . urlencode($qrPayload);
        $avatarUrl = $sv?->avatar_url
            ? $sv->avatar_url
            : 'https://ui-avatars.com/api/?background=4318ff&color=fff&name=' . urlencode($sv?->name ?? 'SV');
    @endphp

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            @if(session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-warning py-2 small">{{ $errors->first() }}</div>
            @endif

            <div class="row g-4 align-items-center">
                <div class="col-md-3 text-center">
                    <img src="{{ $qrImageUrl }}" alt="Mã QR sinh viên" class="img-fluid border rounded-3 p-2 bg-white" style="max-width: 200px;">
                    <div class="small text-muted mt-2">Mã QR: <span class="fw-bold">{{ $maQr }}</span></div>
                </div>
                <div class="col-md-2 text-center">
                    <img src="{{ $avatarUrl }}" alt="Ảnh sinh viên" class="rounded-circle shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                    <div class="small text-muted mt-2">Ảnh sinh viên</div>
                </div>
                <div class="col-md-7">
                    <h5 class="fw-bold mb-3" style="color: #2b3674;">Thông tin sinh viên</h5>
                    <div class="row g-2 small">
                        <div class="col-sm-6"><span class="text-muted">Họ tên:</span> <span class="fw-bold">{{ $sv?->name }}</span></div>
                        <div class="col-sm-6"><span class="text-muted">MSSV:</span> <span class="fw-bold">{{ $sv?->maSV }}</span></div>
                        <div class="col-sm-6"><span class="text-muted">Lớp:</span> <span class="fw-bold">{{ $sv?->lopHoc ?: 'Chưa cập nhật' }}</span></div>
                        <div class="col-sm-6"><span class="text-muted">Ngành học:</span> <span class="fw-bold">{{ $sv?->nganhHoc ?: 'Chưa cập nhật' }}</span></div>
                        <div class="col-sm-6"><span class="text-muted">Email:</span> <span class="fw-bold">{{ $sv?->email }}</span></div>
                        <div class="col-sm-6"><span class="text-muted">Vai trò:</span> <span class="fw-bold">Sinh viên</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2b3674; font-size: 2rem;">Thống kê giờ CTXH.</h2>
            <p class="text-muted">Theo dõi tiến độ và tổng hợp <br> giờ CTXH của sinh viên từ hệ thống AI</p>
        </div>
        <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm" style="background-color: #4318ff; border: none; border-radius: 8px;">
            <i class="fa-solid fa-file-export me-2"></i>Xuất báo cáo
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #4318ff;">
                        <i class="fa-solid fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Tổng sự kiện đã tham gia</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-tong-su-kien">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #fcc43e;">
                        <i class="fa-solid fa-user-check text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Có mặt</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-co-mat">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #05cd99;">
                        <i class="fa-solid fa-user-xmark text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Vắng</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-vang">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #2b3674;">
                        <i class="fa-solid fa-calendar-day text-white"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0" style="font-size: 0.75rem;">Tổng ngày CTXH tích lũy</p>
                        <h4 class="fw-bold mb-0" style="color: #2b3674;" id="stat-tong-ngay-ctxh">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="table-responsive">
            <table class="table align-middle mb-0 text-center">
                <thead style="background-color: #f8fafc;">
                    <tr>
                        <th class="py-4 fw-bold text-muted text-start ps-4">Tên sự kiện</th>
                        <th class="py-4 fw-bold text-muted">Địa điểm</th>
                        <th class="py-4 fw-bold text-muted">Thời gian tổ chức</th>
                        <th class="py-4 fw-bold text-muted">Ngày giờ điểm danh</th>
                        <th class="py-4 fw-bold text-muted">CTXH</th>
                        <th class="py-4 fw-bold text-muted">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="tongket-table-body">
                    <tr>
                        <td colspan="6" class="py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div> Đang tải lịch sử điểm danh...
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
    const currentMaSv = @json(strtoupper(auth()->user()->maSV ?? ''));

    async function loadTongKet() {
        const tbody = document.getElementById('tongket-table-body');
        try {
            const cacheBuster = new Date().getTime();
            const response = await fetch(`http://127.0.0.1:5000/api/thong_ke_sinh_vien?ma_sv=${encodeURIComponent(currentMaSv)}&t=${cacheBuster}`);
            const data = await response.json();

            if (!response.ok || !data.ok) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-5 text-danger">Không tải được dữ liệu điểm danh.</td></tr>';
                return;
            }

            document.getElementById('stat-tong-su-kien').innerText = data.tong_su_kien || 0;
            document.getElementById('stat-co-mat').innerText = data.co_mat || 0;
            document.getElementById('stat-vang').innerText = data.vang || 0;
            document.getElementById('stat-tong-ngay-ctxh').innerText = data.tong_ngay_ctxh || 0;

            const lichSu = data.lich_su || [];
            if (lichSu.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-5 text-muted">Chưa có sự kiện nào trong hệ thống.</td></tr>';
                return;
            }

            tbody.innerHTML = lichSu.map(item => {
                const thoiGianToChuc = (item.thoi_gian_to_chuc || '').replace('T', ' ');
                const thoiGianDiemDanh = (item.thoi_gian_diem_danh || '').replace('T', ' ');
                const isCoMat = item.trang_thai === 'Có mặt';
                const soNgayCtxh = Number(item.so_ngay_ctxh || 0);
                const ctxhDelta = isCoMat ? (soNgayCtxh > 0 ? `+${soNgayCtxh}` : '0') : (soNgayCtxh > 0 ? '-0.5' : '0');
                const ctxhClass = ctxhDelta.startsWith('+') ? 'text-success' : (ctxhDelta.startsWith('-') ? 'text-danger' : 'text-muted');
                return `
                    <tr class="border-bottom">
                        <td class="py-4 text-start ps-4">
                            <div class="fw-bold" style="color: #2b3674;">${item.ten_su_kien || 'Chưa rõ sự kiện'}</div>
                        </td>
                        <td class="py-4 text-muted">${item.dia_diem || 'Chưa cập nhật'}</td>
                        <td class="py-4 text-muted">${thoiGianToChuc || 'Chưa cập nhật'}</td>
                        <td class="py-4 text-muted">${thoiGianDiemDanh || 'Vắng'}</td>
                        <td class="py-4 fw-bold ${ctxhClass}">${ctxhDelta}</td>
                        <td class="py-4">
                            <span class="badge rounded-pill px-3 py-2 ${isCoMat ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">
                                ${isCoMat ? 'Có mặt' : 'Vắng'}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');

        } catch (error) {
            console.error('Lỗi load tổng kết:', error);
            tbody.innerHTML = '<tr><td colspan="6" class="py-5 text-danger">Không thể kết nối máy chủ AI để lấy dữ liệu.</td></tr>';
        }
    }

    document.addEventListener('DOMContentLoaded', loadTongKet);
</script>
@endsection