<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\User;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Contact;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index()
    {
        $goals = Goal::where('tenant_id', $this->tid())
            ->with('user')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'paused' THEN 1 ELSE 2 END")
            ->orderBy('end_date')
            ->get();

        foreach ($goals as $goal) {
            $goal->current_value = $this->calculateProgress($goal);
            if ($goal->isDirty()) $goal->save();
        }

        $stats = [
            'active'    => $goals->where('status', 'active')->count(),
            'completed' => $goals->where('status', 'completed')->count(),
            'avg_progress' => $goals->where('status', 'active')->avg(fn($g) => $g->progress_percent) ?? 0,
            'on_track' => $goals->where('status', 'active')->filter(fn($g) => $g->progress_percent >= 50)->count(),
        ];

        return view('goals.index', compact('goals', 'stats'));
    }

    public function create()
    {
        $users = User::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('goals.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'user_id'      => 'nullable|exists:users,id',
            'type'         => 'required|in:revenue,deals_won,leads_created,contacts_added,calls_made,demos_scheduled',
            'period'       => 'required|in:monthly,quarterly,yearly,custom',
            'target_value' => 'required|numeric|min:0',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'status'       => 'required|in:active,completed,failed,paused',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = auth()->id();
        $data['current_value'] = 0;
        $goal = Goal::create($data);
        return redirect()->route('goals.show', $goal)->with('success', "Goal '{$goal->title}' created.");
    }

    public function show(Goal $goal)
    {
        abort_if($goal->tenant_id !== $this->tid(), 403);
        $goal->load('user');
        $goal->current_value = $this->calculateProgress($goal);
        $goal->save();
        return view('goals.show', compact('goal'));
    }

    public function edit(Goal $goal)
    {
        abort_if($goal->tenant_id !== $this->tid(), 403);
        $users = User::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('goals.edit', compact('goal', 'users'));
    }

    public function update(Request $request, Goal $goal)
    {
        abort_if($goal->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'user_id'      => 'nullable|exists:users,id',
            'type'         => 'required|in:revenue,deals_won,leads_created,contacts_added,calls_made,demos_scheduled',
            'period'       => 'required|in:monthly,quarterly,yearly,custom',
            'target_value' => 'required|numeric|min:0',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'status'       => 'required|in:active,completed,failed,paused',
        ]);
        $goal->update($data);
        return redirect()->route('goals.show', $goal)->with('success', 'Goal updated.');
    }

    public function destroy(Goal $goal)
    {
        abort_if($goal->tenant_id !== $this->tid(), 403);
        $goal->delete();
        return redirect()->route('goals.index')->with('success', 'Goal deleted.');
    }

    private function calculateProgress(Goal $goal): float
    {
        $uid = $goal->user_id;
        $tid = $goal->tenant_id;

        return match($goal->type) {
            'revenue'        => Deal::where('tenant_id', $tid)->when($uid, fn($q) => $q->where('assigned_to', $uid))
                ->where('status','won')
                ->whereBetween('updated_at', [$goal->start_date, $goal->end_date])
                ->sum('value'),
            'deals_won'      => Deal::where('tenant_id', $tid)->when($uid, fn($q) => $q->where('assigned_to', $uid))
                ->where('status','won')
                ->whereBetween('updated_at', [$goal->start_date, $goal->end_date])
                ->count(),
            'leads_created'  => Lead::where('tenant_id', $tid)->when($uid, fn($q) => $q->where('assigned_to', $uid))
                ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                ->count(),
            'contacts_added' => Contact::where('tenant_id', $tid)->when($uid, fn($q) => $q->where('assigned_to', $uid))
                ->whereBetween('created_at', [$goal->start_date, $goal->end_date])
                ->count(),
            default => $goal->current_value,
        };
    }
}
