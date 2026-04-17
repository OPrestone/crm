<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        $tid     = Tenant::where('slug', 'acme-corp')->value('id');
        $owner   = User::where('email', 'demo@acme.com')->first();
        $manager = User::where('email', 'manager@acme.com')->first();
        $staff1  = User::where('email', 'staff@acme.com')->first();
        $staff2  = User::where('email', 'emily@acme.com')->first();

        // Quarter dates
        $q4Start = now()->startOfYear()->addMonths(9)->toDateString();
        $q4End   = now()->startOfYear()->addMonths(12)->subDay()->toDateString();
        $yrStart = now()->startOfYear()->toDateString();
        $yrEnd   = now()->endOfYear()->toDateString();
        $moStart = now()->startOfMonth()->toDateString();
        $moEnd   = now()->endOfMonth()->toDateString();

        // [title, desc, type, period, target, current, startDate, endDate, status, userId]
        $data = [
            [
                'Q4 Revenue Target',
                'Hit $500K in closed revenue for Q4 across all deal types.',
                'revenue', 'quarterly', 500000, 287500, $q4Start, $q4End, 'active', $owner->id,
            ],
            [
                'Annual Revenue Goal 2025',
                'Achieve $1.5M in total revenue for the fiscal year 2025.',
                'revenue', 'yearly', 1500000, 963000, $yrStart, $yrEnd, 'active', $owner->id,
            ],
            [
                'Q4 Deals Won – Sales Team',
                'Close 15 deals in Q4 across all reps.',
                'deals_won', 'quarterly', 15, 9, $q4Start, $q4End, 'active', $manager->id,
            ],
            [
                'Monthly New Leads – October',
                'Generate 50 new leads this month from all channels.',
                'leads_created', 'monthly', 50, 38, $moStart, $moEnd, 'active', $staff2->id,
            ],
            [
                'Monthly Contacts Added',
                'Add 30 net-new qualified contacts to the CRM in October.',
                'contacts_added', 'monthly', 30, 24, $moStart, $moEnd, 'active', $staff1->id,
            ],
            [
                'Q4 Discovery Calls – Michael Lee',
                'Complete 40 outbound discovery calls this quarter.',
                'calls_made', 'quarterly', 40, 31, $q4Start, $q4End, 'active', $staff1->id,
            ],
            [
                'Product Demos Scheduled – Emily Chen',
                'Schedule 20 product demos with qualified prospects this quarter.',
                'demos_scheduled', 'quarterly', 20, 14, $q4Start, $q4End, 'active', $staff2->id,
            ],
            [
                'Annual Contacts Growth',
                'Grow the contact database by 200 net-new qualified contacts this year.',
                'contacts_added', 'yearly', 200, 152, $yrStart, $yrEnd, 'active', $manager->id,
            ],
        ];

        foreach ($data as [$title, $desc, $type, $period, $target, $current, $start, $end, $status, $userId]) {
            Goal::create([
                'tenant_id'     => $tid,
                'user_id'       => $userId,
                'title'         => $title,
                'description'   => $desc,
                'type'          => $type,
                'period'        => $period,
                'target_value'  => $target,
                'current_value' => $current,
                'start_date'    => $start,
                'end_date'      => $end,
                'status'        => $status,
                'created_by'    => $owner->id,
            ]);
        }
    }
}
