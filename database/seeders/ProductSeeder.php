<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tid   = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner = User::where('email', 'demo@prestech.com')->first();

        // [name, sku, category, unit, unitPrice, costPrice, taxRate, description]
        $data = [
            ['CRM Starter Plan',          'CRM-STARTER',   'Subscription',  'seat/mo',  49,    20,  0,   'Access to core CRM features for small teams. Includes contacts, deals, tasks, and basic reporting.'],
            ['CRM Pro Plan',              'CRM-PRO',       'Subscription',  'seat/mo',  99,    40,  0,   'Full-featured CRM with deals, invoicing, reports, email campaigns, and API access.'],
            ['CRM Enterprise Plan',       'CRM-ENT',       'Subscription',  'seat/mo',  199,   80,  0,   'Enterprise-grade CRM with unlimited contacts, custom domains, audit logs, and SLA.'],
            ['Onboarding & Setup',        'SVC-ONBOARD',   'Service',       'session',  2500,  500, 10,  'Dedicated onboarding session: data migration, pipeline setup, user training (up to 8 seats).'],
            ['Custom API Integration',    'SVC-API',       'Service',       'project',  4500,  900, 10,  'Custom REST API integration with your existing tech stack. Includes documentation and testing.'],
            ['Data Migration Service',    'SVC-MIGRATE',   'Service',       'project',  1800,  360, 10,  'Full data migration from your current CRM, spreadsheets, or database. Up to 50,000 records.'],
            ['Priority Support (12mo)',   'SUP-PRIORITY',  'Support',       'year',     3600,  720, 0,   '12-month priority support package. Response SLA: 2 hours. Dedicated account manager.'],
            ['Training Workshop (1 day)', 'TRN-1DAY',      'Training',      'session',  1200,  240, 10,  'Full-day on-site or virtual training workshop for up to 20 participants.'],
            ['Training Workshop (2 days)','TRN-2DAY',      'Training',      'session',  2200,  440, 10,  'Two-day intensive CRM training program covering all modules and best practices.'],
            ['Custom Report Builder',     'ADD-REPORTS',   'Add-on',        'license',  3800,  760, 0,   'Advanced custom report builder add-on. Build unlimited dashboards and scheduled email exports.'],
            ['Social Integration Plugin', 'ADD-SOCIAL',    'Add-on',        'license',  2400,  480, 0,   'Connect LinkedIn, Twitter/X and Instagram for social selling directly from CRM contacts.'],
            ['Dedicated Account Manager', 'SVC-AM',        'Service',       'year',    24000, 4800, 0,   'Full-year dedicated account manager: monthly reviews, proactive health checks, and strategic guidance.'],
            ['Compliance Audit Module',   'ADD-AUDIT',     'Add-on',        'license',  7200, 1440, 0,   'GDPR/SOC 2 compliance module with tamper-proof audit log, data export, and right-to-be-forgotten tools.'],
            ['Field Mobile App License',  'ADD-MOBILE',    'Add-on',        'device/mo', 80,   16,  0,   'Mobile CRM app license for field sales teams. iOS and Android. Offline-capable.'],
            ['Domain & Brand Setup',      'SVC-DOMAIN',    'Service',       'project',  750,   150, 10,  'Custom subdomain or white-label domain setup with SSL certificate and brand configuration.'],
        ];

        foreach ($data as [$name, $sku, $category, $unit, $price, $cost, $tax, $desc]) {
            Product::create([
                'tenant_id'  => $tid,
                'name'       => $name,
                'sku'        => $sku,
                'category'   => $category,
                'unit'       => $unit,
                'unit_price' => $price,
                'cost_price' => $cost,
                'tax_rate'   => $tax,
                'description'=> $desc,
                'is_active'  => true,
                'created_by' => $owner->id,
            ]);
        }
    }
}
