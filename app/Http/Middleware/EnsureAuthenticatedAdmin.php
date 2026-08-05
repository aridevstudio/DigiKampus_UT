<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('admin.login')->withErrors([
                'login' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.',
            ]);
        }

        return $next($request);
    }
}
