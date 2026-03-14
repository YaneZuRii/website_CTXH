<?php

use Illuminate\Support\Facades\Route;

// Các trang có thanh Menu (Sidebar)
Route::get('/', function () { return view('index'); });
Route::get('/sinh-vien', function () { return view('sinh-vien'); });
Route::get('/danh-sach-diem-danh', function () { return view('diemdanh-list'); });


// Các trang xác thực (Không có Sidebar)
Route::get('/login', function () { return view('login'); });
// POST handlers for auth - temporary
Route::post('/login', function () {
    session(['user' => 'demo']);
    return redirect('/');
});

// Forgot password
Route::get('/forgot-password', function() { return view('forgot-password'); });
Route::post('/forgot-password-send', function() {
    // todo: send reset email
    return redirect('/login')->with('status', 'Link reset đã gửi!');
});

