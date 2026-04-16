<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $view = $request->get('view', 'list');
        $query = Deal::where('tenant_id', $this->tid())->with(['contact', 'company', 'stage', 'assignedTo']);
        if ($request->search) $query->where('title', 'like', '%'.$request->search.'%');
        if ($request->status) $query->where('status', $request->status);

        if ($view === 'kanban') {
            $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'deal')->orderBy('position')->get();
            $deals = $query->get()->groupBy('stage_id');
            return view('deals.kanban', compact('stages', 'deals'));
        }

        $deals = $query->latest()->paginate(15)->withQueryString();
        $totalValue = Deal::where('tenant_id', $this->tid())->where('status', 'open')->sum('value');
        return view('deals.index', compact('deals', 'totalValue'));
    }

    public function create()
    {
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'deal')->orderBy('position')->get();
        $users = User::where('tenant_id', $this->tid())->get();
        return view('deals.create', compact('contacts', 'companies', 'stages', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'status' => 'required|in:open,won,lost',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        $deal = Deal::create($data);
        Activity::create([
            'tenant_id' => $this->tid(), 'user_id' => Auth::id(),
            'type' => 'created', 'subject' => 'Deal created: ' . $deal->title,
            'activityable_id' => $deal->id, 'activityable_type' => Deal::class,
        ]);
        return redirect()->route('deals.show', $deal)->with('success', 'Deal created!');
    }

    public function show(Deal $deal)
    {
        abort_if($deal->tenant_id !== $this->tid(), 403);
        $deal->load(['contact', 'company', 'stage', 'assignedTo', 'creator', 'tasks', 'activities.user']);
        return view('deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        abort_if($deal->tenant_id !== $this->tid(), 403);
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $stages = PipelineStage::where('tenant_id', $this->tid())->where('type', 'deal')->orderBy('position')->get();
        $users = User::where('tenant_id', $this->tid())->get();
        return view('deals.edit', compact('deal', 'contacts', 'companies', 'stages', 'users'));
    }

    public function update(Request $request, Deal $deal)
    {
        abort_if($deal->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'stage_id' => 'nullable|exists:pipeline_stages,id',
            'value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'status' => 'required|in:open,won,lost',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $deal->update($data);
        return redirect()->route('deals.show', $deal)->with('success', 'Deal updated!');
    }

    public function destroy(Deal $deal)
    {
        abort_if($deal->tenant_id !== $this->tid(), 403);
        $deal->delete();
        return redirect()->route('deals.index')->with('success', 'Deal deleted.');
    }
}
