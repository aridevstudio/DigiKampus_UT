<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan kesalahan default yang digunakan oleh
    | class validator. Beberapa aturan memiliki beberapa versi seperti aturan
    | ukuran. Silakan sesuaikan pesan-pesan ini sesuai kebutuhanmu.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',

    'min' => [
        'numeric' => ':attribute minimal :3.',
        'file' => ':attribute minimal :min kilobytes.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal memiliki :min item.',
    ],

    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'file' => ':attribute maksimal :max kilobytes.',
        'string' => ':attribute maksimal :max karakter.',
        'array' => ':attribute maksimal memiliki :max item.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Kustom
    |--------------------------------------------------------------------------
    |
    | Di sini kamu bisa menentukan pesan validasi kustom untuk atribut tertentu.
    | Contoh: 'email.required' => 'Email wajib diisi!'
    |
    */

    'custom' => [
        'password' => [
            'min' => 'Password minimal :min karakter.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Kustom
    |--------------------------------------------------------------------------
    |
    | Baris berikut digunakan untuk mengganti nama atribut dengan sesuatu yang
    | lebih mudah dibaca pengguna (misal: “E-Mail Address” → “Alamat Email”).
    |
    */

    'attributes' => [
        'nim' => 'NIM',
        'password' => 'Password',
    ],

];
