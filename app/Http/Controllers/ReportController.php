<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index()
    {
        $tenantId = $this->tid();

        $salesData = [];
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $salesData[] = Invoice::where('tenant_id', $tenantId)->where('status', 'paid')
                ->whereYear('paid_at', $date->year)->whereMonth('paid_at', $date->month)->sum('total');
        }

        $leadConversion = [
            'total' => Lead::where('tenant_id', $tenantId)->count(),
            'converted' => Lead::where('tenant_id', $tenantId)->where('status', 'converted')->count(),
            'lost' => Lead::where('tenant_id', $tenantId)->where('status', 'lost')->count(),
        ];
        $leadConversion['rate'] = $leadConversion['total'] > 0
            ? round($leadConversion['converted'] / $leadConversion['total'] * 100, 1) : 0;

        $dealStats = [
            'total_value' => Deal::where('tenant_id', $tenantId)->sum('value'),
            'won_value' => Deal::where('tenant_id', $tenantId)->where('status', 'won')->sum('value'),
            'avg_value' => Deal::where('tenant_id', $tenantId)->avg('value') ?? 0,
        ];

        $contactSources = Contact::where('tenant_id', $tenantId)
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        $taskCompletion = [
            'total' => Task::where('tenant_id', $tenantId)->count(),
            'completed' => Task::where('tenant_id', $tenantId)->where('status', 'completed')->count(),
            'overdue' => Task::where('tenant_id', $tenantId)->where('due_date', '<', now())->where('status', '!=', 'completed')->count(),
        ];

        return view('reports.index', compact('salesData', 'months', 'leadConversion', 'dealStats', 'contactSources', 'taskCompletion'));
    }

    public function pdf()
    {
        $tenantId = $this->tid();
        $tenant = Auth::user()->tenant;
        $data = [
            'contacts' => Contact::where('tenant_id', $tenantId)->count(),
            'leads' => Lead::where('tenant_id', $tenantId)->count(),
            'deals' => Deal::where('tenant_id', $tenantId)->count(),
            'revenue' => Invoice::where('tenant_id', $tenantId)->where('status', 'paid')->sum('total'),
        ];
        $pdf = Pdf::loadView('reports.pdf', compact('data', 'tenant'));
        return $pdf->stream('crm-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
