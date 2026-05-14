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
        
        <button type="button" id="btn-add-new" class="btn btn-primary fw-bold px-4 shadow" data-bs-toggle="modal" data-bs-target="#addStudentModal" style="background-color: #314dff; border: none; border-radius: 10px; padding: 12px 30px;">
            <i class="fa-solid fa-plus me-2"></i>Thêm sinh viên mới
        </button>
    </div>

    <div id="student-list-container">
        <div class="row g-4" id="cards-wrapper"></div>
    </div>

</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #2b3674;">Thêm Sinh Viên Mới</h5>
                <button type="button" class="btn-close" id="close-modal-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddStudent" onsubmit="return false;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">
                                Mã SV (MSSV) <i class="fa-solid fa-lock text-warning ms-1" id="lock-icon" style="display:none;" title="MSSV không thể thay đổi"></i> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="sv-mssv" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sv-hoten" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Ngày sinh</label>
                            <input type="date" class="form-control" id="sv-ngaysinh">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Lớp</label>
                            <input type="text" class="form-control" id="sv-lop">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Khoa</label>
                            <input type="text" class="form-control" id="sv-khoa">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Ảnh đại diện sinh viên</label>
                        <input type="file" class="form-control" id="sv-avatar" accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-muted">Có thể bỏ trống nếu chưa có ảnh.</small>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary fw-bold" id="btn-save-student" style="background-color: #05cd99; border: none;">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Sinh Viên
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
    const API_URL = '{{ url("/api") }}';
    let currentView = 'cards';
    let isEditMode = false;
    let globalStudents = []; 

    document.addEventListener('DOMContentLoaded', () => {
        const btnSave = document.getElementById('btn-save-student');
        const btnCloseModal = document.getElementById('close-modal-btn');
        const btnAddNew = document.getElementById('btn-add-new');
        const addModal = document.getElementById('addStudentModal');
        
        document.getElementById('view-cards').addEventListener('click', () => switchView('cards'));
        document.getElementById('view-table').addEventListener('click', () => switchView('table'));

        function switchView(view) {
            currentView = view;
            document.getElementById('view-cards').classList.toggle('active', view === 'cards');
            document.getElementById('view-table').classList.toggle('active', view === 'table');
            renderStudentsUI(); 
        }
        
        // --- ĐÃ SỬA: LẤY DỮ LIỆU TỪ CẢ LARAVEL VÀ PYTHON RỒI GỘP LẠI ---
        async function loadStudents() {
            const container = document.getElementById('student-list-container');
            try {
                // 1. LẤY DỮ LIỆU TỪ LARAVEL
                const laravelData = @json($sinhviens);
                let mergedStudents = {};

                // Đưa các bạn bên Laravel vào danh sách gộp
                laravelData.forEach(sv => {
                    const mssv = String(sv.maSV || 'Chưa cập nhật').trim().toUpperCase();
                    mergedStudents[mssv] = {
                        maSV: mssv,
                        hoTen: sv.name, 
                        email: sv.email,
                        trangThai: 'Active',
                        ngaySinh: '',
                        // Nguon chinh: du lieu dang ky tai khoan Laravel
                        lop: sv.lopHoc || '',
                        khoa: sv.nganhHoc || '',
                        avatar: sv.avatar_url || ''
                    };
                });

                // 2. LẤY THÊM DỮ LIỆU TỪ PYTHON
                try {
                    const cacheBuster = new Date().getTime();
                    const response = await fetch('http://127.0.0.1:5000/api/danh_sach?t=' + cacheBuster);
                    
                    if (response.ok) {
                        const pythonData = await response.json();
                        
                        // Duyệt qua danh sách bên Python
                        pythonData.forEach(sv => {
                            const mssv = String(sv.maSV || '').trim().toUpperCase();
                            if (!mssv) return;
                            // Nếu bạn này ĐÃ CÓ trong Laravel, thì chỉ bổ sung thêm thông tin Lớp, Khoa
                            if (mergedStudents[mssv]) {
                                mergedStudents[mssv].ngaySinh = sv.ngaySinh || '';
                                // Khong ghi de du lieu Laravel neu Python khong co gia tri
                                mergedStudents[mssv].lop = sv.lop || mergedStudents[mssv].lop || '';
                                mergedStudents[mssv].khoa = sv.khoa || mergedStudents[mssv].khoa || '';
                            } else {
                                // Nếu bạn này CHƯA CÓ (do tạo hồi xưa bên Python), thì thêm mới hoàn toàn vào bảng
                                mergedStudents[mssv] = {
                                    maSV: mssv,
                                    hoTen: sv.hoTen,
                                email: sv.email || (String(sv.maSV).toLowerCase() + '@stu.edu.vn'),
                                    trangThai: 'Active',
                                    ngaySinh: sv.ngaySinh || '',
                                    lop: sv.lop || '',
                                khoa: sv.khoa || '',
                                avatar: ''
                                };
                            }
                        });
                    }
                } catch (pyErr) {
                    console.warn("Lưu ý: Không thể kết nối với Python, chỉ hiển thị dữ liệu từ Laravel.", pyErr);
                }

                // Chuyển danh sách gộp thành mảng để hiển thị ra UI
                globalStudents = Object.values(mergedStudents);

                renderStudentsUI();
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu:", error);
                container.innerHTML = `<div class="alert alert-danger shadow-sm border-0"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lỗi hiển thị dữ liệu.</div>`;
            }
        }

        function renderStudentsUI() {
            const container = document.getElementById('student-list-container');
            const students = globalStudents;
            const renderAvatar = (s, size = 56) => {
                const initial = (s.hoTen || '?').charAt(0).toUpperCase();
                if (!s.avatar) {
                    return `<div class="avatar-circle" style="width:${size}px;height:${size}px;font-size:${Math.max(18, Math.floor(size/2.2))}px;">${initial}</div>`;
                }
                return `<img src="${s.avatar}" alt="avatar" class="rounded-circle" style="width:${size}px;height:${size}px;object-fit:cover;" onerror="this.outerHTML='<div class=\\'avatar-circle\\' style=\\'width:${size}px;height:${size}px;font-size:${Math.max(18, Math.floor(size/2.2))}px;\\'>${initial}</div>';">`;
            };

            if (students.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted p-5 mt-4" style="background-color: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1;">
                        <i class="fa-solid fa-users fs-1 mb-3" style="color: #94a3b8;"></i>
                        <p class="mb-0">Chưa có sinh viên nào trong hệ thống.</p>
                        <p class="small">Hãy bấm "Thêm sinh viên mới" để bắt đầu.</p>
                    </div>`;
                return;
            }

            if (currentView === 'cards') {
                container.innerHTML = `<div class="row g-4">` + students.map(s => `
                    <div class="col-md-6">
                        <div class="student-card shadow-sm border-0">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center gap-4">
                                    ${renderAvatar(s, 56)}
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
                                <button class="btn-delete" onclick="deleteStudent('${s.maSV}')"><i class="fa-solid fa-trash-can me-2"></i>Delete</button>
                                <button class="btn-edit" onclick="editStudent('${s.maSV}')" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                    <i class="fa-solid fa-pen-to-square me-2"></i>Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('') + `</div>`;
            } else {
                container.innerHTML = `
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr><th class="ps-4 py-4">Ảnh</th><th class="py-4">MSSV</th><th class="py-4">Họ và Tên</th><th class="py-4">Email</th><th class="text-center py-4">Trạng thái</th><th class="text-center py-4">Thao tác</th></tr>
                            </thead>
                            <tbody>
                                ${students.map(s => `
                                    <tr>
                                        <td class="ps-4 py-3">${renderAvatar(s, 42)}</td>
                                        <td class="fw-bold py-3 text-primary">${s.maSV}</td>
                                        <td class="fw-bold py-3">${s.hoTen}</td>
                                        <td class="text-muted py-3">${s.email}</td>
                                        <td class="text-center py-3"><span class="badge rounded-pill ${s.trangThai === 'Active' ? 'badge-active' : 'badge-inactive'}">${s.trangThai}</span></td>
                                        <td class="text-center py-3">
                                            <button class="btn btn-sm btn-light text-primary" onclick="editStudent('${s.maSV}')" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                                <i class="fa-solid fa-pen"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-light text-danger ms-1" onclick="deleteStudent('${s.maSV}')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>`;
            }
        }

        loadStudents();
        
        // ========== XỬ LÝ THÊM / SỬA / XÓA SINH VIÊN ========== //
        if (btnAddNew) {
            btnAddNew.addEventListener('click', () => {
                isEditMode = false;
                document.getElementById('formAddStudent').reset(); 
                document.querySelector('#addStudentModal .modal-title').innerText = 'Thêm Sinh Viên Mới';
                
                // Mở khóa ô MSSV khi thêm mới
                const inputMssv = document.getElementById('sv-mssv');
                inputMssv.readOnly = false;
                inputMssv.style.backgroundColor = '#ffffff'; 
                document.getElementById('lock-icon').style.display = 'none';
            });
        }

        window.editStudent = function(maSV) {
            isEditMode = true;
            document.querySelector('#addStudentModal .modal-title').innerText = 'Chỉnh sửa Sinh Viên';
            
            const maSVChuan = String(maSV || '').trim().toLowerCase();
            const svInfo = globalStudents.find(s => String(s.maSV || '').trim().toLowerCase() === maSVChuan);
            if (svInfo) {
                const inputMssv = document.getElementById('sv-mssv');
                inputMssv.value = svInfo.maSV;
                // Khóa ô MSSV lại, làm mờ đi để người dùng biết không sửa được ô này
                inputMssv.readOnly = true; 
                inputMssv.style.backgroundColor = '#e2e8f0'; 
                document.getElementById('lock-icon').style.display = 'inline';

                document.getElementById('sv-hoten').value = svInfo.hoTen || '';
                document.getElementById('sv-ngaysinh').value = svInfo.ngaySinh || ''; 
                document.getElementById('sv-lop').value = svInfo.lop || '';
                document.getElementById('sv-khoa').value = svInfo.khoa || '';
            } else {
                alert('Không tìm thấy dữ liệu sinh viên để chỉnh sửa. Vui lòng tải lại trang.');
            }
            
            setTimeout(() => { document.getElementById('sv-hoten').focus(); }, 500);
        };

        window.deleteStudent = async function(maSV) {
            const mssv = String(maSV || '').trim();
            if (!mssv) {
                alert('Không thể xóa: thiếu mã sinh viên.');
                return;
            }
            if (!confirm(`Bạn có chắc chắn muốn xóa sinh viên ${mssv} và toàn bộ lịch sử điểm danh (AI) của người này?`)) {
                return;
            }
            try {
                const laravelRes = await fetch('/api/xoa-sinh-vien', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ma_sv: mssv }),
                });
                const laravelResult = await laravelRes.json().catch(() => ({}));
                if (!laravelRes.ok || !laravelResult.success) {
                    alert(laravelResult.error || 'Lỗi: Không thể xóa sinh viên khỏi hệ thống tài khoản.');
                    return;
                }

                let pythonNote = '';
                try {
                    const pyRes = await fetch(`http://127.0.0.1:5000/sinh_vien/${encodeURIComponent(mssv)}/xoa`, { method: 'POST' });
                    if (!pyRes.ok) {
                        pythonNote = '\nLưu ý: chưa xóa được dữ liệu điểm danh trên server AI (kiểm tra Python hoặc thử lại sau).';
                    }
                } catch (_) {
                    pythonNote = '\nLưu ý: không kết nối được server AI — tài khoản đã xóa, dữ liệu điểm danh AI có thể còn.';
                }

                alert('Đã xóa sinh viên khỏi hệ thống.' + pythonNote);
                window.location.reload();
            } catch (err) {
                alert('Lỗi khi xóa sinh viên: ' + (err.message || 'Không xác định'));
            }
        };
        
        document.getElementById('formAddStudent').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnSave.click();
            }
        });

        if (btnSave) {
            btnSave.addEventListener('click', async () => {
                const mssv = document.getElementById('sv-mssv').value.trim();
                const hoten = document.getElementById('sv-hoten').value.trim();
                
                if (!mssv || !hoten) {
                    alert("Vui lòng nhập đầy đủ MSSV và Họ tên!");
                    return;
                }

                const originalBtnText = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
                btnSave.disabled = true;

                try {
                    const lopValue = document.getElementById('sv-lop').value || '';
                    const khoaValue = document.getElementById('sv-khoa').value || '';
                    const ngaySinhValue = document.getElementById('sv-ngaysinh').value || '';
                    const avatarFile = document.getElementById('sv-avatar').files[0];

                    // 1) Luôn đồng bộ Laravel trước để trang admin/trang sinh viên không bị lệch dữ liệu.
                    const laravelData = new FormData();
                    laravelData.append('ma_sv', mssv);
                    laravelData.append('ho_ten', hoten);
                    laravelData.append('lop', lopValue);
                    laravelData.append('khoa', khoaValue);
                    if (avatarFile) {
                        laravelData.append('avatar', avatarFile);
                    }

                    const laravelRes = await fetch('/api/dong-bo-sinh-vien', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: laravelData
                    });
                    const laravelResult = await laravelRes.json();
                    if (!laravelRes.ok || !laravelResult.success) {
                        throw new Error(laravelResult.error || 'Đồng bộ dữ liệu Laravel thất bại.');
                    }

                    // 2) Đồng bộ Python để phục vụ điểm danh AI (không chặn luồng chính nếu Python lỗi).
                    const formData = new FormData();
                    formData.append('ma_sv', mssv);
                    formData.append('ho_ten', hoten);
                    formData.append('ngay_sinh', ngaySinhValue);
                    formData.append('lop', lopValue);
                    formData.append('khoa', khoaValue);
                    if (avatarFile) {
                        formData.append('anh', avatarFile);
                    }

                    const mssvEncoded = encodeURIComponent(mssv);
                    const pythonApiUrl = isEditMode ? `http://127.0.0.1:5000/sinh_vien/${mssvEncoded}/sua` : `http://127.0.0.1:5000/dang_ky`;
                    let pythonSyncFailed = false;
                    try {
                        const response = await fetch(pythonApiUrl, {
                            method: 'POST',
                            body: formData
                        });
                        pythonSyncFailed = !response.ok;
                    } catch (_) {
                        pythonSyncFailed = true;
                    }

                    alert(
                        isEditMode
                            ? (pythonSyncFailed
                                ? "Đã cập nhật dữ liệu tài khoản. Lưu ý: chưa đồng bộ sang AI, hãy kiểm tra server Python."
                                : "Đã cập nhật thông tin sinh viên thành công!")
                            : (pythonSyncFailed
                                ? `Đã tạo tài khoản thành công (Laravel).\nTài khoản: ${mssv.toLowerCase()}@stu.edu.vn\nMật khẩu mặc định: 3 số cuối MSSV\nLưu ý: chưa đồng bộ sang AI, hãy kiểm tra server Python.`
                                : `Đã thêm sinh viên thành công!\nTài khoản: ${mssv.toLowerCase()}@stu.edu.vn\nMật khẩu mặc định: 3 số cuối MSSV`)
                    );
                    if (btnCloseModal) btnCloseModal.click();
                    window.location.reload();
                    
                } catch (error) {
                    console.error("Lỗi gửi dữ liệu:", error);
                    alert("Không thể lưu dữ liệu sang hệ thống tài khoản: " + (error.message || 'Lỗi không xác định'));
                } finally {
                    btnSave.innerHTML = originalBtnText;
                    btnSave.disabled = false;
                }
            });
        }
        if (addModal) {
            addModal.addEventListener('hidden.bs.modal', () => {
                document.querySelector('#addStudentModal .modal-title').innerText = 'Thêm Sinh Viên Mới';
                
                const inputMssv = document.getElementById('sv-mssv');
                inputMssv.readOnly = false;
                inputMssv.style.backgroundColor = '#ffffff'; 
                document.getElementById('lock-icon').style.display = 'none';
                
                document.getElementById('formAddStudent').reset();
            });
        }
    });
</script>
@endsection