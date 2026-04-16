<?php

namespace App\Providers;

use App\Models\Contact;
use App\Policies\ContactPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrap();

        Gate::policy(Contact::class, ContactPolicy::class);

        // Super admin bypass
        Gate::before(function ($user) {
            if ($user->hasRole('super_admin')) return true;
        });
    }
}
