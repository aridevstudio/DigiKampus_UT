<?php

use App\Http\Controllers\Auth\MahasiswaController;
use App\Http\Middleware\EnsureAuthenticatedMahasiswa;
use App\Http\Middleware\RedirectIfAuthenticatedMahasiswa;
use Illuminate\Support\Facades\Route;

// Route untuk login (hanya bisa diakses jika belum login)
Route::prefix('mahasiswa')
    ->middleware(RedirectIfAuthenticatedMahasiswa::class)
    ->group(function () {
        Route::get('/login/mahasiswa', [MahasiswaController::class, 'showLoginForm'])->name('mahasiswa.login');
        Route::post('/login/mahasiswa', [MahasiswaController::class, 'showLoginFormPost'])->name('mahasiswa.post');
    });

// Route untuk dashboard (hanya bisa diakses jika sudah login)
Route::prefix('mahasiswa')
    ->middleware(EnsureAuthenticatedMahasiswa::class)
    ->group(function () {
        // get
        Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'showDashboard'])->name('mahasiswa.dashboard');

        // post
        Route::post('/mahasiswa/logout', [MahasiswaController::class, 'logout'])->name('mahasiswa.logout');
    });
