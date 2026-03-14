@extends('layouts.app')

@section('title', 'Điểm danh & Sinh viên - CTXH')

@section('page-title', 'Quản lý Điểm danh CTXH')

@section('topbar-search')
<div class="input-group" style="width: 350px;">
    <span class="input-group-text bg-white border-0 ps-4 text-muted">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <input type="text" class="form-control border-0 shadow-none" placeholder="Tìm MSSV, Tên sinh viên...">
</div>
@endsection

@section('content')
<div class="modern-card mb-4" style="border-left: 4px solid #05cd99;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0" style="color: #2b3674;">
            <i class="fa-solid fa-clipboard-check text-success me-2"></i>Điểm Danh Hoạt Động
        </h5>
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Đang mở check-in</span>
    </div>
    
    <form id="attendanceForm" class="row g-3 align-items-end" onsubmit="event.preventDefault(); submitAttendance();">
        <div class="col-md-4">
            <label class="form-label fw-bold text-muted small">Mã Sinh Viên (MSSV)</label>
            <input type="text" id="attendance-mssv" class="form-control" placeholder="VD: DH52200837" required style="border-radius: 8px; background: #f8fafc;" autofocus>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold text-muted small">Chiến dịch</label>
            <select id="attendance-campaign" class="form-select" style="border-radius: 8px; background: #f8fafc;">
                <option value="1">Mùa Hè Xanh 2026</option>
                <option value="2">Tiếp Sức Mùa Thi</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" id="btn-submit" class="btn w-100 fw-bold text-white" style="background-color: #05cd99; border-radius: 8px;">
                <i class="fa-solid fa-qrcode me-2"></i>XÁC NHẬN CHECK-IN
            </button>
        </div>
    </form>
    
    <div id="attendance-message" class="mt-3 d-none alert p-2 small fw-semibold"></div>
</div>

<div class="modern-card p-0 overflow-hidden">
    <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #e9edf7 !important;">
        <h5 class="fw-bold mb-0" style="color: #2b3674;">
            <i class="fa-solid fa-users text-primary me-2"></i>Danh sách Sinh viên
        </h5>
        <button type="button" class="btn btn-primary fw-bold px-4" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background-color: #4318ff; border: none; border-radius: 8px;">
            <i class="fa-solid fa-user-plus me-2"></i>Thêm User
        </button>
    </div>

    <div class="table-responsive">
        <table class="table modern-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">MSSV</th>
                    <th>Họ và Tên</th>
                    <th>Trạng thái Điểm danh</th>
                    <th>Ngày tham gia</th>
                    <th class="text-center pe-4">Chỉnh sửa</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <tr>
                    <td class="ps-4 fw-bold text-primary">DH52200837</td>
                    <td class="fw-bold" style="color: #2b3674;">Lê Duy Khang</td>
                    <td><span class="badge" style="background: rgba(5, 205, 153, 0.1); color: #05cd99;">Đã Check-in</span></td>
                    <td class="text-muted">13/03/2026</td>
                    <td class="text-center pe-4">
                        <button class="btn btn-sm btn-light text-primary me-1"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-sm btn-light text-danger"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="addUserModalLabel" style="color: #2b3674;">Thêm Sinh Viên Mới</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addUserForm" onsubmit="event.preventDefault(); saveUser();">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Mã Sinh Viên (MSSV)</label>
                        <input type="text" id="new-mssv" class="form-control" required style="border-radius: 8px; background: #f8fafc;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Họ và Tên</label>
                        <input type="text" id="new-name" class="form-control" required style="border-radius: 8px; background: #f8fafc;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small">Khoa / Lớp</label>
                        <input type="text" id="new-class" class="form-control" style="border-radius: 8px; background: #f8fafc;">
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white py-2" style="background-color: #4318ff; border-radius: 8px;">
                        Lưu thông tin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = 'http://localhost:3000/api'; 

    async function loadUsers() {
        try {
            // const response = await fetch(`${API_URL}/users`);
            // const users = await response.json();
            console.log("Đã gọi API lấy danh sách User!");
        } catch (error) {
            console.error('Lỗi khi load users:', error);
        }
    }

    async function submitAttendance() {
        const mssvInput = document.getElementById('attendance-mssv');
        const mssv = mssvInput.value.trim().toUpperCase();
        const campaign = document.getElementById('attendance-campaign').value;
        const msgBox = document.getElementById('attendance-message');

        if(!mssv) return;

        try {
            /* const response = await fetch(`${API_URL}/attendance`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mssv: mssv, campaign_id: campaign })
            });
            */
            msgBox.className = 'mt-3 alert alert-success p-2 small fw-semibold';
            msgBox.innerHTML = `<i class="fa-solid fa-check-circle me-1"></i> Check-in thành công MSSV: <b>${mssv}</b>`;
            
            mssvInput.value = ''; 
            mssvInput.focus();
            
            setTimeout(() => { msgBox.classList.add('d-none'); }, 3000); 

        } catch (error) {
            console.error('Lỗi điểm danh:', error);
            msgBox.className = 'mt-3 alert alert-danger p-2 small fw-semibold';
            msgBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i> Lỗi kết nối hệ thống!`;
        }
    }

    async function saveUser() {
        const mssv = document.getElementById('new-mssv').value.trim();
        const name = document.getElementById('new-name').value.trim();

        if(!mssv || !name) return;

        try {
            /* const response = await fetch(`${API_URL}/users`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: mssv, name: name })
            });
            */
            
            alert(`Đã gửi API tạo User thành công:\n- Tên: ${name}\n- MSSV: ${mssv}`);
            
            // Clear input & Đóng Modal
            document.getElementById('addUserForm').reset();
            const modalElement = document.getElementById('addUserModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
            
        } catch (error) {
            console.error('Lỗi khi tạo user:', error);
            alert('Có lỗi xảy ra khi lưu User!');
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        loadUsers();
    });
</script>
@endsection