<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Company;
use App\Models\Contact;
use App\Models\CrmNotification;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Wipe all tables so seeder is safely re-runnable ───────────────
        DB::statement('PRAGMA foreign_keys = OFF');
        $tables = [
            'crm_notifications','cards','card_templates','invoice_items','invoices',
            'activities','tasks','deals','leads','contacts','companies',
            'pipeline_stages','model_has_roles','model_has_permissions',
            'role_has_permissions','roles','permissions','users',
            'tenant_plugins','plugins','tenants',
        ];
        foreach ($tables as $t) { DB::table($t)->truncate(); }
        DB::statement('PRAGMA foreign_keys = ON');

        // ── Roles ─────────────────────────────────────────────────────────
        foreach (['super_admin', 'tenant_admin', 'manager', 'staff'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Platform Super-Admin Tenant ───────────────────────────────────
        $superTenant = Tenant::create([
            'name' => 'Platform Admin', 'slug' => 'platform-admin',
            'email' => 'admin@crm.io', 'plan' => 'enterprise', 'status' => 'active',
            'max_users' => 999, 'max_contacts' => 999999, 'currency' => 'USD',
        ]);
        $superAdmin = User::create([
            'name' => 'Super Admin', 'email' => 'admin@crm.io',
            'password' => Hash::make('password'), 'tenant_id' => $superTenant->id,
            'job_title' => 'Platform Administrator',
        ]);
        $superAdmin->assignRole('super_admin');

        // ── Demo Tenant: Acme Corporation ─────────────────────────────────
        $tenant = Tenant::create([
            'name' => 'Acme Corporation', 'slug' => 'acme-corp',
            'email' => 'hello@acmecorp.com', 'phone' => '+1 415-555-0100',
            'website' => 'https://acmecorp.com', 'plan' => 'pro', 'status' => 'active',
            'max_users' => 20, 'max_contacts' => 10000, 'currency' => 'USD',
            'address' => '101 Market Street, San Francisco, CA 94105',
        ]);
        $tid = $tenant->id;

        // ── Users ─────────────────────────────────────────────────────────
        $owner = User::create([
            'name' => 'John Smith', 'email' => 'demo@acme.com',
            'password' => Hash::make('password'), 'tenant_id' => $tid,
            'job_title' => 'CEO', 'phone' => '+1 415-555-0101',
        ]);
        $owner->assignRole('tenant_admin');

        $manager = User::create([
            'name' => 'Sarah Johnson', 'email' => 'manager@acme.com',
            'password' => Hash::make('password'), 'tenant_id' => $tid,
            'job_title' => 'Sales Manager', 'phone' => '+1 415-555-0102',
        ]);
        $manager->assignRole('manager');

        $staff1 = User::create([
            'name' => 'Michael Lee', 'email' => 'staff@acme.com',
            'password' => Hash::make('password'), 'tenant_id' => $tid,
            'job_title' => 'Account Executive', 'phone' => '+1 415-555-0103',
        ]);
        $staff1->assignRole('staff');

        $staff2 = User::create([
            'name' => 'Emily Chen', 'email' => 'emily@acme.com',
            'password' => Hash::make('password'), 'tenant_id' => $tid,
            'job_title' => 'Business Development Rep', 'phone' => '+1 415-555-0104',
        ]);
        $staff2->assignRole('staff');

        $users = [$owner, $manager, $staff1, $staff2];

        // ── Pipeline Stages ───────────────────────────────────────────────
        $dealStageData = [
            ['Prospecting',   '#6c757d', 1, false, false],
            ['Qualification', '#0d6efd', 2, false, false],
            ['Proposal',      '#ffc107', 3, false, false],
            ['Negotiation',   '#fd7e14', 4, false, false],
            ['Closed Won',    '#198754', 5, true,  false],
            ['Closed Lost',   '#dc3545', 6, false, true ],
        ];
        $dealStages = [];
        foreach ($dealStageData as [$name, $color, $pos, $won, $lost]) {
            $dealStages[] = PipelineStage::create([
                'tenant_id' => $tid, 'type' => 'deal',
                'name' => $name, 'color' => $color, 'position' => $pos,
                'is_won' => $won, 'is_lost' => $lost,
            ]);
        }

        $leadStageData = [
            ['New',           '#6c757d', 1],
            ['Contacted',     '#0dcaf0', 2],
            ['Qualified',     '#0d6efd', 3],
            ['Proposal Sent', '#ffc107', 4],
            ['Converted',     '#198754', 5],
            ['Lost',          '#dc3545', 6],
        ];
        $leadStages = [];
        foreach ($leadStageData as [$name, $color, $pos]) {
            $leadStages[] = PipelineStage::create([
                'tenant_id' => $tid, 'type' => 'lead',
                'name' => $name, 'color' => $color, 'position' => $pos,
            ]);
        }

        // ── Companies ─────────────────────────────────────────────────────
        $companyData = [
            ['TechNova Inc',          'Technology',   'San Francisco', 'USA',     '1000-5000',  'contact@technova.io',          'https://technova.io',          12500000],
            ['GlobalRetail Ltd',      'Retail',       'New York',      'USA',     '5000-10000', 'info@globalretail.com',         'https://globalretail.com',     45000000],
            ['Meridian Finance',      'Finance',      'London',        'UK',      '500-1000',   'hello@meridianfinance.co.uk',   'https://meridianfinance.co.uk', 8000000],
            ['SkyBridge Logistics',   'Logistics',    'Chicago',       'USA',     '1000-5000',  'ops@skybridgelog.com',          'https://skybridgelog.com',     22000000],
            ['Helix Biotech',         'Healthcare',   'Boston',        'USA',     '200-500',    'info@helixbio.com',             'https://helixbio.com',          5500000],
            ['Aurora Media Group',    'Media',        'Los Angeles',   'USA',     '500-1000',   'press@auroramedia.com',         'https://auroramedia.com',      18000000],
            ['DataStream Corp',       'Technology',   'Seattle',       'USA',     '1000-5000',  'sales@datastream.io',           'https://datastream.io',        31000000],
            ['PrimeBuild Const.',     'Construction', 'Dallas',        'USA',     '500-1000',   'contact@primebuild.com',        'https://primebuild.com',        9800000],
            ['Solara Energy',         'Energy',       'Houston',       'USA',     '200-500',    'info@solaraenergy.com',         'https://solaraenergy.com',      7200000],
            ['BlueWave Consulting',   'Consulting',   'Miami',         'USA',     '50-200',     'hello@bluewave.co',             'https://bluewave.co',           2400000],
            ['NorthStar Ventures',    'Finance',      'Toronto',       'Canada',  '50-200',     'invest@northstarvc.com',        'https://northstarvc.com',     150000000],
            ['Orion Pharmaceuticals', 'Healthcare',   'Berlin',        'Germany', '500-1000',   'info@orionpharma.de',           'https://orionpharma.de',       19000000],
        ];
        $companies = [];
        foreach ($companyData as [$name, $industry, $city, $country, $size, $email, $website, $revenue]) {
            $companies[] = Company::create([
                'tenant_id' => $tid, 'name' => $name, 'industry' => $industry,
                'city' => $city, 'country' => $country, 'size' => $size,
                'email' => $email, 'website' => $website, 'annual_revenue' => $revenue,
                'phone' => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'notes' => "Key account — {$industry} sector client with long-term potential.",
                'created_by' => $owner->id,
            ]);
        }

        // ── Contacts ──────────────────────────────────────────────────────
        $contactData = [
            ['Alice',    'Thompson',  'CTO',                      90, 'Referral',     'active'],
            ['Benjamin', 'Carter',    'VP of Sales',              78, 'Website',      'active'],
            ['Clara',    'Martinez',  'Director of Finance',      65, 'Event',        'active'],
            ['David',    'Kim',       'CEO',                      95, 'Direct',       'active'],
            ['Eleanor',  'Nguyen',    'Head of Engineering',      82, 'LinkedIn',     'active'],
            ['Franklin', 'Okonkwo',   'Procurement Manager',      55, 'Cold Email',   'active'],
            ['Grace',    'Patel',     'Marketing Director',       70, 'Webinar',      'active'],
            ['Henry',    'Watkins',   'CFO',                      88, 'Referral',     'active'],
            ['Isabella', 'Romano',    'IT Director',              60, 'Website',      'active'],
            ['James',    'Hoffmann',  'Operations Manager',       45, 'Event',        'active'],
            ['Kate',     'Sullivan',  'Business Analyst',         35, 'Website',      'active'],
            ['Liam',     'Fraser',    'Product Manager',          72, 'Referral',     'active'],
            ['Mia',      'Andersson', 'Strategy Lead',            80, 'Social Media', 'active'],
            ['Noah',     'Brennan',   'Sales Executive',          50, 'Cold Email',   'inactive'],
            ['Olivia',   'Dupont',    'Account Manager',          68, 'Direct',       'active'],
            ['Patrick',  'Yamamoto',  'Data Engineer',            40, 'Website',      'active'],
            ['Quinn',    'El-Amin',   'Partner',                  92, 'Referral',     'active'],
            ['Rachel',   'Bloom',     'Chief Revenue Officer',    85, 'Conference',   'active'],
            ['Samuel',   'Osei',      'VP Engineering',           77, 'LinkedIn',     'active'],
            ['Tara',     'Muller',    'Founder',                  93, 'Direct',       'active'],
            ['Ulric',    'Forde',     'Sales Director',           62, 'Webinar',      'active'],
            ['Vera',     'Singh',     'Chief Marketing Officer',  74, 'Referral',     'active'],
            ['William',  'Park',      'Managing Director',        88, 'Event',        'active'],
            ['Xena',     'Cohen',     'Business Dev. Manager',    57, 'Website',      'active'],
            ['Yusuf',    'Hassan',    'Regional Director',        83, 'Conference',   'active'],
        ];
        $cities = ['San Francisco','New York','Chicago','Los Angeles','Seattle','Austin','Boston','Miami','Denver','Atlanta'];
        $contacts = [];
        foreach ($contactData as $i => [$first, $last, $title, $score, $source, $status]) {
            $contacts[] = Contact::create([
                'tenant_id'   => $tid,
                'first_name'  => $first, 'last_name' => $last,
                'email'       => strtolower($first . '.' . $last . '@example.com'),
                'phone'       => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'mobile'      => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'job_title'   => $title,
                'company_id'  => $companies[$i % count($companies)]->id,
                'source'      => $source, 'status' => $status, 'lead_score' => $score,
                'country'     => 'USA', 'city' => $cities[$i % count($cities)],
                'notes'       => "Met via {$source}. Interested in enterprise plan. Follow-up scheduled.",
                'assigned_to' => $users[$i % count($users)]->id,
                'created_by'  => $owner->id,
            ]);
        }

        // ── Leads ─────────────────────────────────────────────────────────
        $leadData = [
            ['TechNova Enterprise Upgrade',      0,  0,  85000, 'new',       'Website',      70, 0],
            ['GlobalRetail Q3 CRM Rollout',      1,  1, 240000, 'contacted', 'Referral',     80, 1],
            ['Meridian Finance Data Migration',  2,  2,  55000, 'qualified', 'Event',        60, 2],
            ['SkyBridge Logistics Suite',        3,  3, 130000, 'new',       'Cold Email',   50, 0],
            ['Helix Biotech Compliance Module',  4,  4,  42000, 'contacted', 'Webinar',      65, 1],
            ['Aurora Media CRM Onboarding',      5,  5,  78000, 'qualified', 'Social Media', 75, 2],
            ['DataStream Corp API Integration',  6,  6,  95000, 'new',       'Direct',       55, 3],
            ['PrimeBuild Field CRM License',     7,  7,  38000, 'contacted', 'Website',      45, 1],
            ['Solara Energy Analytics Pack',     8,  8,  61000, 'qualified', 'LinkedIn',     70, 2],
            ['BlueWave Full-Service Setup',      9,  9,  29000, 'new',       'Referral',     40, 0],
            ['NorthStar VC Portfolio Tracker',  10, 10, 180000, 'contacted', 'Conference',   85, 3],
            ['Orion Pharma Regulatory Suite',   11, 11, 115000, 'qualified', 'Direct',       78, 4],
            ['Pacific Holdings CRM Deal',        0,  0, 320000, 'new',       'Referral',     90, 0],
            ['Vertex Cloud Migration Lead',      2,  2,  47000, 'contacted', 'Website',      52, 1],
            ['Summit Group Sales Automation',    4,  4,  88000, 'qualified', 'Event',        68, 2],
        ];
        $leads = [];
        foreach ($leadData as $i => [$title, $cIdx, $coIdx, $value, $status, $source, $score, $stageIdx]) {
            $leads[] = Lead::create([
                'tenant_id'   => $tid, 'title' => $title,
                'contact_id'  => $contacts[$cIdx]->id,
                'company_id'  => $companies[$coIdx]->id,
                'stage_id'    => $leadStages[$stageIdx]->id,
                'source'      => $source, 'status' => $status,
                'score'       => $score, 'value' => $value,
                'notes'       => "Inbound lead via {$source}. High potential — needs qualification call this week.",
                'assigned_to' => $users[$i % count($users)]->id,
                'created_by'  => $owner->id,
            ]);
        }

        // ── Deals ─────────────────────────────────────────────────────────
        $dealData = [
            ['TechNova Enterprise License 2025', 0,  0, 185000, 75, 30, 'open',  'high',   1],
            ['GlobalRetail Annual Contract',     1,  1, 420000, 60, 45, 'open',  'urgent', 2],
            ['Meridian Finance Modules',         2,  2,  62000, 40, 20, 'open',  'medium', 0],
            ['SkyBridge Route Optimizer',        3,  3, 148000, 80, 10, 'open',  'high',   3],
            ['Helix Biotech Compliance Suite',   4,  4,  38500, 90,  5, 'won',   'high',   4],
            ['Aurora Media Campaign Suite',      5,  5,  72000, 55, 60, 'open',  'medium', 1],
            ['DataStream Corp Core CRM',         6,  6, 215000, 70, 15, 'open',  'urgent', 2],
            ['PrimeBuild Mobile App License',    7,  7,  44000, 30, 90, 'lost',  'low',    5],
            ['Solara Energy Dashboard',          8,  8,  58000, 65, 25, 'open',  'medium', 0],
            ['BlueWave Starter Pack',            9,  9,  22500, 85,  7, 'won',   'low',    4],
            ['NorthStar Fund Analytics',        10, 10, 340000, 50, 55, 'open',  'urgent', 3],
            ['Orion Pharma Regulatory',         11, 11, 127000, 45, 40, 'open',  'high',   2],
            ['Pacific Holdings Enterprise',      0,  0, 580000, 35, 70, 'open',  'urgent', 1],
            ['Vertex Cloud Starter',             2,  2,  29000, 80,  3, 'won',   'low',    4],
            ['Summit Group Automation',          4,  4,  96000, 60, 20, 'open',  'medium', 0],
            ['Zenith Corp Full Suite',           6,  6, 275000, 25, 80, 'lost',  'medium', 5],
            ['Orion Phase 2 Expansion',         11, 11, 195000, 40, 35, 'open',  'high',   2],
            ['TechNova Support Extension',       0,  0,  48000, 90,  3, 'won',   'low',    4],
        ];
        $deals = [];
        foreach ($dealData as $i => [$title, $cIdx, $coIdx, $value, $prob, $days, $status, $priority, $stageIdx]) {
            $deals[] = Deal::create([
                'tenant_id'           => $tid, 'title' => $title,
                'contact_id'          => $contacts[$cIdx]->id,
                'company_id'          => $companies[$coIdx]->id,
                'stage_id'            => $dealStages[$stageIdx]->id,
                'value'               => $value, 'probability' => $prob,
                'expected_close_date' => now()->addDays($days),
                'status'              => $status, 'priority' => $priority,
                'notes'               => "Deal in progress. Next step: send proposal and schedule executive review.",
                'assigned_to'         => $users[$i % count($users)]->id,
                'created_by'          => $owner->id,
            ]);
        }

        // ── Tasks ─────────────────────────────────────────────────────────
        $taskData = [
            ['Call Alice Thompson re: Q3 proposal',    'call',    'high',   'pending',     2],
            ['Send GlobalRetail contract draft',       'email',   'urgent', 'pending',     1],
            ['CRM demo – Meridian Finance team',       'meeting', 'high',   'in_progress', 3],
            ['Follow up on DataStream API proposal',   'email',   'medium', 'pending',     5],
            ['Onboarding call – Helix Biotech',        'call',    'high',   'completed',  -3],
            ['Prepare Solara Energy quote',            'task',    'medium', 'pending',     7],
            ['Review NorthStar contract redlines',     'task',    'urgent', 'in_progress', 1],
            ['Send thank-you note to BlueWave',       'email',   'low',    'completed',  -1],
            ['Kick-off meeting – Pacific Holdings',    'meeting', 'urgent', 'pending',     4],
            ['Update pipeline for Q4 planning',       'task',    'medium', 'pending',     6],
            ['Record demo for Aurora Media',          'task',    'medium', 'in_progress', 2],
            ['Follow up: Orion Pharma decision',      'call',    'high',   'pending',     3],
            ['Prepare Summit Group ROI analysis',     'task',    'high',   'pending',     8],
            ['Send Vertex Cloud trial credentials',   'email',   'low',    'completed',  -2],
            ['Check in with SkyBridge Logistics',     'call',    'medium', 'pending',    10],
            ['Weekly sales pipeline review',          'meeting', 'high',   'in_progress', 0],
            ['Update contact records – batch import', 'task',    'low',    'pending',    14],
            ['Send proposal – PrimeBuild mobile',     'email',   'medium', 'pending',     2],
            ['Competitor analysis report',            'task',    'medium', 'completed',  -5],
            ['Board presentation – Q3 results',      'meeting', 'urgent', 'pending',    12],
        ];
        $tasks = [];
        foreach ($taskData as $i => [$title, $type, $priority, $status, $dueDays]) {
            $tasks[] = Task::create([
                'tenant_id'    => $tid, 'title' => $title,
                'description'  => "Task: {$title}. All stakeholders informed. Action items tracked.",
                'type'         => $type, 'status' => $status, 'priority' => $priority,
                'due_date'     => now()->addDays($dueDays),
                'completed_at' => $status === 'completed' ? now()->subDays(1) : null,
                'assigned_to'  => $users[$i % count($users)]->id,
                'created_by'   => $owner->id,
            ]);
        }

        // ── Invoices ──────────────────────────────────────────────────────
        $invoiceData = [
            ['INV-00001', 0,  0,  'paid',    -45, -15, -10, 500,  [['Enterprise CRM License (Annual)', 1, 18000], ['Onboarding & Setup', 1, 2500], ['Priority Support (12 months)', 1, 3600]]],
            ['INV-00002', 1,  1,  'paid',    -30,  -5,  -3, 800,  [['Pro Plan (5 seats, 1 year)', 5, 2400], ['Data Migration Service', 1, 1800], ['Custom API Integration', 1, 4500]]],
            ['INV-00003', 4,  4,  'paid',    -20,  10,  -8, 300,  [['Compliance Module License', 1, 6500], ['Training Workshop (2 days)', 2, 1200], ['Technical Support (6 months)', 1, 1800]]],
            ['INV-00004', 9,  9,  'paid',    -12,  18,  -3, 200,  [['Starter CRM Pack', 1, 5500], ['Implementation Services', 1, 1200]]],
            ['INV-00005', 13, 2,  'paid',     -8,  22,  -2, 150,  [['Vertex Cloud Starter License', 1, 9000], ['Domain Setup & Configuration', 1, 750]]],
            ['INV-00006', 2,  2,  'sent',     -5,  25, null,  0,  [['Meridian Finance Data Platform', 1, 22000], ['Custom Report Builder', 1, 3800], ['Annual Maintenance Plan', 1, 4200]]],
            ['INV-00007', 6,  6,  'sent',     -3,  27, null,  0,  [['DataStream Core CRM (15 seats)', 15, 1800], ['API Access License', 1, 5000]]],
            ['INV-00008', 3,  3,  'draft',     0,  30, null,  0,  [['SkyBridge Logistics Suite', 1, 34000], ['Field Mobile App (10 devices)', 10, 800], ['Support Package (1 year)', 1, 4800]]],
            ['INV-00009', 5,  5,  'draft',     0,  30, null,  0,  [['Aurora Media Campaign Manager', 1, 16000], ['Social Integration Plugin', 1, 2400]]],
            ['INV-00010', 10, 10, 'overdue', -60, -30, null,  0,  [['NorthStar Portfolio Analytics', 1, 48000], ['Custom Dashboard (3 screens)', 3, 4000], ['Training Retainer (quarterly)', 1, 6000]]],
            ['INV-00011', 11, 11, 'sent',    -15,  15, null,  0,  [['Orion Pharma Regulatory Suite', 1, 28500], ['Compliance Audit Module', 1, 7200]]],
            ['INV-00012', 0,  0,  'draft',     0,  45, null,  0,  [['TechNova Enterprise (Phase 2)', 1, 95000], ['Dedicated Account Manager (1yr)', 1, 24000]]],
        ];
        foreach ($invoiceData as [$num, $cIdx, $coIdx, $status, $issueDays, $dueDays, $paidDays, $discount, $items]) {
            $invoice = Invoice::create([
                'tenant_id'      => $tid, 'invoice_number' => $num,
                'contact_id'     => $contacts[$cIdx]->id,
                'company_id'     => $companies[$coIdx]->id,
                'created_by'     => $owner->id, 'status' => $status,
                'tax_rate'       => 10, 'discount' => $discount, 'currency' => 'USD',
                'issue_date'     => now()->addDays($issueDays),
                'due_date'       => now()->addDays($dueDays),
                'paid_at'        => $paidDays !== null ? now()->addDays($paidDays) : null,
                'notes'          => 'Thank you for your business. We appreciate your continued partnership.',
                'terms'          => 'Net 30. Late payments subject to 1.5% monthly interest.',
                'subtotal' => 0, 'tax_amount' => 0, 'total' => 0,
            ]);
            foreach ($items as [$desc, $qty, $price]) {
                $invoice->items()->create([
                    'description' => $desc, 'quantity' => $qty,
                    'unit_price' => $price, 'total' => $qty * $price,
                ]);
            }
            $invoice->recalculate();
        }

        // ── Activities ────────────────────────────────────────────────────
        $activitySeeds = [
            ['created', 'New contact added: Alice Thompson',          $contacts[0],  $owner,   '-3 days'],
            ['email',   'Intro email sent to Benjamin Carter',        $contacts[1],  $staff1,  '-2 days'],
            ['call',    'Discovery call with Clara Martinez',         $contacts[2],  $manager, '-5 days'],
            ['meeting', 'Product demo for David Kim',                 $contacts[3],  $owner,   '-4 days'],
            ['note',    'Contract review notes added for Eleanor',    $contacts[4],  $manager, '-1 day'],
            ['created', 'Lead created: TechNova Enterprise Upgrade',  $leads[0],     $staff2,  '-6 days'],
            ['updated', 'Lead stage updated to Contacted',            $leads[1],     $manager, '-3 days'],
            ['email',   'Proposal sent to GlobalRetail team',         $leads[1],     $staff1,  '-2 days'],
            ['call',    'Qualification call with Meridian team',      $leads[2],     $manager, '-4 days'],
            ['created', 'Deal created: Enterprise License 2025',      $deals[0],     $owner,   '-7 days'],
            ['updated', 'Deal value updated to $185,000',            $deals[0],     $manager, '-2 days'],
            ['meeting', 'Contract negotiation – GlobalRetail',        $deals[1],     $owner,   '-1 day'],
            ['email',   'Follow-up email to DataStream Corp',         $contacts[6],  $staff1,  '-3 days'],
            ['call',    'Check-in with Solara Energy contact',        $contacts[8],  $staff2,  '-5 days'],
            ['note',    'Meeting notes added for NorthStar deal',     $deals[10],    $manager, '-1 day'],
            ['created', 'New deal: Pacific Holdings Enterprise',      $deals[12],    $owner,   '-2 days'],
            ['email',   'Thank-you email – BlueWave deal closed',    $deals[9],     $staff1,  '-6 days'],
            ['updated', 'Orion Pharma deal probability updated',      $deals[11],    $manager, '-1 day'],
            ['call',    'Intro call – Tara Muller (Solara Energy)',   $contacts[19], $staff2,  '-8 days'],
            ['meeting', 'QBR with Henry Watkins (Meridian)',          $contacts[7],  $owner,   '-10 days'],
        ];
        foreach ($activitySeeds as [$type, $subject, $model, $user, $ago]) {
            $activity = Activity::create([
                'tenant_id'         => $tid, 'user_id' => $user->id,
                'type'              => $type, 'subject' => $subject,
                'description'       => "Action: {$subject}. Logged by {$user->name}.",
                'activityable_id'   => $model->id,
                'activityable_type' => get_class($model),
            ]);
            $activity->created_at = now()->modify($ago);
            $activity->saveQuietly();
        }

        // ── Card Templates ────────────────────────────────────────────────
        $templates = [];
        $templates[] = CardTemplate::create([
            'tenant_id' => $tid, 'name' => 'Executive Blue', 'category' => 'professional',
            'design' => ['bg_color' => '#0f172a', 'text_color' => '#ffffff', 'accent_color' => '#3b82f6', 'layout' => 'horizontal'],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'website', 'linkedin'],
            'created_by' => $owner->id,
        ]);
        $templates[] = CardTemplate::create([
            'tenant_id' => $tid, 'name' => 'Modern Minimal', 'category' => 'minimal',
            'design' => ['bg_color' => '#ffffff', 'text_color' => '#1a202c', 'accent_color' => '#10b981', 'layout' => 'vertical'],
            'fields' => ['name', 'title', 'company', 'email', 'phone'],
            'created_by' => $owner->id,
        ]);
        $templates[] = CardTemplate::create([
            'tenant_id' => $tid, 'name' => 'Bold Creative', 'category' => 'creative',
            'design' => ['bg_color' => '#7c3aed', 'text_color' => '#ffffff', 'accent_color' => '#fbbf24', 'layout' => 'horizontal'],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'twitter'],
            'created_by' => $manager->id,
        ]);
        $templates[] = CardTemplate::create([
            'tenant_id' => $tid, 'name' => 'Corporate Slate', 'category' => 'professional',
            'design' => ['bg_color' => '#1e293b', 'text_color' => '#e2e8f0', 'accent_color' => '#f59e0b', 'layout' => 'horizontal'],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'address'],
            'created_by' => $owner->id,
        ]);

        // ── Cards ─────────────────────────────────────────────────────────
        $cardSeeds = [
            [$contacts[0],  $templates[0], 'Alice Thompson',   'CTO',                   'TechNova Inc',       'alice.thompson@example.com',  '+1 555-210-4321', 'technova.io',     null,              'linkedin.com/in/alicethompson', null],
            [$contacts[3],  $templates[1], 'David Kim',        'CEO',                   'GlobalRetail Ltd',   'david.kim@example.com',       '+1 555-311-6789', 'globalretail.com',null,              null,                            null],
            [$contacts[6],  $templates[2], 'Grace Patel',      'Marketing Director',    'Aurora Media Group', 'grace.patel@example.com',     '+1 555-412-5555', null,              null,              null,                            '@gracepatel'],
            [$contacts[7],  $templates[3], 'Henry Watkins',    'CFO',                   'Meridian Finance',   'henry.watkins@example.com',   '+1 555-513-8888', null,              '10 Finance Sq, London', null,               null],
            [$contacts[17], $templates[0], 'Rachel Bloom',     'Chief Revenue Officer', 'DataStream Corp',    'rachel.bloom@example.com',    '+1 555-614-7777', 'datastream.io',   null,              'linkedin.com/in/rachelbloom',   null],
            [$contacts[19], $templates[1], 'Tara Muller',      'Founder',               'Solara Energy',      'tara.muller@example.com',     '+1 555-715-3333', 'solaraenergy.com',null,              null,                            null],
            [$contacts[22], $templates[3], 'William Park',     'Managing Director',     'NorthStar Ventures', 'william.park@example.com',    '+1 555-816-2222', 'northstarvc.com', '1 Bay St, Toronto',null,                           null],
        ];
        foreach ($cardSeeds as [$contact, $template, $name, $title, $company, $email, $phone, $website, $address, $linkedin, $twitter]) {
            $data = array_filter(compact('name','title','company','email','phone','website','address','linkedin','twitter'));
            Card::create([
                'tenant_id' => $tid, 'template_id' => $template->id,
                'contact_id' => $contact->id, 'name' => "{$name} — {$template->name}",
                'data' => $data, 'created_by' => $owner->id,
            ]);
        }

        // ── CRM Notifications ─────────────────────────────────────────────
        $notifData = [
            [$owner,   'info',    'New Lead Assigned',       'TechNova Enterprise Upgrade has been assigned to you.',           'funnel-fill',             'primary', '/leads',    '-2 hours'],
            [$owner,   'success', 'Deal Won!',               'BlueWave Starter Pack marked as Won — $22,500.',                 'trophy-fill',             'success', '/deals',    '-5 hours'],
            [$owner,   'warning', 'Task Overdue',            '"Send GlobalRetail contract draft" is past its due date.',        'exclamation-triangle-fill','warning', '/tasks',    '-1 day'],
            [$owner,   'success', 'Invoice Paid',            'INV-00001 has been paid — $24,100 received.',                    'receipt',                 'success', '/invoices', '-3 hours'],
            [$owner,   'info',    'New Contact Added',       'Alice Thompson was added to your contacts.',                     'person-plus-fill',        'primary', '/contacts', '-1 day'],
            [$manager, 'info',    'Deal Stage Updated',      'GlobalRetail Annual Contract moved to Proposal stage.',          'arrow-right-circle-fill', 'info',    '/deals',    '-4 hours'],
            [$manager, 'success', 'Deal Won!',               'Vertex Cloud Starter closed successfully. Great work!',          'trophy-fill',             'success', '/deals',    '-2 days'],
            [$manager, 'warning', 'Lead Score Alert',        'SkyBridge Logistics lead score has dropped below 50.',           'graph-down-arrow',        'warning', '/leads',    '-6 hours'],
            [$manager, 'info',    'New Task Assigned',       '"CRM demo – Meridian Finance" has been assigned to you.',        'check2-square',           'primary', '/tasks',    '-3 hours'],
            [$staff1,  'success', 'Invoice Sent',            'INV-00002 sent to GlobalRetail Ltd successfully.',               'envelope-check-fill',     'success', '/invoices', '-1 day'],
            [$staff1,  'info',    'New Deal Created',        'Pacific Holdings Enterprise deal has been opened.',              'briefcase-fill',          'primary', '/deals',    '-2 hours'],
            [$staff1,  'warning', 'Follow-up Reminder',     'You have 3 overdue follow-up tasks this week.',                  'bell-fill',               'warning', '/tasks',    '-8 hours'],
            [$staff2,  'info',    'Contact Updated',         'Benjamin Carter\'s profile was updated.',                       'person-fill',             'info',    '/contacts', '-5 hours'],
            [$staff2,  'success', 'Lead Converted',          'Helix Biotech Compliance lead converted to a Deal.',            'funnel-fill',             'success', '/leads',    '-1 day'],
        ];
        foreach ($notifData as $i => [$user, $type, $title, $body, $icon, $color, $url, $ago]) {
            $notif = CrmNotification::create([
                'tenant_id' => $tid, 'user_id' => $user->id, 'type' => $type,
                'title' => $title, 'body' => $body, 'icon' => $icon, 'color' => $color, 'url' => $url,
                'read_at' => $i < 5 ? now()->subHours(rand(1, 24)) : null,
            ]);
            $notif->created_at = now()->modify($ago);
            $notif->saveQuietly();
        }

        // ── Plugins ───────────────────────────────────────────────────────
        $this->call(PluginSeeder::class);
    }
}
