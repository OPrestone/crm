@extends('layouts.app')
@section('title', $webForm->name)
@section('page-title', $webForm->name)
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $webForm->name }}</h1>
        @if($webForm->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('web_forms.public', $webForm) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Preview</a>
        <a href="{{ route('web_forms.edit', $webForm) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('web_forms.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header fw-600">Embed Code</div>
            <div class="card-body">
                <p class="text-muted small">Copy this code and paste it into your website's HTML.</p>
                <div class="bg-dark text-light p-3 rounded small" style="font-family:monospace;word-break:break-all;">{{ $webForm->embed_code }}</div>
                <button class="btn btn-outline-secondary btn-sm mt-2" onclick="navigator.clipboard.writeText(`{{ $webForm->embed_code }}`); this.innerHTML='<i class=\'bi bi-check me-1\'></i>Copied!'"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-600">Form Details</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-600">Action</td><td>Create {{ ucfirst($webForm->submit_action) }}</td></tr>
                    <tr><td class="text-muted fw-600">Fields</td><td>{{ count($webForm->fields ?? []) }}</td></tr>
                    <tr><td class="text-muted fw-600">Submissions</td><td>{{ $submissions->total() }}</td></tr>
                    <tr><td class="text-muted fw-600">Created</td><td>{{ $webForm->created_at->format('M d, Y') }}</td></tr>
                    @if($webForm->success_message)
                    <tr><td class="text-muted fw-600">Success Msg</td><td class="small">{{ $webForm->success_message }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-600">Form Fields</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                @foreach($webForm->fields ?? [] as $field)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-600 small">{{ $field['label'] }}</span>
                        <div class="text-muted" style="font-size:11px;">{{ $field['name'] }} · {{ $field['type'] }}</div>
                    </div>
                    @if($field['required'] ?? false)<span class="badge bg-danger-subtle text-danger">Required</span>@else<span class="badge bg-secondary-subtle text-secondary">Optional</span>@endif
                </li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600 d-flex justify-content-between align-items-center">
                <span>Submissions <span class="badge bg-secondary ms-1">{{ $submissions->total() }}</span></span>
            </div>
            <div class="card-body p-0">
                @if($submissions->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Submitted</th><th>Data Preview</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($submissions as $sub)
                        <tr>
                            <td class="text-muted small">{{ $sub->created_at->format('M d, Y H:i') }}</td>
                            <td class="small">
                                @foreach(array_slice($sub->data ?? [], 0, 2) as $k => $v)
                                <span class="text-muted">{{ $k }}:</span> {{ Str::limit($v, 25) }}<br>
                                @endforeach
                            </td>
                            <td>@if($sub->processed)<span class="badge bg-success">Processed</span>@else<span class="badge bg-warning">Pending</span>@endif</td>
                            <td><a href="{{ route('web_forms.submission', [$webForm, $sub]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-top">{{ $submissions->links() }}</div>
                @else
                <div class="empty-state py-5">
                    <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                    <h6>No submissions yet</h6>
                    <p class="text-muted small">Share or embed the form to start collecting leads.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
