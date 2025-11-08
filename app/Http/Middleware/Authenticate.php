<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Cek prefix URL atau guard aktif
            if ($request->is('admin/*')) {
                return route('admin.login');
            } elseif ($request->is('dosen/*')) {
                return route('dosen.login');
            } elseif ($request->is('mahasiswa/*')) {
                return route('mahasiswa.login');
            }


        }
    }
}
