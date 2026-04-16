<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionPlan;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Commission::where('tenant_id', $this->tid())
            ->with(['user','deal','plan']);

        if ($uid = $request->user_id) $query->where('user_id', $uid);
        if ($status = $request->status) $query->where('status', $status);

        $commissions = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total_pending'  => Commission::where('tenant_id', $this->tid())->where('status','pending')->sum('amount'),
            'total_approved' => Commission::where('tenant_id', $this->tid())->where('status','approved')->sum('amount'),
            'total_paid'     => Commission::where('tenant_id', $this->tid())->where('status','paid')->sum('amount'),
            'count'          => Commission::where('tenant_id', $this->tid())->count(),
        ];

        $users = User::where('tenant_id', $this->tid())->get();
        $plans = CommissionPlan::where('tenant_id', $this->tid())->where('is_active', true)->get();

        // Rep earnings summary
        $repSummary = User::where('tenant_id', $this->tid())
            ->get()
            ->map(fn($u) => [
                'user'    => $u,
                'pending' => Commission::where('tenant_id', $this->tid())->where('user_id',$u->id)->where('status','pending')->sum('amount'),
                'paid'    => Commission::where('tenant_id', $this->tid())->where('user_id',$u->id)->where('status','paid')->sum('amount'),
                'count'   => Commission::where('tenant_id', $this->tid())->where('user_id',$u->id)->count(),
            ])
            ->filter(fn($r) => $r['count'] > 0);

        return view('commissions.index', compact('commissions','stats','users','plans','repSummary'));
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'plan_id' => 'required|exists:commission_plans,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $deal = Deal::where('tenant_id', $this->tid())->findOrFail($data['deal_id']);
        $plan = CommissionPlan::where('tenant_id', $this->tid())->findOrFail($data['plan_id']);

        $existing = Commission::where('deal_id', $deal->id)
            ->where('user_id', $data['user_id'])->first();
        if ($existing) {
            return back()->with('error', 'Commission already exists for this deal and user.');
        }

        $amount = $plan->calculate((float) $deal->value);

        Commission::create([
            'tenant_id'  => $this->tid(),
            'user_id'    => $data['user_id'],
            'deal_id'    => $deal->id,
            'plan_id'    => $plan->id,
            'deal_value' => $deal->value,
            'amount'     => $amount,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Commission of $' . number_format($amount, 2) . ' calculated!');
    }

    public function approve(Commission $commission)
    {
        abort_if($commission->tenant_id !== $this->tid(), 403);
        $commission->update(['status' => 'approved']);
        return back()->with('success', 'Commission approved.');
    }

    public function markPaid(Commission $commission)
    {
        abort_if($commission->tenant_id !== $this->tid(), 403);
        $commission->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Commission marked as paid.');
    }

    public function destroy(Commission $commission)
    {
        abort_if($commission->tenant_id !== $this->tid(), 403);
        $commission->delete();
        return back()->with('success', 'Commission deleted.');
    }

    // Plans
    public function plans()
    {
        $plans = CommissionPlan::where('tenant_id', $this->tid())->latest()->get();
        return view('commissions.plans', compact('plans'));
    }

    public function planCreate()
    {
        return view('commissions.plan-create');
    }

    public function planStore(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'type'          => 'required|in:flat,percentage,tiered',
            'rate'          => 'required|numeric|min:0',
            'min_deal_value'=> 'nullable|numeric|min:0',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        $data['is_active']  = true;
        CommissionPlan::create($data);
        return redirect()->route('commissions.plans')->with('success', 'Plan created!');
    }

    public function planEdit(CommissionPlan $plan)
    {
        abort_if($plan->tenant_id !== $this->tid(), 403);
        return view('commissions.plan-edit', compact('plan'));
    }

    public function planUpdate(Request $request, CommissionPlan $plan)
    {
        abort_if($plan->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'name'           => 'required|string|max:200',
            'type'           => 'required|in:flat,percentage,tiered',
            'rate'           => 'required|numeric|min:0',
            'min_deal_value' => 'nullable|numeric|min:0',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $plan->update($data);
        return redirect()->route('commissions.plans')->with('success', 'Plan updated!');
    }

    public function planDestroy(CommissionPlan $plan)
    {
        abort_if($plan->tenant_id !== $this->tid(), 403);
        $plan->delete();
        return redirect()->route('commissions.plans')->with('success', 'Plan deleted.');
    }
}
