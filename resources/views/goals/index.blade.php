@extends('layouts.app')
@section('title', 'Goals')
@section('page-title', 'Goals & Targets')

@section('content')
<div class="page-header">
    <div>
        <h1>Goals &amp; Targets</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Goals</li>
        </ol></nav>
    </div>
    <a href="{{ route('goals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Goal</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-primary-subtle"><i class="bi bi-bullseye text-primary"></i></div>
        <div class="stat-value">{{ $stats['active'] }}</div><div class="stat-label">Active Goals</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-success-subtle"><i class="bi bi-trophy text-success"></i></div>
        <div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-label">Completed</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-info-subtle"><i class="bi bi-percent text-info"></i></div>
        <div class="stat-value">{{ round($stats['avg_progress']) }}%</div><div class="stat-label">Avg Progress</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-warning-subtle"><i class="bi bi-arrow-up-circle text-warning"></i></div>
        <div class="stat-value">{{ $stats['on_track'] }}</div><div class="stat-label">On Track</div>
    </div></div>
</div>

@if($goals->count())
<div class="row g-4">
    @foreach($goals as $goal)
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon bg-{{ $goal->progress_color }}-subtle" style="width:40px;height:40px;">
                            <i class="bi {{ $goal->type_icon }} text-{{ $goal->progress_color }}"></i>
                        </div>
                        <div>
                            <a href="{{ route('goals.show', $goal) }}" class="fw-600 text-decoration-none">{{ $goal->title }}</a>
                            <div class="text-muted small">{{ ucfirst($goal->period) }} · {{ ucwords(str_replace('_',' ',$goal->type)) }}</div>
                        </div>
                    </div>
                    <span class="badge bg-{{ $goal->status === 'active' ? 'primary' : ($goal->status === 'completed' ? 'success' : 'secondary') }}-subtle text-{{ $goal->status === 'active' ? 'primary' : ($goal->status === 'completed' ? 'success' : 'secondary') }}">{{ ucfirst($goal->status) }}</span>
                </div>

                <div class="mb-1 d-flex justify-content-between small">
                    <span class="text-muted">Progress</span>
                    <span class="fw-600">{{ $goal->progress_percent }}%</span>
                </div>
                <div class="progress mb-3" style="height:8px;">
                    <div class="progress-bar bg-{{ $goal->progress_color }}" style="width:{{ $goal->progress_percent }}%"></div>
                </div>

                <div class="d-flex justify-content-between small text-muted">
                    <span>{{ $goal->type === 'revenue' ? '$' : '' }}{{ number_format($goal->current_value, $goal->type === 'revenue' ? 2 : 0) }} current</span>
                    <span>{{ $goal->type === 'revenue' ? '$' : '' }}{{ number_format($goal->target_value, $goal->type === 'revenue' ? 2 : 0) }} target</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>{{ $goal->start_date->format('d M Y') }}</span>
                    <span>{{ $goal->end_date->format('d M Y') }}</span>
                </div>

                @if($goal->user)<div class="mt-2 small text-muted"><i class="bi bi-person me-1"></i>{{ $goal->user->name }}</div>@endif
            </div>
            <div class="card-footer bg-transparent d-flex gap-2">
                <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary flex-1">View</a>
                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card"><div class="card-body text-center py-5">
    <i class="bi bi-bullseye text-muted" style="font-size:3rem;"></i>
    <h5 class="mt-3 text-muted">No goals yet</h5>
    <a href="{{ route('goals.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus-lg me-1"></i>Create First Goal</a>
</div></div>
@endif
@endsection
