<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();
        $deals    = Deal::where('tenant_id', $tid)->get();
        $products = Product::where('tenant_id', $tid)->get()->keyBy('sku');

        // Helper: index or null
        $p = fn(string $sku) => $products->get($sku);

        // [number, title, cIdx, coIdx, dealIdx, status, issueDays, validDays, discount, taxRate, items[sku, qty]]
        $data = [
            ['QT-00001', 'TechNova Enterprise Proposal 2025',    0,  0,  0,  'accepted', -30, 60, 0,   10, [['CRM-ENT', 10], ['SVC-ONBOARD', 1], ['SUP-PRIORITY', 1]]],
            ['QT-00002', 'GlobalRetail Annual CRM Proposal',     1,  1,  1,  'sent',     -10, 30, 1000, 0, [['CRM-PRO', 20], ['SVC-MIGRATE', 1], ['SVC-API', 1]]],
            ['QT-00003', 'Meridian Finance Data Platform Quote', 2,  2,  2,  'draft',      0, 30, 0,   10, [['CRM-ENT', 5],  ['ADD-REPORTS', 1], ['TRN-2DAY', 1]]],
            ['QT-00004', 'SkyBridge Logistics Suite Proposal',   3,  3,  3,  'sent',      -5, 45, 500,  10, [['CRM-ENT', 8],  ['ADD-MOBILE', 10], ['SUP-PRIORITY', 1]]],
            ['QT-00005', 'DataStream Corp Core CRM Quote',       6,  6,  6,  'draft',      0, 30, 0,    0, [['CRM-PRO', 15], ['SVC-API', 1],    ['ADD-SOCIAL', 1]]],
            ['QT-00006', 'NorthStar Fund Analytics Proposal',   10, 10, 10, 'expired',  -45, 30, 0,    0, [['CRM-ENT', 3],  ['ADD-AUDIT', 1],  ['SVC-AM', 1]]],
            ['QT-00007', 'Aurora Media Campaign Suite Quote',    5,  5,  5,  'accepted', -20, 60, 0,   10, [['CRM-PRO', 5],  ['ADD-SOCIAL', 1], ['TRN-1DAY', 1]]],
            ['QT-00008', 'Pacific Holdings Enterprise Proposal', 0,  0, 12,  'sent',      -2, 30, 2000, 10, [['CRM-ENT', 25], ['SVC-AM', 1],    ['SVC-ONBOARD', 1], ['ADD-AUDIT', 1]]],
        ];

        foreach ($data as $i => [$num, $title, $cIdx, $coIdx, $dealIdx, $status, $issueDays, $validDays, $discount, $taxRate, $items]) {
            $deal = $deals->get($dealIdx);
            $quote = Quote::create([
                'tenant_id'   => $tid,
                'quote_number'=> $num,
                'title'       => $title,
                'contact_id'  => $contacts[$cIdx]->id,
                'company_id'  => $companies[$coIdx]->id,
                'deal_id'     => $deal ? $deal->id : null,
                'status'      => $status,
                'issue_date'  => now()->addDays($issueDays),
                'valid_until' => now()->addDays($issueDays + $validDays),
                'currency'    => 'USD',
                'tax_rate'    => $taxRate,
                'discount'    => $discount,
                'subtotal'    => 0,
                'tax_amount'  => 0,
                'total'       => 0,
                'notes'       => 'We appreciate your consideration. This quote is valid for the period stated above.',
                'terms'       => 'Payment due within 30 days of acceptance. Prices in USD.',
                'created_by'  => $owner->id,
            ]);

            foreach ($items as $sort => [$sku, $qty]) {
                $product = $p($sku);
                if ($product) {
                    $quote->items()->create([
                        'product_id'  => $product->id,
                        'description' => $product->name,
                        'quantity'    => $qty,
                        'unit_price'  => $product->unit_price,
                        'total'       => $qty * $product->unit_price,
                        'sort_order'  => $sort + 1,
                    ]);
                }
            }
            $quote->load('items');
            $quote->recalculate();
        }
    }
}
