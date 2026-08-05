<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (!$request->isSecure() && app()->environment('local')) {
            return $response;
        }

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Report-only first: inventory inline/external dependencies before
        // enforcing CSP so existing pages do not break unexpectedly.
        $response->headers->set(
            'Content-Security-Policy-Report-Only',
            "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; img-src 'self' data: https:; media-src 'self' https:; frame-src 'self' https://www.youtube-nocookie.com https://www.youtube.com; connect-src 'self' https: wss:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:"
        );

        return $response;
    }
}
