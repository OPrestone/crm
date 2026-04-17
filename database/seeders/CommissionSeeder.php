<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\CommissionPlan;
use App\Models\Deal;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        $tid     = Tenant::where('slug', 'acme-corp')->value('id');
        $owner   = User::where('email', 'demo@acme.com')->first();
        $manager = User::where('email', 'manager@acme.com')->first();
        $staff1  = User::where('email', 'staff@acme.com')->first();
        $staff2  = User::where('email', 'emily@acme.com')->first();
        $deals   = Deal::where('tenant_id', $tid)->get();

        // ── Commission Plans ──────────────────────────────────────────────
        $standardPlan = CommissionPlan::create([
            'tenant_id'      => $tid,
            'name'           => 'Standard Sales Commission',
            'type'           => 'percentage',
            'rate'           => 5.00,
            'min_deal_value' => 5000,
            'tiers'          => null,
            'is_active'      => true,
            'created_by'     => $owner->id,
        ]);

        $tieredPlan = CommissionPlan::create([
            'tenant_id'      => $tid,
            'name'           => 'Tiered Enterprise Commission',
            'type'           => 'tiered',
            'rate'           => 0,
            'min_deal_value' => 25000,
            'tiers'          => [
                ['min' => 25000,  'max' => 100000,  'rate' => 6.0],
                ['min' => 100001, 'max' => 300000,  'rate' => 7.5],
                ['min' => 300001, 'max' => null,     'rate' => 9.0],
            ],
            'is_active'      => true,
            'created_by'     => $owner->id,
        ]);

        CommissionPlan::create([
            'tenant_id'      => $tid,
            'name'           => 'BDR Flat Bonus',
            'type'           => 'flat',
            'rate'           => 500,
            'min_deal_value' => 1000,
            'tiers'          => null,
            'is_active'      => true,
            'created_by'     => $manager->id,
        ]);

        // ── Commissions (for won deals) ───────────────────────────────────
        $wonDeals = $deals->where('status', 'won');
        $reps     = [$owner, $manager, $staff1, $staff2];

        foreach ($wonDeals as $i => $deal) {
            $plan   = $i % 2 === 0 ? $standardPlan : $tieredPlan;
            $amount = round($deal->value * ($plan->type === 'flat' ? 0 : ($plan->type === 'percentage' ? $plan->rate : 7.5)) / 100, 2);
            if ($plan->type === 'flat') $amount = 500;

            Commission::create([
                'tenant_id'  => $tid,
                'user_id'    => $reps[$i % count($reps)]->id,
                'deal_id'    => $deal->id,
                'plan_id'    => $plan->id,
                'deal_value' => $deal->value,
                'amount'     => $amount,
                'status'     => $i === 0 ? 'paid' : 'approved',
                'paid_at'    => $i === 0 ? now()->subDays(5) : null,
                'notes'      => "Commission calculated at {$plan->rate}% of deal value.",
            ]);
        }
    }
}
