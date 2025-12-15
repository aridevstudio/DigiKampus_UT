<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Middleware\EnsureAuthenticatedAdmin;
use App\Http\Middleware\RedirectIfAuthenticatedAdmin;
use Illuminate\Support\Facades\Route;

// Route untuk login (hanya bisa diakses jika belum login)
Route::prefix('admin')
    ->middleware(RedirectIfAuthenticatedAdmin::class)
    ->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
        Route::get('/forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('admin.forgot-password');
        Route::post('/forgot-password', [AdminController::class, 'sendForgotPasswordOtp'])->name('admin.forgot-password.post');
        Route::get('/verify-otp', [AdminController::class, 'showVerifyOtpForm'])->name('admin.verify-otp');
        Route::post('/verify-otp', [AdminController::class, 'verifyOtp'])->name('admin.verify-otp.post');
        Route::get('/reset-password', [AdminController::class, 'showResetPasswordForm'])->name('admin.reset-password');
        Route::post('/reset-password', [AdminController::class, 'resetPassword'])->name('admin.reset-password.post');
    });

// Route untuk dashboard (hanya bisa diakses jika sudah login)
Route::prefix('admin')
    ->middleware(EnsureAuthenticatedAdmin::class)
    ->group(function () {
        // get
        Route::get('/dashboard', [AdminController::class, 'showDashboard'])->name('admin.dashboard');

        // post
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });
