<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignContact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner    = User::where('email', 'demo@prestech.com')->first();
        $manager  = User::where('email', 'manager@prestech.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();

        $body = '<h2>Hello {{first_name}},</h2><p>We have exciting news to share about our latest CRM updates.</p><p>Click below to learn more.</p><p>Best,<br>{{from_name}}</p>';

        // [name, subject, status, segment, scheduledOffsetDays, sentOffsetDays]
        $campaigns = [
            [
                'name'       => 'Q4 Product Launch Announcement',
                'subject'    => 'Exciting new features are here – see what\'s new!',
                'status'     => 'sent',
                'segment'    => 'all',
                'scheduled'  => -15,
                'sent'       => -14,
                'user'       => $owner,
            ],
            [
                'name'       => 'Enterprise Plan Upsell – October',
                'subject'    => 'Upgrade to Enterprise and unlock AI, audit logs & more',
                'status'     => 'sent',
                'segment'    => 'pro',
                'scheduled'  => -7,
                'sent'       => -6,
                'user'       => $manager,
            ],
            [
                'name'       => 'Webinar Invite: CRM Best Practices',
                'subject'    => 'Join us live – CRM Best Practices for 2026',
                'status'     => 'scheduled',
                'segment'    => 'all',
                'scheduled'  => 5,
                'sent'       => null,
                'user'       => $owner,
            ],
            [
                'name'       => 'Holiday Greetings 2025',
                'subject'    => 'Season\'s Greetings from prestech Corporation',
                'status'     => 'draft',
                'segment'    => 'all',
                'scheduled'  => null,
                'sent'       => null,
                'user'       => $manager,
            ],
            [
                'name'       => 'Win-back: Inactive Contacts',
                'subject'    => 'We miss you – here\'s a special offer inside',
                'status'     => 'sent',
                'segment'    => 'inactive',
                'scheduled'  => -30,
                'sent'       => -29,
                'user'       => $owner,
            ],
        ];

        foreach ($campaigns as $campaignData) {
            $campaign = EmailCampaign::create([
                'tenant_id'    => $tid,
                'name'         => $campaignData['name'],
                'subject'      => $campaignData['subject'],
                'from_name'    => 'prestech Corporation',
                'from_email'   => 'marketing@prestechcorp.com',
                'body'         => $body,
                'status'       => $campaignData['status'],
                'segment'      => $campaignData['segment'],
                'scheduled_at' => $campaignData['scheduled'] !== null ? now()->addDays($campaignData['scheduled']) : null,
                'sent_at'      => $campaignData['sent'] !== null ? now()->addDays($campaignData['sent']) : null,
                'created_by'   => $campaignData['user']->id,
            ]);

            // Add recipients for sent campaigns
            if ($campaignData['status'] === 'sent') {
                $subset = $contacts->take(15);
                foreach ($subset as $i => $contact) {
                    $sentAt    = now()->addDays($campaignData['sent']);
                    $openedAt  = ($i % 3 !== 2) ? $sentAt->copy()->addMinutes(rand(10, 1440)) : null;
                    $clickedAt = ($openedAt && $i % 4 === 0) ? $openedAt->copy()->addMinutes(rand(2, 60)) : null;

                    EmailCampaignContact::create([
                        'campaign_id' => $campaign->id,
                        'contact_id'  => $contact->id,
                        'sent_at'     => $sentAt,
                        'opened_at'   => $openedAt,
                        'clicked_at'  => $clickedAt,
                    ]);
                }
            }
        }
    }
}
