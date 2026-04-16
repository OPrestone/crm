<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ResolveTenantByDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        $tenant = null;

        if ($host !== $appHost) {
            $isSubdomain = str_ends_with($host, '.' . $appHost);
            if ($isSubdomain) {
                $sub = str_replace('.' . $appHost, '', $host);
                $tenant = Tenant::where('subdomain', $sub)->where('status', 'active')->first();
            } else {
                $tenant = Tenant::where('custom_domain', $host)
                    ->where('domain_status', 'active')
                    ->where('status', 'active')
                    ->first();
            }
        }

        if ($tenant) {
            app()->instance('current.tenant.domain', $tenant);
            View::share('domainTenant', $tenant);
        }

        return $next($request);
    }
}
