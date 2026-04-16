<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }

        $tid  = Auth::user()->tenant_id;
        $now  = now();

        // ── Core KPI stats ────────────────────────────────────────────────
        $totalContacts  = Contact::where('tenant_id', $tid)->count();
        $newContacts    = Contact::where('tenant_id', $tid)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();

        $activeLeads    = Lead::where('tenant_id', $tid)->whereNotIn('status', ['lost', 'converted'])->count();
        $newLeads       = Lead::where('tenant_id', $tid)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();

        $openDeals      = Deal::where('tenant_id', $tid)->where('status', 'open')->count();
        $pipelineValue  = Deal::where('tenant_id', $tid)->where('status', 'open')->sum('value');
        $wonDeals       = Deal::where('tenant_id', $tid)->where('status', 'won')->count();
        $lostDeals      = Deal::where('tenant_id', $tid)->where('status', 'lost')->count();
        $winRate        = ($wonDeals + $lostDeals) > 0 ? round($wonDeals / ($wonDeals + $lostDeals) * 100) : 0;

        $revenueTotal   = Invoice::where('tenant_id', $tid)->where('status', 'paid')->sum('total');
        $revenueMonth   = Invoice::where('tenant_id', $tid)->where('status', 'paid')->whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year)->sum('total');

        $overdueTasks   = Task::where('tenant_id', $tid)->whereNotIn('status', ['completed'])->whereDate('due_date', '<', today())->count();

        $stats = compact(
            'totalContacts', 'newContacts',
            'activeLeads', 'newLeads',
            'openDeals', 'pipelineValue',
            'wonDeals', 'winRate',
            'revenueTotal', 'revenueMonth',
            'overdueTasks'
        );

        // ── Tasks: overdue → today → upcoming ─────────────────────────────
        $tasks = Task::where('tenant_id', $tid)
            ->whereNotIn('status', ['completed'])
            ->orderByRaw("CASE WHEN due_date < date('now') THEN 0 WHEN due_date = date('now') THEN 1 ELSE 2 END")
            ->orderBy('due_date')
            ->take(6)
            ->get();

        // ── Top open deals by value ───────────────────────────────────────
        $topDeals = Deal::where('tenant_id', $tid)
            ->where('status', 'open')
            ->with(['contact', 'stage'])
            ->orderByDesc('value')
            ->take(6)
            ->get();

        // ── Recent activity ───────────────────────────────────────────────
        $recentActivities = Activity::where('tenant_id', $tid)
            ->with('user')
            ->latest()
            ->take(7)
            ->get();

        // ── Pipeline stage breakdown (doughnut) ───────────────────────────
        $pipelineData = PipelineStage::where('tenant_id', $tid)
            ->where('type', 'deal')
            ->orderBy('position')
            ->get()
            ->map(fn($s) => [
                'name'  => $s->name,
                'color' => $s->color,
                'count' => $s->deals()->count(),
                'value' => $s->deals()->sum('value'),
            ])
            ->filter(fn($s) => $s['count'] > 0)
            ->values();

        // ── Revenue chart — last 6 months ─────────────────────────────────
        $chartMonths   = collect();
        $chartRevenues = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $chartMonths->push($date->format('M Y'));
            $chartRevenues->push(
                Invoice::where('tenant_id', $tid)
                    ->where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('total')
            );
        }
        $chartData = ['months' => $chartMonths, 'revenues' => $chartRevenues];

        $showOnboarding = is_null(Auth::user()->onboarding_completed_at);

        return view('dashboard', compact(
            'stats', 'tasks', 'topDeals',
            'recentActivities', 'pipelineData',
            'chartData', 'showOnboarding'
        ));
    }
}
