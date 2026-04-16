<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $tid = Auth::user()->tenant_id;

        $query = AuditLog::where('tenant_id', $tid)
            ->with('user')
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('event')) {
            $query->where('event', 'like', '%' . $request->event . '%');
        }
        if ($request->filled('resource')) {
            $query->where('auditable_type', 'like', '%' . $request->resource . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs  = $query->paginate(50)->withQueryString();
        $users = \App\Models\User::where('tenant_id', $tid)->get();

        $stats = [
            'today'      => AuditLog::where('tenant_id', $tid)->whereDate('created_at', today())->count(),
            'this_week'  => AuditLog::where('tenant_id', $tid)->whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'total'      => AuditLog::where('tenant_id', $tid)->count(),
            'users_active' => AuditLog::where('tenant_id', $tid)->whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        return view('audit-log.index', compact('logs', 'users', 'stats'));
    }
}
