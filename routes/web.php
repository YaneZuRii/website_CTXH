<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController; 
use Illuminate\Support\Facades\Storage;



// Đăng nhập
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Tắt đăng ký tài khoản công khai cho sinh viên.
Route::match(['get', 'post'], '/sinhvien/register-sinh-vien', function () {
    abort(404);
});

// Tắt đăng ký Admin từ giao diện công khai.
// Admin vẫn đăng nhập bình thường bằng tài khoản đã có sẵn trong hệ thống.
Route::match(['get', 'post'], '/admin/register-admin', function () {
    abort(404);
});


Route::middleware(['auth'])->group(function () {
    
    // Trang tổng kết cá nhân 
    Route::get('/tong-ket', function () { return view('tong-ket'); });
    Route::post('/sinh-vien/cap-nhat-anh', function (Request $request) {
        if (auth()->user()->role == 1) {
            return back()->with('error', 'Chỉ sinh viên mới được cập nhật ảnh cá nhân.');
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh trước khi cập nhật.',
            'avatar.image' => 'Tệp tải lên phải là ảnh hợp lệ.',
            'avatar.mimes' => 'Chỉ hỗ trợ ảnh JPG, PNG hoặc WEBP.',
            'avatar.max' => 'Kích thước ảnh không vượt quá 2MB.',
        ]);

        $user = auth()->user();
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return back()->with('status', 'Cập nhật ảnh sinh viên thành công.');
    })->name('student.avatar.update');

    // Trả ảnh avatar từ storage/app/public ổn định, không phụ thuộc symlink /public/storage
    Route::get('/media/avatar/{path}', function (string $path) {
        $safePath = str_replace('\\', '/', trim($path));
        if ($safePath === '' || !Storage::disk('public')->exists($safePath)) {
            abort(404);
        }
        $fullPath = Storage::disk('public')->path($safePath);
        return response()->file($fullPath);
    })->where('path', '.*')->name('media.avatar');
    
    // TRẠM PHÂN LUỒNG: Bẻ lái theo Role
    Route::get('/', function () {
        if (auth()->user()->role == 1) {
            return redirect('/tong-quan'); 
        }
        return redirect('/tong-ket');
    });
});


/*
|--------------------------------------------------------------------------
| 3. KHU VỰC ADMIN 
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    Route::get('/tong-quan', function () {
        if (auth()->user()->role != 1) return redirect('/tong-ket')->with('error', 'Bạn không có quyền!');
        
        // --- THÊM MỚI: LẤY DỮ LIỆU ĐỔ RA DASHBOARD ---
        $totalSV = \App\Models\User::where('role', 0)->count(); // Đếm tổng số sinh viên
        
        return view('index', compact('totalSV')); // Truyền biến $totalSV sang file index.blade.php
    });

    Route::get('/sinh-vien', function () {
        if (auth()->user()->role != 1) return redirect('/tong-ket')->with('error', 'Bạn không có quyền!');
        
        // --- THÊM MỚI: LẤY DANH SÁCH SINH VIÊN ---
        // Lấy tất cả user có role = 0, sắp xếp người mới nhất lên đầu
        $sinhviens = \App\Models\User::where('role', 0)->orderBy('id', 'desc')->get();
        
        return view('sinh-vien', compact('sinhviens')); // Truyền danh sách $sinhviens sang file sinh-vien.blade.php
    });

    Route::get('/su-kien', function () {
        if (auth()->user()->role != 1) return redirect('/tong-ket')->with('error', 'Bạn không có quyền!');
        return view('su-kien'); 
    });

});


/*
|--------------------------------------------------------------------------
| 4. KHU VỰC ĐIỂM DANH (AI CAMERA)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/diem-danh', function () { return view('diem-danh'); });
    
    Route::post('/diem-danh/camera', function(Request $request) {
        $event = $request->input('ten_su_kien');
        if (!$event) return redirect('/diem-danh')->with('error', 'Vui lòng chọn sự kiện!');
        
        return response('')->header('Content-Type', 'text/html')->setContent('
            <html><body style="margin:0;padding:20px;text-align:center;">
                <p>Đang khởi động Camera AI cho sự kiện: <strong>' . htmlspecialchars($event) . '</strong></p>
                <form method="POST" action="http://127.0.0.1:5000/diem_danh" target="_blank" id="form">
                    <input type="hidden" name="ten_su_kien" value="' . htmlspecialchars($event) . '">
                </form>
                <script>document.getElementById("form").submit();</script>
            </body></html>
        ');
    });
});

// API LƯU TÀI KHOẢN KHI ADMIN TẠO BẰNG CAMERA
Route::post('/api/tao-nhanh-sinh-vien', function(Request $request) {
    try {
        // Kiểm tra xem MSSV này đã có trong Database chưa
        $exists = \App\Models\User::where('maSV', $request->ma_sv)->first();
        
        if (!$exists) {
            $user = new \App\Models\User();
            $user->name = $request->ho_ten;
            $maSv = strtoupper(trim($request->ma_sv));
            $user->maSV = $maSv;
            $user->maQR = 'CTXH-' . $maSv;
            $user->email = $maSv . '@stu.edu.vn'; // Tự động tạo email
            $user->password = \Illuminate\Support\Facades\Hash::make('123456'); // Pass mặc định
            $user->role = 0; // Sinh viên
            $user->save();
        }
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// API đồng bộ dữ liệu sinh viên về Laravel (dùng cho admin khi thêm/sửa)
Route::post('/api/dong-bo-sinh-vien', function(Request $request) {
    try {
        $maSv = strtoupper(trim((string) $request->input('ma_sv')));
        $hoTen = trim((string) $request->input('ho_ten'));
        $lopHoc = trim((string) $request->input('lop', ''));
        $nganhHoc = trim((string) $request->input('khoa', ''));
        $emailInput = trim((string) $request->input('email', ''));
        $email = $emailInput !== '' ? $emailInput : (strtolower($maSv) . '@stu.edu.vn');
        $schema = \Illuminate\Support\Facades\Schema::class;
        $hasMaQr = $schema::hasColumn('users', 'maQR');
        $hasLopHoc = $schema::hasColumn('users', 'lopHoc');
        $hasNganhHoc = $schema::hasColumn('users', 'nganhHoc');
        $hasAvatar = $schema::hasColumn('users', 'avatar');

        // Tự vá schema nếu DB cũ chưa có cột, tránh lỗi "lưu xong nhưng không đồng bộ"
        if (!$hasMaQr || !$hasLopHoc || !$hasNganhHoc || !$hasAvatar) {
            $schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) use ($hasMaQr, $hasLopHoc, $hasNganhHoc, $hasAvatar) {
                if (!$hasMaQr) $table->string('maQR')->nullable();
                if (!$hasLopHoc) $table->string('lopHoc')->nullable();
                if (!$hasNganhHoc) $table->string('nganhHoc')->nullable();
                if (!$hasAvatar) $table->string('avatar')->nullable();
            });
            $hasMaQr = true;
            $hasLopHoc = true;
            $hasNganhHoc = true;
            $hasAvatar = true;
        }

        if ($maSv === '' || $hoTen === '') {
            return response()->json(['success' => false, 'error' => 'Thiếu mã SV hoặc họ tên'], 400);
        }

        $user = \App\Models\User::where('maSV', $maSv)->first();
        if (!$user) {
            $user = new \App\Models\User();
            $user->maSV = $maSv;
            if ($hasMaQr) {
                $user->maQR = 'CTXH-' . $maSv;
            }
            preg_match_all('/\d/', $maSv, $digitMatches);
            $chuoiSo = implode('', $digitMatches[0]);
            $matKhauMacDinh = strlen($chuoiSo) >= 3 ? substr($chuoiSo, -3) : substr($maSv, -3);
            $user->password = \Illuminate\Support\Facades\Hash::make($matKhauMacDinh);
            $user->role = 0;
        }

        if ($hasAvatar && $request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $hoTen;
        $user->email = $email;
        $user->lopHoc = $lopHoc;
        $user->nganhHoc = $nganhHoc;
        $user->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

// API xóa sinh viên khỏi Laravel (bắt buộc để danh sách admin cập nhật); đồng bộ Python tùy chọn từ phía client.
Route::post('/api/xoa-sinh-vien', function (Request $request) {
    if (!auth()->check() || (int) auth()->user()->role !== 1) {
        return response()->json(['success' => false, 'error' => 'Không có quyền thực hiện thao tác này.'], 403);
    }

    try {
        $maSv = strtoupper(trim((string) $request->input('ma_sv')));
        if ($maSv === '') {
            return response()->json(['success' => false, 'error' => 'Thiếu mã sinh viên.'], 400);
        }

        $user = \App\Models\User::whereRaw('UPPER(TRIM(maSV)) = ?', [$maSv])->where('role', 0)->first();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Không tìm thấy sinh viên trong hệ thống tài khoản.'], 404);
        }

        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/api/thong-tin-sinh-vien', function(Request $request) {
    try {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'error' => 'Chưa đăng nhập'], 401);
        }

        $maSv = strtoupper(trim((string) $request->query('ma_sv', '')));
        if ($maSv === '') {
            return response()->json(['success' => false, 'error' => 'Thiếu mã sinh viên'], 400);
        }

        $user = \App\Models\User::whereRaw('UPPER(maSV) = ?', [$maSv])->first();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Không tìm thấy sinh viên'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'maSV' => strtoupper((string) ($user->maSV ?? $maSv)),
                'hoTen' => $user->name ?: 'Không xác định',
                'lop' => $user->lopHoc ?: 'Không xác định',
                'khoa' => $user->nganhHoc ?: 'Không xác định',
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});