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

Route::prefix('dosen')
    ->middleware(EnsureAuthenticatedDosen::class)
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DosenController::class, 'showDashboard'])->name('dosen.dashboard');
        
        // Kursus Management
        Route::get('/kursus', [DosenController::class, 'showKursusSaya'])->name('dosen.kursus');
        Route::get('/kursus/buat', [DosenController::class, 'showBuatKursus'])->name('dosen.kursus.buat');
        Route::post('/kursus/buat', [DosenController::class, 'storeCourse'])->name('dosen.kursus.store');
        Route::get('/kursus/{id}', [DosenController::class, 'getKursusDetail'])->name('dosen.kursus.detail');
        Route::get('/kursus/{id}/edit', [DosenController::class, 'showEditKursus'])->name('dosen.kursus.edit');
        Route::put('/kursus/{id}', [DosenController::class, 'updateCourse'])->name('dosen.kursus.update');
        Route::get('/kursus/{id}/preview', [DosenController::class, 'previewKursus'])->name('dosen.kursus.preview');
        Route::get('/kursus/{id}/progres', [DosenController::class, 'showProgresKursus'])->name('dosen.kursus.progres');
        
        // Modul Management
        // Module Management (Hierarchy)
        Route::post('/kursus/{id}/module', [DosenController::class, 'storeModule'])->name('dosen.module.store');
        Route::put('/kursus/{id}/module/reorder', [DosenController::class, 'reorderModules'])->name('dosen.module.reorder');
        Route::put('/kursus/{id}/module/{moduleId}', [DosenController::class, 'updateModule'])->name('dosen.module.update');
        Route::delete('/kursus/{id}/module/{moduleId}', [DosenController::class, 'deleteModule'])->name('dosen.module.delete');

        // Material Management (Content)
        Route::get('/kursus/{id}/material/{materialId}', [DosenController::class, 'getMaterialDetail'])->name('dosen.material.detail');
        Route::post('/kursus/{id}/material', [DosenController::class, 'storeMaterial'])->name('dosen.material.store');
        Route::put('/kursus/{id}/material/reorder', [DosenController::class, 'reorderMaterials'])->name('dosen.material.reorder');
        Route::put('/kursus/{id}/material/{materialId}', [DosenController::class, 'updateMaterial'])->name('dosen.material.update');
        Route::delete('/kursus/{id}/material/{materialId}', [DosenController::class, 'deleteMaterial'])->name('dosen.material.delete');
        Route::post('/kursus/{id}/publish', [DosenController::class, 'publishCourse'])->name('dosen.kursus.publish');
        
        // Progres Mahasiswa (all courses)
        Route::get('/progres-mahasiswa', [DosenController::class, 'showProgresMahasiswa'])->name('dosen.progres');
        
        // Auth
        Route::post('/logout', [DosenController::class, 'logout'])->name('dosen.logout');
        
        // Fallback for unimplemented features
        Route::view('/fitur-belum-tersedia', 'Auth.dosen.coming-soon')->name('dosen.coming-soon');
    });
