<?php

namespace App\Providers;

use App\Models\Contact;
use App\Policies\ContactPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        Gate::policy(Contact::class, ContactPolicy::class);

        // Super admin bypass
        Gate::before(function ($user) {
            if ($user->hasRole('super_admin')) return true;
        });

        // API rate limiter — 120 requests/minute per app (client_secret) or IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->bearerToken() ?: $request->ip());
        });
    }
}
