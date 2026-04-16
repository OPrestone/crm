<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $view = $request->get('view', 'list');
        $query = Lead::where('tenant_id', $this->tid())->with(['contact', 'company', 'stage', 'assignedTo']);
        if ($request->search) {
            $q = $request->search;
            $query->where('title', 'like', "%$q%");
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->source) $query->where('source', $request->source);

        if ($view === 'kanban') {
            $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'lead')->orderBy('position')->get();
            $leads = $query->get()->groupBy('stage_id');
            return view('leads.kanban', compact('stages', 'leads'));
        }

        $leads = $query->latest()->paginate(15)->withQueryString();
        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'lead')->orderBy('position')->get();
        $users = User::where('tenant_id', $this->tid())->get();
        return view('leads.create', compact('contacts', 'companies', 'stages', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'source' => 'nullable|string|max:50',
            'status' => 'required|in:new,contacted,qualified,lost,converted',
            'score' => 'nullable|integer|min:0|max:100',
            'value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        $lead = Lead::create($data);
        Activity::create([
            'tenant_id' => $this->tid(), 'user_id' => Auth::id(),
            'type' => 'created', 'subject' => 'Lead created: ' . $lead->title,
            'activityable_id' => $lead->id, 'activityable_type' => Lead::class,
        ]);
        return redirect()->route('leads.show', $lead)->with('success', 'Lead created!');
    }

    public function show(Lead $lead)
    {
        abort_if($lead->tenant_id !== $this->tid(), 403);
        $lead->load(['contact', 'company', 'stage', 'assignedTo', 'creator', 'tasks', 'activities.user']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        abort_if($lead->tenant_id !== $this->tid(), 403);
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'lead')->orderBy('position')->get();
        $users = User::where('tenant_id', $this->tid())->get();
        return view('leads.edit', compact('lead', 'contacts', 'companies', 'stages', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        abort_if($lead->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'source' => 'nullable|string|max:50',
            'status' => 'required|in:new,contacted,qualified,lost,converted',
            'score' => 'nullable|integer|min:0|max:100',
            'value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $lead->update($data);
        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated!');
    }

    public function destroy(Lead $lead)
    {
        abort_if($lead->tenant_id !== $this->tid(), 403);
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }
}
