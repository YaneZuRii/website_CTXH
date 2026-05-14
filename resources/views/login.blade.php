<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - CTXH SYS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-center py-3">
                <h2 class="fw-bold mb-0" style="color: #2b3674;">
                    <i class="fa-solid fa-layer-group me-2" style="color: #4318ff;"></i>CTXH SYS
                </h2>
            </div>
        </div>
    </header>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="auth-container">
            <div class="modern-card auth-card shadow-sm p-4" style="border-radius: 12px; background: #fff; max-width: 400px; margin: 0 auto;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Đăng nhập hệ thống</h3>
                    <p class="text-muted mb-0">Vào quản lý CTXH Dashboard</p>
                </div>

                {{-- 1. THÔNG BÁO THÀNH CÔNG --}}
                @if(session('status'))
                    <div class="alert alert-success text-center p-2 small fw-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-check me-1"></i> {{ session('status') }}
                    </div>
                @endif

                {{-- 2. THÔNG BÁO LỖI ĐĂNG NHẬP --}}
                @if(session('error'))
                    <div class="alert alert-danger text-center p-2 small fw-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- 3. THÔNG BÁO LỖI ĐỊNH DẠNG --}}
                @if($errors->any())
                    <div class="alert alert-warning text-center p-2 small fw-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> Vui lòng kiểm tra lại thông tin!
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color: #2b3674;">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg bg-light border-0" placeholder="VD: admin@stu.edu.vn" required autofocus>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">Mật khẩu</label>
                            <a href="/forgot-password" class="text-decoration-none small fw-medium" style="color: #4318ff;">Quên mật khẩu?</a>
                        </div>
                        <input type="password" name="password" class="form-control form-control-lg bg-light border-0" placeholder="Nhập mật khẩu..." required>
                    </div>
                    <button type="submit" class="btn btn-primary mb-3 w-100 py-2 fw-bold" style="background-color: #4318ff; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer class="app-footer mt-auto py-3">
        <div class="container text-center">
            <p class="mb-1 small fw-medium" style="color: #a3aed1;">
                <i class="fa-solid fa-location-dot me-1"></i> 108 Cao Lỗ, P.4, Q.8, TP.HCM
            </p>
            <p class="mb-0 xsmall" style="color: #a3aed1;">
                MSSV: DH52200837 | <i class="fa-solid fa-envelope me-1"></i> khangbeo2003@gmail.com
            </p>
        </div>
    </footer>
</body>
</html>