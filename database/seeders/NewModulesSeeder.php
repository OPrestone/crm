<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\CommissionPlan;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Deal;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignContact;
use App\Models\SalesQuota;
use App\Models\Territory;
use App\Models\User;
use App\Models\WebForm;
use App\Models\WebFormSubmission;
use Illuminate\Database\Seeder;

class NewModulesSeeder extends Seeder
{
    public function run(): void
    {
        $user     = User::where('email', 'demo@acme.com')->first();
        $tid      = $user->tenant_id;
        $contacts = Contact::where('tenant_id', $tid)->get();
        $deals    = Deal::where('tenant_id', $tid)->get();

        // ── Email Campaigns ────────────────────────────────────────────────
        $c1 = EmailCampaign::create([
            'tenant_id'  => $tid,
            'name'       => 'Spring 2026 Promotion',
            'subject'    => 'Exclusive Spring Deals — 30% Off This Week Only',
            'from_name'  => 'Acme Corporation',
            'from_email' => 'hello@acmecorp.io',
            'body'       => "Hi {{first_name}},\n\nSpring is here and we're celebrating with our biggest promotion!\n\nFor a limited time, enjoy 30% off all Pro and Enterprise plans.\n\nWhat's included:\n• Unlimited contacts & companies\n• Full API access\n• Dedicated onboarding support\n• Priority SLA\n\nOffer expires April 30, 2026.\n\nBest,\nThe Acme Team",
            'segment'    => 'all',
            'status'     => 'sent',
            'sent_at'    => now()->subDays(3),
            'created_by' => $user->id,
        ]);
        foreach ($contacts->take(18) as $i => $c) {
            EmailCampaignContact::create([
                'campaign_id' => $c1->id,
                'contact_id'  => $c->id,
                'sent_at'     => now()->subDays(3),
                'opened_at'   => $i < 9  ? now()->subDays(3)->addHours(rand(1, 8)) : null,
                'clicked_at'  => $i < 4  ? now()->subDays(3)->addHours(rand(1, 4)) : null,
            ]);
        }

        $c2 = EmailCampaign::create([
            'tenant_id'  => $tid,
            'name'       => 'Product Update — Q2 2026',
            'subject'    => "What's New in Acme CRM — Q2 2026 Release Notes",
            'from_name'  => 'Acme Product Team',
            'from_email' => 'product@acmecorp.io',
            'body'       => "Hi {{first_name}},\n\nWe've shipped major updates this quarter!\n\n• Email Campaigns\n• Web Forms\n• Contract Management\n• Sales Forecasting\n\nLog in to explore these features.\n\nHappy selling,\nThe Acme Product Team",
            'segment'    => 'all',
            'status'     => 'sent',
            'sent_at'    => now()->subDays(10),
            'created_by' => $user->id,
        ]);
        foreach ($contacts->take(22) as $i => $c) {
            EmailCampaignContact::create([
                'campaign_id' => $c2->id,
                'contact_id'  => $c->id,
                'sent_at'     => now()->subDays(10),
                'opened_at'   => $i < 14 ? now()->subDays(10)->addHours(rand(2, 12)) : null,
                'clicked_at'  => $i < 7  ? now()->subDays(10)->addHours(rand(1, 6))  : null,
            ]);
        }

        EmailCampaign::create([
            'tenant_id'  => $tid,
            'name'       => 'Re-engagement Campaign',
            'subject'    => "We miss you! Here's what's changed since your last visit",
            'from_name'  => 'Acme Corporation',
            'from_email' => 'hello@acmecorp.io',
            'body'       => "Hi {{first_name}},\n\nWe noticed you haven't logged in recently. A lot has changed!\n\nCome back and explore.\n\nBest,\nAcme Team",
            'segment'    => 'all',
            'status'     => 'draft',
            'created_by' => $user->id,
        ]);

        // ── Web Forms ──────────────────────────────────────────────────────
        $f1 = WebForm::create([
            'tenant_id'       => $tid,
            'name'            => 'Contact Us',
            'description'     => 'General enquiry form for the website homepage.',
            'fields'          => [
                ['label' => 'First Name', 'name' => 'first_name', 'type' => 'text',     'required' => true],
                ['label' => 'Last Name',  'name' => 'last_name',  'type' => 'text',     'required' => true],
                ['label' => 'Email',      'name' => 'email',      'type' => 'email',    'required' => true],
                ['label' => 'Phone',      'name' => 'phone',      'type' => 'text',     'required' => false],
                ['label' => 'Message',    'name' => 'message',    'type' => 'textarea', 'required' => false],
            ],
            'submit_action'   => 'contact',
            'success_message' => "Thanks for reaching out! We'll get back to you within one business day.",
            'is_active'       => true,
            'created_by'      => $user->id,
        ]);

        WebForm::create([
            'tenant_id'       => $tid,
            'name'            => 'Demo Request',
            'description'     => 'Captures demo requests from the pricing page.',
            'fields'          => [
                ['label' => 'Full Name',  'name' => 'first_name', 'type' => 'text',     'required' => true],
                ['label' => 'Work Email', 'name' => 'email',      'type' => 'email',    'required' => true],
                ['label' => 'Company',    'name' => 'company',    'type' => 'text',     'required' => true],
                ['label' => 'Team Size',  'name' => 'team_size',  'type' => 'text',     'required' => false],
                ['label' => 'Message',    'name' => 'message',    'type' => 'textarea', 'required' => false],
            ],
            'submit_action'   => 'both',
            'success_message' => "Thanks! Our team will reach out within 24 hours to schedule your demo.",
            'is_active'       => true,
            'created_by'      => $user->id,
        ]);

        foreach ([
            ['Sarah', 'Lee',   'sarah.lee@fintech.io',     '+1 555-001-1111', 'Looking forward to working with you!'],
            ['James', 'Wright','james.w@retailco.com',     '+1 555-002-2222', 'Quick question about enterprise pricing.'],
            ['Lisa',  'Chen',  'lisa.chen@cloudwave.io',   '+1 555-003-3333', 'Please contact me about the Pro plan.'],
        ] as [$fn, $ln, $em, $ph, $msg]) {
            WebFormSubmission::create([
                'form_id'    => $f1->id,
                'tenant_id'  => $tid,
                'data'       => ['first_name' => $fn, 'last_name' => $ln, 'email' => $em, 'phone' => $ph, 'message' => $msg],
                'ip_address' => '203.0.113.' . rand(1, 254),
                'processed'  => true,
            ]);
        }

        // ── Contracts ──────────────────────────────────────────────────────
        $tpl = ContractTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Standard Software Subscription Agreement',
            'content'    => "SOFTWARE SUBSCRIPTION AGREEMENT\n\nThis Agreement is entered into as of {{date}} between {{tenant_name}} (\"Vendor\") and {{company_name}} (\"Customer\").\n\n1. SERVICES\nVendor agrees to provide Customer access to the CRM platform as described in the attached Order Form.\n\n2. TERM\nThis Agreement commences on the Effective Date and continues for twelve (12) months unless terminated earlier.\n\n3. FEES\nCustomer agrees to pay {{deal_value}} per annum, invoiced annually in advance.\n\n4. DATA PROTECTION\nVendor will process Customer data in accordance with applicable data protection legislation, including GDPR.\n\n5. GOVERNING LAW\nThis Agreement shall be governed by the laws of the jurisdiction in which Vendor is incorporated.\n\nIN WITNESS WHEREOF, the parties have executed this Agreement.\n\nVendor: _______________________    Date: ____________\n\nCustomer: _____________________    Date: ____________",
            'created_by' => $user->id,
        ]);

        $contact1 = $contacts->first();
        $contact2 = $contacts->skip(3)->first();
        $wonDeal  = $deals->where('status', 'won')->first();

        Contract::create([
            'tenant_id'   => $tid,
            'title'       => 'Annual CRM Subscription — TechNova Inc',
            'contact_id'  => $contact1?->id,
            'deal_id'     => $wonDeal?->id,
            'template_id' => $tpl->id,
            'content'     => $tpl->content,
            'value'       => 24000,
            'status'      => 'signed',
            'start_date'  => now()->subMonths(6),
            'end_date'    => now()->addMonths(6),
            'signed_at'   => now()->subMonths(6),
            'signed_by'   => $contact1?->full_name ?? 'Alice Thompson',
            'created_by'  => $user->id,
        ]);

        Contract::create([
            'tenant_id'   => $tid,
            'title'       => 'Enterprise Platform Agreement — GlobalRetail Ltd',
            'contact_id'  => $contact2?->id,
            'template_id' => $tpl->id,
            'content'     => $tpl->content,
            'value'       => 48000,
            'status'      => 'pending_signature',
            'start_date'  => now(),
            'end_date'    => now()->addYear(),
            'created_by'  => $user->id,
        ]);

        Contract::create([
            'tenant_id'  => $tid,
            'title'      => 'Starter Plan Agreement — Aurora Media Group',
            'content'    => "Service agreement for Starter plan subscription. Terms and conditions apply.",
            'value'      => 3600,
            'status'     => 'draft',
            'created_by' => $user->id,
        ]);

        // ── Sales Quotas ───────────────────────────────────────────────────
        $period = now()->format('Y-m');
        SalesQuota::updateOrCreate(
            ['tenant_id' => $tid, 'user_id' => null,      'period' => $period],
            ['amount' => 150000]
        );
        SalesQuota::updateOrCreate(
            ['tenant_id' => $tid, 'user_id' => $user->id, 'period' => $period],
            ['amount' => 80000]
        );
        for ($i = 1; $i <= 5; $i++) {
            $p = now()->subMonths($i)->format('Y-m');
            SalesQuota::updateOrCreate(
                ['tenant_id' => $tid, 'user_id' => null, 'period' => $p],
                ['amount' => 120000]
            );
        }

        // ── Commission Plans & Commissions ─────────────────────────────────
        $plan1 = CommissionPlan::create(['tenant_id' => $tid, 'name' => 'Standard 10%',  'type' => 'percentage', 'rate' => 10,  'min_deal_value' => 0,     'is_active' => true, 'created_by' => $user->id]);
        $plan2 = CommissionPlan::create(['tenant_id' => $tid, 'name' => 'Enterprise 8%', 'type' => 'percentage', 'rate' => 8,   'min_deal_value' => 10000, 'is_active' => true, 'created_by' => $user->id]);
        CommissionPlan::create(          ['tenant_id' => $tid, 'name' => 'Flat $500',     'type' => 'flat',       'rate' => 500, 'min_deal_value' => 0,     'is_active' => true, 'created_by' => $user->id]);

        $wonDeals = Deal::where('tenant_id', $tid)->where('status', 'won')->take(5)->get();
        foreach ($wonDeals as $i => $deal) {
            $plan = $i % 2 === 0 ? $plan1 : $plan2;
            Commission::create([
                'tenant_id'  => $tid,
                'user_id'    => $user->id,
                'deal_id'    => $deal->id,
                'plan_id'    => $plan->id,
                'deal_value' => $deal->value,
                'amount'     => $plan->calculate((float) $deal->value),
                'status'     => $i < 2 ? 'paid' : ($i < 4 ? 'approved' : 'pending'),
                'paid_at'    => $i < 2 ? now()->subWeeks(2) : null,
            ]);
        }

        // ── Territories ────────────────────────────────────────────────────
        $t1 = Territory::create(['tenant_id' => $tid, 'name' => 'North America',       'description' => 'US and Canada accounts',                              'type' => 'geographic', 'color' => '#4361ee', 'created_by' => $user->id]);
        $t2 = Territory::create(['tenant_id' => $tid, 'name' => 'Enterprise Accounts', 'description' => 'Companies with 200+ employees and deals over $50k',   'type' => 'account',    'color' => '#f72585', 'created_by' => $user->id]);
        $t3 = Territory::create(['tenant_id' => $tid, 'name' => 'FinTech & Finance',   'description' => 'All financial services and fintech companies',         'type' => 'industry',   'color' => '#2ec4b6', 'created_by' => $user->id]);

        $t1->users()->sync([$user->id]);
        $t2->users()->sync([$user->id]);

        $this->command->info('New modules demo data seeded successfully!');
    }
}
