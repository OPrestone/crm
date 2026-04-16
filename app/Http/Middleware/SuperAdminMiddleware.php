<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(auth()->check() && auth()->user()->isSuperAdmin(), 403, 'Super Admin only.');
        return $next($request);
    }
}
