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
</head>
<body>
    <div class="wrapper">
        {{-- Sidebar --}}
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="/" class="text-decoration-none d-block">
                     <h4 class="fw-bold m-0" style="color: #2b3674;">
                        <i class="fa-solid fa-layer-group me-2" style="color: #2bf35d;"></i>CTXH 
                    </h4>
                 </a>
            </div>
            <ul class="sidebar-menu mt-3">
                <li class="px-3 mb-2 text-uppercase fw-bold" style="color: #a3aed1; font-size: 0.75rem;">Main Menu</li> 
                
                @if(Auth::check())
                    
                    {{-- PHÂN QUYỀN: NẾU LÀ ADMIN (role = 1) --}}
                    @if(Auth::user()->role == 1)
                        <li><a href="/tong-quan" class="{{ request()->is('tong-quan') || request()->is('/') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Tổng quan</a></li>
                        <li><a href="/sinh-vien" class="{{ request()->is('sinh-vien') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Quản lý Sinh viên</a></li>
                        <li><a href="/su-kien" class="{{ request()->is('su-kien') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Quản lý Sự kiện</a></li>
                    
                    {{-- PHÂN QUYỀN: NẾU LÀ SINH VIÊN (role = 0) --}}
                    @else
                        {{-- ĐÃ SỬA: Trả lại nút Điểm danh cho Sinh viên --}}
                        <li><a href="/diem-danh" class="{{ request()->is('diem-danh') ? 'active' : '' }}"><i class="fa-solid fa-calendar-check"></i> Điểm danh sự kiện</a></li>
                        <li><a href="/tong-ket" class="{{ request()->is('tong-ket') ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i> Tổng kết cá nhân</a></li>
                    @endif

                    {{-- NÚT ĐĂNG XUẤT CHUNG CHO CẢ HAI --}}
                    <li class="mt-2">
                        <form method="POST" action="/logout" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: inherit; font: inherit; cursor: pointer;">
                                <i class="fa-solid fa-right-from-bracket text-danger"></i> <span class="text-danger">Đăng xuất</span>
                            </button>
                        </form>
                    </li>
                
                {{-- NẾU CHƯA ĐĂNG NHẬP --}}
                @else
                    <li class="mt-2"><a href="/login"><i class="fa-solid fa-right-to-bracket text-primary"></i> <span class="text-primary">Đăng nhập</span></a></li>
                @endif
            </ul>
        </nav>
        
        <div class="main-content">
            {{-- Topbar dynamic --}}
            <div class="topbar d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0" style="color: #3cbe8d;">@yield('page-title')</h4>
                </div>
                
                <div class="d-flex align-items-center gap-3 bg-white px-3 py-1 shadow-sm" style="border-radius: 50px;">
                    <div id="app-user-info" class="text-end">
                        @if(Auth::check())
                            <span class="fw-bold d-block" style="color: #2b3674; font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ Auth::user()->role == 1 ? 'Quản trị viên' : 'Sinh viên' }}
                            </small>
                        @else
                            <span class="fw-bold d-block" style="color: #2b3674; font-size: 0.9rem;">Khách</span>
                            <small class="text-muted" style="font-size: 0.75rem;">Chưa đăng nhập</small>
                        @endif
                    </div>
                    
                    <div id="app-user-avatar" style="width: 40px; height: 40px; overflow: hidden; border-radius: 50%; border: 2px solid #69e67e;">
                        @if(Auth::check())
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=05cd99&color=fff" alt="User" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name=Guest&background=random" alt="User" class="w-100 h-100" style="object-fit: cover;">
                        @endif
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>

    <footer class="app-footer">
        <div class="container text-center py-4">
            <p class="mb-2" style="color: #a3aed1; font-size: 0.85rem;"><i class="fa-solid fa-location-dot me-2"></i>108 Cao Lỗ, Phường 4, Quận 8</p>
            <p class="mb-0 small" style="color: #a3aed1;">0326885324 | khangbeo2003@gmail.com</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('head-scripts')
</body>
</html>