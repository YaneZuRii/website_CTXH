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
            <div class="modern-card auth-card shadow-sm">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Đăng nhập hệ thống</h3>
                    <p class="text-muted mb-0">Vào quản lý CTXH Dashboard</p>
                </div>
                <form action="/login" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small" style="color: #2b3674;">Email / MSSV</label>
                        <input type="text" name="email" class="form-control-saas" placeholder="VD: khang@stu.vn hoặc DH52200837" required>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">Mật khẩu</label>
                                <a href="/forgot-password" class="text-decoration-none small fw-medium" style="color: #4318ff;">Quên mật khẩu?</a>
                        </div>
                        <input type="password" name="password" class="form-control-saas" placeholder="Nhập mật khẩu..." required>
                    </div>
                    <button type="submit" class="btn-primary-saas mb-3 w-100">→ Đăng nhập</button>
                    <div class="text-center">
                        <small class="text-muted">Liên hệ admin để có tài khoản.</small>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="app-footer mt-auto">
        <div class="container">
            <div class="text-center py-3">
                <p class="mb-1 small fw-medium" style="color: #a3aed1;">
                    <i class="fa-solid fa-location-dot me-1 text-muted"></i>108 Cao Lỗ, P.4, Q.8, TP.HCM
                </p>
                <p class="mb-0 xsmall" style="color: #a3aed1;">
                    <i class="fa-solid fa-phone me-2 text-muted"></i>0326 885 324 | 
                    <i class="fa-solid fa-envelope me-1 text-muted"></i><a href="mailto:khangbeo2003@gmail.com" class="text-muted text-decoration-none">khangbeo2003@gmail.com</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

