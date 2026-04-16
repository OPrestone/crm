@extends('layouts.app')

@section('title', 'Plugins — ' . $tenant->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-700 mb-0">{{ $tenant->name }} — Plugins</h1>
        <p class="text-muted mb-0">Enable or disable modules for this tenant. Overrides apply on top of their plan.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.plugins.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-puzzle me-1"></i> All Plugins
        </a>
        <a href="{{ route('admin.tenants') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Tenants
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Tenant Info + Plan Selector --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-700 text-muted text-uppercase small mb-3">Tenant Info</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar-circle bg-primary-subtle text-primary fw-700" style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem">
                        {{ substr($tenant->name,0,1) }}
                    </div>
                    <div>
                        <div class="fw-700">{{ $tenant->name }}</div>
                        <div class="text-muted small">{{ $tenant->email }}</div>
                    </div>
                </div>
                @php $planColors = ['free'=>'secondary','starter'=>'info','pro'=>'primary','enterprise'=>'danger']; @endphp
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-{{ $planColors[$tenant->plan] ?? 'secondary' }} fs-6 px-3 py-2">
                        {{ ucfirst($tenant->plan) }} Plan
                    </span>
                    <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $tenant->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-700 text-muted text-uppercase small mb-3">Change Subscription Plan</h6>
                <p class="text-muted small mb-3">Changing the plan resets all non-override plugin assignments to match the new plan's defaults.</p>
                <form method="POST" action="{{ route('admin.plugins.plan', $tenant) }}" class="d-flex gap-2 align-items-end">
                    @csrf
                    <div class="flex-grow-1">
                        <label class="form-label small fw-600">New Plan</label>
                        <select name="plan" class="form-select">
                            @foreach($plans as $p)
                            <option value="{{ $p }}" @selected($tenant->plan === $p)>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Change plan? This will reset plan-based plugin assignments.')">
                        <i class="bi bi-arrow-repeat me-1"></i> Apply Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Plugin Grid --}}
<div class="row g-3">
@foreach($pluginData as $item)
@php
    $plugin   = $item->plugin;
    $isEnabled = $item->status === 'enabled';
    $source   = $item->source;
    $planColors = ['free'=>'secondary','starter'=>'info','pro'=>'primary','enterprise'=>'danger'];
    $sourceBadge = match($source) {
        'plan'        => '<span class="badge bg-success-subtle text-success small"><i class="bi bi-award me-1"></i>From Plan</span>',
        'manual'      => '<span class="badge bg-warning-subtle text-warning small"><i class="bi bi-hand-index me-1"></i>Manual Override</span>',
        'unavailable' => '<span class="badge bg-secondary-subtle text-secondary small"><i class="bi bi-lock me-1"></i>Not in Plan</span>',
        default       => '',
    };
@endphp
<div class="col-md-6 col-lg-4">
    <div class="card h-100 {{ $isEnabled ? '' : 'opacity-75' }}">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box-sm rounded bg-{{ $plugin->color }}-subtle">
                        <i class="bi {{ $plugin->icon }} text-{{ $plugin->color }}"></i>
                    </div>
                    <div>
                        <div class="fw-700">{{ $plugin->name }}</div>
                        <span class="badge bg-{{ $planColors[$plugin->min_plan] ?? 'secondary' }}-subtle text-{{ $planColors[$plugin->min_plan] ?? 'secondary' }} small">
                            {{ ucfirst($plugin->min_plan) }}+
                        </span>
                    </div>
                </div>
                <div>
                    @if($isEnabled)
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Enabled</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Disabled</span>
                    @endif
                </div>
            </div>

            <p class="text-muted small mb-3">{{ $plugin->description }}</p>

            {!! $sourceBadge !!}

            <div class="d-flex gap-2 mt-3">
                @if(!$isEnabled)
                <form method="POST" action="{{ route('admin.plugins.toggle', [$tenant, $plugin]) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="enable">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-toggle-on me-1"></i> Enable
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.plugins.toggle', [$tenant, $plugin]) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="disable">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-toggle-off me-1"></i> Disable
                    </button>
                </form>
                @endif

                @if($item->has_override)
                <form method="POST" action="{{ route('admin.plugins.toggle', [$tenant, $plugin]) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Reset to plan default">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
</div>
@endsection
