@extends('layouts.app')
@section('title', 'Submission Detail')
@section('page-title', 'Submission Detail')
@section('content')
<div class="page-header">
    <div><h1>Submission #{{ $submission->id }}</h1><p class="text-muted mb-0">{{ $webForm->name }}</p></div>
    <a href="{{ route('web_forms.show', $webForm) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600">Submitted Data</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Field</th><th>Value</th></tr></thead>
                    <tbody>
                    @foreach($submission->data as $key => $value)
                    <tr><td class="fw-600 text-muted">{{ $key }}</td><td>{{ $value ?? '—' }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header fw-600">Details</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-600">Submitted</td><td>{{ $submission->created_at->format('M d, Y g:i A') }}</td></tr>
                    <tr><td class="text-muted fw-600">IP Address</td><td>{{ $submission->ip_address ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Processed</td><td>@if($submission->processed)<span class="badge bg-success">Yes</span>@else<span class="badge bg-warning">No</span>@endif</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
