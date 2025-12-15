<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

require __DIR__.'/mahasiswa.php';
require __DIR__.'/admin.php';
require __DIR__.'/dosen.php';
