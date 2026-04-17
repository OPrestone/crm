<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $manager  = User::where('email', 'manager@acme.com')->first();
        $staff1   = User::where('email', 'staff@acme.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();

        // [title, description, offsetDays, durationMins, type, status, color, userVar, contactIdx, companyIdx, location]
        $data = [
            ['Discovery Call – TechNova',        'Initial call to assess needs and current pain points.',         1,   30,  'call',      'scheduled',  '#0d6efd', $owner,   0,  0,  'Phone'],
            ['Product Demo – GlobalRetail',       'Full CRM demo for GlobalRetail procurement team.',            2,   60,  'demo',      'scheduled',  '#198754', $manager, 1,  1,  'Zoom'],
            ['QBR – Meridian Finance',            'Quarterly business review and roadmap discussion.',           -5,   90,  'meeting',   'completed',  '#6f42c1', $owner,   2,  2,  'London Office'],
            ['Follow-up Call – Helix Biotech',    'Check-in on compliance module rollout progress.',             3,   30,  'call',      'scheduled',  '#fd7e14', $staff1,  4,  4,  'Phone'],
            ['Onboarding Session – BlueWave',     'Kick-off onboarding and platform training session.',         -2,  120,  'meeting',   'completed',  '#0dcaf0', $manager, 9,  9,  'Google Meet'],
            ['Executive Briefing – NorthStar',    'C-suite briefing on AI analytics and portfolio module.',      5,   45,  'meeting',   'scheduled',  '#dc3545', $owner,   10, 10, 'Toronto HQ'],
            ['Contract Review – Orion Pharma',    'Walk-through of regulatory compliance contract terms.',       7,   60,  'meeting',   'scheduled',  '#ffc107', $manager, 11, 11, 'Zoom'],
            ['Cold Outreach Call – SkyBridge',    'First contact with SkyBridge procurement lead.',            -8,   20,  'call',      'completed',  '#6c757d', $staff1,  3,  3,  'Phone'],
            ['Platform Walkthrough – DataStream', 'Technical deep-dive into API and data integration features.', 4,   90,  'demo',      'scheduled',  '#20c997', $manager, 6,  6,  'Teams'],
            ['Renewal Check-in – TechNova',       'Annual renewal discussion and upsell opportunity.',           9,   30,  'follow_up', 'scheduled',  '#0d6efd', $owner,   0,  0,  'Phone'],
            ['Team Training – Aurora Media',      'CRM platform training for Aurora Media marketing team.',    -3,  120,  'meeting',   'completed',  '#d63384', $staff1,  5,  5,  'Google Meet'],
            ['Demo – Solara Energy',              'Technical demo of analytics and reporting dashboards.',       6,   60,  'demo',      'scheduled',  '#198754', $manager, 8,  8,  'Zoom'],
        ];

        foreach ($data as [$title, $desc, $days, $mins, $type, $status, $color, $user, $cIdx, $coIdx, $location]) {
            $start = now()->addDays($days)->setHour(rand(9, 16))->setMinute(0)->setSecond(0);
            $end   = $start->copy()->addMinutes($mins);
            Appointment::create([
                'tenant_id'  => $tid,
                'user_id'    => $user->id,
                'contact_id' => $contacts[$cIdx]->id,
                'company_id' => $companies[$coIdx]->id,
                'title'      => $title,
                'description'=> $desc,
                'start_at'   => $start,
                'end_at'     => $end,
                'location'   => $location,
                'type'       => $type,
                'status'     => $status,
                'color'      => $color,
                'created_by' => $owner->id,
            ]);
        }
    }
}
