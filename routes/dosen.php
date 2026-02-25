<?php

use App\Http\Controllers\Auth\DosenController;
use App\Http\Controllers\Auth\DosenContentApiController;
use App\Http\Middleware\EnsureAuthenticatedDosen;
use App\Http\Middleware\RedirectIfAuthenticatedDosen;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dosen Routes
|--------------------------------------------------------------------------
|
| Routes untuk panel dosen: authentication, manajemen kursus, modul,
| materi, progres mahasiswa, dan komunikasi.
|
*/

// ============================================================================
// GUEST ROUTES (Belum Login)
// ============================================================================
Route::prefix('dosen')
    ->middleware(RedirectIfAuthenticatedDosen::class)
    ->group(function () {
        
        // Authentication
        Route::get('/login', [DosenController::class, 'showLoginForm'])->name('dosen.login');
        Route::post('/login', [DosenController::class, 'login'])->name('dosen.login.post');
        
        // Google OAuth
        Route::get('/auth/google', [DosenController::class, 'redirectToGoogle'])->name('dosen.google.redirect');
        Route::get('/auth/google/callback', [DosenController::class, 'handleGoogleCallback'])->name('dosen.google.callback');
        
        // Password Reset Flow
        Route::get('/forgot-password', [DosenController::class, 'showForgotPasswordForm'])->name('dosen.forgot-password');
        Route::post('/forgot-password', [DosenController::class, 'sendForgotPasswordOtp'])->name('dosen.forgot-password.post');
        Route::get('/verify-otp', [DosenController::class, 'showVerifyOtpForm'])->name('dosen.verify-otp');
        Route::post('/verify-otp', [DosenController::class, 'verifyOtp'])->name('dosen.verify-otp.post');
        Route::get('/reset-password', [DosenController::class, 'showResetPasswordForm'])->name('dosen.reset-password');
        Route::post('/reset-password', [DosenController::class, 'resetPassword'])->name('dosen.reset-password.post');
    });

// ============================================================================
// AUTHENTICATED ROUTES (Sudah Login)
// ============================================================================
Route::prefix('dosen')
    ->middleware(EnsureAuthenticatedDosen::class)
    ->group(function () {
        
        // ----------------------------------------------------------------------
        // Dashboard
        // ----------------------------------------------------------------------
        Route::get('/dashboard', [DosenController::class, 'showDashboard'])->name('dosen.dashboard');

        // ----------------------------------------------------------------------
        // Profile
        // ----------------------------------------------------------------------
        Route::get('/profile', [DosenController::class, 'showProfile'])->name('dosen.profile');
        Route::put('/profile', [DosenController::class, 'updateProfile'])->name('dosen.profile.update');
        
        // ----------------------------------------------------------------------
        // Kursus Management
        // ----------------------------------------------------------------------
        Route::prefix('kursus')->group(function () {
            Route::get('/', [DosenController::class, 'showKursusSaya'])->name('dosen.kursus');
            Route::get('/buat', [DosenController::class, 'showBuatKursus'])->name('dosen.kursus.buat');
            Route::post('/buat', [DosenController::class, 'storeCourse'])->name('dosen.kursus.store');
            
            // Kursus Detail Routes
            Route::get('/{id}', [DosenController::class, 'getKursusDetail'])->name('dosen.kursus.detail');
            Route::get('/{id}/edit', [DosenController::class, 'showEditKursus'])->name('dosen.kursus.edit');
            Route::put('/{id}', [DosenController::class, 'updateCourse'])->name('dosen.kursus.update');
            Route::get('/{id}/modul', [DosenController::class, 'showKelolaModul'])->name('dosen.kursus.modul');
            Route::get('/{id}/preview', [DosenController::class, 'previewKursus'])->name('dosen.kursus.preview');
            Route::get('/{id}/progres', [DosenController::class, 'showProgresKursus'])->name('dosen.kursus.progres');
            Route::post('/{id}/publish', [DosenController::class, 'publishCourse'])->name('dosen.kursus.publish');
            
            // Module Management
            Route::post('/{id}/module', [DosenController::class, 'storeModule'])->name('dosen.module.store');
            Route::put('/{id}/module/reorder', [DosenController::class, 'reorderModules'])->name('dosen.module.reorder');
            Route::put('/{id}/module/{moduleId}', [DosenController::class, 'updateModule'])->name('dosen.module.update');
            Route::delete('/{id}/module/{moduleId}', [DosenController::class, 'deleteModule'])->name('dosen.module.delete');
            
            // Material/Content Management
            Route::get('/{id}/material/{materialId}', [DosenController::class, 'getMaterialDetail'])->name('dosen.material.detail');
            Route::post('/{id}/material', [DosenController::class, 'storeMaterial'])->name('dosen.material.store');
            Route::put('/{id}/material/reorder', [DosenController::class, 'reorderMaterials'])->name('dosen.material.reorder');
            Route::put('/{id}/material/{materialId}', [DosenController::class, 'updateMaterial'])->name('dosen.material.update');
            Route::delete('/{id}/material/{materialId}', [DosenController::class, 'deleteMaterial'])->name('dosen.material.delete');
        });
        
        // ----------------------------------------------------------------------
        // Content Creation (Video, Quiz, dll)
        // ----------------------------------------------------------------------
        Route::prefix('api')->group(function () {
            Route::get('/courses', [DosenContentApiController::class, 'courses'])->name('dosen.api.courses');
            Route::get('/video-duration', [DosenContentApiController::class, 'resolveVideoDuration'])->name('dosen.api.video-duration');
            Route::post('/courses/{courseId}/modules', [DosenContentApiController::class, 'addModule'])->name('dosen.api.courses.modules.store');
            Route::get('/schedules/upcoming', [DosenContentApiController::class, 'upcomingSchedules'])->name('dosen.api.schedules.upcoming');
            Route::post('/schedules', [DosenContentApiController::class, 'storeSchedule'])->name('dosen.api.schedules.store');
        });

        Route::prefix('konten')->group(function () {
            Route::view('/video', 'Auth.dosen.kelola-video')->name('dosen.kelola-video');
            Route::view('/quiz', 'Auth.dosen.kelola-quiz')->name('dosen.kelola-quiz');
            Route::view('/bacaan', 'Auth.dosen.kelola-bacaan')->name('dosen.kelola-bacaan');
            Route::view('/tugas', 'Auth.dosen.kelola-tugas')->name('dosen.kelola-tugas');
        });
        
        // ----------------------------------------------------------------------
        // Progres & Monitoring
        // ----------------------------------------------------------------------
        Route::get('/progres-mahasiswa', [DosenController::class, 'showProgresMahasiswa'])->name('dosen.progres');
        
        // ----------------------------------------------------------------------
        // Komunikasi
        // ----------------------------------------------------------------------
        Route::view('/pesan', 'Auth.dosen.pesan')->name('dosen.pesan');
        
        // ----------------------------------------------------------------------
        // Notifications (JSON endpoints for header dropdown)
        // ----------------------------------------------------------------------
        Route::get('/notifications', [DosenController::class, 'getNotifications'])->name('dosen.notifications');
        Route::get('/notifications/count', [DosenController::class, 'getNotificationCount'])->name('dosen.notifications.count');
        Route::post('/notifications/{id}/read', [DosenController::class, 'markNotificationRead'])->name('dosen.notifications.read');
        Route::post('/notifications/read-all', [DosenController::class, 'markAllNotificationsRead'])->name('dosen.notifications.readAll');
        
        // ----------------------------------------------------------------------
        // Messages (unread count for header badge + chat endpoints)
        // ----------------------------------------------------------------------
        Route::get('/messages/unread-count', [DosenController::class, 'getUnreadMessageCount'])->name('dosen.messages.unreadCount');
        Route::get('/messages/conversations', [DosenController::class, 'getConversations'])->name('dosen.messages.conversations');
        Route::get('/messages/chat/{studentId}', [DosenController::class, 'getChatMessages'])->name('dosen.messages.chat');
        Route::post('/messages/send', [DosenController::class, 'sendChatMessage'])->name('dosen.messages.send');
        
        // ----------------------------------------------------------------------
        // Authentication
        // ----------------------------------------------------------------------
        Route::post('/logout', [DosenController::class, 'logout'])->name('dosen.logout');
        
        // ----------------------------------------------------------------------
        // Fallback
        // ----------------------------------------------------------------------
        Route::view('/fitur-belum-tersedia', 'Auth.dosen.coming-soon')->name('dosen.coming-soon');
    });
