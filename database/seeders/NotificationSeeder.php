<?php

namespace Database\Seeders;

use App\Models\CrmNotification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $tid     = Tenant::where('slug', 'acme-corp')->value('id');
        $owner   = User::where('email', 'demo@acme.com')->first();
        $manager = User::where('email', 'manager@acme.com')->first();
        $staff1  = User::where('email', 'staff@acme.com')->first();
        $staff2  = User::where('email', 'emily@acme.com')->first();

        // [user, type, title, body, icon, color, url, ago, readOffset(null=unread)]
        $data = [
            [$owner,   'info',    'New Lead Assigned',       'TechNova Enterprise Upgrade has been assigned to you.',           'funnel-fill',              'primary', '/leads',    '-2 hours',  null],
            [$owner,   'success', 'Deal Won!',               'BlueWave Starter Pack marked as Won — $22,500.',                 'trophy-fill',              'success', '/deals',    '-5 hours',  null],
            [$owner,   'warning', 'Task Overdue',            '"Send GlobalRetail contract draft" is past its due date.',        'exclamation-triangle-fill','warning', '/tasks',    '-1 day',    null],
            [$owner,   'success', 'Invoice Paid',            'INV-00001 has been paid — $24,100 received.',                    'receipt',                  'success', '/invoices', '-3 hours',  null],
            [$owner,   'info',    'New Contact Added',       'Alice Thompson was added to your contacts.',                     'person-plus-fill',         'primary', '/contacts', '-1 day',    null],
            [$manager, 'info',    'Deal Stage Updated',      'GlobalRetail Annual Contract moved to Proposal stage.',          'arrow-right-circle-fill',  'info',    '/deals',    '-4 hours',  null],
            [$manager, 'success', 'Deal Won!',               'Vertex Cloud Starter closed successfully. Great work!',          'trophy-fill',              'success', '/deals',    '-2 days',   null],
            [$manager, 'warning', 'Lead Score Alert',        'SkyBridge Logistics lead score has dropped below 50.',           'graph-down-arrow',         'warning', '/leads',    '-6 hours',  null],
            [$manager, 'info',    'New Task Assigned',       '"CRM demo – Meridian Finance" has been assigned to you.',        'check2-square',            'primary', '/tasks',    '-3 hours',  null],
            [$staff1,  'success', 'Invoice Sent',            'INV-00002 sent to GlobalRetail Ltd successfully.',               'envelope-check-fill',      'success', '/invoices', '-1 day',    null],
            [$staff1,  'info',    'New Deal Created',        'Pacific Holdings Enterprise deal has been opened.',              'briefcase-fill',           'primary', '/deals',    '-2 hours',  null],
            [$staff1,  'warning', 'Follow-up Reminder',      'You have 3 overdue follow-up tasks this week.',                  'bell-fill',                'warning', '/tasks',    '-8 hours',  null],
            [$staff2,  'info',    'Contact Updated',         'Benjamin Carter\'s profile was updated.',                       'person-fill',              'info',    '/contacts', '-5 hours',  null],
            [$staff2,  'success', 'Lead Converted',          'Helix Biotech Compliance lead converted to a Deal.',            'funnel-fill',              'success', '/leads',    '-1 day',    null],
            [$owner,   'info',    'Quote Accepted',          'QT-00001 has been accepted by TechNova — $203,700.',             'file-earmark-check-fill',  'primary', '/quotes',   '-12 hours', null],
            [$owner,   'warning', 'Ticket Escalated',        'TKT-00002 has been escalated to urgent priority.',               'headset',                  'danger',  '/tickets',  '-30 mins',  null],
        ];

        foreach ($data as $i => [$user, $type, $title, $body, $icon, $color, $url, $ago, $readOffset]) {
            $notif = CrmNotification::create([
                'tenant_id' => $tid,
                'user_id'   => $user->id,
                'type'      => $type,
                'title'     => $title,
                'body'      => $body,
                'icon'      => $icon,
                'color'     => $color,
                'url'       => $url,
                'read_at'   => $i < 5 ? now()->subHours(rand(1, 24)) : null,
            ]);
            $notif->created_at = now()->modify($ago);
            $notif->saveQuietly();
        }
    }
}
