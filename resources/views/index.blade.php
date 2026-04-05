@extends('layouts.app')

@section('title', 'Tổng quan - CTXH')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #4318ff;">
                        <i class="fa-solid fa-users text-white fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0 small">Tổng số SV</p>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">4</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #b943ff;">
                        <i class="fa-solid fa-layer-group text-white fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted fw-semibold mb-0 small">Tổng số Sự kiện</p>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">5</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold" style="color: #000;">Sinh viên mới cập nhật</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; min-width: 45px; background-color: #4318ff !important;">K</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Lê Duy Khang</div>
                                <div class="text-muted small">MSSV: DH52200837</div>
                            </div>
                            <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.7rem; padding: 6px 12px;">Active</span>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; min-width: 45px; background-color: #4318ff !important;">K</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Lê Duy Khánh</div>
                                <div class="text-muted small">MSSV: DH............</div>
                            </div>
                            <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.7rem; padding: 6px 12px;">Active</span>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; min-width: 45px; background-color: #4318ff !important;">D</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Nguyễn Trọng Dương</div>
                                <div class="text-muted small">MSSV: DH............</div>
                            </div>
                            <span class="badge" style="background: #fee2e2; color: #dc2626; font-size: 0.7rem; padding: 6px 12px;">InActive</span>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px; min-width: 45px; background-color: #4318ff !important;">D</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">Douw</div>
                                <div class="text-muted small">MSSV: DH............</div>
                            </div>
                            <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.7rem; padding: 6px 12px;">Active</span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="#" class="text-primary text-decoration-none fw-bold small"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-muted small">Hiến máu tình nguyện</div>
                                <div class="fw-bold text-dark">20/15 giờ</div>
                            </div>
                            <div class="text-end fw-bold" style="color: #10b981; font-size: 0.75rem;">Hoàn thành xuất sắc</div>
                        </div>
                        <div class="p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-muted small">Mùa hè Xanh</div>
                                <div class="fw-bold text-dark">15/15 giờ</div>
                            </div>
                            <div class="text-end fw-bold" style="color: #10b981; font-size: 0.75rem;">Đạt yêu cầu</div>
                        </div>
                        <div class="p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-muted small">Vắng mặt</div>
                                <div class="fw-bold text-dark">5/15 giờ</div>
                            </div>
                            <div class="text-end fw-bold" style="color: #ef4444; font-size: 0.75rem;">Cảnh cáo</div>
                        </div>
                        <div class="p-3 rounded-4" style="background: #fff5f5; border: 1px solid #f8e7e7;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-muted small">Hỗ trợ Tân SV</div>
                                <div class="fw-bold text-dark">45/15 giờ</div>
                            </div>
                            <div class="text-end fw-bold" style="color: #10b981; font-size: 0.75rem;">Vượt chỉ tiêu</div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ url('/tong-ket') }}" class="text-primary text-decoration-none fw-bold small">Xem chi tiết thống kê <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection