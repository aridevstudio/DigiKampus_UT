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
        Route::get('/profile', [MahasiswaController::class, 'showProfile'])->name('mahasiswa.profile');
        Route::get('/notification', [MahasiswaController::class, 'showNotification'])->name('mahasiswa.notification');
        Route::get('/profile/edit', [MahasiswaController::class, 'showEditProfile'])->name('mahasiswa.profile.edit');
        Route::get('/get-courses', [MahasiswaController::class, 'showGetCourses'])->name('mahasiswa.get-courses');
        Route::get('/course/{id}', [MahasiswaController::class, 'showCourseDetail'])->name('mahasiswa.course-detail');
        Route::get('/checkout', [MahasiswaController::class, 'showCheckout'])->name('mahasiswa.checkout');
        Route::get('/payment', [MahasiswaController::class, 'showPayment'])->name('mahasiswa.payment');
        Route::get('/payment-success', [MahasiswaController::class, 'showPaymentSuccess'])->name('mahasiswa.payment-success');

        // post
        Route::post('/logout', [MahasiswaController::class, 'logout'])->name('mahasiswa.logout');
        Route::put('/profile/update', [MahasiswaController::class, 'updateProfile'])->name('mahasiswa.profile.update');
    });
