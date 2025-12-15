<?php

use App\Http\Controllers\Auth\DosenController;
use App\Http\Middleware\EnsureAuthenticatedDosen;
use App\Http\Middleware\RedirectIfAuthenticatedDosen;
use Illuminate\Support\Facades\Route;

// Route untuk login (hanya bisa diakses jika belum login)
Route::prefix('dosen')
    ->middleware(RedirectIfAuthenticatedDosen::class)
    ->group(function () {
        Route::get('/login', [DosenController::class, 'showLoginForm'])->name('dosen.login');
        Route::post('/login', [DosenController::class, 'login'])->name('dosen.login.post');
        
        // Google OAuth
        Route::get('/auth/google', [DosenController::class, 'redirectToGoogle'])->name('dosen.google.redirect');
        Route::get('/auth/google/callback', [DosenController::class, 'handleGoogleCallback'])->name('dosen.google.callback');
        
        // Forgot Password
        Route::get('/forgot-password', [DosenController::class, 'showForgotPasswordForm'])->name('dosen.forgot-password');
        Route::post('/forgot-password', [DosenController::class, 'sendForgotPasswordOtp'])->name('dosen.forgot-password.post');
        Route::get('/verify-otp', [DosenController::class, 'showVerifyOtpForm'])->name('dosen.verify-otp');
        Route::post('/verify-otp', [DosenController::class, 'verifyOtp'])->name('dosen.verify-otp.post');
        Route::get('/reset-password', [DosenController::class, 'showResetPasswordForm'])->name('dosen.reset-password');
        Route::post('/reset-password', [DosenController::class, 'resetPassword'])->name('dosen.reset-password.post');
    });

// Route untuk dashboard (hanya bisa diakses jika sudah login)
Route::prefix('dosen')
    ->middleware(EnsureAuthenticatedDosen::class)
    ->group(function () {
        // get
        Route::get('/dashboard', [DosenController::class, 'showDashboard'])->name('dosen.dashboard');

        // post
        Route::post('/logout', [DosenController::class, 'logout'])->name('dosen.logout');
    });
