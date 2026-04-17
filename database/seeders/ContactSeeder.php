<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $tid       = Tenant::where('slug', 'acme-corp')->value('id');
        $owner     = User::where('email', 'demo@acme.com')->first();
        $users     = User::where('tenant_id', $tid)->get();
        $companies = Company::where('tenant_id', $tid)->get();

        $cities = ['San Francisco', 'New York', 'Chicago', 'Los Angeles', 'Seattle', 'Austin', 'Boston', 'Miami', 'Denver', 'Atlanta'];

        $data = [
            ['Alice',    'Thompson',  'CTO',                      90, 'Referral',     'active'],
            ['Benjamin', 'Carter',    'VP of Sales',              78, 'Website',      'active'],
            ['Clara',    'Martinez',  'Director of Finance',      65, 'Event',        'active'],
            ['David',    'Kim',       'CEO',                      95, 'Direct',       'active'],
            ['Eleanor',  'Nguyen',    'Head of Engineering',      82, 'LinkedIn',     'active'],
            ['Franklin', 'Okonkwo',   'Procurement Manager',      55, 'Cold Email',   'active'],
            ['Grace',    'Patel',     'Marketing Director',       70, 'Webinar',      'active'],
            ['Henry',    'Watkins',   'CFO',                      88, 'Referral',     'active'],
            ['Isabella', 'Romano',    'IT Director',              60, 'Website',      'active'],
            ['James',    'Hoffmann',  'Operations Manager',       45, 'Event',        'active'],
            ['Kate',     'Sullivan',  'Business Analyst',         35, 'Website',      'active'],
            ['Liam',     'Fraser',    'Product Manager',          72, 'Referral',     'active'],
            ['Mia',      'Andersson', 'Strategy Lead',            80, 'Social Media', 'active'],
            ['Noah',     'Brennan',   'Sales Executive',          50, 'Cold Email',   'inactive'],
            ['Olivia',   'Dupont',    'Account Manager',          68, 'Direct',       'active'],
            ['Patrick',  'Yamamoto',  'Data Engineer',            40, 'Website',      'active'],
            ['Quinn',    'El-Amin',   'Partner',                  92, 'Referral',     'active'],
            ['Rachel',   'Bloom',     'Chief Revenue Officer',    85, 'Conference',   'active'],
            ['Samuel',   'Osei',      'VP Engineering',           77, 'LinkedIn',     'active'],
            ['Tara',     'Muller',    'Founder',                  93, 'Direct',       'active'],
            ['Ulric',    'Forde',     'Sales Director',           62, 'Webinar',      'active'],
            ['Vera',     'Singh',     'Chief Marketing Officer',  74, 'Referral',     'active'],
            ['William',  'Park',      'Managing Director',        88, 'Event',        'active'],
            ['Xena',     'Cohen',     'Business Dev. Manager',    57, 'Website',      'active'],
            ['Yusuf',    'Hassan',    'Regional Director',        83, 'Conference',   'active'],
        ];

        foreach ($data as $i => [$first, $last, $title, $score, $source, $status]) {
            Contact::create([
                'tenant_id'   => $tid,
                'first_name'  => $first,
                'last_name'   => $last,
                'email'       => strtolower("{$first}.{$last}@example.com"),
                'phone'       => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'mobile'      => '+1 555-' . rand(200, 899) . '-' . rand(1000, 9999),
                'job_title'   => $title,
                'company_id'  => $companies[$i % $companies->count()]->id,
                'source'      => $source,
                'status'      => $status,
                'lead_score'  => $score,
                'country'     => 'USA',
                'city'        => $cities[$i % count($cities)],
                'notes'       => "Met via {$source}. Interested in enterprise plan. Follow-up scheduled.",
                'assigned_to' => $users[$i % $users->count()]->id,
                'created_by'  => $owner->id,
            ]);
        }
    }
}
