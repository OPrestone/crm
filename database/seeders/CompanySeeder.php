<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $tid   = Tenant::where('slug', 'acme-corp')->value('id');
        $owner = User::where('email', 'demo@acme.com')->first();

        $data = [
            ['TechNova Inc',          'Technology',   'San Francisco', 'USA',     '1000-5000',  'contact@technova.io',           'https://technova.io',           12500000],
            ['GlobalRetail Ltd',      'Retail',       'New York',      'USA',     '5000-10000', 'info@globalretail.com',          'https://globalretail.com',      45000000],
            ['Meridian Finance',      'Finance',      'London',        'UK',      '500-1000',   'hello@meridianfinance.co.uk',    'https://meridianfinance.co.uk',  8000000],
            ['SkyBridge Logistics',   'Logistics',    'Chicago',       'USA',     '1000-5000',  'ops@skybridgelog.com',           'https://skybridgelog.com',      22000000],
            ['Helix Biotech',         'Healthcare',   'Boston',        'USA',     '200-500',    'info@helixbio.com',              'https://helixbio.com',           5500000],
            ['Aurora Media Group',    'Media',        'Los Angeles',   'USA',     '500-1000',   'press@auroramedia.com',          'https://auroramedia.com',       18000000],
            ['DataStream Corp',       'Technology',   'Seattle',       'USA',     '1000-5000',  'sales@datastream.io',            'https://datastream.io',         31000000],
            ['PrimeBuild Const.',     'Construction', 'Dallas',        'USA',     '500-1000',   'contact@primebuild.com',         'https://primebuild.com',         9800000],
            ['Solara Energy',         'Energy',       'Houston',       'USA',     '200-500',    'info@solaraenergy.com',          'https://solaraenergy.com',       7200000],
            ['BlueWave Consulting',   'Consulting',   'Miami',         'USA',     '50-200',     'hello@bluewave.co',              'https://bluewave.co',            2400000],
            ['NorthStar Ventures',    'Finance',      'Toronto',       'Canada',  '50-200',     'invest@northstarvc.com',         'https://northstarvc.com',      150000000],
            ['Orion Pharmaceuticals', 'Healthcare',   'Berlin',        'Germany', '500-1000',   'info@orionpharma.de',            'https://orionpharma.de',        19000000],
        ];

        foreach ($data as [$name, $industry, $city, $country, $size, $email, $website, $revenue]) {
            Company::create([
                'tenant_id'      => $tid,
                'name'           => $name,
                'industry'       => $industry,
                'city'           => $city,
                'country'        => $country,
                'size'           => $size,
                'email'          => $email,
                'website'        => $website,
                'annual_revenue' => $revenue,
                'phone'          => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'notes'          => "Key account — {$industry} sector client with long-term growth potential.",
                'created_by'     => $owner->id,
            ]);
        }
    }
}
