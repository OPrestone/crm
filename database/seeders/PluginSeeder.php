<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\Tenant;
use App\Models\TenantPlugin;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    public static array $plugins = [
        ['name' => 'Contacts',              'slug' => 'contacts',        'description' => 'Manage contacts and relationships.',              'icon' => 'bi-people-fill',          'color' => 'primary',   'route_prefix' => 'contacts',        'min_plan' => 'free',       'is_core' => true,  'sort_order' => 10],
        ['name' => 'Companies',             'slug' => 'companies',       'description' => 'Organise and manage company accounts.',           'icon' => 'bi-building-fill',         'color' => 'secondary', 'route_prefix' => 'companies',       'min_plan' => 'free',       'is_core' => true,  'sort_order' => 20],
        ['name' => 'Leads',                 'slug' => 'leads',           'description' => 'Track and qualify sales leads with Kanban.',      'icon' => 'bi-funnel-fill',           'color' => 'warning',   'route_prefix' => 'leads',           'min_plan' => 'free',       'is_core' => false, 'sort_order' => 30],
        ['name' => 'Tasks',                 'slug' => 'tasks',           'description' => 'Assign and track tasks for your team.',           'icon' => 'bi-check2-square',         'color' => 'info',      'route_prefix' => 'tasks',           'min_plan' => 'free',       'is_core' => false, 'sort_order' => 40],
        ['name' => 'Deals',                 'slug' => 'deals',           'description' => 'Manage deals in a visual pipeline.',             'icon' => 'bi-currency-dollar',       'color' => 'success',   'route_prefix' => 'deals',           'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 50],
        ['name' => 'Invoicing',             'slug' => 'invoicing',       'description' => 'Create and send professional invoices.',         'icon' => 'bi-receipt-cutoff',        'color' => 'danger',    'route_prefix' => 'invoices',        'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 60],
        ['name' => 'Reports',               'slug' => 'reports',         'description' => 'Sales analytics and business intelligence.',     'icon' => 'bi-bar-chart-fill',        'color' => 'primary',   'route_prefix' => 'reports',         'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 70],
        ['name' => 'Notifications',         'slug' => 'notifications',   'description' => 'In-app notifications and alerts.',               'icon' => 'bi-bell-fill',             'color' => 'warning',   'route_prefix' => 'notifications',   'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 80],
        ['name' => 'Card Generator',        'slug' => 'cards',           'description' => 'Generate digital and printable business cards.', 'icon' => 'bi-person-vcard-fill',     'color' => 'info',      'route_prefix' => 'cards',           'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 90],
        ['name' => 'AI & Intelligence',     'slug' => 'ai_tools',        'description' => 'AI-powered lead scoring, insights and email.',   'icon' => 'bi-stars',                 'color' => 'purple',    'route_prefix' => 'ai',              'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 100],
        ['name' => 'ID Verification (KYC)', 'slug' => 'id_verification', 'description' => 'Identity verification and KYC compliance.',     'icon' => 'bi-shield-fill-check',     'color' => 'danger',    'route_prefix' => 'id-verification', 'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 110],
        ['name' => 'Settings',              'slug' => 'settings',        'description' => 'Tenant configuration and preferences.',          'icon' => 'bi-gear-fill',             'color' => 'secondary', 'route_prefix' => 'settings',        'min_plan' => 'free',       'is_core' => true,  'sort_order' => 120],
    ];

    public function run(): void
    {
        foreach (self::$plugins as $plugin) {
            Plugin::updateOrCreate(['slug' => $plugin['slug']], $plugin);
        }

        foreach (Tenant::all() as $tenant) {
            $tenant->clearPluginCache();
        }
    }
}
