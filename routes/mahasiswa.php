<?php

use App\Http\Controllers\Auth\MahasiswaController;
use App\Http\Middleware\EnsureAuthenticatedMahasiswa;
use App\Http\Middleware\RedirectIfAuthenticatedMahasiswa;
use Illuminate\Support\Facades\Route;

// Route untuk login (hanya bisa diakses jika belum login)
Route::prefix('mahasiswa')
    ->middleware(RedirectIfAuthenticatedMahasiswa::class)
    ->group(function () {
        Route::get('/login', [MahasiswaController::class, 'showLoginForm'])->name('mahasiswa.login');
        Route::post('/login', [MahasiswaController::class, 'showLoginFormPost'])->name('mahasiswa.post');
        Route::get('/forgot-password', [MahasiswaController::class, 'showForgotPasswordForm'])->name('mahasiswa.forgot-password');
        Route::post('/forgot-password', [MahasiswaController::class, 'sendForgotPasswordOtp'])->name('mahasiswa.forgot-password.post');
        Route::get('/verify-otp', [MahasiswaController::class, 'showVerifyOtpForm'])->name('mahasiswa.verify-otp');
        Route::post('/verify-otp', [MahasiswaController::class, 'verifyOtp'])->name('mahasiswa.verify-otp.post');
        Route::get('/reset-password', [MahasiswaController::class, 'showResetPasswordForm'])->name('mahasiswa.reset-password');
        Route::post('/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password.post');
    });

// Route untuk dashboard (hanya bisa diakses jika sudah login)
Route::prefix('mahasiswa')
    ->middleware(EnsureAuthenticatedMahasiswa::class)
    ->group(function () {
        // get
        Route::get('/dashboard', [MahasiswaController::class, 'showDashboard'])->name('mahasiswa.dashboard');
        Route::get('/calendar', [MahasiswaController::class, 'showCalendar'])->name('mahasiswa.calendar');

        // post
        Route::post('/logout', [MahasiswaController::class, 'logout'])->name('mahasiswa.logout');
    });
