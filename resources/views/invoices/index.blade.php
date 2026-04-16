@extends('layouts.app')
@section('title', 'Invoices')
@section('page-title', 'Invoices')
@section('content')
<div class="page-header">
    <div><h1>Invoices</h1></div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Invoice</a>
</div>
<div class="row g-3 mb-4">
    @php $statusColors = ['paid'=>'success','sent'=>'info','draft'=>'secondary','overdue'=>'danger','cancelled'=>'dark']; @endphp
    @foreach($statusColors as $status => $color)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card text-center p-3">
            <div class="fw-700 fs-5">${{ number_format($totals[$status] ?? 0, 0) }}</div>
            <div><span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span></div>
        </div>
    </div>
    @endforeach
</div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-6"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search invoice number..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['draft','sent','paid','overdue','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        @if($invoices->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Invoice #</th><th>Contact / Company</th><th>Issued</th><th>Due</th><th>Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-600 text-decoration-none">{{ $invoice->invoice_number }}</a></td>
                    <td>
                        @if($invoice->contact)<div style="font-size:13px;">{{ $invoice->contact->full_name }}</div>@endif
                        @if($invoice->company)<div class="text-muted" style="font-size:12px;">{{ $invoice->company->name }}</div>@endif
                    </td>
                    <td class="text-muted" style="font-size:12px;">{{ $invoice->issue_date->format('M j, Y') }}</td>
                    <td>
                        <span class="{{ $invoice->isOverdue() ? 'text-danger fw-600' : 'text-muted' }}" style="font-size:12px;">
                            {{ $invoice->due_date->format('M j, Y') }}
                        </span>
                    </td>
                    <td class="fw-700">${{ number_format($invoice->total, 2) }}</td>
                    <td><span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }}">{{ ucfirst($invoice->status) }}</span></td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('invoices.show', $invoice) }}"><i class="bi bi-eye"></i> View</a></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.edit', $invoice) }}"><i class="bi bi-pencil"></i> Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.pdf', $invoice) }}" target="_blank"><i class="bi bi-file-pdf"></i> PDF</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('invoices.destroy', $invoice) }}', '{{ $invoice->invoice_number }}')"><i class="bi bi-trash"></i> Delete</button></li>
                        </ul></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $invoices->links() }}</div>
        @else
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-receipt"></i></div><h5>No invoices yet</h5><a href="{{ route('invoices.create') }}" class="btn btn-primary">Create Invoice</a></div>
        @endif
    </div>
</div>
@endsection
