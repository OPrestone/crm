<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner    = User::where('email', 'demo@prestech.com')->first();
        $manager  = User::where('email', 'manager@prestech.com')->first();
        $staff1   = User::where('email', 'staff@prestech.com')->first();
        $staff2   = User::where('email', 'emily@prestech.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();

        $agents = [$owner, $manager, $staff1, $staff2];

        // [number, subject, description, status, priority, category, channel, cIdx, coIdx, agentIdx, resolvedDays, firstRespMins]
        $data = [
            ['TKT-00001', 'Cannot access billing portal',
             'I keep getting a 403 error when trying to access the billing section. Tried clearing cache, still the same.',
             'open',        'high',   'Billing',     'email',    0,  0,  0, null, 15],
            ['TKT-00002', 'API rate limits exceeded unexpectedly',
             'Our integration is hitting rate limits even though we are well below the documented threshold per minute.',
             'in_progress', 'urgent', 'API',         'portal',   1,  1,  1, null, 5],
            ['TKT-00003', 'Data export not including custom fields',
             'When we export contacts to CSV, the custom fields we created are missing from the file.',
             'resolved',    'medium', 'Data',        'email',    2,  2,  2, -2,   30],
            ['TKT-00004', 'Email campaign bouncing for all contacts',
             'Our last three email campaigns show 100% bounce rate. All contacts had valid emails before.',
             'open',        'urgent', 'Email',       'phone',    3,  3,  0, null, 8],
            ['TKT-00005', 'Invoice PDF not generating correctly',
             'The PDF invoice generator cuts off the last line item on multi-page invoices.',
             'in_progress', 'medium', 'Invoicing',   'email',    4,  4,  1, null, 45],
            ['TKT-00006', 'User cannot log in after password reset',
             'After clicking the password reset link, the user is redirected to an error page.',
             'resolved',    'high',   'Auth',        'email',    5,  5,  2, -5,   10],
            ['TKT-00007', 'Mobile app crashing on deal stage update',
             'Whenever we drag a deal to a new stage on mobile, the app freezes and crashes.',
             'open',        'high',   'Mobile',      'portal',   6,  6,  3, null, 60],
            ['TKT-00008', 'Custom domain SSL certificate expired',
             'Our custom CRM domain is showing a certificate expired warning to all users.',
             'closed',      'urgent', 'Domain',      'phone',    7,  7,  0, -10,  2],
            ['TKT-00009', 'Report builder not showing last month data',
             'The custom revenue report only shows data up to 45 days ago, not last month.',
             'pending',     'low',    'Reporting',   'email',    8,  8,  1, null, 120],
            ['TKT-00010', 'Contact import failing silently',
             'Uploaded a CSV of 500 contacts, import says success but no records appear in contacts list.',
             'open',        'medium', 'Data',        'email',    9,  9,  2, null, 25],
        ];

        foreach ($data as $i => [$num, $subject, $desc, $status, $priority, $category, $channel, $cIdx, $coIdx, $agentIdx, $resolvedDays, $firstRespMins]) {
            $ticket = Ticket::create([
                'tenant_id'        => $tid,
                'ticket_number'    => $num,
                'contact_id'       => $contacts[$cIdx]->id,
                'company_id'       => $companies[$coIdx]->id,
                'assigned_to'      => $agents[$agentIdx]->id,
                'subject'          => $subject,
                'description'      => $desc,
                'status'           => $status,
                'priority'         => $priority,
                'category'         => $category,
                'channel'          => $channel,
                'resolved_at'      => $resolvedDays !== null ? now()->addDays($resolvedDays) : null,
                'first_response_at'=> now()->subMinutes($firstRespMins),
                'created_by'       => $owner->id,
            ]);

            // First reply (agent acknowledgement)
            $ticket->replies()->create([
                'user_id'     => $agents[$agentIdx]->id,
                'body'        => "Thank you for reaching out. We've received your ticket and are investigating. We'll update you within 2 business hours.",
                'is_internal' => false,
            ]);

            // Second reply (internal note) for non-open tickets
            if (in_array($status, ['in_progress', 'resolved', 'closed', 'pending'])) {
                $ticket->replies()->create([
                    'user_id'     => $agents[$agentIdx % count($agents)]->id,
                    'body'        => "Internal note: Reproduced the issue in staging. Root cause identified — working on a fix. ETA 24 hours.",
                    'is_internal' => true,
                ]);
            }

            // Resolution reply for resolved/closed
            if (in_array($status, ['resolved', 'closed'])) {
                $ticket->replies()->create([
                    'user_id'     => $agents[$agentIdx]->id,
                    'body'        => "Great news — this issue has been resolved. Please let us know if you experience anything further. We're closing this ticket.",
                    'is_internal' => false,
                ]);
            }
        }
    }
}
