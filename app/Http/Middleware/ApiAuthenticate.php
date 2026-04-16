<?php

namespace App\Http\Middleware;

use App\Models\DeveloperApp;
use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Missing Bearer token.'], 401);
        }

        $app = DeveloperApp::where('client_secret', $token)->where('is_active', true)->first();

        if (!$app) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid or revoked API key.'], 401);
        }

        $ipWhitelist = $app->allowed_ips;
        if (!empty($ipWhitelist)) {
            $clientIp = $request->ip();
            $allowed = is_array($ipWhitelist) ? $ipWhitelist : array_map('trim', explode(',', (string) $ipWhitelist));
            if (!in_array($clientIp, $allowed)) {
                return response()->json(['error' => 'Forbidden', 'message' => 'IP not whitelisted.'], 403);
            }
        }

        $request->attributes->set('api_app', $app);
        $request->attributes->set('api_tenant', $app->tenant);

        $startTime = microtime(true);
        $response = $next($request);
        $elapsed = (int) ((microtime(true) - $startTime) * 1000);

        $app->increment('total_requests');
        $app->update(['last_used_at' => now()]);

        try {
            ApiLog::create([
                'developer_app_id' => $app->id,
                'tenant_id'        => $app->tenant_id,
                'method'           => $request->method(),
                'endpoint'         => $request->path(),
                'status_code'      => $response->getStatusCode(),
                'response_time_ms' => $elapsed,
                'ip_address'       => $request->ip(),
            ]);
        } catch (\Throwable) {}

        return $response;
    }

    private function extractToken(Request $request): ?string
    {
        $bearer = $request->bearerToken();
        if ($bearer) return $bearer;

        $key = $request->header('X-API-Key') ?? $request->query('api_key');
        return $key ?: null;
    }
}
