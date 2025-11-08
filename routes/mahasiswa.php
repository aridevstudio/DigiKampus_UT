<?php

use App\Http\Controllers\Auth\MahasiswaController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'mahasiswa', 'middleware' => 'guest'], function () {
    Route::get('login/mahasiswa', [MahasiswaController::class, 'showLoginForm'])->name('mahasiswa.login');
    Route::post('login/mahasiswa', [MahasiswaController::class, 'showLoginFormPost'])->name('mahasiswa.post');
});

Route::group(['prefix' => 'mahasiswa', 'middleware' => ['auth.custom:mahasiswa']], function () {
    Route::get('mahasiswa/dashboard', [MahasiswaController::class, 'showDashboard'])->name('mahasiswa.Dashboard');
});
