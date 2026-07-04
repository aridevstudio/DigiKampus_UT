<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust reverse-proxy headers (Vercel, Cloudflare, Nginx) so Laravel
        // recognizes HTTPS termination at the proxy. Without this, request->isSecure()
        // returns false and `URL::forceScheme('https')` would still be required to
        // re-encode scheme on generated URLs. Set TRUSTED_PROXIES env to a
        // comma-separated CIDR/list to harden beyond the default `*`.
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '*'),
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
                | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,
        );

        // Enable Sanctum SPA/session auth on API routes.
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            'payments/midtrans/notification',
        ]);

        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
