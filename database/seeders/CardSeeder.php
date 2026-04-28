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
        $tid     = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner   = User::where('email', 'demo@prestech.com')->first();
        $manager = User::where('email', 'manager@prestech.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();

        // ══════════════════════════════════════════════════════════════════
        //  BUSINESS CARD TEMPLATES
        // ══════════════════════════════════════════════════════════════════

        $t1 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Executive Blue',
            'category'   => 'business',
            'is_default' => true,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#0f172a',
                'text_color'    => '#ffffff',
                'accent_color'  => '#3b82f6',
                'logo_position' => 'top-right',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_divider'  => true,
                'divider_color' => '#3b82f6',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'website', 'linkedin'],
            'created_by' => $owner->id,
        ]);

        $t2 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Modern Minimal',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'vertical',
                'bg_color'      => '#ffffff',
                'text_color'    => '#1a202c',
                'accent_color'  => '#10b981',
                'logo_position' => 'top-center',
                'font_family'   => 'Inter',
                'border_radius' => '16px',
                'show_divider'  => false,
                'border'        => '1px solid #e2e8f0',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone'],
            'created_by' => $owner->id,
        ]);

        $t3 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Bold Creative',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#7c3aed',
                'text_color'    => '#ffffff',
                'accent_color'  => '#fbbf24',
                'logo_position' => 'top-left',
                'font_family'   => 'Poppins',
                'border_radius' => '0px',
                'show_divider'  => true,
                'divider_color' => '#fbbf24',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'twitter'],
            'created_by' => $manager->id,
        ]);

        $t4 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Corporate Slate',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#1e293b',
                'text_color'    => '#e2e8f0',
                'accent_color'  => '#f59e0b',
                'logo_position' => 'bottom-right',
                'font_family'   => 'Georgia',
                'border_radius' => '8px',
                'show_divider'  => true,
                'divider_color' => '#f59e0b',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'address'],
            'created_by' => $owner->id,
        ]);

        $t5 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Rose Gold',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'vertical',
                'bg_color'      => '#fff1f2',
                'text_color'    => '#881337',
                'accent_color'  => '#e11d48',
                'logo_position' => 'top-center',
                'font_family'   => 'Georgia',
                'border_radius' => '20px',
                'show_divider'  => true,
                'divider_color' => '#fda4af',
                'gradient'      => 'linear-gradient(135deg, #fff1f2 0%, #fce7f3 100%)',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'website', 'instagram'],
            'created_by' => $owner->id,
        ]);

        $t6 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Tech Dark Gradient',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#0f0f1a',
                'text_color'    => '#e2e8f0',
                'accent_color'  => '#06b6d4',
                'logo_position' => 'top-right',
                'font_family'   => 'monospace',
                'border_radius' => '12px',
                'show_divider'  => true,
                'divider_color' => '#06b6d4',
                'gradient'      => 'linear-gradient(135deg, #0f0f1a 0%, #1e1b4b 100%)',
                'glow'          => '0 0 20px rgba(6,182,212,.25)',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'github', 'linkedin'],
            'created_by' => $manager->id,
        ]);

        $t7 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Forest & Earth',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#f0fdf4',
                'text_color'    => '#14532d',
                'accent_color'  => '#16a34a',
                'logo_position' => 'top-left',
                'font_family'   => 'Georgia',
                'border_radius' => '10px',
                'show_divider'  => false,
                'border'        => '2px solid #86efac',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'website'],
            'created_by' => $owner->id,
        ]);

        $t8 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Classic Black & White',
            'category'   => 'business',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#000000',
                'text_color'    => '#ffffff',
                'accent_color'  => '#ffffff',
                'logo_position' => 'top-left',
                'font_family'   => 'Georgia',
                'border_radius' => '0px',
                'show_divider'  => true,
                'divider_color' => '#ffffff',
            ],
            'fields' => ['name', 'title', 'company', 'email', 'phone', 'website'],
            'created_by' => $owner->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  ID CARD TEMPLATES
        // ══════════════════════════════════════════════════════════════════

        $t9 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Staff ID Badge',
            'category'   => 'id',
            'is_default' => true,
            'design'     => [
                'layout'          => 'vertical',
                'bg_color'        => '#ffffff',
                'text_color'      => '#1e293b',
                'accent_color'    => '#1d4ed8',
                'header_color'    => '#1d4ed8',
                'header_text'     => 'EMPLOYEE',
                'logo_position'   => 'header-center',
                'font_family'     => 'Inter',
                'border_radius'   => '12px',
                'show_photo'      => true,
                'photo_shape'     => 'circle',
                'show_barcode'    => true,
                'barcode_type'    => 'qr',
                'lanyard_slot'    => true,
            ],
            'fields' => ['photo', 'name', 'title', 'department', 'employee_id', 'email', 'phone', 'access_level'],
            'created_by' => $owner->id,
        ]);

        $t10 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Visitor Pass',
            'category'   => 'id',
            'is_default' => false,
            'design'     => [
                'layout'          => 'vertical',
                'bg_color'        => '#fffbeb',
                'text_color'      => '#92400e',
                'accent_color'    => '#d97706',
                'header_color'    => '#d97706',
                'header_text'     => 'VISITOR',
                'logo_position'   => 'header-left',
                'font_family'     => 'Inter',
                'border_radius'   => '12px',
                'show_photo'      => true,
                'photo_shape'     => 'square',
                'show_barcode'    => true,
                'barcode_type'    => 'qr',
                'lanyard_slot'    => true,
                'expiry_display'  => true,
            ],
            'fields' => ['photo', 'name', 'company', 'host', 'visit_date', 'expiry', 'visitor_id'],
            'created_by' => $owner->id,
        ]);

        $t11 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Contractor Pass',
            'category'   => 'id',
            'is_default' => false,
            'design'     => [
                'layout'          => 'vertical',
                'bg_color'        => '#fefce8',
                'text_color'      => '#713f12',
                'accent_color'    => '#eab308',
                'header_color'    => '#1c1917',
                'header_text'     => 'CONTRACTOR',
                'logo_position'   => 'header-left',
                'font_family'     => 'Inter',
                'border_radius'   => '8px',
                'show_photo'      => true,
                'photo_shape'     => 'square',
                'show_barcode'    => true,
                'barcode_type'    => 'barcode',
                'lanyard_slot'    => true,
                'stripe'          => '#eab308',
            ],
            'fields' => ['photo', 'name', 'company', 'contractor_id', 'site_access', 'valid_until', 'emergency_contact'],
            'created_by' => $manager->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  MEMBERSHIP CARD TEMPLATES
        // ══════════════════════════════════════════════════════════════════

        $t12 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Premium Gold Member',
            'category'   => 'membership',
            'is_default' => true,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#1c1917',
                'text_color'    => '#fef3c7',
                'accent_color'  => '#f59e0b',
                'logo_position' => 'top-left',
                'font_family'   => 'Georgia',
                'border_radius' => '12px',
                'show_divider'  => true,
                'divider_color' => '#f59e0b',
                'gradient'      => 'linear-gradient(135deg, #1c1917 0%, #292524 60%, #44403c 100%)',
                'foil_effect'   => true,
                'tier_badge'    => 'GOLD',
                'tier_color'    => '#f59e0b',
            ],
            'fields' => ['name', 'member_id', 'tier', 'since', 'expiry', 'points'],
            'created_by' => $owner->id,
        ]);

        $t13 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'VIP Black',
            'category'   => 'membership',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#09090b',
                'text_color'    => '#fafafa',
                'accent_color'  => '#a1a1aa',
                'logo_position' => 'top-right',
                'font_family'   => 'Georgia',
                'border_radius' => '16px',
                'show_divider'  => true,
                'divider_color' => '#52525b',
                'gradient'      => 'linear-gradient(135deg, #09090b 0%, #18181b 100%)',
                'foil_effect'   => true,
                'tier_badge'    => 'VIP',
                'tier_color'    => '#d4d4d8',
            ],
            'fields' => ['name', 'member_id', 'tier', 'since', 'expiry', 'benefits'],
            'created_by' => $owner->id,
        ]);

        $t14 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Club Membership',
            'category'   => 'membership',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#eff6ff',
                'text_color'    => '#1e3a8a',
                'accent_color'  => '#2563eb',
                'logo_position' => 'top-left',
                'font_family'   => 'Inter',
                'border_radius' => '10px',
                'show_divider'  => false,
                'border'        => '2px solid #bfdbfe',
                'tier_badge'    => 'MEMBER',
                'tier_color'    => '#2563eb',
            ],
            'fields' => ['name', 'member_id', 'tier', 'since', 'expiry', 'points', 'email'],
            'created_by' => $manager->id,
        ]);

        $t15 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Silver Loyalty',
            'category'   => 'membership',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#f1f5f9',
                'text_color'    => '#334155',
                'accent_color'  => '#64748b',
                'logo_position' => 'top-left',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_divider'  => true,
                'divider_color' => '#94a3b8',
                'gradient'      => 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)',
                'foil_effect'   => false,
                'tier_badge'    => 'SILVER',
                'tier_color'    => '#64748b',
            ],
            'fields' => ['name', 'member_id', 'tier', 'since', 'expiry', 'points'],
            'created_by' => $owner->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  EVENT BADGE TEMPLATES
        // ══════════════════════════════════════════════════════════════════

        $t16 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Conference Badge',
            'category'   => 'event',
            'is_default' => true,
            'design'     => [
                'layout'        => 'vertical',
                'bg_color'      => '#ffffff',
                'text_color'    => '#1e293b',
                'accent_color'  => '#6366f1',
                'header_color'  => '#6366f1',
                'header_text'   => 'CONFERENCE 2026',
                'logo_position' => 'header-center',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_photo'    => true,
                'photo_shape'   => 'circle',
                'show_barcode'  => true,
                'barcode_type'  => 'qr',
                'lanyard_slot'  => true,
                'ribbon'        => null,
            ],
            'fields' => ['photo', 'name', 'title', 'company', 'attendee_id', 'sessions', 'dietary'],
            'created_by' => $owner->id,
        ]);

        $t17 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Speaker Badge',
            'category'   => 'event',
            'is_default' => false,
            'design'     => [
                'layout'        => 'vertical',
                'bg_color'      => '#ffffff',
                'text_color'    => '#1e293b',
                'accent_color'  => '#dc2626',
                'header_color'  => '#0f172a',
                'header_text'   => 'CONFERENCE 2026',
                'logo_position' => 'header-center',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_photo'    => true,
                'photo_shape'   => 'circle',
                'show_barcode'  => true,
                'barcode_type'  => 'qr',
                'lanyard_slot'  => true,
                'ribbon'        => 'SPEAKER',
                'ribbon_color'  => '#dc2626',
            ],
            'fields' => ['photo', 'name', 'title', 'company', 'talk_title', 'talk_time', 'speaker_id'],
            'created_by' => $owner->id,
        ]);

        $t18 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Workshop Pass',
            'category'   => 'event',
            'is_default' => false,
            'design'     => [
                'layout'        => 'horizontal',
                'bg_color'      => '#fdf4ff',
                'text_color'    => '#581c87',
                'accent_color'  => '#a855f7',
                'header_color'  => '#7e22ce',
                'header_text'   => 'WORKSHOP PASS',
                'logo_position' => 'top-left',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_photo'    => false,
                'show_barcode'  => true,
                'barcode_type'  => 'qr',
                'lanyard_slot'  => false,
                'ribbon'        => null,
            ],
            'fields' => ['name', 'company', 'workshop', 'date', 'room', 'seat', 'attendee_id'],
            'created_by' => $manager->id,
        ]);

        $t19 = CardTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Exhibitor Badge',
            'category'   => 'event',
            'is_default' => false,
            'design'     => [
                'layout'        => 'vertical',
                'bg_color'      => '#ffffff',
                'text_color'    => '#1e293b',
                'accent_color'  => '#0891b2',
                'header_color'  => '#0c4a6e',
                'header_text'   => 'EXHIBITOR',
                'logo_position' => 'header-left',
                'font_family'   => 'Inter',
                'border_radius' => '12px',
                'show_photo'    => true,
                'photo_shape'   => 'square',
                'show_barcode'  => true,
                'barcode_type'  => 'qr',
                'lanyard_slot'  => true,
                'ribbon'        => 'EXHIBITOR',
                'ribbon_color'  => '#0891b2',
                'booth'         => true,
            ],
            'fields' => ['photo', 'name', 'title', 'company', 'booth_number', 'exhibitor_id', 'contact'],
            'created_by' => $manager->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        //  CARDS  (individual cards generated from templates)
        // ══════════════════════════════════════════════════════════════════

        $cardData = [
            // Business cards
            [$contacts[0],  $t1, 'Alice Thompson',   'CTO',                   'TechNova Inc',       'alice.thompson@example.com',  '+1 555-210-4321', 'technova.io',      null,                     'linkedin.com/in/alicethompson', null],
            [$contacts[3],  $t2, 'David Kim',         'CEO',                   'GlobalRetail Ltd',   'david.kim@example.com',       '+1 555-311-6789', 'globalretail.com', null,                     null,                            null],
            [$contacts[6],  $t3, 'Grace Patel',       'Marketing Director',    'Aurora Media Group', 'grace.patel@example.com',     '+1 555-412-5555', null,               null,                     null,                            '@gracepatel'],
            [$contacts[7],  $t4, 'Henry Watkins',     'CFO',                   'Meridian Finance',   'henry.watkins@example.com',   '+1 555-513-8888', null,               '10 Finance Sq, London',  null,                            null],
            [$contacts[17], $t1, 'Rachel Bloom',      'Chief Revenue Officer', 'DataStream Corp',    'rachel.bloom@example.com',    '+1 555-614-7777', 'datastream.io',    null,                     'linkedin.com/in/rachelbloom',   null],
            [$contacts[19], $t5, 'Tara Muller',       'Founder',               'Solara Energy',      'tara.muller@example.com',     '+1 555-715-3333', 'solaraenergy.com', null,                     null,                            null],
            [$contacts[22], $t4, 'William Park',      'Managing Director',     'NorthStar Ventures', 'william.park@example.com',    '+1 555-816-2222', 'northstarvc.com',  '1 Bay St, Toronto',      null,                            null],
            [$contacts[1],  $t6, 'Brandon Reyes',     'Lead Engineer',         'CloudNine Systems',  'brandon.reyes@example.com',   '+1 555-917-1111', null,               null,                     'linkedin.com/in/brandonreyes',  null],
            [$contacts[4],  $t7, 'Elena Vasquez',     'Sustainability Lead',   'Greenworks Co',      'elena.vasquez@example.com',   '+1 555-018-9999', 'greenworks.io',    null,                     null,                            null],
            [$contacts[9],  $t8, 'James Holloway',    'Partner',               'Holloway & Birch',   'james.holloway@example.com',  '+1 555-119-6666', 'hollowaybirch.com',null,                     null,                            null],
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
