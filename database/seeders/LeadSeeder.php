<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $users    = User::where('tenant_id', $tid)->get();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();
        $stages   = PipelineStage::where('tenant_id', $tid)->where('type', 'lead')->orderBy('position')->get();

        // [title, contactIdx, companyIdx, value, status, source, score, stageIdx]
        $data = [
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

        foreach ($data as $i => [$title, $cIdx, $coIdx, $value, $status, $source, $score, $stageIdx]) {
            Lead::create([
                'tenant_id'   => $tid,
                'title'       => $title,
                'contact_id'  => $contacts[$cIdx]->id,
                'company_id'  => $companies[$coIdx]->id,
                'stage_id'    => $stages[$stageIdx]->id,
                'source'      => $source,
                'status'      => $status,
                'score'       => $score,
                'value'       => $value,
                'notes'       => "Inbound lead via {$source}. High potential — needs qualification call this week.",
                'assigned_to' => $users[$i % $users->count()]->id,
                'created_by'  => $owner->id,
            ]);
        }
    }
}
