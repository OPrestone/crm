<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();

        // ── Disable FK constraints (driver-aware) ─────────────────────────
        match ($driver) {
            'sqlite' => DB::statement('PRAGMA foreign_keys = OFF'),
            'mysql'  => DB::statement('SET FOREIGN_KEY_CHECKS = 0'),
            'pgsql'  => DB::statement("SET session_replication_role = 'replica'"),
            default  => null,
        };

        // ── All data tables in reverse-dependency order ───────────────────
        $tables = [
            // Spatie permission pivot tables
            'model_has_permissions', 'role_has_permissions', 'model_has_roles',
            'permissions', 'roles',

            // Module data tables
            'webhook_deliveries',
            'developer_apps',
            'territory_user', 'territories',
            'commissions', 'commission_plans',
            'sales_quotas',
            'contract_templates', 'contracts',
            'web_form_submissions', 'web_forms',
            'email_campaign_contacts', 'email_campaigns',
            'crm_notifications',
            'cards', 'card_templates',
            'goals',
            'documents',
            'ticket_replies', 'tickets',
            'quote_items', 'quotes',
            'invoice_items', 'invoices',
            'products',
            'appointments',
            'activities',
            'tasks',
            'deals', 'leads',
            'contacts', 'companies',
            'pipeline_stages',
            'crm_settings',
            'tenant_plugins', 'plugins',
            'users', 'tenants',
        ];

        foreach ($tables as $table) {
            // PostgreSQL TRUNCATE supports RESTART IDENTITY to reset sequences
            if ($driver === 'pgsql') {
                DB::statement("TRUNCATE TABLE \"{$table}\" RESTART IDENTITY CASCADE");
            } else {
                DB::table($table)->truncate();
            }
        }

        // ── Re-enable FK constraints ──────────────────────────────────────
        match ($driver) {
            'sqlite' => DB::statement('PRAGMA foreign_keys = ON'),
            'mysql'  => DB::statement('SET FOREIGN_KEY_CHECKS = 1'),
            'pgsql'  => DB::statement("SET session_replication_role = 'default'"),
            default  => null,
        };

        // ── Seed in dependency order ──────────────────────────────────────
        $this->call([
            // Foundation
            RoleSeeder::class,          // roles
            TenantSeeder::class,        // tenants + users + crm_settings

            // Pipeline before leads/deals
            PipelineSeeder::class,      // pipeline_stages

            // Core CRM data
            CompanySeeder::class,       // companies
            ContactSeeder::class,       // contacts
            LeadSeeder::class,          // leads
            DealSeeder::class,          // deals

            // Tasks & Activity
            TaskSeeder::class,          // tasks
            ActivitySeeder::class,      // activities (linked to contacts/leads/deals)

            // Calendar
            AppointmentSeeder::class,   // appointments

            // Product catalog before invoices/quotes
            ProductSeeder::class,       // products

            // Financial documents
            InvoiceSeeder::class,       // invoices + invoice_items
            QuoteSeeder::class,         // quotes + quote_items (needs products)

            // Support
            TicketSeeder::class,        // tickets + ticket_replies

            // Documents
            DocumentSeeder::class,      // documents (polymorphic)

            // Goals & KPIs
            GoalSeeder::class,          // goals

            // Business cards
            CardSeeder::class,          // card_templates + cards

            // Notifications
            NotificationSeeder::class,  // crm_notifications

            // Marketing
            EmailCampaignSeeder::class, // email_campaigns + recipients
            WebFormSeeder::class,       // web_forms + submissions

            // Contracts
            ContractSeeder::class,      // contract_templates + contracts

            // Forecasting & Commissions
            ForecastingSeeder::class,   // sales_quotas
            CommissionSeeder::class,    // commission_plans + commissions

            // Territories
            TerritorySeeder::class,     // territories + territory_user

            // Developer API
            DeveloperSeeder::class,     // developer_apps

            // Plugins (last — grants access based on plan)
            PluginSeeder::class,        // plugins + tenant_plugins
        ]);
    }
}
