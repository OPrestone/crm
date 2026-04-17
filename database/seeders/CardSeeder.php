<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $manager  = User::where('email', 'manager@acme.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();

        // ── Card Templates ────────────────────────────────────────────────
        $t1 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Executive Blue',
            'category'   => 'professional',
            'design'     => ['bg_color' => '#0f172a', 'text_color' => '#ffffff', 'accent_color' => '#3b82f6', 'layout' => 'horizontal'],
            'fields'     => ['name', 'title', 'company', 'email', 'phone', 'website', 'linkedin'],
            'created_by' => $owner->id,
        ]);

        $t2 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Modern Minimal',
            'category'   => 'minimal',
            'design'     => ['bg_color' => '#ffffff', 'text_color' => '#1a202c', 'accent_color' => '#10b981', 'layout' => 'vertical'],
            'fields'     => ['name', 'title', 'company', 'email', 'phone'],
            'created_by' => $owner->id,
        ]);

        $t3 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Bold Creative',
            'category'   => 'creative',
            'design'     => ['bg_color' => '#7c3aed', 'text_color' => '#ffffff', 'accent_color' => '#fbbf24', 'layout' => 'horizontal'],
            'fields'     => ['name', 'title', 'company', 'email', 'phone', 'twitter'],
            'created_by' => $manager->id,
        ]);

        $t4 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Corporate Slate',
            'category'   => 'professional',
            'design'     => ['bg_color' => '#1e293b', 'text_color' => '#e2e8f0', 'accent_color' => '#f59e0b', 'layout' => 'horizontal'],
            'fields'     => ['name', 'title', 'company', 'email', 'phone', 'address'],
            'created_by' => $owner->id,
        ]);

        // ── Cards ─────────────────────────────────────────────────────────
        $cardData = [
            [$contacts[0],  $t1, 'Alice Thompson',   'CTO',                   'TechNova Inc',       'alice.thompson@example.com',  '+1 555-210-4321', 'technova.io',      null,                     'linkedin.com/in/alicethompson', null],
            [$contacts[3],  $t2, 'David Kim',        'CEO',                   'GlobalRetail Ltd',   'david.kim@example.com',       '+1 555-311-6789', 'globalretail.com', null,                     null,                            null],
            [$contacts[6],  $t3, 'Grace Patel',      'Marketing Director',    'Aurora Media Group', 'grace.patel@example.com',     '+1 555-412-5555', null,               null,                     null,                            '@gracepatel'],
            [$contacts[7],  $t4, 'Henry Watkins',    'CFO',                   'Meridian Finance',   'henry.watkins@example.com',   '+1 555-513-8888', null,               '10 Finance Sq, London',  null,                            null],
            [$contacts[17], $t1, 'Rachel Bloom',     'Chief Revenue Officer', 'DataStream Corp',    'rachel.bloom@example.com',    '+1 555-614-7777', 'datastream.io',    null,                     'linkedin.com/in/rachelbloom',   null],
            [$contacts[19], $t2, 'Tara Muller',      'Founder',               'Solara Energy',      'tara.muller@example.com',     '+1 555-715-3333', 'solaraenergy.com', null,                     null,                            null],
            [$contacts[22], $t4, 'William Park',     'Managing Director',     'NorthStar Ventures', 'william.park@example.com',    '+1 555-816-2222', 'northstarvc.com',  '1 Bay St, Toronto',      null,                            null],
        ];

        foreach ($cardData as [$contact, $template, $name, $title, $company, $email, $phone, $website, $address, $linkedin, $twitter]) {
            $cardFields = array_filter(compact('name', 'title', 'company', 'email', 'phone', 'website', 'address', 'linkedin', 'twitter'));
            Card::create([
                'tenant_id'   => $tid,
                'template_id' => $template->id,
                'contact_id'  => $contact->id,
                'name'        => "{$name} — {$template->name}",
                'data'        => $cardFields,
                'created_by'  => $owner->id,
            ]);
        }
    }
}
