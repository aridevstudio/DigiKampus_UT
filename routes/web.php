<?php

use App\Http\Controllers\Mahasiswa\CheckoutController;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ratedCourses = Course::query()
        ->where('jumlah_ulasan', '>', 0)
        ->get(['rating', 'jumlah_ulasan']);

    $totalReviews = (int) $ratedCourses->sum('jumlah_ulasan');
    $weightedRating = $totalReviews > 0
        ? $ratedCourses->sum(fn (Course $course) => ((float) $course->rating) * ((int) $course->jumlah_ulasan)) / $totalReviews
        : 0;

    $landingStats = [
        ['count' => User::where('role', 'mahasiswa')->where('status', 'aktif')->count(), 'label' => 'Mahasiswa Aktif', 'suffix' => '+'],
        ['count' => Course::query()->aktif()->where('kategori', 'kursus')->count(), 'label' => 'Kursus Tersedia', 'suffix' => '+'],
        ['count' => User::where('role', 'dosen')->where('status', 'aktif')->count(), 'label' => 'Dosen Pengajar', 'suffix' => '+'],
        ['count' => (int) round(($weightedRating / 5) * 100), 'label' => 'Kepuasan Pengguna', 'suffix' => '%'],
    ];

    return view('home', compact('landingStats'));
});

Route::post('/payments/midtrans/notification', [CheckoutController::class, 'midtransNotification'])
    ->name('midtrans.notification');

require __DIR__.'/mahasiswa.php';
require __DIR__.'/admin.php';
require __DIR__.'/dosen.php';
