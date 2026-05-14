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
                        <h3 class="fw-bold mb-0" id="total-sv-count" style="color: #2b3674;">...</h3>
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
                        <h3 class="fw-bold mb-0" id="total-event-count" style="color: #2b3674;">...</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold" style="color: #2b3674;">Danh sách sự kiện đã tạo</h5>
                </div>
                <div class="card-body p-4">
                    <div id="latest-event-detail">
                        <div class="text-center py-4 text-muted small">Đang tải dữ liệu sự kiện...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="eventStudentModalTitle">Danh sách sinh viên đăng ký</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="event-student-list-body" class="small text-muted">Đang tải dữ liệu...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    async function xemChiTietDangKy(maSK, tenSK) {
        const modalTitle = document.getElementById('eventStudentModalTitle');
        const modalBody = document.getElementById('event-student-list-body');
        modalTitle.innerText = `Danh sách đăng ký - ${tenSK}`;
        modalBody.innerHTML = 'Đang tải dữ liệu...';

        const modal = new bootstrap.Modal(document.getElementById('eventStudentModal'));
        modal.show();

        try {
            const response = await fetch(`http://127.0.0.1:5000/api/chi_tiet_dang_ky_su_kien?maSK=${encodeURIComponent(maSK)}`);
            const result = await response.json();
            if (!response.ok || !result.ok) {
                modalBody.innerHTML = `<div class="text-danger">${result.thong_diep || 'Không tải được dữ liệu.'}</div>`;
                return;
            }

            const ds = result.sinhVienDangKy || [];
            if (ds.length === 0) {
                modalBody.innerHTML = '<div class="text-muted">Chưa có sinh viên đăng ký sự kiện này.</div>';
                return;
            }

            modalBody.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th>Lớp</th>
                                <th>Khoa</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${ds.map(sv => `
                                <tr>
                                    <td>${sv.maSV}</td>
                                    <td>${sv.hoTen}</td>
                                    <td>${sv.lop}</td>
                                    <td>${sv.khoa}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } catch (error) {
            modalBody.innerHTML = '<div class="text-danger">Lỗi kết nối máy chủ AI.</div>';
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const cacheBuster = new Date().getTime();
        
        try {
            const resStudents = await fetch(`http://127.0.0.1:5000/api/danh_sach?t=${cacheBuster}`);
            const students = await resStudents.json();
            document.getElementById('total-sv-count').innerText = students.length;

            const resEvents = await fetch(`http://127.0.0.1:5000/api/danh_sach_su_kien?t=${cacheBuster}`);
            const events = await resEvents.json();
            document.getElementById('total-event-count').innerText = events.length;
            const latestContainer = document.getElementById('latest-event-detail');
            if (!events || events.length === 0) {
                latestContainer.innerHTML = '<p class="text-center text-muted small">Chưa có sự kiện nào.</p>';
            } else {
                latestContainer.innerHTML = `
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Tên sự kiện</th>
                                    <th>Mã SK</th>
                                    <th>Số lượng SV</th>
                                    <th>Còn slot</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${events.map(ev => {
                                    const soLuong = Number(ev.soLuong || 0);
                                    const soDangKy = Number(ev.soDangKy || 0);
                                    const slotConLai = Number(ev.choConLai ?? 0);
                                    const slotText = slotConLai < 0 ? 'Không giới hạn' : Math.max(0, slotConLai);
                                    return `
                                        <tr>
                                            <td class="fw-bold">${ev.tenSK}</td>
                                            <td>${ev.maSK}</td>
                                            <td>${soDangKy}${soLuong > 0 ? `/${soLuong}` : ''}</td>
                                            <td>${slotText}</td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary" onclick="xemChiTietDangKy('${ev.maSK}', '${(ev.tenSK || '').replace(/'/g, "\\'")}')">Xem chi tiết</button>
                                            </td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }

        } catch (error) {
            console.error("Lỗi cập nhật Dashboard:", error);
            const latestContainer = document.getElementById('latest-event-detail');
            if (latestContainer) {
                latestContainer.innerHTML = '<div class="alert alert-danger small py-2 text-center">Mất kết nối máy chủ AI</div>';
            }
        }
    });
</script>
@endsection