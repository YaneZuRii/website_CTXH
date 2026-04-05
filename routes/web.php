<?php

use Illuminate\Support\Facades\Route;

// các trang có thanh Menu (Sidebar)
Route::get('/', function () { return view('index'); });
Route::get('/sinh-vien', function () { return view('sinh-vien'); });
Route::get('/danh-sach-diem-danh', function () { return view('diemdanh-list'); });
Route::get('/su-kien', function () { return view('su-kien'); });
Route::get('/tong-ket', function () { return view('tong-ket'); });


// các trang xác thực (Không có Sidebar)
Route::get('/login', function () { return view('login'); });
Route::get('/register', function () { return view('register'); });
// POST handlers for auth - temporary
Route::post('/login', function () {
    session(['user' => 'demo']);
    return redirect('/');
});
Route::post('/register', function () {
    // todo: save user to database
    session(['user' => request('email')]);
    return redirect('/');
});

// quên mk
Route::get('/forgot-password', function() { return view('forgot-password'); });
Route::post('/forgot-password-send', function() {
    // todo: send reset email
    return redirect('/login')->with('status', 'Link reset đã gửi!');
});



