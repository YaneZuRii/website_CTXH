@extends('layouts.app')

@section('title', 'Danh sách Điểm danh - CTXH')

@section('page-title', 'Dữ Liệu Điểm Danh')

@section('content')
<div class="container-fluid px-4 pb-4">
    <div class="modern-card p-0 overflow-hidden">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #e9edf7 !important;">
            <div>
                <h5 class="fw-bold mb-1" style="color: #2b3674;">Chiến dịch: Mùa Hè Xanh 2026</h5>
                <p class="text-muted small mb-0"><i class="fa-solid fa-rotate me-1"></i> Cập nhật lần cuối lúc 15:30</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #4318ff; border: none;">
                <i class="fa-solid fa-download me-2"></i> Xuất Excel
            </button>
        </div>

        <div class="table-responsive">
            <table class="table modern-table mb-0">
                <thead>
                    <tr>
                        <th width="60" class="text-center ps-4">STT</th>
                        <th>Mã Sinh Viên</th>
                        <th>Họ và Tên</th>
                        <th>Khoa / Lớp</th>
                        <th>Thời gian Check-in</th>
                        <th class="text-center pe-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold text-muted ps-4">1</td>
                        <td class="text-muted fw-semibold">DH52200837</td>
                        <td class="fw-bold" style="color: #2b3674;">Lê Duy Khang</td>
                        <td class="text-muted fw-semibold">CNTT - DH52200837</td>
                        <td class="text-muted fw-semibold">07:15:22 - 13/03/2026</td>
                        <td class="text-center pe-4">
                            <span class="status-badge" style="background: rgba(5, 205, 153, 0.1); color: #05cd99;">
                                <i class="fa-solid fa-check me-1"></i> Hợp lệ
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold text-muted ps-4">2</td>
                        <td class="text-muted fw-semibold">DH...........</td>
                        <td class="fw-bold" style="color: #2b3674;">Lê Duy Khánh</td>
                        <td class="text-muted fw-semibold">CNTT - DH...........</td>
                        <td class="text-muted fw-semibold">07:45:10 - 13/03/2026</td>
                        <td class="text-center pe-4">
                            <span class="status-badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fa-solid fa-clock me-1"></i> Trễ giờ
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold text-muted ps-4">3</td>
                        <td class="text-muted fw-semibold">DH...........</td>
                        <td class="fw-bold" style="color: #2b3674;">Nguyễn Trọng Dương</td>
                        <td class="text-muted fw-semibold">CNTT - DH............</td>
                        <td class="text-muted fw-semibold">-- : -- : --</td>
                        <td class="text-center pe-4">
                            <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                <i class="fa-solid fa-xmark me-1"></i> Vắng mặt
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection

