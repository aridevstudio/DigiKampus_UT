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
        Route::get('/dosen', [AdminController::class, 'showDosen'])->name('admin.dosen');
        Route::get('/mahasiswa', [AdminController::class, 'showMahasiswa'])->name('admin.mahasiswa');

        // post
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
        // Dosen CRUD
        Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('admin.dosen.store');
        Route::get('/dosen/{id}', [AdminController::class, 'getDosen'])->name('admin.dosen.get');
        Route::put('/dosen/{id}', [AdminController::class, 'updateDosen'])->name('admin.dosen.update');
        Route::delete('/dosen/{id}', [AdminController::class, 'deleteDosen'])->name('admin.dosen.delete');
        Route::post('/dosen/import', [AdminController::class, 'importDosen'])->name('admin.dosen.import');
        
        // Mahasiswa CRUD
        Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('admin.mahasiswa.store');
        Route::get('/mahasiswa/{id}', [AdminController::class, 'getMahasiswa'])->name('admin.mahasiswa.get');
        Route::put('/mahasiswa/{id}', [AdminController::class, 'updateMahasiswa'])->name('admin.mahasiswa.update');
        Route::delete('/mahasiswa/{id}', [AdminController::class, 'deleteMahasiswa'])->name('admin.mahasiswa.delete');
        Route::post('/mahasiswa/import', [AdminController::class, 'importMahasiswa'])->name('admin.mahasiswa.import');
        
        // Kursus Management
        Route::get('/kursus', [AdminController::class, 'showKursus'])->name('admin.kursus');
        Route::post('/kursus', [AdminController::class, 'storeKursus'])->name('admin.kursus.store');
        Route::get('/kursus/{id}', [AdminController::class, 'getKursus'])->name('admin.kursus.get');
        Route::put('/kursus/{id}', [AdminController::class, 'updateKursus'])->name('admin.kursus.update');
        Route::delete('/kursus/{id}', [AdminController::class, 'deleteKursus'])->name('admin.kursus.delete');

        // Profile
        Route::get('/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

        // Notifications
        Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('admin.notifications');
        Route::get('/notifications/count', [AdminController::class, 'getNotificationCount'])->name('admin.notifications.count');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.read');
        Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.readAll');

        // YouTube Playlist Sync
        Route::post('/kursus/{id}/sync-playlist', [AdminController::class, 'syncYoutubePlaylist'])->name('admin.kursus.syncPlaylist');
        Route::get('/kursus/{id}/youtube-videos', [AdminController::class, 'getYoutubeVideos'])->name('admin.kursus.youtubeVideos');

        // Excel Import (with preview)
        Route::post('/import/{type}/preview', [AdminController::class, 'previewImport'])->name('admin.import.preview');
        Route::post('/import/{type}/confirm', [AdminController::class, 'confirmImport'])->name('admin.import.confirm');
        Route::get('/import/{type}/template', [AdminController::class, 'downloadTemplate'])->name('admin.import.template');
    });
