<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeveloperSeeder extends Seeder
{
    public function run(): void
    {
        $tid   = Tenant::where('slug', 'acme-corp')->value('id');
        $owner = User::where('email', 'demo@acme.com')->first();

        $apps = [
            [
                'name'           => 'Acme CRM Zapier Integration',
                'description'    => 'Connects Acme CRM with Zapier to enable 5,000+ app automations. Used for lead capture from HubSpot forms.',
                'client_id'      => Str::random(32),
                'client_secret'  => Str::random(64),
                'webhook_url'    => 'https://hooks.zapier.com/hooks/catch/1234567/abcdef/',
                'webhook_events' => ['contact.created', 'deal.won', 'invoice.paid'],
                'allowed_ips'    => ['54.208.168.0/24', '52.206.10.0/24'],
                'rate_limit'     => 2000,
                'is_active'      => true,
                'last_used_at'   => now()->subHours(2),
                'total_requests' => 14823,
            ],
            [
                'name'           => 'Slack Notifications Bot',
                'description'    => 'Posts deal updates, task reminders, and invoice alerts to the #sales Slack channel.',
                'client_id'      => Str::random(32),
                'client_secret'  => Str::random(64),
                'webhook_url'    => 'https://hooks.slack.com/services/T0000/B0000/xxxxxxxxxxxx',
                'webhook_events' => ['deal.created', 'deal.won', 'deal.lost', 'task.overdue'],
                'allowed_ips'    => null,
                'rate_limit'     => 500,
                'is_active'      => true,
                'last_used_at'   => now()->subMinutes(45),
                'total_requests' => 3291,
            ],
            [
                'name'           => 'Internal Analytics Dashboard',
                'description'    => 'Read-only API app for the internal Metabase analytics dashboard. Powers the executive KPI board.',
                'client_id'      => Str::random(32),
                'client_secret'  => Str::random(64),
                'webhook_url'    => null,
                'webhook_events' => null,
                'allowed_ips'    => ['10.0.0.0/8'],
                'rate_limit'     => 5000,
                'is_active'      => true,
                'last_used_at'   => now()->subHours(1),
                'total_requests' => 89204,
            ],
        ];

        foreach ($apps as $app) {
            DB::table('developer_apps')->insert(array_merge($app, [
                'tenant_id'      => $tid,
                'created_by'     => $owner->id,
                'webhook_events' => $app['webhook_events'] ? json_encode($app['webhook_events']) : null,
                'allowed_ips'    => $app['allowed_ips'] ? json_encode($app['allowed_ips']) : null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]));
        }
    }
}
