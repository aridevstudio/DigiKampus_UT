<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\MahasiswaAuthController;
use App\Http\Controllers\Api\Auth\DosenAuthController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Mahasiswa\MahasiswaDashboardController;

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
