@extends('layouts.app')
@section('title', 'Quotes')
@section('page-title', 'Quotes & Proposals')

@section('content')
<div class="page-header">
    <div>
        <h1>Quotes &amp; Proposals</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Quotes</li>
        </ol></nav>
    </div>
    <a href="{{ route('quotes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Quote</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-secondary-subtle"><i class="bi bi-file-earmark text-secondary"></i></div>
            <div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Quotes</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-info-subtle"><i class="bi bi-send text-info"></i></div>
            <div class="stat-value">{{ $stats['sent'] }}</div><div class="stat-label">Sent</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-success-subtle"><i class="bi bi-check-circle text-success"></i></div>
            <div class="stat-value">{{ $stats['accepted'] }}</div><div class="stat-label">Accepted</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-primary-subtle"><i class="bi bi-currency-dollar text-primary"></i></div>
            <div class="stat-value">${{ number_format($stats['value'], 0) }}</div><div class="stat-label">Won Value</div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search quotes..." value="{{ request('search') }}">
            </div></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['draft','sent','accepted','rejected','expired'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($quotes->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Quote #</th><th>Title</th><th>Contact / Company</th><th>Status</th><th>Total</th><th>Valid Until</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($quotes as $quote)
                    <tr>
                        <td><a href="{{ route('quotes.show', $quote) }}" class="fw-600 text-decoration-none">{{ $quote->quote_number }}</a></td>
                        <td>{{ Str::limit($quote->title, 40) }}</td>
                        <td>
                            @if($quote->contact)<div class="small">{{ $quote->contact->full_name }}</div>@endif
                            @if($quote->company)<div class="text-muted small">{{ $quote->company->name }}</div>@endif
                        </td>
                        <td><span class="badge bg-{{ $quote->status_badge }}-subtle text-{{ $quote->status_badge }}">{{ ucfirst($quote->status) }}</span></td>
                        <td class="fw-600">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</td>
                        <td>
                            @if($quote->valid_until)
                                <span class="{{ $quote->isExpired() ? 'text-danger' : 'text-muted' }} small">{{ $quote->valid_until->format('d M Y') }}</span>
                            @else <span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-sm btn-outline-danger" target="_blank"><i class="bi bi-file-pdf"></i></a>
                            <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('quotes.destroy', $quote) }}" class="d-inline" onsubmit="return confirm('Delete this quote?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $quotes->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-ruled text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No quotes yet</h5>
            <a href="{{ route('quotes.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus-lg me-1"></i>Create First Quote</a>
        </div>
        @endif
    </div>
</div>
@endsection
