<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\SalesQuota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $period = $request->get('period', now()->format('Y-m'));
        $year   = substr($period, 0, 4);
        $month  = substr($period, 5, 2);

        // Open deals in the period's close window
        $openDeals = Deal::where('tenant_id', $this->tid())
            ->where('status', 'open')
            ->with(['stage', 'assignedTo', 'contact'])
            ->orderByDesc('value')
            ->get();

        // Weighted pipeline value
        $weightedTotal = $openDeals->sum(fn($d) => $d->value * ($d->probability / 100));

        // Won deals this period
        $wonDeals = Deal::where('tenant_id', $this->tid())
            ->where('status', 'won')
            ->whereYear('expected_close_date', $year)
            ->whereMonth('expected_close_date', $month)
            ->get();
        $wonValue = $wonDeals->sum('value');

        // Tenant-wide quota for this period
        $quota = SalesQuota::where('tenant_id', $this->tid())
            ->whereNull('user_id')
            ->where('period', $period)
            ->first();
        $quotaAmount = $quota ? $quota->amount : 0;

        // Per-rep breakdown
        $users = User::where('tenant_id', $this->tid())->get();
        $repData = $users->map(function ($user) use ($openDeals, $wonDeals, $period) {
            $myOpen = $openDeals->where('assigned_to', $user->id);
            $myWon  = $wonDeals->where('assigned_to', $user->id);
            $quota  = SalesQuota::where('tenant_id', $this->tid())
                ->where('user_id', $user->id)
                ->where('period', $period)
                ->first();
            return [
                'user'     => $user,
                'pipeline' => $myOpen->sum('value'),
                'weighted' => $myOpen->sum(fn($d) => $d->value * ($d->probability / 100)),
                'won'      => $myWon->sum('value'),
                'quota'    => $quota ? $quota->amount : 0,
                'deals'    => $myOpen->count(),
            ];
        });

        // Monthly won trend (last 6 months)
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $trend[] = [
                'label' => $m->format('M Y'),
                'value' => Deal::where('tenant_id', $this->tid())
                    ->where('status', 'won')
                    ->whereYear('expected_close_date', $m->year)
                    ->whereMonth('expected_close_date', $m->month)
                    ->sum('value'),
            ];
        }

        // Stage funnel
        $stages = PipelineStage::where('tenant_id', $this->tid())
            ->where('type', 'deal')
            ->orderBy('position')
            ->get()
            ->map(function ($stage) use ($openDeals) {
                $stageDeals = $openDeals->where('stage_id', $stage->id);
                return [
                    'name'     => $stage->name,
                    'color'    => $stage->color,
                    'count'    => $stageDeals->count(),
                    'value'    => $stageDeals->sum('value'),
                    'weighted' => $stageDeals->sum(fn($d) => $d->value * ($d->probability / 100)),
                ];
            });

        // Months for period selector (last 12 + next 3)
        $periods = [];
        for ($i = 11; $i >= -3; $i--) {
            $m = now()->subMonths($i);
            $periods[$m->format('Y-m')] = $m->format('M Y');
        }

        return view('forecasting.index', compact(
            'period','openDeals','weightedTotal','wonDeals','wonValue',
            'quotaAmount','repData','trend','stages','periods','quota','users'
        ));
    }

    public function setQuota(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'period'  => 'required|regex:/^\d{4}-\d{2}$/',
            'amount'  => 'required|numeric|min:0',
        ]);

        SalesQuota::updateOrCreate(
            [
                'tenant_id' => $this->tid(),
                'user_id'   => $data['user_id'] ?: null,
                'period'    => $data['period'],
            ],
            ['amount' => $data['amount']]
        );

        return back()->with('success', 'Quota updated!');
    }
}
