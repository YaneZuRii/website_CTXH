<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - CTXH SYS</title>
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
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Tạo tài khoản</h3>
                    <p class="text-muted mb-0">Gia nhập hệ thống CTXH</p>
                </div>
                <form action="/register" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">Họ tên</label>
                            <input type="text" name="name" class="form-control-saas" placeholder="Lê Duy Khang" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">MSSV</label>
                            <input type="text" name="mssv" class="form-control-saas" placeholder="DH52200837" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">Email</label>
                            <input type="email" name="email" class="form-control-saas" placeholder="khang@stu.edu.vn" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small" style="color: #2b3674;">Mật khẩu</label>
                            <input type="password" name="password" class="form-control-saas" placeholder="Mật khẩu mạnh..." required>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary-saas mt-3 w-100">→ Đăng ký ngay</button>
                    <div class="text-center mt-3">
                        <small class="text-muted">Liên hệ admin để đăng ký.</small>
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

