<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 

class AuthController extends Controller
{
    /**
     * Xử lý Đăng nhập
     */
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Phân luồng: 1 = Admin, 0 = Sinh viên
            if (Auth::user()->role == 1) {
                return redirect('/tong-quan'); 
            }
            return redirect('/tong-ket'); 
        }

        return back()->with('error', 'Sai email hoặc mật khẩu!');
    }

    /**
     * xử lý đăng xuất
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * xử lý đk sinh viên (Role = 0)
     */
    public function registerSinhVien(Request $request) {
        $request->validate([
            'name' => 'required',
            'maSV' => 'required|unique:users,maSV', // ktra mssv đã tồn tại chưa
            'lopHoc' => 'required|string|max:50',
            'nganhHoc' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // yêu cầu nhập lại mật khẩu và phải trùng khớp
        ], [
            'maSV.unique' => 'Mã số sinh viên này đã được đăng ký!',
            'lopHoc.required' => 'Vui lòng nhập lớp học!',
            'nganhHoc.required' => 'Vui lòng nhập ngành học!',
            'email.unique' => 'Email này đã tồn tại trong hệ thống!',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->maSV = $request->maSV; // Lưu MSSV cho Sinh viên
        $user->maQR = 'CTXH-' . strtoupper(trim($request->maSV));
        $user->lopHoc = $request->lopHoc;
        $user->nganhHoc = $request->nganhHoc;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 0; // Gán quyền Sinh viên
        $user->save();

        return redirect('/login')->with('status', 'Đăng ký Sinh viên thành công! Vui lòng đăng nhập.');
    }

    /**
     * xử lý đk admin (Role = 1)
     */
    public function registerAdmin(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email', 
            'password' => 'required|min:6|confirmed',    
        ], [
            'email.unique' => 'Email này đã tồn tại trong hệ thống!',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password); 
        $user->role = 1; // Gán quyền Admin
        $user->save(); 
        
        return redirect('/login')->with('status', 'Tạo tài khoản Admin thành công! Vui lòng đăng nhập.');
    }
}