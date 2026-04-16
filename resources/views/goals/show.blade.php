@extends('layouts.app')
@section('title', $goal->title)
@section('page-title', 'Goal Details')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $goal->title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('goals.index') }}">Goals</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($goal->title, 30) }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('goals.edit', $goal) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
            @csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between fw-600">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi {{ $goal->type_icon }} text-{{ $goal->progress_color }} fs-5"></i>
                    <span>{{ ucwords(str_replace('_',' ',$goal->type)) }}</span>
                </div>
                <span class="badge bg-{{ $goal->status === 'active' ? 'primary' : ($goal->status === 'completed' ? 'success' : 'secondary') }}">{{ ucfirst($goal->status) }}</span>
            </div>
            <div class="card-body">
                @if($goal->description)<p class="text-muted">{{ $goal->description }}</p><hr>@endif

                <div class="text-center mb-4">
                    <div class="fw-700 mb-1" style="font-size:2.5rem;color:var(--bs-{{ $goal->progress_color }});">{{ $goal->progress_percent }}%</div>
                    <div class="text-muted small">Progress toward target</div>
                    <div class="progress my-3" style="height:16px;border-radius:8px;">
                        <div class="progress-bar bg-{{ $goal->progress_color }}" style="width:{{ $goal->progress_percent }}%;border-radius:8px;"></div>
                    </div>
                    <div class="row g-2 text-center">
                        <div class="col-6 col-md-3">
                            <div class="fw-700 fs-5">{{ $goal->type === 'revenue' ? '$' : '' }}{{ number_format($goal->current_value, $goal->type === 'revenue' ? 2 : 0) }}</div>
                            <div class="text-muted small">Current</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fw-700 fs-5">{{ $goal->type === 'revenue' ? '$' : '' }}{{ number_format($goal->target_value, $goal->type === 'revenue' ? 2 : 0) }}</div>
                            <div class="text-muted small">Target</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fw-700 fs-5">{{ max(0, $goal->target_value - $goal->current_value) > 0 ? ($goal->type === 'revenue' ? '$' : '') . number_format(max(0, $goal->target_value - $goal->current_value), $goal->type === 'revenue' ? 2 : 0) : '✓' }}</div>
                            <div class="text-muted small">Remaining</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fw-700 fs-5">{{ max(0, now()->diffInDays($goal->end_date, false)) }}</div>
                            <div class="text-muted small">Days Left</div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="row g-3">
                    <div class="col-md-4"><div class="text-muted small">Period</div><div class="fw-600">{{ ucfirst($goal->period) }}</div></div>
                    <div class="col-md-4"><div class="text-muted small">Start</div><div class="fw-600">{{ $goal->start_date->format('d M Y') }}</div></div>
                    <div class="col-md-4"><div class="text-muted small">End</div><div class="fw-600">{{ $goal->end_date->format('d M Y') }}</div></div>
                    @if($goal->user)
                    <div class="col-md-4"><div class="text-muted small">Assigned To</div><div class="fw-600">{{ $goal->user->name }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
