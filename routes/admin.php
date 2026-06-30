<?php

use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\AdminContentApiController;
use App\Http\Controllers\Auth\AdminForumCategoryController;
use App\Http\Controllers\Auth\AdminForumCommentController;
use App\Http\Controllers\Auth\AdminForumTopicController;
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
        Route::match(['GET', 'POST'], '/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
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
        Route::put('/kursus/{id}/approve-webinar', [AdminController::class, 'approveWebinar'])->name('admin.kursus.approveWebinar');
        Route::put('/kursus/{id}/reject-webinar', [AdminController::class, 'rejectWebinar'])->name('admin.kursus.rejectWebinar');
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

        // Support Tickets
        Route::get('/support-tickets', [AdminController::class, 'showSupportTickets'])->name('admin.support-tickets');
        Route::post('/support-tickets/{id}/reply', [AdminController::class, 'replySupportTicket'])->name('admin.support-tickets.reply');

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

        // Kategori Management
        Route::get('/kategori', [AdminController::class, 'showKategori'])->name('admin.kategori');
        Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('admin.kategori.store');
        Route::get('/kategori/{id}', [AdminController::class, 'getKategori'])->name('admin.kategori.get');
        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('admin.kategori.update');
        Route::delete('/kategori/{id}', [AdminController::class, 'deleteKategori'])->name('admin.kategori.delete');

        // Bootcamp & Ticket Ops
        Route::get('/bootcamp-tiket', [AdminController::class, 'showBootcampTiket'])->name('admin.bootcamp-tiket');
        Route::get('/bootcamp-tiket/export', [AdminController::class, 'exportBootcampBatch'])->name('admin.bootcamp-tiket.export');
        Route::post('/bootcamp-tiket/external-mentors', [AdminController::class, 'storeBootcampExternalMentor'])->name('admin.bootcamp-tiket.external-mentors.store');
        Route::post('/bootcamp-tiket', [AdminController::class, 'storeBootcamp'])->name('admin.bootcamp-tiket.store');
        Route::put('/bootcamp-tiket/{id}/sales/open', [AdminController::class, 'openBootcampSales'])->name('admin.bootcamp-tiket.sales.open');
        Route::put('/bootcamp-tiket/{id}/sales/close', [AdminController::class, 'closeBootcampSales'])->name('admin.bootcamp-tiket.sales.close');
        Route::put('/bootcamp-tiket/{id}/batch', [AdminController::class, 'updateBootcampBatch'])->name('admin.bootcamp-tiket.batch.update');
        Route::post('/bootcamp-tiket/{id}/assign-mentor', [AdminController::class, 'assignBootcampMentor'])->name('admin.bootcamp-tiket.mentor.assign');
        Route::delete('/bootcamp-tiket/{id}', [AdminController::class, 'deleteBootcamp'])->name('admin.bootcamp-tiket.delete');

        // Frontend-only pages
        Route::get('/sertifikasi', [AdminController::class, 'showSertifikasi'])->name('admin.sertifikasi');
        Route::post('/sertifikasi/blangko', [AdminController::class, 'storeCertificateTemplate'])->name('admin.sertifikasi.templates.store');
        Route::get('/sertifikasi/blangko/{id}/image', [AdminController::class, 'showCertificateTemplateImage'])->name('admin.sertifikasi.templates.image');
        Route::put('/sertifikasi/blangko/{id}', [AdminController::class, 'updateCertificateTemplate'])->name('admin.sertifikasi.templates.update');
        Route::delete('/sertifikasi/blangko/{id}', [AdminController::class, 'deleteCertificateTemplate'])->name('admin.sertifikasi.templates.delete');
        Route::post('/sertifikasi/certificates', [AdminController::class, 'storeAutomaticCertificate'])->name('admin.sertifikasi.certificates.store');
        Route::get('/sertifikasi/certificates/{id}', [AdminController::class, 'getAutomaticCertificate'])->name('admin.sertifikasi.certificates.get');
        Route::put('/sertifikasi/certificates/{id}', [AdminController::class, 'updateAutomaticCertificate'])->name('admin.sertifikasi.certificates.update');
        Route::delete('/sertifikasi/certificates/{id}', [AdminController::class, 'deleteAutomaticCertificate'])->name('admin.sertifikasi.certificates.delete');
        Route::view('/chat', 'Auth.admin.chat')->name('admin.chat');
        Route::get('/messages/conversations', [AdminController::class, 'getChatConversations'])->name('admin.messages.conversations');
        Route::get('/messages/chat/{conversationId}', [AdminController::class, 'getChatConversationMessages'])->name('admin.messages.chat');
        Route::post('/messages/send', [AdminController::class, 'sendAdminChatMessage'])->name('admin.messages.send');
        Route::delete('/messages/{messageId}', [AdminController::class, 'deleteChatMessage'])->name('admin.messages.delete');
        Route::post('/messages/conversation/{conversationId}/purge-role', [AdminController::class, 'purgeChatByRole'])->name('admin.messages.purgeRole');
        Route::delete('/messages/conversations/{conversationId}', [AdminController::class, 'deleteChatConversation'])->name('admin.messages.conversation.delete');
        Route::get('/finance-report', [AdminController::class, 'showFinanceReport'])->name('admin.finance-report');
        Route::get('/finance-report/export', [AdminController::class, 'exportFinanceReportExcel'])->name('admin.finance-report.export');
        Route::post('/finance-report/service-fee', [AdminController::class, 'updateFinanceServiceFee'])->name('admin.finance-report.service-fee.update');
        Route::get('/voucher', [AdminController::class, 'showVoucher'])->name('admin.voucher');
        Route::post('/voucher', [AdminController::class, 'storeVoucher'])->name('admin.voucher.store');
        Route::get('/voucher/{id}', [AdminController::class, 'getVoucher'])->name('admin.voucher.get');
        Route::put('/voucher/{id}', [AdminController::class, 'updateVoucher'])->name('admin.voucher.update');
        Route::put('/voucher/{id}/reset-usage', [AdminController::class, 'resetVoucherUsage'])->name('admin.voucher.reset-usage');
        Route::delete('/voucher/{id}', [AdminController::class, 'deleteVoucher'])->name('admin.voucher.delete');

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

        // Forum Management (Kategori, Topik moderasi, Moderasi Komentar)
        Route::prefix('forum')->group(function () {
            Route::get('/kategori', [AdminForumCategoryController::class, 'index'])->name('admin.forum-kategori');
            Route::post('/kategori', [AdminForumCategoryController::class, 'store'])->name('admin.forum-kategori.store');
            Route::get('/kategori/{id}', [AdminForumCategoryController::class, 'show'])->name('admin.forum-kategori.show');
            Route::put('/kategori/{id}', [AdminForumCategoryController::class, 'update'])->name('admin.forum-kategori.update');
            Route::put('/kategori/{id}/toggle', [AdminForumCategoryController::class, 'toggleActive'])->name('admin.forum-kategori.toggle');
            Route::put('/kategori/reorder', [AdminForumCategoryController::class, 'reorder'])->name('admin.forum-kategori.reorder');
            Route::delete('/kategori/{id}', [AdminForumCategoryController::class, 'destroy'])->name('admin.forum-kategori.destroy');

            Route::get('/topik', [AdminForumTopicController::class, 'index'])->name('admin.forum-topik');
            Route::get('/topik/{id}', [AdminForumTopicController::class, 'show'])->name('admin.forum-topik.show');
            Route::put('/topik/{id}/pin', [AdminForumTopicController::class, 'togglePin'])->name('admin.forum-topik.pin');
            Route::put('/topik/{id}/lock', [AdminForumTopicController::class, 'toggleLock'])->name('admin.forum-topik.lock');
            Route::put('/topik/{id}/status', [AdminForumTopicController::class, 'setStatus'])->name('admin.forum-topik.status');
            Route::delete('/topik/{id}', [AdminForumTopicController::class, 'destroy'])->name('admin.forum-topik.destroy');

            Route::get('/komentar', [AdminForumCommentController::class, 'index'])->name('admin.forum-komentar');
            Route::get('/topik/{topicId}/komentar', [AdminForumCommentController::class, 'showTopic'])->name('admin.forum-komentar.topic');
            Route::put('/komentar/{id}/status', [AdminForumCommentController::class, 'setStatus'])->name('admin.forum-komentar.status');
            Route::delete('/komentar/{id}', [AdminForumCommentController::class, 'destroy'])->name('admin.forum-komentar.destroy');
        });
    });
