<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS scheme in any non-local environment so generated URLs
        // (asset(), url(), route() without the `false` flag) never produce
        // http:// endpoints that browsers block as Mixed Content.
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Pin the URL generator root to the explicit APP_URL when one is set.
        // Laravel's `route(..., false)` calls are unaffected because they
        // intentionally produce relative paths; this only anchors absolute
        // URL helpers used by asset() and similar helpers.
        $rootUrl = rtrim((string) config('app.url'), '/');
        if ($rootUrl !== '' && (str_starts_with($rootUrl, 'http://') || str_starts_with($rootUrl, 'https://'))) {
            // If a non-local environment was deployed with an http:// APP_URL
            // by mistake, transparently upgrade it to https:// rather than
            // accepting the mixed-content leak at startup.
            if (config('app.env') !== 'local' && str_starts_with($rootUrl, 'http://')) {
                $rootUrl = 'https://' . substr($rootUrl, 7);
            }
            URL::forceRootUrl($rootUrl);
        }
    }
}
