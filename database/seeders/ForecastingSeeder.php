<?php

namespace Database\Seeders;

use App\Models\SalesQuota;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForecastingSeeder extends Seeder
{
    public function run(): void
    {
        $tid     = Tenant::where('slug', 'acme-corp')->value('id');
        $owner   = User::where('email', 'demo@acme.com')->first();
        $manager = User::where('email', 'manager@acme.com')->first();
        $staff1  = User::where('email', 'staff@acme.com')->first();
        $staff2  = User::where('email', 'emily@acme.com')->first();

        $year = now()->year;

        // Sales quotas: quarterly targets per rep
        $quotas = [
            [$owner->id,   "{$year}-Q1", 400000],
            [$owner->id,   "{$year}-Q2", 450000],
            [$owner->id,   "{$year}-Q3", 475000],
            [$owner->id,   "{$year}-Q4", 500000],
            [$manager->id, "{$year}-Q1", 200000],
            [$manager->id, "{$year}-Q2", 225000],
            [$manager->id, "{$year}-Q3", 250000],
            [$manager->id, "{$year}-Q4", 275000],
            [$staff1->id,  "{$year}-Q1", 120000],
            [$staff1->id,  "{$year}-Q2", 135000],
            [$staff1->id,  "{$year}-Q3", 150000],
            [$staff1->id,  "{$year}-Q4", 160000],
            [$staff2->id,  "{$year}-Q1",  80000],
            [$staff2->id,  "{$year}-Q2",  90000],
            [$staff2->id,  "{$year}-Q3", 100000],
            [$staff2->id,  "{$year}-Q4", 115000],
        ];

        foreach ($quotas as [$userId, $period, $amount]) {
            SalesQuota::create([
                'tenant_id' => $tid,
                'user_id'   => $userId,
                'period'    => $period,
                'amount'    => $amount,
            ]);
        }
    }
}
