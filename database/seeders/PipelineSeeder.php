<?php

namespace Database\Seeders;

use App\Models\PipelineStage;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        $tid = Tenant::where('slug', 'acme-corp')->value('id');

        $dealStages = [
            ['Prospecting',   '#6c757d', 1, false, false],
            ['Qualification', '#0d6efd', 2, false, false],
            ['Proposal',      '#ffc107', 3, false, false],
            ['Negotiation',   '#fd7e14', 4, false, false],
            ['Closed Won',    '#198754', 5, true,  false],
            ['Closed Lost',   '#dc3545', 6, false, true ],
        ];
        foreach ($dealStages as [$name, $color, $pos, $won, $lost]) {
            PipelineStage::create([
                'tenant_id' => $tid, 'type' => 'deal',
                'name' => $name, 'color' => $color, 'position' => $pos,
                'is_won' => $won, 'is_lost' => $lost,
            ]);
        }

        $leadStages = [
            ['New',           '#6c757d', 1],
            ['Contacted',     '#0dcaf0', 2],
            ['Qualified',     '#0d6efd', 3],
            ['Proposal Sent', '#ffc107', 4],
            ['Converted',     '#198754', 5],
            ['Lost',          '#dc3545', 6],
        ];
        foreach ($leadStages as [$name, $color, $pos]) {
            PipelineStage::create([
                'tenant_id' => $tid, 'type' => 'lead',
                'name' => $name, 'color' => $color, 'position' => $pos,
                'is_won' => false, 'is_lost' => false,
            ]);
        }
    }
}
