@extends('layouts.app')
@section('title', 'Commission Plans')
@section('page-title', 'Commission Plans')
@section('content')
<div class="page-header">
    <div><h1>Commission Plans</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
        <a href="{{ route('commissions.plans.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Plan</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="card table-card">
    <div class="card-body p-0">
        @if($plans->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Plan Name</th><th>Type</th><th>Rate</th><th>Min Deal Value</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($plans as $p)
                <tr>
                    <td class="fw-600">{{ $p->name }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($p->type) }}</span></td>
                    <td>@if($p->type === 'flat')${{ number_format($p->rate,2) }}@else{{ $p->rate }}%@endif</td>
                    <td>${{ number_format($p->min_deal_value,0) }}</td>
                    <td>@if($p->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('commissions.plans.edit', $p) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('commissions.plans.destroy', $p) }}','{{ $p->name }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-list-check"></i></div>
            <h5>No commission plans</h5>
            <p class="text-muted">Define how commissions are calculated for your sales team.</p>
            <a href="{{ route('commissions.plans.create') }}" class="btn btn-primary">Create Plan</a>
        </div>
        @endif
    </div>
</div>
@endsection
