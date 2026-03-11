<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\AdminContentApiController;
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
        Route::put('/dosen/{id}/approve', [AdminController::class, 'approveDosen'])->name('admin.dosen.approve');
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

        // Module Management
        Route::get('/kursus/{id}/modul', [AdminController::class, 'showKelolaModul'])->name('admin.kursus.modul');
        Route::post('/kursus/{id}/module', [AdminController::class, 'storeModule'])->name('admin.module.store');
        Route::put('/kursus/{id}/module/reorder', [AdminController::class, 'reorderModules'])->name('admin.module.reorder');
        Route::put('/kursus/{id}/module/{moduleId}', [AdminController::class, 'updateModule'])->name('admin.module.update');
        Route::delete('/kursus/{id}/module/{moduleId}', [AdminController::class, 'deleteModule'])->name('admin.module.delete');
        
        // Material/Content Management
        Route::get('/kursus/{id}/material/{materialId}', [AdminController::class, 'getMaterialDetail'])->name('admin.material.detail');
        Route::post('/kursus/{id}/material', [AdminController::class, 'storeMaterial'])->name('admin.material.store');
        Route::put('/kursus/{id}/material/reorder', [AdminController::class, 'reorderMaterials'])->name('admin.material.reorder');
        Route::put('/kursus/{id}/material/{materialId}', [AdminController::class, 'updateMaterial'])->name('admin.material.update');
        Route::delete('/kursus/{id}/material/{materialId}', [AdminController::class, 'deleteMaterial'])->name('admin.material.delete');

        // Quiz Management
        Route::get('/kursus/{courseId}/module/{moduleId}/quiz', [AdminController::class, 'showKelolaQuiz'])->name('admin.quiz.kelola');
        Route::post('/kursus/{courseId}/module/{moduleId}/quiz', [AdminController::class, 'storeQuiz'])->name('admin.quiz.store');
        Route::post('/kursus/{courseId}/module/{moduleId}/pretest', [AdminController::class, 'togglePretest'])->name('admin.quiz.togglePretest');
        Route::get('/kursus/{courseId}/quiz/{quizId}', [AdminController::class, 'getQuiz'])->name('admin.quiz.get');
        Route::put('/kursus/{courseId}/quiz/{quizId}', [AdminController::class, 'updateQuiz'])->name('admin.quiz.update');
        Route::delete('/kursus/{courseId}/quiz/{quizId}', [AdminController::class, 'deleteQuiz'])->name('admin.quiz.delete');
        Route::post('/kursus/{courseId}/quiz/{quizId}/question', [AdminController::class, 'storeQuestion'])->name('admin.quiz.question.store');
        Route::put('/kursus/{courseId}/quiz/{quizId}/question/reorder', [AdminController::class, 'reorderQuestions'])->name('admin.quiz.question.reorder');
        Route::put('/kursus/{courseId}/quiz/{quizId}/question/{questionId}', [AdminController::class, 'updateQuestion'])->name('admin.quiz.question.update');
        Route::delete('/kursus/{courseId}/quiz/{quizId}/question/{questionId}', [AdminController::class, 'deleteQuestion'])->name('admin.quiz.question.delete');

        // Admin Content Pages
        Route::prefix('konten')->group(function () {
            Route::view('/video', 'Auth.admin.kelola-video')->name('admin.kelola-video');
            Route::view('/bacaan', 'Auth.admin.kelola-bacaan')->name('admin.kelola-bacaan');
            Route::view('/tugas', 'Auth.admin.kelola-tugas')->name('admin.kelola-tugas');
        });

        // Admin API routes for content
        Route::prefix('api')->group(function () {
            Route::get('/courses', [AdminContentApiController::class, 'courses'])->name('admin.api.courses');
            Route::get('/courses/{courseId}/students', [AdminContentApiController::class, 'courseStudents'])->name('admin.api.courses.students');
            Route::get('/video-duration', [AdminContentApiController::class, 'resolveVideoDuration'])->name('admin.api.video-duration');
            Route::post('/courses/{courseId}/modules', [AdminContentApiController::class, 'addModule'])->name('admin.api.courses.modules.store');
        });

        // Profile
        Route::get('/profile', [AdminController::class, 'showProfile'])->name('admin.profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');

        // Notifications
        Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('admin.notifications');
        Route::get('/notifications/count', [AdminController::class, 'getNotificationCount'])->name('admin.notifications.count');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.read');
        Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.readAll');

        // News/Pengumuman Management
        Route::get('/pengumuman', [AdminController::class, 'showPengumuman'])->name('admin.pengumuman');

        // Prodi (Jurusan) Management
        Route::get('/prodi', [AdminController::class, 'showProdi'])->name('admin.prodi');
        Route::post('/prodi', [AdminController::class, 'storeProdi'])->name('admin.prodi.store');
        Route::get('/prodi/{id}', [AdminController::class, 'getProdi'])->name('admin.prodi.get');
        Route::put('/prodi/{id}', [AdminController::class, 'updateProdi'])->name('admin.prodi.update');
        Route::delete('/prodi/{id}', [AdminController::class, 'deleteProdi'])->name('admin.prodi.delete');

        // Frontend-only pages
        Route::view('/kategori', 'Auth.admin.kategori')->name('admin.kategori');
        Route::view('/sertifikasi', 'Auth.admin.sertifikasi')->name('admin.sertifikasi');
        Route::view('/chat', 'Auth.admin.chat')->name('admin.chat');
        Route::view('/finance-report', 'Auth.admin.finance-report')->name('admin.finance-report');

        Route::post('/pengumuman', [AdminController::class, 'storePengumuman'])->name('admin.pengumuman.store');
        Route::get('/pengumuman/{id}', [AdminController::class, 'getPengumuman'])->name('admin.pengumuman.get');
        Route::put('/pengumuman/{id}', [AdminController::class, 'updatePengumuman'])->name('admin.pengumuman.update');
        Route::delete('/pengumuman/{id}', [AdminController::class, 'deletePengumuman'])->name('admin.pengumuman.delete');

        // YouTube Playlist Sync
        Route::post('/kursus/{id}/sync-playlist', [AdminController::class, 'syncYoutubePlaylist'])->name('admin.kursus.syncPlaylist');
        Route::get('/kursus/{id}/youtube-videos', [AdminController::class, 'getYoutubeVideos'])->name('admin.kursus.youtubeVideos');

        // Excel Import & Export
        Route::get('/export/{type}/excel', [AdminController::class, 'exportExcel'])->name('admin.export.excel');
        Route::post('/import/{type}/preview', [AdminController::class, 'previewImport'])->name('admin.import.preview');
        Route::post('/import/{type}/confirm', [AdminController::class, 'confirmImport'])->name('admin.import.confirm');
        Route::get('/import/{type}/template', [AdminController::class, 'downloadTemplate'])->name('admin.import.template');
    });
