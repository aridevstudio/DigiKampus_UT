<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedMahasiswa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('mahasiswa')->check()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi mahasiswa sudah berakhir. Silakan login kembali.',
                    'redirect' => route('mahasiswa.login'),
                ], 401);
            }

            return redirect()->route('mahasiswa.login')->withErrors([
                'login' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.',
            ]);
        }

        return $next($request);
    }
}
