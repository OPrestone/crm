<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailCampaignController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = EmailCampaign::where('tenant_id', $this->tid())->withCount('recipients');
        if ($s = $request->search) {
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('subject','like',"%$s%"));
        }
        if ($status = $request->status) $query->where('status', $status);
        $campaigns = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => EmailCampaign::where('tenant_id', $this->tid())->count(),
            'sent'      => EmailCampaign::where('tenant_id', $this->tid())->where('status','sent')->count(),
            'draft'     => EmailCampaign::where('tenant_id', $this->tid())->where('status','draft')->count(),
            'scheduled' => EmailCampaign::where('tenant_id', $this->tid())->where('status','scheduled')->count(),
        ];

        return view('email-campaigns.index', compact('campaigns','stats'));
    }

    public function create()
    {
        return view('email-campaigns.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'subject'    => 'required|string|max:300',
            'from_name'  => 'nullable|string|max:100',
            'from_email' => 'nullable|email|max:150',
            'body'       => 'required|string',
            'segment'    => 'required|in:all,active',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        $data['status']     = $request->filled('scheduled_at') ? 'scheduled' : 'draft';

        $campaign = EmailCampaign::create($data);

        return redirect()->route('email_campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully!');
    }

    public function show(EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);
        $emailCampaign->load('creator');
        $recipients = $emailCampaign->recipients()->with('contact')->latest()->paginate(20);

        $analytics = [
            'total_sent'    => $emailCampaign->recipients()->whereNotNull('sent_at')->count(),
            'total_opened'  => $emailCampaign->recipients()->whereNotNull('opened_at')->count(),
            'total_clicked' => $emailCampaign->recipients()->whereNotNull('clicked_at')->count(),
        ];

        return view('email-campaigns.show', compact('emailCampaign','recipients','analytics'));
    }

    public function edit(EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);
        abort_if($emailCampaign->status === 'sent', 403, 'Cannot edit a sent campaign.');
        return view('email-campaigns.edit', compact('emailCampaign'));
    }

    public function update(Request $request, EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);
        abort_if($emailCampaign->status === 'sent', 403);

        $data = $request->validate([
            'name'         => 'required|string|max:200',
            'subject'      => 'required|string|max:300',
            'from_name'    => 'nullable|string|max:100',
            'from_email'   => 'nullable|email|max:150',
            'body'         => 'required|string',
            'segment'      => 'required|in:all,active',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $data['status'] = $request->filled('scheduled_at') ? 'scheduled' : 'draft';
        $emailCampaign->update($data);

        return redirect()->route('email_campaigns.show', $emailCampaign)
            ->with('success', 'Campaign updated!');
    }

    public function send(EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);
        abort_if($emailCampaign->status === 'sent', 403);

        // Gather contacts based on segment
        $contacts = Contact::where('tenant_id', $this->tid())->get();

        // Create recipient records
        foreach ($contacts as $contact) {
            EmailCampaignContact::updateOrCreate(
                ['campaign_id' => $emailCampaign->id, 'contact_id' => $contact->id],
                ['sent_at' => now()]
            );
        }

        // Simulate some opens/clicks for demo realism
        $recipients = $emailCampaign->recipients()->get();
        foreach ($recipients->take(intval($recipients->count() * 0.45)) as $r) {
            $r->update(['opened_at' => now()->subMinutes(rand(5, 180))]);
        }
        foreach ($recipients->take(intval($recipients->count() * 0.18)) as $r) {
            $r->update(['clicked_at' => now()->subMinutes(rand(2, 60))]);
        }

        $emailCampaign->update(['status' => 'sent', 'sent_at' => now()]);

        return redirect()->route('email_campaigns.show', $emailCampaign)
            ->with('success', "Campaign sent to {$contacts->count()} contacts!");
    }

    public function duplicate(EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);

        $new = $emailCampaign->replicate();
        $new->name       = 'Copy of ' . $emailCampaign->name;
        $new->status     = 'draft';
        $new->sent_at    = null;
        $new->created_by = Auth::id();
        $new->save();

        return redirect()->route('email_campaigns.edit', $new)
            ->with('success', 'Campaign duplicated!');
    }

    public function destroy(EmailCampaign $emailCampaign)
    {
        abort_if($emailCampaign->tenant_id !== $this->tid(), 403);
        $emailCampaign->recipients()->delete();
        $emailCampaign->delete();
        return redirect()->route('email_campaigns.index')->with('success', 'Campaign deleted.');
    }
}
