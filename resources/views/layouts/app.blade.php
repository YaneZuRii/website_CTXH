<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CTXH Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('scripts')
    {{-- Chart.js for dashboard --}}
    @yield('head-scripts')
</head>
<body>
    <div class="wrapper">
        {{-- Sidebar chung cho tất cả dashboard pages --}}
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="/" class="text-decoration-none d-block">
                     <h4 class="fw-bold m-0" style="color: #2b3674; transition: 0.3s;" 
                          onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <i class="fa-solid fa-layer-group me-2" style="color: #4318ff;"></i>CTXH 
                    </h4>
                 </a>
            </div>
            <ul class="sidebar-menu mt-3">
                <li class="px-3 mb-2 text-uppercase fw-bold" style="color: #a3aed1; font-size: 0.75rem;">Main Menu</li> 
                <li><a href="/" class="{{ request()->is('/') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Tổng quan</a></li>
                <li><a href="/sinh-vien" class="{{ request()->is('sinh-vien') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Quản lý Sinh viên</a></li>
                <li><a href="/danh-sach-diem-danh" class="{{ request()->is('danh-sach-diem-danh') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Điểm danh</a></li>
                <li class="mt-2"><a href="/login"><i class="fa-solid fa-right-from-bracket text-danger"></i> <span class="text-danger">Đăng xuất</span></a></li>
            </ul>
        </nav>
        
        <div class="main-content">
            {{-- Topbar dynamic --}}
            <div class="topbar">
                <div>
                    <h4 class="fw-bold mb-0" style="color: #2b3674;">@yield('page-title')</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-user text-secondary" style="font-size: 32px;"></i>
                    <span class="fw-bold" style="color: #2b3674;">Admin Khang</span>
                </div>
                @yield('topbar-search')
            </div>

            @yield('content')
        </div>
    </div>

    {{-- Footer mới --}}
    <footer class="app-footer">
        <div class="container">
            <div class="text-center py-4">
                <p class="mb-2" style="color: #a3aed1; font-size: 0.85rem;">
                    <i class="fa-solid fa-location-dot me-2"></i>108 Cao Lỗ, Phường 4, Quận 8
                </p>
                <p class="mb-0 small" style="color: #a3aed1;">
                    <i class="fa-solid fa-phone me-2"></i>0326885324 | 
                    <i class="fa-solid fa-envelope me-2 ms-2"></i>khangbeo2003@gmail.com
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

