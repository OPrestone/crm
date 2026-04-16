<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Company::where('tenant_id', $this->tid())->withCount(['contacts', 'deals']);
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")->orWhere('industry', 'like', "%$q%"));
        }
        if ($request->industry) $query->where('industry', $request->industry);
        $companies = $query->latest()->paginate(15)->withQueryString();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:200',
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'annual_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        $company = Company::create($data);
        Activity::create([
            'tenant_id' => $this->tid(), 'user_id' => Auth::id(),
            'type' => 'created', 'subject' => 'Company created: ' . $company->name,
            'activityable_id' => $company->id, 'activityable_type' => Company::class,
        ]);
        return redirect()->route('companies.show', $company)->with('success', 'Company created!');
    }

    public function show(Company $company)
    {
        abort_if($company->tenant_id !== $this->tid(), 403);
        $company->load(['contacts', 'deals.stage', 'leads', 'invoices']);
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        abort_if($company->tenant_id !== $this->tid(), 403);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        abort_if($company->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:200',
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'annual_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $company->update($data);
        return redirect()->route('companies.show', $company)->with('success', 'Company updated!');
    }

    public function destroy(Company $company)
    {
        abort_if($company->tenant_id !== $this->tid(), 403);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
