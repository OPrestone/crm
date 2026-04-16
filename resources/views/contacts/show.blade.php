@extends('layouts.app')
@section('title', $contact->full_name)
@section('page-title', $contact->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $contact->full_name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
            <li class="breadcrumb-item active">{{ $contact->full_name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('contacts.destroy', $contact) }}', '{{ $contact->full_name }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center pt-4">
                <div class="avatar-circle mx-auto mb-3" style="width:64px;height:64px;font-size:22px;">{{ $contact->initials }}</div>
                <h5 class="fw-700 mb-1">{{ $contact->full_name }}</h5>
                <p class="text-muted mb-2">{{ $contact->job_title ?? 'No title' }}</p>
                <span class="badge bg-{{ $contact->status_badge }}-subtle text-{{ $contact->status_badge }} me-1">{{ ucfirst($contact->status) }}</span>
                <span class="badge bg-{{ $contact->score_color }}-subtle text-{{ $contact->score_color }}">Score: {{ $contact->lead_score }}</span>
            </div>
            <hr class="my-0">
            <div class="card-body">
                @if($contact->email)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-envelope text-muted"></i>
                    <a href="mailto:{{ $contact->email }}" class="text-decoration-none">{{ $contact->email }}</a>
                </div>
                @endif
                @if($contact->phone)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-telephone text-muted"></i>
                    <span>{{ $contact->phone }}</span>
                </div>
                @endif
                @if($contact->mobile)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-phone text-muted"></i>
                    <span>{{ $contact->mobile }}</span>
                </div>
                @endif
                @if($contact->company)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-building text-muted"></i>
                    <a href="{{ route('companies.show', $contact->company) }}" class="text-decoration-none">{{ $contact->company->name }}</a>
                </div>
                @endif
                @if($contact->city || $contact->country)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-geo-alt text-muted"></i>
                    <span>{{ collect([$contact->city, $contact->country])->filter()->join(', ') }}</span>
                </div>
                @endif
                @if($contact->source)
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-signpost text-muted"></i>
                    <span class="text-muted">{{ $contact->source }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Lead Score</h6></div>
            <div class="card-body px-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-{{ $contact->score_color }} fw-600">{{ ucfirst($contact->score_level) }}</span>
                    <span class="fw-700">{{ $contact->lead_score }}/100</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar bg-{{ $contact->score_color }}" style="width:{{ $contact->lead_score }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <ul class="nav nav-tabs mb-4" id="contactTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#deals-tab">Deals ({{ $contact->deals->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#leads-tab">Leads ({{ $contact->leads->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tasks-tab">Tasks ({{ $contact->tasks->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoices-tab">Invoices ({{ $contact->invoices->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#activity-tab">Activity</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="deals-tab">
                <div class="card">
                    <div class="card-header bg-transparent pt-3 px-4 d-flex justify-content-between">
                        <h6 class="fw-700 mb-0">Deals</h6>
                        <a href="{{ route('deals.create') }}?contact_id={{ $contact->id }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i></a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($contact->deals as $deal)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('deals.show', $deal) }}" class="fw-600 text-decoration-none">{{ $deal->title }}</a>
                                <div class="text-muted" style="font-size:12px;">{{ $deal->stage?->name ?? 'No stage' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-600">${{ number_format($deal->value, 0) }}</div>
                                <span class="badge bg-{{ $deal->status_badge }}-subtle text-{{ $deal->status_badge }}" style="font-size:10px;">{{ ucfirst($deal->status) }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No deals yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="leads-tab">
                <div class="card">
                    <div class="card-body p-0">
                        @forelse($contact->leads as $lead)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('leads.show', $lead) }}" class="fw-600 text-decoration-none">{{ $lead->title }}</a>
                                <div class="text-muted" style="font-size:12px;">{{ $lead->source ?? 'Direct' }}</div>
                            </div>
                            <span class="badge bg-{{ $lead->status_badge }}">{{ ucfirst($lead->status) }}</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No leads yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tasks-tab">
                <div class="card">
                    <div class="card-body p-0">
                        @forelse($contact->tasks as $task)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-{{ $task->type_icon }} text-muted"></i>
                                <div>
                                    <div class="fw-600" style="font-size:13px;">{{ $task->title }}</div>
                                    @if($task->due_date)<div class="text-muted" style="font-size:11px;">Due: {{ $task->due_date->format('M j, Y') }}</div>@endif
                                </div>
                            </div>
                            <span class="badge bg-{{ $task->status_badge }}-subtle text-{{ $task->status_badge }}" style="font-size:10px;">{{ ucfirst($task->status) }}</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No tasks yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="invoices-tab">
                <div class="card">
                    <div class="card-body p-0">
                        @forelse($contact->invoices as $invoice)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <a href="{{ route('invoices.show', $invoice) }}" class="fw-600 text-decoration-none">{{ $invoice->invoice_number }}</a>
                                <div class="text-muted" style="font-size:12px;">Due: {{ $invoice->due_date->format('M j, Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-600">${{ number_format($invoice->total, 2) }}</div>
                                <span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }}" style="font-size:10px;">{{ ucfirst($invoice->status) }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No invoices yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="activity-tab">
                <div class="card">
                    <div class="card-body">
                        @forelse($contact->activities as $activity)
                        <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-circle bg-{{ $activity->type_color }} text-white" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">
                                <i class="bi bi-{{ $activity->type_icon }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="fw-600" style="font-size:13px;">{{ $activity->subject }}</div>
                                @if($activity->description)<div class="text-muted" style="font-size:12px;">{{ $activity->description }}</div>@endif
                                <div class="text-muted" style="font-size:11px;">{{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-3" style="font-size:13px;">No activity yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @if($contact->notes)
        <div class="card mt-4">
            <div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Notes</h6></div>
            <div class="card-body px-4">{{ $contact->notes }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
