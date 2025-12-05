<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\MahasiswaAuthController;

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
        Route::post('/logout', [MahasiswaAuthController::class, 'logout']);
    });
});
