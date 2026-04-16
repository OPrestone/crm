<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Contact::where('tenant_id', $this->tid())->with(['company', 'assignedTo']);
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('first_name', 'like', "%$q%")->orWhere('last_name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")->orWhere('phone', 'like', "%$q%"));
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->source) $query->where('source', $request->source);
        $contacts = $query->latest()->paginate(15)->withQueryString();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('contacts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'mobile' => 'nullable|string|max:30',
            'company_id' => 'nullable|exists:companies,id',
            'job_title' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,blocked',
            'lead_score' => 'nullable|integer|min:0|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        $data['lead_score'] = $data['lead_score'] ?? 0;
        $contact = Contact::create($data);
        Activity::create([
            'tenant_id' => $this->tid(), 'user_id' => Auth::id(),
            'type' => 'created', 'subject' => 'Contact created: ' . $contact->full_name,
            'activityable_id' => $contact->id, 'activityable_type' => Contact::class,
        ]);
        return redirect()->route('contacts.show', $contact)->with('success', 'Contact created successfully!');
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->load(['company', 'assignedTo', 'creator', 'deals.stage', 'leads.stage', 'tasks', 'activities.user', 'invoices']);
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('view', $contact);
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('view', $contact);
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'mobile' => 'nullable|string|max:30',
            'company_id' => 'nullable|exists:companies,id',
            'job_title' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,blocked',
            'lead_score' => 'nullable|integer|min:0|max:100',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        $contact->update($data);
        return redirect()->route('contacts.show', $contact)->with('success', 'Contact updated!');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('view', $contact);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    }
}
