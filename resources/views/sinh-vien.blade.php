@extends('layouts.app')

@section('title', 'Quản lý Sinh Viên - CTXH')
@section('page-title', 'Danh sách Sinh viên')

@section('content')
<div class="container-fluid px-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="btn-group p-1 bg-light shadow-sm" style="border-radius: 12px;">
                <button class="btn btn-white fw-bold border-0 px-4 py-2 active" id="view-cards" onclick="switchView('cards')" style="border-radius: 10px;">Cards View</button>
                <button class="btn text-muted fw-bold border-0 px-4 py-2" id="view-table" onclick="switchView('table')" style="border-radius: 10px;">Table View</button>
            </div>
        </div>
        
        <button type="button" class="btn btn-primary fw-bold px-4 shadow" data-bs-toggle="modal" data-bs-target="#addStudentModal" style="background-color: #314dff; border: none; border-radius: 10px; padding: 12px 30px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm sinh viên mới
        </button>
    </div>

    <div id="student-list-container">
        <div class="row g-4" id="cards-wrapper">
            </div>
    </div>

</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h4 class="modal-title fw-bold" style="color: #2b3674;">Thêm Sinh Viên Mới</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addStudentForm" onsubmit="event.preventDefault(); saveStudent();">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Mã Sinh Viên (MSSV)</label>
                            <input type="text" id="new-mssv" class="form-control bg-light border-0 py-3" required style="border-radius: 12px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted small">Họ và Tên</label>
                            <input type="text" id="new-name" class="form-control bg-light border-0 py-3" required style="border-radius: 12px;">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold text-muted small">Khoa / Lớp</label>
                            <input type="text" id="new-class" class="form-control bg-light border-0 py-3" style="border-radius: 12px;">
                        </div>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white py-3 mt-2 shadow-sm" style="background-color: #314dff; border-radius: 12px; font-size: 1.1rem;">Lưu thông tin ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = '{{ url("/api") }}';
    let currentView = 'cards';

    function switchView(view) {
        currentView = view;
        document.getElementById('view-cards').classList.toggle('active', view === 'cards');
        document.getElementById('view-table').classList.toggle('active', view === 'table');
        loadStudents();
    }

    async function loadStudents() {
        // Dữ liệu mẫu (Khớp Figma)
        const students = [
            { maSV: 'DH52200837', hoTen: 'Lê Duy Khang', email: 'DH52200837.stu.edu.vn', trangThai: 'Active' },
            { maSV: 'DH52200123', hoTen: 'Lê Duy Khánh', email: 'DH52200123.stu.edu.vn', trangThai: 'Active' },
            { maSV: 'DH52200456', hoTen: 'Nguyễn Trọng Dương', email: 'DH52200456.stu.edu.vn', trangThai: 'Inactive' },
            { maSV: 'DH52200999', hoTen: 'Douw', email: 'DH52200999.stu.edu.vn', trangThai: 'Active' }
        ];

        const container = document.getElementById('student-list-container');

        if (currentView === 'cards') {
            container.innerHTML = `<div class="row g-4">` + students.map(s => `
                <div class="col-md-6">
                    <div class="student-card shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="avatar-circle">${s.hoTen.charAt(0)}</div>
                                <div>
                                    <div class="student-name mb-1">${s.hoTen}</div>
                                    <div class="student-info">
                                        <div><i class="fa-solid fa-id-card me-2"></i>MSSV: <b>${s.maSV}</b></div>
                                        <div><i class="fa-solid fa-envelope me-2"></i>${s.email}</div>
                                    </div>
                                </div>
                            </div>
                            <span class="badge rounded-pill ${s.trangThai === 'Active' ? 'badge-active' : 'badge-inactive'}">${s.trangThai}</span>
                        </div>
                        <div class="d-flex gap-3 justify-content-end mt-2">
                            <button class="btn-delete"><i class="fa-solid fa-trash-can me-2"></i>Delete</button>
                            <button class="btn-edit"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Profile</button>
                        </div>
                    </div>
                </div>
            `).join('') + `</div>`;
        } else {
            // Render Table to rõ
            container.innerHTML = `
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr><th class="ps-4 py-4">MSSV</th><th class="py-4">Họ và Tên</th><th class="py-4">Email</th><th class="text-center py-4">Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            ${students.map(s => `
                                <tr>
                                    <td class="ps-4 fw-bold py-3 text-primary">${s.maSV}</td>
                                    <td class="fw-bold py-3">${s.hoTen}</td>
                                    <td class="text-muted py-3">${s.email}</td>
                                    <td class="text-center py-3"><span class="badge rounded-pill ${s.trangThai === 'Active' ? 'badge-active' : 'badge-inactive'}">${s.trangThai}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', loadStudents);
</script>
@endsection