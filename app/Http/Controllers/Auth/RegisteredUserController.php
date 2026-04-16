<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create tenant
        $allowedPlans = ['free', 'starter', 'pro', 'enterprise'];
        $plan = in_array($request->input('plan'), $allowedPlans) ? $request->input('plan') : 'free';

        $slug = Str::slug($request->company_name) . '-' . Str::random(4);
        $tenant = Tenant::create([
            'name' => $request->company_name,
            'slug' => $slug,
            'email' => $request->email,
            'plan' => $plan,
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
        ]);
        $user->assignRole('tenant_admin');

        // Default deal pipeline stages
        $dealStages = [
            ['name' => 'Prospecting', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Qualification', 'color' => '#0d6efd', 'position' => 2],
            ['name' => 'Proposal', 'color' => '#ffc107', 'position' => 3],
            ['name' => 'Negotiation', 'color' => '#fd7e14', 'position' => 4],
            ['name' => 'Closed Won', 'color' => '#198754', 'position' => 5, 'is_won' => true],
            ['name' => 'Closed Lost', 'color' => '#dc3545', 'position' => 6, 'is_lost' => true],
        ];
        foreach ($dealStages as $stage) {
            PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'deal', ...$stage]);
        }

        // Default lead stages
        $leadStages = [
            ['name' => 'New', 'color' => '#6c757d', 'position' => 1],
            ['name' => 'Contacted', 'color' => '#0dcaf0', 'position' => 2],
            ['name' => 'Qualified', 'color' => '#198754', 'position' => 3],
        ];
        foreach ($leadStages as $stage) {
            PipelineStage::create(['tenant_id' => $tenant->id, 'type' => 'lead', ...$stage]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
