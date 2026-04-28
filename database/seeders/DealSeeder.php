<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner    = User::where('email', 'demo@prestech.com')->first();
        $users    = User::where('tenant_id', $tid)->get();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();
        $stages   = PipelineStage::where('tenant_id', $tid)->where('type', 'deal')->orderBy('position')->get();

        // [title, contactIdx, companyIdx, value, prob, closeDays, status, priority, stageIdx]
        $data = [
            ['TechNova Enterprise License 2025', 0,  0, 185000, 75, 30, 'open', 'high',   1],
            ['GlobalRetail Annual Contract',     1,  1, 420000, 60, 45, 'open', 'urgent', 2],
            ['Meridian Finance Modules',         2,  2,  62000, 40, 20, 'open', 'medium', 0],
            ['SkyBridge Route Optimizer',        3,  3, 148000, 80, 10, 'open', 'high',   3],
            ['Helix Biotech Compliance Suite',   4,  4,  38500, 90,  5, 'won',  'high',   4],
            ['Aurora Media Campaign Suite',      5,  5,  72000, 55, 60, 'open', 'medium', 1],
            ['DataStream Corp Core CRM',         6,  6, 215000, 70, 15, 'open', 'urgent', 2],
            ['PrimeBuild Mobile App License',    7,  7,  44000, 30, 90, 'lost', 'low',    5],
            ['Solara Energy Dashboard',          8,  8,  58000, 65, 25, 'open', 'medium', 0],
            ['BlueWave Starter Pack',            9,  9,  22500, 85,  7, 'won',  'low',    4],
            ['NorthStar Fund Analytics',        10, 10, 340000, 50, 55, 'open', 'urgent', 3],
            ['Orion Pharma Regulatory',         11, 11, 127000, 45, 40, 'open', 'high',   2],
            ['Pacific Holdings Enterprise',      0,  0, 580000, 35, 70, 'open', 'urgent', 1],
            ['Vertex Cloud Starter',             2,  2,  29000, 80,  3, 'won',  'low',    4],
            ['Summit Group Automation',          4,  4,  96000, 60, 20, 'open', 'medium', 0],
            ['Zenith Corp Full Suite',           6,  6, 275000, 25, 80, 'lost', 'medium', 5],
            ['Orion Phase 2 Expansion',         11, 11, 195000, 40, 35, 'open', 'high',   2],
            ['TechNova Support Extension',       0,  0,  48000, 90,  3, 'won',  'low',    4],
        ];

        foreach ($data as $i => [$title, $cIdx, $coIdx, $value, $prob, $days, $status, $priority, $stageIdx]) {
            Deal::create([
                'tenant_id'           => $tid,
                'title'               => $title,
                'contact_id'          => $contacts[$cIdx]->id,
                'company_id'          => $companies[$coIdx]->id,
                'stage_id'            => $stages[$stageIdx]->id,
                'value'               => $value,
                'probability'         => $prob,
                'expected_close_date' => now()->addDays($days),
                'status'              => $status,
                'priority'            => $priority,
                'notes'               => "Deal in progress. Next step: send proposal and schedule executive review.",
                'assigned_to'         => $users[$i % $users->count()]->id,
                'created_by'          => $owner->id,
            ]);
        }
    }
}
