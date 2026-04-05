<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - CTXH SYS</title>
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
            <div class="modern-card auth-card shadow-sm w-100" style="max-width: 450px;">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fa-solid fa-key text-primary" style="font-size: 3rem; color: #4318ff;"></i>
                    </div>
                    <h3 class="fw-bold mb-2" style="color: #2b3674;">Quên mật khẩu?</h3>
                    <p class="text-muted mb-0">Nhập email để nhận link reset mật khẩu</p>
                </div>
                <form action="/forgot-password-send" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold small" style="color: #2b3674;">Email đăng ký</label>
                        <input type="email" name="email" class="form-control-saas" placeholder="VD: khangbeo2003@gmail.com" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary-saas w-100 mb-3">Gửi link reset</button>
                    <div class="text-center">
                        <a href="/login" class="btn btn-link p-0 text-decoration-none fw-medium" style="color: #4318ff;">
                            ← Quay lại đăng nhập
                        </a>
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