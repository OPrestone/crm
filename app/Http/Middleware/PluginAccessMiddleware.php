<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PluginAccessMiddleware
{
    public function handle(Request $request, Closure $next, string $plugin): mixed
    {
        $user   = auth()->user();
        $tenant = $user?->tenant;

        if (!$tenant || !$tenant->hasPlugin($plugin)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'This module is not enabled for your plan.'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', "The <strong>" . ucfirst(str_replace('_', ' ', $plugin)) . "</strong> module is not available on your current plan. Please upgrade or contact your administrator.");
        }

        return $next($request);
    }
}
