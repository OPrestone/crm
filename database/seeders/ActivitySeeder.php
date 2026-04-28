<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner    = User::where('email', 'demo@prestech.com')->first();
        $manager  = User::where('email', 'manager@prestech.com')->first();
        $staff1   = User::where('email', 'staff@prestech.com')->first();
        $staff2   = User::where('email', 'emily@prestech.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $leads    = Lead::where('tenant_id', $tid)->get();
        $deals    = Deal::where('tenant_id', $tid)->get();

        $seeds = [
            ['created', 'New contact added: Alice Thompson',         $contacts[0],  $owner,   '-3 days'],
            ['email',   'Intro email sent to Benjamin Carter',       $contacts[1],  $staff1,  '-2 days'],
            ['call',    'Discovery call with Clara Martinez',        $contacts[2],  $manager, '-5 days'],
            ['meeting', 'Product demo for David Kim',                $contacts[3],  $owner,   '-4 days'],
            ['note',    'Contract review notes added for Eleanor',   $contacts[4],  $manager, '-1 day'],
            ['created', 'Lead created: TechNova Enterprise Upgrade', $leads[0],     $staff2,  '-6 days'],
            ['updated', 'Lead stage updated to Contacted',           $leads[1],     $manager, '-3 days'],
            ['email',   'Proposal sent to GlobalRetail team',        $leads[1],     $staff1,  '-2 days'],
            ['call',    'Qualification call with Meridian team',     $leads[2],     $manager, '-4 days'],
            ['created', 'Deal created: Enterprise License 2025',     $deals[0],     $owner,   '-7 days'],
            ['updated', 'Deal value updated to $185,000',           $deals[0],     $manager, '-2 days'],
            ['meeting', 'Contract negotiation – GlobalRetail',       $deals[1],     $owner,   '-1 day'],
            ['email',   'Follow-up email to DataStream Corp',        $contacts[6],  $staff1,  '-3 days'],
            ['call',    'Check-in with Solara Energy contact',       $contacts[8],  $staff2,  '-5 days'],
            ['note',    'Meeting notes added for NorthStar deal',    $deals[10],    $manager, '-1 day'],
            ['created', 'New deal: Pacific Holdings Enterprise',     $deals[12],    $owner,   '-2 days'],
            ['email',   'Thank-you email – BlueWave deal closed',   $deals[9],     $staff1,  '-6 days'],
            ['updated', 'Orion Pharma deal probability updated',     $deals[11],    $manager, '-1 day'],
            ['call',    'Intro call – Tara Muller (Solara Energy)',  $contacts[19], $staff2,  '-8 days'],
            ['meeting', 'QBR with Henry Watkins (Meridian)',         $contacts[7],  $owner,   '-10 days'],
        ];

        foreach ($seeds as [$type, $subject, $model, $user, $ago]) {
            $activity = Activity::create([
                'tenant_id'         => $tid,
                'user_id'           => $user->id,
                'type'              => $type,
                'subject'           => $subject,
                'description'       => "Action: {$subject}. Logged by {$user->name}.",
                'activityable_id'   => $model->id,
                'activityable_type' => get_class($model),
            ]);
            $activity->created_at = now()->modify($ago);
            $activity->saveQuietly();
        }
    }
}
