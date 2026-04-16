<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'users' => User::count(),
            'contacts' => Contact::count(),
            'leads' => Lead::count(),
            'deals' => Deal::count(),
            'revenue' => Invoice::where('status', 'paid')->sum('total'),
        ];
        $tenants = Tenant::withCount(['users', 'contacts', 'leads', 'deals'])->latest()->take(10)->get();
        $recentUsers = User::with('tenant', 'roles')->latest()->take(8)->get();
        return view('admin.dashboard', compact('stats', 'tenants', 'recentUsers'));
    }

    public function tenants(Request $request)
    {
        $query = Tenant::withCount(['users', 'contacts', 'deals']);
        if ($request->search) $query->where('name', 'like', '%'.$request->search.'%');
        if ($request->status) $query->where('status', $request->status);
        if ($request->plan) $query->where('plan', $request->plan);
        $tenants = $query->latest()->paginate(20)->withQueryString();
        return view('admin.tenants', compact('tenants'));
    }

    public function editTenant(Tenant $tenant)
    {
        return view('admin.tenant-edit', compact('tenant'));
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'plan' => 'required|in:free,starter,pro,enterprise',
            'status' => 'required|in:active,suspended,cancelled',
            'max_users' => 'required|integer|min:1',
            'max_contacts' => 'required|integer|min:1',
        ]);
        $tenant->update($data);
        return redirect()->route('admin.tenants')->with('success', 'Tenant updated!');
    }

    public function users(Request $request)
    {
        $query = User::with(['tenant', 'roles']);
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%"));
        }
        $users = $query->latest()->paginate(20)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function createTenant()
    {
        return view('admin.tenant-create');
    }

    public function storeTenant(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:200',
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'plan' => 'required|in:free,starter,pro,enterprise',
        ]);

        $slug = Str::slug($data['company_name']) . '-' . Str::random(4);
        $tenant = Tenant::create([
            'name' => $data['company_name'],
            'slug' => $slug,
            'email' => $data['email'],
            'plan' => $data['plan'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $tenant->id,
        ]);
        $user->assignRole('tenant_admin');

        // Create default pipeline stages
        $dealStages = [
            ['name' => 'Prospecting', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Qualification', 'color' => '#0d6efd', 'position' => 2],
            ['name' => 'Proposal', 'color' => '#ffc107', 'position' => 3],
            ['name' => 'Negotiation', 'color' => '#fd7e14', 'position' => 4],
            ['name' => 'Closed Won', 'color' => '#198754', 'position' => 5, 'is_won' => true],
            ['name' => 'Closed Lost', 'color' => '#dc3545', 'position' => 6, 'is_lost' => true],
        ];
        foreach ($dealStages as $stage) {
            \App\Models\PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'deal', ...$stage]);
        }
        $leadStages = [
            ['name' => 'New', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Contacted', 'color' => '#0dcaf0', 'position' => 2],
            ['name' => 'Qualified', 'color' => '#198754', 'position' => 3],
        ];
        foreach ($leadStages as $stage) {
            \App\Models\PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'lead', ...$stage]);
        }

        return redirect()->route('admin.tenants')->with('success', "Tenant '{$tenant->name}' created!");
    }
}
