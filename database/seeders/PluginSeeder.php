<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    public static array $plugins = [
        // ── Free ──────────────────────────────────────────────────────────────
        ['name' => 'Contacts',              'slug' => 'contacts',        'description' => 'Manage contacts and relationships.',                    'icon' => 'bi-people-fill',           'color' => 'primary',   'route_prefix' => 'contacts',        'min_plan' => 'free',       'is_core' => true,  'sort_order' => 10],
        ['name' => 'Companies',             'slug' => 'companies',       'description' => 'Organise and manage company accounts.',                 'icon' => 'bi-building-fill',          'color' => 'secondary', 'route_prefix' => 'companies',       'min_plan' => 'free',       'is_core' => true,  'sort_order' => 20],
        ['name' => 'Leads',                 'slug' => 'leads',           'description' => 'Track and qualify sales leads with Kanban.',            'icon' => 'bi-funnel-fill',            'color' => 'warning',   'route_prefix' => 'leads',           'min_plan' => 'free',       'is_core' => false, 'sort_order' => 30],
        ['name' => 'Tasks',                 'slug' => 'tasks',           'description' => 'Assign and track tasks for your team.',                 'icon' => 'bi-check2-square',          'color' => 'info',      'route_prefix' => 'tasks',           'min_plan' => 'free',       'is_core' => false, 'sort_order' => 40],
        ['name' => 'Activities',            'slug' => 'activities',      'description' => 'Full timeline of all CRM activities and interactions.', 'icon' => 'bi-clock-history',          'color' => 'secondary', 'route_prefix' => 'activities',      'min_plan' => 'free',       'is_core' => true,  'sort_order' => 45],
        ['name' => 'Settings',              'slug' => 'settings',        'description' => 'Tenant configuration and preferences.',                 'icon' => 'bi-gear-fill',              'color' => 'secondary', 'route_prefix' => 'settings',        'min_plan' => 'free',       'is_core' => true,  'sort_order' => 120],

        // ── Starter ───────────────────────────────────────────────────────────
        ['name' => 'Deals',                 'slug' => 'deals',           'description' => 'Manage deals in a visual pipeline.',                    'icon' => 'bi-currency-dollar',        'color' => 'success',   'route_prefix' => 'deals',           'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 50],
        ['name' => 'Products Catalog',      'slug' => 'products',        'description' => 'Manage your product and service catalog with pricing.',  'icon' => 'bi-box-seam-fill',          'color' => 'info',      'route_prefix' => 'products',        'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 55],
        ['name' => 'Quotes & Proposals',    'slug' => 'quotes',          'description' => 'Create and send professional quotes to prospects.',      'icon' => 'bi-file-earmark-ruled-fill','color' => 'warning',   'route_prefix' => 'quotes',          'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 58],
        ['name' => 'Invoicing',             'slug' => 'invoicing',       'description' => 'Create and send professional invoices.',                 'icon' => 'bi-receipt-cutoff',         'color' => 'danger',    'route_prefix' => 'invoices',        'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 60],
        ['name' => 'Calendar',              'slug' => 'calendar',        'description' => 'Schedule and manage appointments and meetings.',         'icon' => 'bi-calendar3',              'color' => 'primary',   'route_prefix' => 'appointments',    'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 65],
        ['name' => 'Help Desk',             'slug' => 'helpdesk',        'description' => 'Customer support ticket management and tracking.',       'icon' => 'bi-headset',                'color' => 'info',      'route_prefix' => 'tickets',         'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 68],
        ['name' => 'Documents',             'slug' => 'documents',       'description' => 'Store and manage files linked to contacts and deals.',   'icon' => 'bi-folder2-open',           'color' => 'warning',   'route_prefix' => 'documents',       'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 72],
        ['name' => 'Goals & Targets',       'slug' => 'goals',           'description' => 'Set and track sales goals for your team.',               'icon' => 'bi-bullseye',               'color' => 'success',   'route_prefix' => 'goals',           'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 75],
        ['name' => 'Reports',               'slug' => 'reports',         'description' => 'Sales analytics and business intelligence.',             'icon' => 'bi-bar-chart-fill',         'color' => 'primary',   'route_prefix' => 'reports',         'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 78],
        ['name' => 'Notifications',         'slug' => 'notifications',   'description' => 'In-app notifications and alerts.',                       'icon' => 'bi-bell-fill',              'color' => 'warning',   'route_prefix' => 'notifications',   'min_plan' => 'starter',    'is_core' => false, 'sort_order' => 82],

        // ── Pro ───────────────────────────────────────────────────────────────
        ['name' => 'Card Generator',        'slug' => 'cards',           'description' => 'Generate digital and printable business cards.',         'icon' => 'bi-person-vcard-fill',      'color' => 'info',      'route_prefix' => 'cards',           'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 90],
        ['name' => 'AI & Intelligence',     'slug' => 'ai_tools',        'description' => 'AI-powered lead scoring, insights and email.',           'icon' => 'bi-stars',                  'color' => 'purple',    'route_prefix' => 'ai',              'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 95],
        ['name' => 'Email Campaigns',       'slug' => 'email_campaigns', 'description' => 'Design and send targeted email campaigns to contacts.', 'icon' => 'bi-envelope-paper-fill',    'color' => 'primary',   'route_prefix' => 'email-campaigns', 'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 97],
        ['name' => 'Web Forms',             'slug' => 'web_forms',       'description' => 'Build embeddable lead capture forms.',                   'icon' => 'bi-ui-checks-grid',         'color' => 'success',   'route_prefix' => 'web-forms',       'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 99],
        ['name' => 'Contracts',             'slug' => 'contracts',       'description' => 'Manage the full contract lifecycle with e-signature.',   'icon' => 'bi-file-earmark-text-fill', 'color' => 'warning',   'route_prefix' => 'contracts',       'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 101],
        ['name' => 'Sales Forecasting',     'slug' => 'forecasting',     'description' => 'Predict future revenue with AI pipeline analysis.',      'icon' => 'bi-graph-up-arrow',         'color' => 'info',      'route_prefix' => 'forecasting',     'min_plan' => 'pro',        'is_core' => false, 'sort_order' => 103],

        // ── Enterprise ────────────────────────────────────────────────────────
        ['name' => 'ID Verification (KYC)', 'slug' => 'id_verification', 'description' => 'Identity verification and KYC compliance.',             'icon' => 'bi-shield-fill-check',      'color' => 'danger',    'route_prefix' => 'id-verification', 'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 110],
        ['name' => 'Commission Tracking',   'slug' => 'commissions',     'description' => 'Define plans and auto-calculate rep commissions.',        'icon' => 'bi-cash-coin',              'color' => 'success',   'route_prefix' => 'commissions',     'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 113],
        ['name' => 'Territory Management',  'slug' => 'territories',     'description' => 'Define territories and auto-route leads to reps.',        'icon' => 'bi-map-fill',               'color' => 'secondary', 'route_prefix' => 'territories',     'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 115],
        ['name' => 'Audit Log',             'slug' => 'audit_log',       'description' => 'Tamper-proof activity log for GDPR & SOC 2.',            'icon' => 'bi-journal-check',          'color' => 'danger',    'route_prefix' => 'audit-log',       'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 117],
        ['name' => 'API & Webhooks',        'slug' => 'api_access',      'description' => 'REST API and configurable webhooks for integrations.',   'icon' => 'bi-code-slash',             'color' => 'dark',      'route_prefix' => 'api-access',      'min_plan' => 'enterprise', 'is_core' => false, 'sort_order' => 119],
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
