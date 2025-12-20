<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\MahasiswaAuthController;
use App\Http\Controllers\Api\Auth\DosenAuthController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Mahasiswa\MahasiswaDashboardController;
use App\Http\Controllers\Api\Mahasiswa\CourseController;
use App\Http\Controllers\Api\Mahasiswa\MyCourseController;
use App\Http\Controllers\Api\Mahasiswa\CartController;
use App\Http\Controllers\Api\Mahasiswa\FavoriteController;
use App\Http\Controllers\Api\Mahasiswa\NotificationController;
use App\Http\Controllers\Api\Mahasiswa\StatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth/mahasiswa')->group(function () {
    Route::post('/login', [MahasiswaAuthController::class, 'login']);
    Route::post('/forgot-password', [MahasiswaAuthController::class, 'forgotPassword']);
    Route::post('/verify-otp', [MahasiswaAuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [MahasiswaAuthController::class, 'resetPassword']);
});

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('mahasiswa')->group(function () {
        Route::get('/profile', [MahasiswaAuthController::class, 'profile']);
        Route::put('/profile', [MahasiswaAuthController::class, 'updateProfile']);
        Route::put('/change-password', [MahasiswaAuthController::class, 'changePassword']);
        Route::post('/logout', [MahasiswaAuthController::class, 'logout']);

        // Dashboard routes
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index']);
        Route::get('/dashboard/progress', [MahasiswaDashboardController::class, 'progress']);
        Route::get('/dashboard/courses', [MahasiswaDashboardController::class, 'enrolledCourses']);
        Route::get('/dashboard/news', [MahasiswaDashboardController::class, 'news']);
        Route::get('/dashboard/agenda', [MahasiswaDashboardController::class, 'agenda']);

        // Course routes (Get Courses - for browsing)
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/webinar', [CourseController::class, 'webinar']);
        Route::get('/courses/tiket', [CourseController::class, 'tiket']);
        Route::get('/courses/kursus', [CourseController::class, 'kursus']);
        Route::get('/courses/{id}', [CourseController::class, 'show']);
        Route::post('/courses/{id}/rate', [CourseController::class, 'rate']);

        // My Courses routes (Kursus Saya - enrolled courses for learning)
        Route::get('/my-courses', [MyCourseController::class, 'index']);
        Route::get('/my-courses/weekly-target', [MyCourseController::class, 'weeklyTarget']);
        Route::get('/my-courses/recommendations', [MyCourseController::class, 'recommendations']);
        Route::get('/my-courses/{id}', [MyCourseController::class, 'show']);
        Route::post('/my-courses/{courseId}/materials/{materialId}/complete', [MyCourseController::class, 'completeMaterial']);

        // Cart routes (Keranjang)
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/add', [CartController::class, 'add']);
        Route::delete('/cart/{id}', [CartController::class, 'remove']);
        Route::delete('/cart', [CartController::class, 'clear']);

        // Favorites routes (Simpan)
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/add', [FavoriteController::class, 'add']);
        Route::delete('/favorites/{id}', [FavoriteController::class, 'remove']);
        Route::post('/favorites/toggle/{courseId}', [FavoriteController::class, 'toggle']);

        // Notifications routes (Notifikasi)
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Status routes (Online/Offline)
        Route::get('/status', [StatusController::class, 'getStatus']);
        Route::post('/status/online', [StatusController::class, 'setOnline']);
        Route::post('/status/offline', [StatusController::class, 'setOffline']);
        Route::post('/status/heartbeat', [StatusController::class, 'heartbeat']);
    });

    Route::prefix('dosen')->group(function () {
        Route::get('/profile', [DosenAuthController::class, 'profile']);
        Route::post('/logout', [DosenAuthController::class, 'logout']);
    });
});

// Dosen public routes
Route::prefix('auth/dosen')->group(function () {
    Route::post('/register', [DosenAuthController::class, 'register']);
    Route::post('/login', [DosenAuthController::class, 'login']);
    Route::post('/forgot-password', [DosenAuthController::class, 'forgotPassword']);
    Route::post('/verify-otp', [DosenAuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [DosenAuthController::class, 'resetPassword']);
    Route::get('/google', [DosenAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [DosenAuthController::class, 'handleGoogleCallback']);
});

// Admin public routes
Route::prefix('auth/admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/verify-otp', [AdminAuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword']);
});

// Admin protected routes
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/profile', [AdminAuthController::class, 'profile']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});
