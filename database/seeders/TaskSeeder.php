<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tid   = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner = User::where('email', 'demo@prestech.com')->first();
        $users = User::where('tenant_id', $tid)->get();

        // [title, type, priority, status, dueDays]
        $data = [
            ['Call Alice Thompson re: Q3 proposal',    'call',    'high',   'pending',     2],
            ['Send GlobalRetail contract draft',       'email',   'urgent', 'pending',     1],
            ['CRM demo – Meridian Finance team',       'meeting', 'high',   'in_progress', 3],
            ['Follow up on DataStream API proposal',   'email',   'medium', 'pending',     5],
            ['Onboarding call – Helix Biotech',        'call',    'high',   'completed',  -3],
            ['Prepare Solara Energy quote',            'task',    'medium', 'pending',     7],
            ['Review NorthStar contract redlines',     'task',    'urgent', 'in_progress', 1],
            ['Send thank-you note to BlueWave',        'email',   'low',    'completed',  -1],
            ['Kick-off meeting – Pacific Holdings',    'meeting', 'urgent', 'pending',     4],
            ['Update pipeline for Q4 planning',        'task',    'medium', 'pending',     6],
            ['Record demo for Aurora Media',           'task',    'medium', 'in_progress', 2],
            ['Follow up: Orion Pharma decision',       'call',    'high',   'pending',     3],
            ['Prepare Summit Group ROI analysis',      'task',    'high',   'pending',     8],
            ['Send Vertex Cloud trial credentials',    'email',   'low',    'completed',  -2],
            ['Check in with SkyBridge Logistics',      'call',    'medium', 'pending',    10],
            ['Weekly sales pipeline review',           'meeting', 'high',   'in_progress', 0],
            ['Update contact records – batch import',  'task',    'low',    'pending',    14],
            ['Send proposal – PrimeBuild mobile',      'email',   'medium', 'pending',     2],
            ['Competitor analysis report',             'task',    'medium', 'completed',  -5],
            ['Board presentation – Q3 results',        'meeting', 'urgent', 'pending',    12],
        ];

        foreach ($data as $i => [$title, $type, $priority, $status, $dueDays]) {
            Task::create([
                'tenant_id'    => $tid,
                'title'        => $title,
                'description'  => "Task: {$title}. All stakeholders informed. Action items tracked.",
                'type'         => $type,
                'status'       => $status,
                'priority'     => $priority,
                'due_date'     => now()->addDays($dueDays),
                'completed_at' => $status === 'completed' ? now()->subDays(1) : null,
                'assigned_to'  => $users[$i % $users->count()]->id,
                'created_by'   => $owner->id,
            ]);
        }
    }
}
