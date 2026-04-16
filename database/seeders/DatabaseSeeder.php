<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['super_admin', 'tenant_admin', 'manager', 'staff'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create super admin tenant
        $superTenant = Tenant::create([
            'name' => 'Platform Admin',
            'slug' => 'platform-admin',
            'email' => 'admin@crm.io',
            'plan' => 'enterprise',
            'status' => 'active',
            'max_users' => 999,
            'max_contacts' => 999999,
        ]);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@crm.io',
            'password' => Hash::make('password'),
            'tenant_id' => $superTenant->id,
        ]);
        $superAdmin->assignRole('super_admin');

        // Create demo tenant
        $tenant = Tenant::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'email' => 'demo@acme.com',
            'plan' => 'pro',
            'status' => 'active',
            'max_users' => 10,
            'max_contacts' => 5000,
        ]);

        $adminUser = User::create([
            'name' => 'John Smith',
            'email' => 'demo@acme.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'job_title' => 'CEO',
        ]);
        $adminUser->assignRole('tenant_admin');

        $managerUser = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'manager@acme.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
            'job_title' => 'Sales Manager',
        ]);
        $managerUser->assignRole('manager');

        // Deal pipeline stages
        $dealStages = [
            ['name' => 'Prospecting', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Qualification', 'color' => '#0d6efd', 'position' => 2],
            ['name' => 'Proposal', 'color' => '#ffc107', 'position' => 3],
            ['name' => 'Negotiation', 'color' => '#fd7e14', 'position' => 4],
            ['name' => 'Closed Won', 'color' => '#198754', 'position' => 5, 'is_won' => true],
            ['name' => 'Closed Lost', 'color' => '#dc3545', 'position' => 6, 'is_lost' => true],
        ];
        foreach ($dealStages as $s) {
            PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'deal', ...$s]);
        }

        // Lead pipeline stages
        $leadStages = [
            ['name' => 'New', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Contacted', 'color' => '#0dcaf0', 'position' => 2],
            ['name' => 'Qualified', 'color' => '#198754', 'position' => 3],
        ];
        foreach ($leadStages as $s) {
            PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'lead', ...$s]);
        }

        $dealStageIds = PipelineStage::where('tenant_id', $tenant->id)->where('type', 'deal')->pluck('id')->toArray();
        $leadStageIds = PipelineStage::where('tenant_id', $tenant->id)->where('type', 'lead')->pluck('id')->toArray();

        // Companies
        $companies = [];
        $companyData = [
            ['Google LLC', 'Technology', 'Mountain View', 'USA', '10000+'],
            ['Microsoft Corp', 'Technology', 'Redmond', 'USA', '10000+'],
            ['Stripe Inc', 'Finance', 'San Francisco', 'USA', '1000-5000'],
            ['Shopify', 'Technology', 'Ottawa', 'Canada', '5000-10000'],
            ['HubSpot', 'Technology', 'Cambridge', 'USA', '1000-5000'],
        ];
        foreach ($companyData as [$name, $industry, $city, $country, $size]) {
            $companies[] = Company::create([
                'tenant_id' => $tenant->id,
                'name' => $name,
                'industry' => $industry,
                'city' => $city,
                'country' => $country,
                'size' => $size,
                'email' => strtolower(str_replace([' ', '.'], '', $name)) . '@company.com',
                'annual_revenue' => rand(1000000, 50000000),
                'created_by' => $adminUser->id,
            ]);
        }

        // Contacts
        $contactNames = [
            ['Alice', 'Johnson', 'CTO', 85], ['Bob', 'Williams', 'VP Sales', 72],
            ['Carol', 'Davis', 'Director', 60], ['David', 'Brown', 'CEO', 95],
            ['Emma', 'Wilson', 'Manager', 45], ['Frank', 'Martinez', 'Engineer', 30],
            ['Grace', 'Anderson', 'Designer', 55], ['Henry', 'Taylor', 'Analyst', 40],
            ['Isabella', 'Thomas', 'Consultant', 70], ['James', 'Jackson', 'Advisor', 88],
        ];
        $sources = ['Website', 'Referral', 'Social Media', 'Direct', 'Event'];
        $contacts = [];
        foreach ($contactNames as $i => [$first, $last, $title, $score]) {
            $contacts[] = Contact::create([
                'tenant_id' => $tenant->id,
                'first_name' => $first,
                'last_name' => $last,
                'email' => strtolower($first . '.' . $last . '@example.com'),
                'phone' => '+1 555-' . rand(100, 999) . '-' . rand(1000, 9999),
                'job_title' => $title,
                'company_id' => $companies[array_rand($companies)]->id,
                'source' => $sources[array_rand($sources)],
                'status' => 'active',
                'lead_score' => $score,
                'country' => 'USA',
                'city' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'][array_rand(['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'])],
                'created_by' => $adminUser->id,
            ]);
        }

        // Leads
        $leadTitles = [
            'Website Contact Form', 'LinkedIn Outreach', 'Conference Referral',
            'Cold Email Response', 'Webinar Registration', 'Free Trial Signup',
        ];
        foreach ($leadTitles as $i => $title) {
            Lead::create([
                'tenant_id' => $tenant->id,
                'title' => $title,
                'contact_id' => $contacts[array_rand($contacts)]->id,
                'company_id' => $companies[array_rand($companies)]->id,
                'stage_id' => $leadStageIds[array_rand($leadStageIds)],
                'source' => $sources[array_rand($sources)],
                'status' => ['new', 'contacted', 'qualified'][array_rand(['new', 'contacted', 'qualified'])],
                'score' => rand(20, 90),
                'value' => rand(5000, 100000),
                'assigned_to' => $managerUser->id,
                'created_by' => $adminUser->id,
            ]);
        }

        // Deals
        $dealTitles = [
            'Enterprise License 2025', 'Pro Plan Upgrade', 'Annual Support Contract',
            'Custom Integration Project', 'Consulting Retainer', 'SaaS Subscription',
        ];
        foreach ($dealTitles as $i => $title) {
            Deal::create([
                'tenant_id' => $tenant->id,
                'title' => $title,
                'contact_id' => $contacts[array_rand($contacts)]->id,
                'company_id' => $companies[array_rand($companies)]->id,
                'stage_id' => $dealStageIds[array_rand($dealStageIds)],
                'value' => rand(10000, 500000),
                'probability' => rand(20, 90),
                'expected_close_date' => now()->addDays(rand(7, 90)),
                'status' => ['open', 'open', 'open', 'won', 'lost'][array_rand(['open', 'open', 'open', 'won', 'lost'])],
                'priority' => ['low', 'medium', 'high', 'urgent'][array_rand(['low', 'medium', 'high', 'urgent'])],
                'assigned_to' => $managerUser->id,
                'created_by' => $adminUser->id,
            ]);
        }

        // Tasks
        $taskTitles = [
            'Follow up with Alice', 'Send proposal to Bob', 'Schedule demo call',
            'Review contract terms', 'Update CRM records', 'Team standup meeting',
        ];
        foreach ($taskTitles as $i => $title) {
            Task::create([
                'tenant_id' => $tenant->id,
                'title' => $title,
                'type' => ['task', 'call', 'email', 'meeting'][array_rand(['task', 'call', 'email', 'meeting'])],
                'status' => ['pending', 'in_progress', 'completed'][array_rand(['pending', 'in_progress', 'completed'])],
                'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                'due_date' => now()->addDays(rand(-5, 30)),
                'assigned_to' => [$adminUser->id, $managerUser->id][array_rand([$adminUser->id, $managerUser->id])],
                'created_by' => $adminUser->id,
            ]);
        }

        // Invoices
        for ($i = 1; $i <= 5; $i++) {
            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'invoice_number' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'contact_id' => $contacts[array_rand($contacts)]->id,
                'company_id' => $companies[array_rand($companies)]->id,
                'created_by' => $adminUser->id,
                'status' => ['paid', 'sent', 'draft', 'paid', 'paid'][$i - 1],
                'tax_rate' => 10,
                'discount' => rand(0, 500),
                'currency' => 'USD',
                'issue_date' => now()->subDays(rand(1, 60)),
                'due_date' => now()->addDays(rand(7, 30)),
                'paid_at' => $i <= 3 ? now()->subDays(rand(1, 15)) : null,
                'notes' => 'Thank you for your business.',
                'terms' => 'Net 30',
                'subtotal' => 0, 'tax_amount' => 0, 'total' => 0,
            ]);

            $items = [
                ['CRM Software License', rand(1, 3), rand(1000, 5000)],
                ['Implementation Services', rand(1, 5), rand(500, 2000)],
            ];
            foreach ($items as [$desc, $qty, $price]) {
                $invoice->items()->create([
                    'description' => $desc,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $qty * $price,
                ]);
            }
            $invoice->recalculate();
        }
    }
}
