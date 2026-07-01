<?php

use App\Http\Controllers\Auth\MahasiswaController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Http\Controllers\Mahasiswa\CourseController;
use App\Http\Controllers\Mahasiswa\ChatController;
use App\Http\Controllers\Mahasiswa\CheckoutController;
use App\Http\Controllers\Mahasiswa\ForumController;
use App\Http\Controllers\Mahasiswa\LearningGoalController;
use App\Http\Controllers\Mahasiswa\AppsHubController;
use App\Http\Controllers\Mahasiswa\SupportController;
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
        Route::post('/notification/{id}/read', [DashboardController::class, 'markNotificationRead'])->name('mahasiswa.notification.read');
        Route::post('/notification/read-all', [DashboardController::class, 'markAllNotificationsRead'])->name('mahasiswa.notification.read-all');
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('mahasiswa.profile');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('mahasiswa.profile.edit');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('mahasiswa.profile.update');
        
        // Courses
        Route::get('/courses', [CourseController::class, 'myCourses'])->name('mahasiswa.courses');
        Route::get('/get-courses', [CourseController::class, 'index'])->name('mahasiswa.get-courses');
        Route::get('/course/{id}', [CourseController::class, 'show'])->name('mahasiswa.course-detail');
        Route::get('/course/{id}/learn', [CourseController::class, 'learn'])->name('mahasiswa.course-learn');

        // Bootcamp (separated presentation layer, shares Course data)
        Route::get('/bootcamp', [CourseController::class, 'bootcampCatalog'])->name('mahasiswa.bootcamp');
        Route::get('/bootcamp/{id}', [CourseController::class, 'bootcampDetail'])->name('mahasiswa.bootcamp-detail');
        Route::get('/bootcamp/{id}/learn', [CourseController::class, 'bootcampLearn'])->name('mahasiswa.bootcamp-learn');
        Route::get('/bootcamp-saya', [CourseController::class, 'bootcampMy'])->name('mahasiswa.bootcamp-saya');
        Route::post('/bootcamp/{courseId}/review', [CourseController::class, 'submitCourseReview'])->name('mahasiswa.bootcamp.review');
        Route::post('/course/{courseId}/review', [CourseController::class, 'submitCourseReview'])->name('mahasiswa.course.review');
        Route::post('/course/material/{id}/complete', [CourseController::class, 'completeMaterial'])->name('mahasiswa.material.complete');
        Route::get('/course/{courseId}/discussions', [CourseController::class, 'getDiscussions'])->name('mahasiswa.course-discussions.index');
        Route::post('/course/{courseId}/discussions', [CourseController::class, 'sendDiscussion'])->name('mahasiswa.course-discussions.store');
        Route::get('/course/{courseId}/quiz/{quizId}', [CourseController::class, 'quiz'])->name('mahasiswa.course-quiz');
        Route::post('/course/{courseId}/quiz/{quizId}/answer', [CourseController::class, 'saveQuizAnswer'])->name('mahasiswa.quiz-answer');
        Route::post('/course/{courseId}/quiz/{quizId}/flag', [CourseController::class, 'toggleQuizFlag'])->name('mahasiswa.quiz-flag');
        Route::post('/course/{courseId}/quiz/{quizId}/reset', [CourseController::class, 'resetQuiz'])->name('mahasiswa.quiz-reset');
        Route::post('/course/{courseId}/quiz/{quizId}/submit', [CourseController::class, 'submitQuiz'])->name('mahasiswa.quiz-submit');
        Route::get('/course/{courseId}/quiz/{quizId}/result', [CourseController::class, 'quizResult'])->name('mahasiswa.quiz-result');
        Route::get('/course/{courseId}/assignment/{assignmentId}', [CourseController::class, 'assignmentDetail'])->name('mahasiswa.assignment-detail');
        Route::get('/course/{courseId}/assignment/{assignmentId}/submit', [CourseController::class, 'assignmentSubmission'])->name('mahasiswa.assignment-submission');
        Route::get('/course/{courseId}/assignment/{assignmentId}/status', [CourseController::class, 'assignmentStatus'])->name('mahasiswa.assignment-status');
        Route::post('/course/{courseId}/assignment/{assignmentId}/submit', [CourseController::class, 'submitAssignment'])->name('mahasiswa.submit-assignment');
        Route::get('/course/{courseId}/module/{moduleId}/feedback', [CourseController::class, 'moduleFeedback'])->name('mahasiswa.module-feedback');
        
        // Favorites
        Route::get('/favorites', [CourseController::class, 'favorites'])->name('mahasiswa.favorites');
        Route::post('/favorite/add', [CourseController::class, 'addToFavorite'])->name('mahasiswa.favorite.add');
        Route::delete('/favorite/{id}', [CourseController::class, 'removeFromFavorite'])->name('mahasiswa.favorite.remove');

        // Chat
        Route::get('/chat', [ChatController::class, 'index'])->name('mahasiswa.chat');
        Route::get('/messages/conversations', [ChatController::class, 'getConversations'])->name('mahasiswa.messages.conversations');
        Route::get('/messages/chat/{dosenId}', [ChatController::class, 'getChatMessages'])->name('mahasiswa.messages.chat');
        Route::post('/messages/send', [ChatController::class, 'sendChatMessage'])->name('mahasiswa.messages.send');
        
        // Checkout & Payment
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('mahasiswa.checkout');
        Route::post('/cart/add', [CheckoutController::class, 'addToCart'])->name('mahasiswa.cart.add');
        Route::delete('/cart/{id}', [CheckoutController::class, 'removeFromCart'])->name('mahasiswa.cart.remove');
        Route::get('/payment', [CheckoutController::class, 'payment'])->name('mahasiswa.payment');
        Route::get('/payment-success', [CheckoutController::class, 'success'])->name('mahasiswa.payment-success');
        Route::get('/support', [SupportController::class, 'index'])->name('mahasiswa.support');
        Route::post('/support/ask', [SupportController::class, 'ask'])->name('mahasiswa.support.ask');
        
        // Finance
        Route::get('/finance', [CheckoutController::class, 'finance'])->name('mahasiswa.finance');
        Route::get('/finance/transaction/{id}', [CheckoutController::class, 'transactionDetail'])->name('mahasiswa.transaction-detail');
        
        // Apps Hub Launcher — kartu di /apps titik ke URL eksternal di tab baru.
        // Tidak ada lagi endpoint /apps/{slug} atau REST chat; cukup halaman index.
        Route::get('/apps', [AppsHubController::class, 'index'])->name('mahasiswa.apps');

        // Forum Komunitas (diskusi umum mahasiswa)
        Route::prefix('forum')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('mahasiswa.forum');
            Route::get('/create', [ForumController::class, 'create'])->name('mahasiswa.forum.create');
            Route::post('/', [ForumController::class, 'store'])->name('mahasiswa.forum.store');
            Route::get('/topic/{slug}', [ForumController::class, 'show'])->name('mahasiswa.forum.show');
            Route::post('/topic/{slug}/comment', [ForumController::class, 'storeComment'])->name('mahasiswa.forum.comment.store');
        });
        Route::get('/learning-goals', [LearningGoalController::class, 'index'])->name('mahasiswa.learning-goals');
        Route::get('/news', [DashboardController::class, 'news'])->name('mahasiswa.news');
        
        // Logout
        Route::match(['GET', 'POST'], '/logout', [MahasiswaController::class, 'logout'])->name('mahasiswa.logout');
    });
