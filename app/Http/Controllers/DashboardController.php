<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }

        $tenantId = Auth::user()->tenant_id;

        $stats = [
            'contacts' => Contact::where('tenant_id', $tenantId)->count(),
            'new_contacts' => Contact::where('tenant_id', $tenantId)->whereMonth('created_at', now()->month)->count(),
            'leads' => Lead::where('tenant_id', $tenantId)->whereNotIn('status', ['lost', 'converted'])->count(),
            'new_leads' => Lead::where('tenant_id', $tenantId)->whereMonth('created_at', now()->month)->count(),
            'deals' => Deal::where('tenant_id', $tenantId)->where('status', 'open')->count(),
            'won_deals' => Deal::where('tenant_id', $tenantId)->where('status', 'won')->count(),
            'revenue' => Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->sum('total'),
            'monthly_revenue' => Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->whereMonth('paid_at', now()->month)->sum('total'),
        ];

        $recentTasks = Task::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->take(5)->get();

        $recentLeads = Lead::where('tenant_id', $tenantId)
            ->with('contact')
            ->latest()->take(5)->get();

        $recentActivities = Activity::where('tenant_id', $tenantId)
            ->with('user')
            ->latest()->take(6)->get();

        $pipelineData = PipelineStage::where('tenant_id', $tenantId)
            ->where('type', 'deal')
            ->orderBy('position')
            ->get()
            ->map(fn($s) => [
                'name' => $s->name,
                'color' => $s->color,
                'count' => $s->deals()->count(),
            ]);

        // Revenue chart - last 6 months
        $months = collect();
        $revenues = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));
            $revenues->push(Invoice::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('total'));
        }

        $chartData = ['months' => $months, 'revenues' => $revenues];

        return view('dashboard', compact('stats', 'recentTasks', 'recentLeads', 'recentActivities', 'pipelineData', 'chartData'));
    }
}
