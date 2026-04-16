<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Deal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Contract::where('tenant_id', $this->tid())->with(['contact','deal']);
        if ($s = $request->search) {
            $query->where(fn($q) => $q->where('title','like',"%$s%")->orWhere('contract_number','like',"%$s%"));
        }
        if ($status = $request->status) $query->where('status', $status);
        $contracts = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => Contract::where('tenant_id', $this->tid())->count(),
            'draft'    => Contract::where('tenant_id', $this->tid())->where('status','draft')->count(),
            'pending'  => Contract::where('tenant_id', $this->tid())->where('status','pending_signature')->count(),
            'signed'   => Contract::where('tenant_id', $this->tid())->where('status','signed')->count(),
            'total_value' => Contract::where('tenant_id', $this->tid())->where('status','signed')->sum('value'),
        ];

        return view('contracts.index', compact('contracts','stats'));
    }

    public function create(Request $request)
    {
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $deals     = Deal::where('tenant_id', $this->tid())->orderBy('title')->get();
        $templates = ContractTemplate::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('contracts.create', compact('contacts','deals','templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:300',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'template_id' => 'nullable|exists:contract_templates,id',
            'content'     => 'required|string',
            'value'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,pending_signature,signed,expired,cancelled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'signed_at'   => 'nullable|date',
            'signed_by'   => 'nullable|string|max:200',
            'notes'       => 'nullable|string',
        ]);

        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();

        $contract = Contract::create($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract created!');
    }

    public function show(Contract $contract)
    {
        abort_if($contract->tenant_id !== $this->tid(), 403);
        $contract->load(['contact','deal','template','creator']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        abort_if($contract->tenant_id !== $this->tid(), 403);
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $deals     = Deal::where('tenant_id', $this->tid())->orderBy('title')->get();
        $templates = ContractTemplate::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('contracts.edit', compact('contract','contacts','deals','templates'));
    }

    public function update(Request $request, Contract $contract)
    {
        abort_if($contract->tenant_id !== $this->tid(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:300',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'template_id' => 'nullable|exists:contract_templates,id',
            'content'     => 'required|string',
            'value'       => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,pending_signature,signed,expired,cancelled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'signed_at'   => 'nullable|date',
            'signed_by'   => 'nullable|string|max:200',
            'notes'       => 'nullable|string',
        ]);

        $contract->update($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated!');
    }

    public function destroy(Contract $contract)
    {
        abort_if($contract->tenant_id !== $this->tid(), 403);
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Contract deleted.');
    }

    public function pdf(Contract $contract)
    {
        abort_if($contract->tenant_id !== $this->tid(), 403);
        $contract->load(['contact','deal','creator']);
        $pdf = Pdf::loadView('contracts.pdf', compact('contract'));
        return $pdf->download('contract-' . $contract->contract_number . '.pdf');
    }

    // Templates
    public function templates()
    {
        $templates = ContractTemplate::where('tenant_id', $this->tid())->latest()->paginate(15);
        return view('contracts.templates', compact('templates'));
    }

    public function templateCreate()
    {
        return view('contracts.template-create');
    }

    public function templateStore(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:200',
            'content' => 'required|string',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        ContractTemplate::create($data);
        return redirect()->route('contracts.templates')->with('success', 'Template created!');
    }

    public function templateEdit(ContractTemplate $template)
    {
        abort_if($template->tenant_id !== $this->tid(), 403);
        return view('contracts.template-edit', compact('template'));
    }

    public function templateUpdate(Request $request, ContractTemplate $template)
    {
        abort_if($template->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'name'    => 'required|string|max:200',
            'content' => 'required|string',
        ]);
        $template->update($data);
        return redirect()->route('contracts.templates')->with('success', 'Template updated!');
    }

    public function templateDestroy(ContractTemplate $template)
    {
        abort_if($template->tenant_id !== $this->tid(), 403);
        $template->delete();
        return redirect()->route('contracts.templates')->with('success', 'Template deleted.');
    }

    public function templateContent(ContractTemplate $template)
    {
        abort_if($template->tenant_id !== $this->tid(), 403);
        return response()->json(['content' => $template->content]);
    }
}
