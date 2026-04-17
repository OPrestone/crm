<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Database\Seeder;

class TerritorySeeder extends Seeder
{
    public function run(): void
    {
        $tid     = Tenant::where('slug', 'acme-corp')->value('id');
        $owner   = User::where('email', 'demo@acme.com')->first();
        $manager = User::where('email', 'manager@acme.com')->first();
        $staff1  = User::where('email', 'staff@acme.com')->first();
        $staff2  = User::where('email', 'emily@acme.com')->first();

        // [name, description, type, color, rules, users[]]
        $territories = [
            [
                'name'        => 'West Coast USA',
                'description' => 'Covers California, Oregon, Washington, Nevada, and Arizona.',
                'type'        => 'geographic',
                'color'       => '#0d6efd',
                'rules'       => ['states' => ['CA', 'OR', 'WA', 'NV', 'AZ'], 'countries' => ['USA']],
                'users'       => [$owner, $staff1],
            ],
            [
                'name'        => 'East Coast USA',
                'description' => 'Covers New York, New Jersey, Massachusetts, Florida, and Georgia.',
                'type'        => 'geographic',
                'color'       => '#198754',
                'rules'       => ['states' => ['NY', 'NJ', 'MA', 'FL', 'GA'], 'countries' => ['USA']],
                'users'       => [$manager, $staff2],
            ],
            [
                'name'        => 'Enterprise Accounts (>$1M ARR)',
                'description' => 'All accounts with over $1M in annual recurring revenue regardless of location.',
                'type'        => 'account',
                'color'       => '#dc3545',
                'rules'       => ['min_arr' => 1000000, 'company_size' => ['1000-5000', '5000-10000', '10000+']],
                'users'       => [$owner, $manager],
            ],
            [
                'name'        => 'Technology & SaaS',
                'description' => 'All technology and software-as-a-service industry accounts.',
                'type'        => 'industry',
                'color'       => '#6f42c1',
                'rules'       => ['industries' => ['Technology', 'SaaS', 'Software']],
                'users'       => [$staff1],
            ],
            [
                'name'        => 'Financial Services',
                'description' => 'All finance, banking, and investment sector accounts.',
                'type'        => 'industry',
                'color'       => '#fd7e14',
                'rules'       => ['industries' => ['Finance', 'Banking', 'Investment']],
                'users'       => [$staff2, $manager],
            ],
            [
                'name'        => 'EMEA Region',
                'description' => 'Europe, Middle East and Africa accounts.',
                'type'        => 'geographic',
                'color'       => '#20c997',
                'rules'       => ['countries' => ['UK', 'Germany', 'France', 'Spain', 'UAE', 'South Africa']],
                'users'       => [$manager],
            ],
        ];

        foreach ($territories as $data) {
            $territory = Territory::create([
                'tenant_id'   => $tid,
                'name'        => $data['name'],
                'description' => $data['description'],
                'type'        => $data['type'],
                'color'       => $data['color'],
                'rules'       => $data['rules'],
                'created_by'  => $owner->id,
            ]);
            $territory->users()->attach(array_column($data['users'], 'id'));
        }
    }
}
