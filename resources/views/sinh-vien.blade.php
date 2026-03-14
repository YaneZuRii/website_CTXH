@extends('layouts.app')

@section('title', 'Quản lý Sinh Viên - CTXH')

@section('page-title', 'Danh sách Sinh viên')

@section('content')
<div class="container-fluid px-4 pb-4">
    <div class="modern-card p-0 overflow-hidden">
        {{-- Search + Add button in header --}}
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="input-group w-25">
                <input type="text" class="form-control" placeholder="Tìm MSSV, tên..." style="border-radius: 8px;">
            </div>
            <button class="btn btn-primary" style="background-color: #4318ff; border: none; border-radius: 8px;">
                <i class="fa-solid fa-plus me-2"></i>Thêm sinh viên
            </button>
        </div>
        <div class="table-responsive">
            <table class="table modern-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">MSSV</th>
                        <th>Họ và Tên</th>
                        <th>Khoa / Lớp</th>
                        <th>Tổng giờ CTXH</th>
                        <th class="text-center pe-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- todo: connect to Student model, add pagination --}}
                    <tr>
                        <td class="ps-4 fw-bold">DH52200837</td>
                        <td class="fw-bold" style="color: #2b3674;">Lê Duy Khang</td>
                        <td class="text-muted">CNTT - DH52200837</td>
                        <td><span class="badge" style="background: rgba(67, 24, 255, 0.1); color: #4318ff;">45 Giờ</span></td>
                        <td class="text-center pe-4"><span class="status-badge" style="background: rgba(5, 205, 153, 0.1); color: #05cd99;">Đủ điều kiện</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-bold">21000789</td>
                        <td class="fw-bold" style="color: #2b3674;">Nguyễn Trọng Dương</td>
                        <td class="text-muted">CNTT - DH.........</td>
                        <td><span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">12 Giờ</span></td>
                        <td class="text-center pe-4"><span class="status-badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Cảnh báo</span></td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-bold">21000789</td>
                        <td class="fw-bold" style="color: #2b3674;">Lê Duy Khánh</td>
                        <td class="text-muted">CNTT - DH.........</td>
                        <td><span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">12 Giờ</span></td>
                        <td class="text-center pe-4"><span class="status-badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Cảnh báo</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

