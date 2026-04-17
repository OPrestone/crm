<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();

        // [number, cIdx, coIdx, status, issueDays, dueDays, paidDays, discount, items[]]
        $data = [
            ['INV-00001', 0,  0,  'paid',    -45, -15, -10, 500,  [
                ['Enterprise CRM License (Annual)', 1, 18000],
                ['Onboarding & Setup',              1,  2500],
                ['Priority Support (12 months)',    1,  3600],
            ]],
            ['INV-00002', 1,  1,  'paid',    -30,  -5,  -3, 800,  [
                ['Pro Plan (5 seats, 1 year)',   5, 2400],
                ['Data Migration Service',       1, 1800],
                ['Custom API Integration',       1, 4500],
            ]],
            ['INV-00003', 4,  4,  'paid',    -20,  10,  -8, 300,  [
                ['Compliance Module License',    1, 6500],
                ['Training Workshop (2 days)',   2, 1200],
                ['Technical Support (6 months)',1,  1800],
            ]],
            ['INV-00004', 9,  9,  'paid',    -12,  18,  -3, 200,  [
                ['Starter CRM Pack',            1, 5500],
                ['Implementation Services',     1, 1200],
            ]],
            ['INV-00005', 13, 2,  'paid',     -8,  22,  -2, 150,  [
                ['Vertex Cloud Starter License',1, 9000],
                ['Domain Setup & Configuration',1,  750],
            ]],
            ['INV-00006', 2,  2,  'sent',     -5,  25, null, 0,   [
                ['Meridian Finance Data Platform',  1, 22000],
                ['Custom Report Builder',           1,  3800],
                ['Annual Maintenance Plan',         1,  4200],
            ]],
            ['INV-00007', 6,  6,  'sent',     -3,  27, null, 0,   [
                ['DataStream Core CRM (15 seats)', 15, 1800],
                ['API Access License',              1, 5000],
            ]],
            ['INV-00008', 3,  3,  'draft',     0,  30, null, 0,   [
                ['SkyBridge Logistics Suite',         1, 34000],
                ['Field Mobile App (10 devices)',    10,   800],
                ['Support Package (1 year)',          1,  4800],
            ]],
            ['INV-00009', 5,  5,  'draft',     0,  30, null, 0,   [
                ['Aurora Media Campaign Manager',1, 16000],
                ['Social Integration Plugin',   1,  2400],
            ]],
            ['INV-00010', 10, 10, 'overdue', -60, -30, null, 0,   [
                ['NorthStar Portfolio Analytics',     1, 48000],
                ['Custom Dashboard (3 screens)',      3,  4000],
                ['Training Retainer (quarterly)',     1,  6000],
            ]],
            ['INV-00011', 11, 11, 'sent',    -15,  15, null, 0,   [
                ['Orion Pharma Regulatory Suite',1, 28500],
                ['Compliance Audit Module',      1,  7200],
            ]],
            ['INV-00012', 0,  0,  'draft',     0,  45, null, 0,   [
                ['TechNova Enterprise (Phase 2)',          1, 95000],
                ['Dedicated Account Manager (1yr)',        1, 24000],
            ]],
        ];

        foreach ($data as [$num, $cIdx, $coIdx, $status, $issueDays, $dueDays, $paidDays, $discount, $items]) {
            $invoice = Invoice::create([
                'tenant_id'      => $tid,
                'invoice_number' => $num,
                'contact_id'     => $contacts[$cIdx]->id,
                'company_id'     => $companies[$coIdx]->id,
                'created_by'     => $owner->id,
                'status'         => $status,
                'tax_rate'       => 10,
                'discount'       => $discount,
                'currency'       => 'USD',
                'issue_date'     => now()->addDays($issueDays),
                'due_date'       => now()->addDays($dueDays),
                'paid_at'        => $paidDays !== null ? now()->addDays($paidDays) : null,
                'notes'          => 'Thank you for your business. We appreciate your continued partnership.',
                'terms'          => 'Net 30. Late payments subject to 1.5% monthly interest.',
                'subtotal'       => 0,
                'tax_amount'     => 0,
                'total'          => 0,
            ]);
            foreach ($items as [$desc, $qty, $price]) {
                $invoice->items()->create([
                    'description' => $desc,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'total'       => $qty * $price,
                ]);
            }
            $invoice->recalculate();
        }
    }
}
