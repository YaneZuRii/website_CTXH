<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Sinh viên - CTXH SYS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #f8f9fa;">
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="auth-container" style="max-width: 450px; width: 100%;">
            <div class="modern-card auth-card shadow-sm p-4" style="border-radius: 12px; background: #fff;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Đăng ký Sinh viên</h3>
                    <p class="text-muted mb-0 small">Tạo tài khoản tích lũy giờ CTXH</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger p-2 small fw-bold">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- QUAN TRỌNG: Action phải có chữ /sinhvien/ --}}
                <form action="/sinhvien/register-sinh-vien" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và Tên</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="VD: {{ $name ?? 'Lê Duy Khang' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">MSSV</label>
                        <input type="text" name="maSV" class="form-control bg-light border-0" placeholder="VD: DH52200837" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Sinh viên</label>
                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="mssv@stu.edu.vn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lớp</label>
                        <input type="text" name="lopHoc" class="form-control bg-light border-0" placeholder="VD: D22_TH03" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ngành học</label>
                        <input type="text" name="nganhHoc" class="form-control bg-light border-0" placeholder="VD: Công nghệ thông tin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mật khẩu</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control bg-light border-0" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 8px; background-color: #4318ff; border: none;">
                        <i class="fa-solid fa-user-plus me-2"></i> Đăng ký Sinh viên
                    </button>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Bạn là cán bộ? <a href="/admin/register-admin" class="fw-bold text-danger text-decoration-none">Đăng ký Admin</a></small>
                </div>
            </div>
        </div>
    </main>
</body>
</html>