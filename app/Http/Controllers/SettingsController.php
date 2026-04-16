<?php

namespace App\Http\Controllers;

use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index()
    {
        $tenant = Auth::user()->tenant;
        $users = User::where('tenant_id', $this->tid())->with('roles')->get();
        $stages = PipelineStage::where('tenant_id', $this->tid())->orderBy('position')->get();
        $roles = Role::whereIn('name', ['tenant_admin', 'manager', 'staff'])->get();
        return view('settings.index', compact('tenant', 'users', 'stages', 'roles'));
    }

    public function updateTenant(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|url',
            'industry' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'timezone' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
        ]);
        $tenant->update($data);
        return redirect()->route('settings.index')->with('success', 'Company settings updated!');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:tenant_admin,manager,staff',
            'job_title' => 'nullable|string|max:100',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $this->tid(),
            'job_title' => $data['job_title'] ?? null,
        ]);
        $user->assignRole($data['role']);
        return redirect()->route('settings.index')->with('success', 'User created!');
    }

    public function destroyUser(User $user)
    {
        abort_if($user->tenant_id !== $this->tid(), 403);
        abort_if($user->id === Auth::id(), 403);
        $user->delete();
        return redirect()->route('settings.index')->with('success', 'User removed.');
    }

    public function storeStage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:deal,lead',
            'color' => 'required|string|max:20',
            'is_won' => 'nullable|boolean',
            'is_lost' => 'nullable|boolean',
        ]);
        $position = PipelineStage::where('tenant_id', $this->tid())->where('type', $data['type'])->max('position') + 1;
        PipelineStage::create([
            ...$data, 'tenant_id' => $this->tid(), 'position' => $position,
            'is_won' => $request->boolean('is_won'),
            'is_lost' => $request->boolean('is_lost'),
        ]);
        return redirect()->route('settings.index')->with('success', 'Stage added!');
    }

    public function destroyStage(PipelineStage $stage)
    {
        abort_if($stage->tenant_id !== $this->tid(), 403);
        $stage->delete();
        return redirect()->route('settings.index')->with('success', 'Stage removed.');
    }
}
