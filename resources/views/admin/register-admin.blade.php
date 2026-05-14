<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Admin - CTXH SYS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #f8f9fa;">
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="auth-container" style="max-width: 450px; width: 100%;">
            <div class="modern-card auth-card shadow-sm p-4" style="border-radius: 12px; background: #fff;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Đăng ký Quản Trị Viên</h3>
                    <p class="text-muted mb-0 small">Tạo tài khoản quản trị hệ thống</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger p-2 small fw-bold">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                {{-- QUAN TRỌNG: Action phải có chữ /admin/ --}}
                <form action="/admin/register-admin" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và Tên</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="Họ tên Admin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Admin</label>
                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="admin@stu.edu.vn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mật khẩu</label>
                        <input type="password" name="password" class="form-control bg-light border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control bg-light border-0" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold" style="border-radius: 8px;">
                        <i class="fa-solid fa-user-shield me-2"></i> Đăng ký ngay
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">Bạn là sinh viên? <a href="/sinhvien/register-sinh-vien" class="fw-bold text-primary text-decoration-none">Đăng ký SV</a></small>
                </div>
            </div>
        </div>
    </main>
</body>
</html>