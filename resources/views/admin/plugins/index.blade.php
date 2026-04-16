@extends('layouts.app')

@section('title', 'Plugin Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-700 mb-0">Plugin Management</h1>
        <p class="text-muted mb-0">Configure which modules are available on each subscription plan.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Admin
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Plan Overview --}}
<div class="row g-3 mb-4">
    @foreach(['free' => ['secondary','Free','Basic CRM for small teams'], 'starter' => ['info','Starter','Core sales pipeline'], 'pro' => ['primary','Pro','Advanced tools & AI'], 'enterprise' => ['danger','Enterprise','Full compliance suite']] as $plan => [$color, $label, $desc])
    <div class="col-md-3">
        <div class="card border-{{ $color }} h-100">
            <div class="card-header bg-{{ $color }} bg-opacity-10 border-bottom border-{{ $color }}">
                <h6 class="mb-0 text-{{ $color }} fw-700">{{ $label }}</h6>
                <small class="text-muted">{{ $desc }}</small>
            </div>
            <div class="card-body p-2">
                @foreach($plugins->where('active', true) as $plugin)
                    @if($plugin->isIncludedInPlan($plan))
                    <div class="d-flex align-items-center gap-2 py-1 px-2 rounded mb-1 bg-{{ $color }}-subtle">
                        <i class="bi {{ $plugin->icon }} text-{{ $color }} small"></i>
                        <span class="small fw-500">{{ $plugin->name }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- All Plugins Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-700">All Plugins</h5>
        <span class="badge bg-primary-subtle text-primary">{{ $plugins->count() }} total</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Plugin</th>
                    <th>Description</th>
                    <th>Min Plan</th>
                    <th>Core</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($plugins as $plugin)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-sm bg-{{ $plugin->color }}-subtle rounded">
                            <i class="bi {{ $plugin->icon }} text-{{ $plugin->color }}"></i>
                        </div>
                        <div>
                            <div class="fw-600">{{ $plugin->name }}</div>
                            <code class="small text-muted">{{ $plugin->slug }}</code>
                        </div>
                    </div>
                </td>
                <td class="text-muted small">{{ $plugin->description }}</td>
                <td>
                    @php $planColors = ['free'=>'secondary','starter'=>'info','pro'=>'primary','enterprise'=>'danger']; @endphp
                    <span class="badge bg-{{ $planColors[$plugin->min_plan] ?? 'secondary' }}-subtle text-{{ $planColors[$plugin->min_plan] ?? 'secondary' }}">
                        {{ ucfirst($plugin->min_plan) }}
                    </span>
                </td>
                <td>
                    @if($plugin->is_core)
                        <span class="badge bg-success-subtle text-success"><i class="bi bi-shield-fill me-1"></i>Core</span>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                </td>
                <td>
                    @if($plugin->active)
                        <span class="badge bg-success-subtle text-success"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Active</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Disabled</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPlugin{{ $plugin->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </td>
            </tr>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editPlugin{{ $plugin->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.plugins.update', $plugin) }}">
                        @csrf @method('PATCH')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-700">Edit: {{ $plugin->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-600">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $plugin->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-600">Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ $plugin->description }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-600">Minimum Plan</label>
                                    <select name="min_plan" class="form-select">
                                        @foreach(['free','starter','pro','enterprise'] as $p)
                                        <option value="{{ $p }}" @selected($plugin->min_plan === $p)>{{ ucfirst($p) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="active" value="1" id="active{{ $plugin->id }}" @checked($plugin->active)>
                                    <label class="form-check-label" for="active{{ $plugin->id }}">Plugin Active (visible to tenants)</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
