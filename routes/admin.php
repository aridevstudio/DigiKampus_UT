<?php

use Illuminate\Support\Facades\Route;


Route::get('/authAdmin', function () {
    return view('home');
})->name('admin');



