<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\DeveloperApp;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    private function tenantId(): int
    {
        return Auth::user()->tenant_id;
    }

    public function index()
    {
        $tenantId = $this->tenantId();
        $apps = DeveloperApp::where('tenant_id', $tenantId)->get();

        $stats = [
            'apps'          => $apps->count(),
            'active_apps'   => $apps->where('is_active', true)->count(),
            'calls_today'   => ApiLog::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            'calls_week'    => ApiLog::where('tenant_id', $tenantId)->where('created_at', '>=', now()->subDays(7))->count(),
            'errors_today'  => ApiLog::where('tenant_id', $tenantId)->whereDate('created_at', today())->where('status_code', '>=', 400)->count(),
            'webhooks_sent' => WebhookDelivery::where('tenant_id', $tenantId)->where('status', 'delivered')->count(),
            'webhooks_failed'=> WebhookDelivery::where('tenant_id', $tenantId)->where('status', 'failed')->count(),
            'total_requests'=> $apps->sum('total_requests'),
        ];

        $recentLogs = ApiLog::where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->take(10)->get();

        $recentWebhooks = WebhookDelivery::where('tenant_id', $tenantId)
            ->with('app')
            ->orderByDesc('created_at')
            ->take(5)->get();

        // Chart: API calls by day for last 7 days
        $chartDays = collect();
        $chartCalls = collect();
        $chartErrors = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartDays->push(now()->subDays($i)->format('D'));
            $chartCalls->push(ApiLog::where('tenant_id', $tenantId)->whereDate('created_at', $date)->count());
            $chartErrors->push(ApiLog::where('tenant_id', $tenantId)->whereDate('created_at', $date)->where('status_code', '>=', 400)->count());
        }

        return view('developer.index', compact('apps', 'stats', 'recentLogs', 'recentWebhooks', 'chartDays', 'chartCalls', 'chartErrors'));
    }

    public function apps()
    {
        $apps = DeveloperApp::where('tenant_id', $this->tenantId())
            ->withCount('logs')
            ->with('creator')
            ->latest()->get();

        return view('developer.apps.index', compact('apps'));
    }

    public function createApp()
    {
        $events = DeveloperApp::allEvents();
        return view('developer.apps.create', compact('events'));
    }

    public function storeApp(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string|max:500',
            'webhook_url'    => 'nullable|url|max:500',
            'webhook_events' => 'nullable|array',
            'allowed_ips'    => 'nullable|string',
            'rate_limit'     => 'nullable|integer|min:100|max:100000',
        ]);

        $secret = DeveloperApp::generateSecret();
        $ips = null;
        if (!empty($data['allowed_ips'])) {
            $ips = array_map('trim', explode("\n", $data['allowed_ips']));
            $ips = array_filter($ips);
        }

        $app = DeveloperApp::create([
            'tenant_id'      => $this->tenantId(),
            'created_by'     => Auth::id(),
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'client_id'      => DeveloperApp::generateClientId(),
            'client_secret'  => $secret,
            'webhook_url'    => $data['webhook_url'] ?? null,
            'webhook_events' => $data['webhook_events'] ?? [],
            'allowed_ips'    => $ips ?: null,
            'rate_limit'     => $data['rate_limit'] ?? 1000,
        ]);

        // Show secret once on creation (store in session flash)
        session()->flash('new_secret', $secret);
        session()->flash('success', "App \"{$app->name}\" created. Save your secret key — it won't be shown again.");

        return redirect()->route('developer.apps.show', $app);
    }

    public function showApp(DeveloperApp $app)
    {
        $this->authorizeApp($app);
        $events = DeveloperApp::allEvents();

        $recentLogs = ApiLog::where('developer_app_id', $app->id)
            ->orderByDesc('created_at')
            ->take(15)->get();

        $recentWebhooks = WebhookDelivery::where('developer_app_id', $app->id)
            ->orderByDesc('created_at')
            ->take(10)->get();

        $stats = [
            'total'     => $app->total_requests,
            'today'     => ApiLog::where('developer_app_id', $app->id)->whereDate('created_at', today())->count(),
            'errors'    => ApiLog::where('developer_app_id', $app->id)->where('status_code', '>=', 400)->count(),
            'wh_sent'   => WebhookDelivery::where('developer_app_id', $app->id)->where('status', 'delivered')->count(),
        ];

        return view('developer.apps.show', compact('app', 'events', 'recentLogs', 'recentWebhooks', 'stats'));
    }

    public function updateApp(Request $request, DeveloperApp $app)
    {
        $this->authorizeApp($app);
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string|max:500',
            'webhook_url'    => 'nullable|url|max:500',
            'webhook_events' => 'nullable|array',
            'allowed_ips'    => 'nullable|string',
            'rate_limit'     => 'nullable|integer|min:100|max:100000',
            'is_active'      => 'nullable|boolean',
        ]);

        $ips = null;
        if (!empty($data['allowed_ips'])) {
            $ips = array_filter(array_map('trim', explode("\n", $data['allowed_ips'])));
        }

        $app->update([
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'webhook_url'    => $data['webhook_url'] ?? null,
            'webhook_events' => $data['webhook_events'] ?? [],
            'allowed_ips'    => $ips ?: null,
            'rate_limit'     => $data['rate_limit'] ?? 1000,
            'is_active'      => isset($data['is_active']) ? (bool)$data['is_active'] : $app->is_active,
        ]);

        return back()->with('success', 'App updated successfully.');
    }

    public function regenerateSecret(DeveloperApp $app)
    {
        $this->authorizeApp($app);
        $secret = DeveloperApp::generateSecret();
        $app->update(['client_secret' => $secret]);
        session()->flash('new_secret', $secret);
        return back()->with('success', 'Secret regenerated. Save it now — it won\'t be shown again.');
    }

    public function destroyApp(DeveloperApp $app)
    {
        $this->authorizeApp($app);
        $app->logs()->delete();
        $app->webhookDeliveries()->delete();
        $app->delete();
        return redirect()->route('developer.apps')->with('success', 'App deleted successfully.');
    }

    public function logs(Request $request)
    {
        $tenantId = $this->tenantId();
        $apps = DeveloperApp::where('tenant_id', $tenantId)->get();

        $query = ApiLog::where('tenant_id', $tenantId);

        if ($request->app_id) $query->where('developer_app_id', $request->app_id);
        if ($request->method) $query->where('method', $request->method);
        if ($request->status) {
            match ($request->status) {
                '2xx' => $query->whereBetween('status_code', [200, 299]),
                '4xx' => $query->whereBetween('status_code', [400, 499]),
                '5xx' => $query->whereBetween('status_code', [500, 599]),
                default => null
            };
        }
        if ($request->date) $query->whereDate('created_at', $request->date);

        $logs = $query->orderByDesc('created_at')->paginate(50);

        return view('developer.logs', compact('logs', 'apps'));
    }

    public function webhookLogs(Request $request)
    {
        $tenantId = $this->tenantId();
        $apps = DeveloperApp::where('tenant_id', $tenantId)->get();

        $query = WebhookDelivery::where('tenant_id', $tenantId)->with('app');
        if ($request->app_id) $query->where('developer_app_id', $request->app_id);
        if ($request->event)  $query->where('event', $request->event);
        if ($request->status) $query->where('status', $request->status);

        $deliveries = $query->orderByDesc('created_at')->paginate(50);
        $events = DeveloperApp::allEvents();

        return view('developer.webhooks', compact('deliveries', 'apps', 'events'));
    }

    public function testWebhook(Request $request, DeveloperApp $app)
    {
        $this->authorizeApp($app);
        $request->validate(['event' => 'required|string']);

        if (!$app->webhook_url) {
            return back()->with('error', 'No webhook URL configured for this app.');
        }

        $payload = [
            'event'     => $request->event,
            'timestamp' => now()->toISOString(),
            'data'      => ['id' => 1, 'test' => true, 'message' => 'This is a test webhook from ' . config('app.name') . ' CRM'],
        ];

        $delivery = WebhookDelivery::create([
            'developer_app_id' => $app->id,
            'tenant_id'        => $this->tenantId(),
            'event'            => $request->event,
            'payload'          => $payload,
            'endpoint_url'     => $app->webhook_url,
            'status'           => 'pending',
            'attempts'         => 0,
        ]);

        // Simulate delivery attempt
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['X-CRM-Event' => $request->event, 'X-CRM-App' => $app->client_id])
                ->post($app->webhook_url, $payload);

            $delivery->update([
                'status'       => $response->successful() ? 'delivered' : 'failed',
                'attempts'     => 1,
                'response_code'=> $response->status(),
                'response_body'=> substr($response->body(), 0, 1000),
                'delivered_at' => now(),
            ]);

            $msg = $response->successful() ? 'Test webhook delivered successfully!' : 'Webhook sent but endpoint returned ' . $response->status();
        } catch (\Exception $e) {
            $delivery->update(['status' => 'failed', 'attempts' => 1, 'error_message' => $e->getMessage()]);
            $msg = 'Webhook delivery failed: ' . $e->getMessage();
        }

        return back()->with('success', $msg);
    }

    public function docs()
    {
        $apps = DeveloperApp::where('tenant_id', $this->tenantId())->where('is_active', true)->first();
        return view('developer.docs', compact('apps'));
    }

    private function authorizeApp(DeveloperApp $app): void
    {
        abort_if($app->tenant_id !== $this->tenantId(), 403);
    }
}
