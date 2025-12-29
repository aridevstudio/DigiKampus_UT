<?php

use App\Http\Controllers\Auth\MahasiswaController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Http\Controllers\Mahasiswa\CourseController;
use App\Http\Controllers\Mahasiswa\CheckoutController;
use App\Http\Middleware\EnsureAuthenticatedMahasiswa;
use App\Http\Middleware\RedirectIfAuthenticatedMahasiswa;
use Illuminate\Support\Facades\Route;

// ============================================
// AUTH ROUTES (hanya bisa diakses jika belum login)
// ============================================
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

// ============================================
// PROTECTED ROUTES (hanya bisa diakses jika sudah login)
// ============================================
Route::prefix('mahasiswa')
    ->middleware(EnsureAuthenticatedMahasiswa::class)
    ->group(function () {
        
        // Dashboard & Calendar
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('mahasiswa.dashboard');
        Route::get('/calendar', [DashboardController::class, 'calendar'])->name('mahasiswa.calendar');
        Route::get('/notification', [DashboardController::class, 'notification'])->name('mahasiswa.notification');
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('mahasiswa.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('mahasiswa.profile.edit');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('mahasiswa.profile.update');
        
        // Courses
        Route::get('/courses', [CourseController::class, 'myCourses'])->name('mahasiswa.courses');
        Route::get('/get-courses', [CourseController::class, 'index'])->name('mahasiswa.get-courses');
        Route::get('/course/{id}', [CourseController::class, 'show'])->name('mahasiswa.course-detail');
        Route::get('/course/{id}/learn', [CourseController::class, 'learn'])->name('mahasiswa.course-learn');
        Route::post('/course/material/{id}/complete', [CourseController::class, 'completeMaterial'])->name('mahasiswa.material.complete');
        
        // Favorites
        Route::get('/favorites', [CourseController::class, 'favorites'])->name('mahasiswa.favorites');
        Route::post('/favorite/add', [CourseController::class, 'addToFavorite'])->name('mahasiswa.favorite.add');
        Route::delete('/favorite/{id}', [CourseController::class, 'removeFromFavorite'])->name('mahasiswa.favorite.remove');
        
        // Checkout & Payment
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('mahasiswa.checkout');
        Route::post('/cart/add', [CheckoutController::class, 'addToCart'])->name('mahasiswa.cart.add');
        Route::delete('/cart/{id}', [CheckoutController::class, 'removeFromCart'])->name('mahasiswa.cart.remove');
        Route::get('/payment', [CheckoutController::class, 'payment'])->name('mahasiswa.payment');
        Route::get('/payment-success', [CheckoutController::class, 'success'])->name('mahasiswa.payment-success');
        
        // Logout
        Route::post('/logout', [MahasiswaController::class, 'logout'])->name('mahasiswa.logout');
    });
