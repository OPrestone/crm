@extends('layouts.app')
@section('title', 'ID Verification')
@section('page-title', 'ID Verification')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-shield-check me-2 text-primary"></i>{{ $idVerification->full_name }}</h1>
        <p class="text-muted mb-0">{{ $idVerification->id_type_label }} · Created {{ $idVerification->created_at->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('id-verification.edit', $idVerification) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('id-verification.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success border-0 mb-4">{{ session('success') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0">Verification Details</h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-{{ $idVerification->status_badge }}-subtle text-{{ $idVerification->status_badge }} fs-6 px-3 py-2">
                        <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>{{ ucfirst(str_replace('_',' ',$idVerification->status)) }}
                    </span>
                    <span class="badge bg-{{ $idVerification->risk_badge }}-subtle text-{{ $idVerification->risk_badge }} fs-6 px-3 py-2">
                        {{ ucfirst($idVerification->risk_level) }} Risk
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3"><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Full Name</div><div class="fw-600">{{ $idVerification->full_name }}</div></div>
                        <div class="mb-3"><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Date of Birth</div><div class="fw-600">{{ $idVerification->date_of_birth?->format('M d, Y') ?? '—' }}</div></div>
                        <div class="mb-3"><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Nationality</div><div class="fw-600">{{ $idVerification->nationality ?? '—' }}</div></div>
                        <div><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Gender</div><div class="fw-600">{{ $idVerification->gender ? ucfirst($idVerification->gender) : '—' }}</div></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">ID Type</div><div class="fw-600">{{ $idVerification->id_type_label }}</div></div>
                        <div class="mb-3"><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">ID Number</div><div class="fw-600 font-monospace">{{ $idVerification->id_number ?? '—' }}</div></div>
                        <div class="mb-3">
                            <div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Issue / Expiry</div>
                            <div class="fw-600">{{ $idVerification->issue_date?->format('M d, Y') ?? '—' }} → <span class="{{ $idVerification->isExpired()?'text-danger':'' }}">{{ $idVerification->expiry_date?->format('M d, Y') ?? '—' }}{{ $idVerification->isExpired()?' (Expired)':'' }}</span></div>
                        </div>
                        <div><div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Issuing Country</div><div class="fw-600">{{ $idVerification->issuing_country ?? '—' }}</div></div>
                    </div>
                    @if($idVerification->address)
                    <div class="col-12">
                        <div class="text-muted fw-600 mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Address</div>
                        <div class="fw-600">{{ $idVerification->address }}</div>
                    </div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="mb-2 fw-700">Confidence Score</div>
                <div class="d-flex align-items-center gap-3">
                    <div class="progress flex-1" style="height:12px;">
                        <div class="progress-bar bg-{{ $idVerification->confidence_score>=70?'success':($idVerification->confidence_score>=40?'warning':'danger') }}" style="width:{{ $idVerification->confidence_score }}%"></div>
                    </div>
                    <span class="fw-700 {{ $idVerification->confidence_score>=70?'text-success':($idVerification->confidence_score>=40?'text-warning':'text-danger') }}">{{ $idVerification->confidence_score }}%</span>
                </div>
            </div>
        </div>

        @if($idVerification->document_front || $idVerification->document_back || $idVerification->selfie)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Uploaded Documents</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @if($idVerification->document_front)
                    <div class="col-md-4">
                        <div class="text-muted fw-600 mb-2" style="font-size:11px;text-transform:uppercase;">Document Front</div>
                        @php $ext = pathinfo($idVerification->document_front, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                        <img src="{{ Storage::url($idVerification->document_front) }}" class="img-fluid rounded-3 border" style="max-height:200px;object-fit:cover;width:100%;">
                        @else
                        <a href="{{ Storage::url($idVerification->document_front) }}" target="_blank" class="btn btn-outline-primary w-100"><i class="bi bi-file-earmark-pdf me-1"></i>View PDF</a>
                        @endif
                    </div>
                    @endif
                    @if($idVerification->document_back)
                    <div class="col-md-4">
                        <div class="text-muted fw-600 mb-2" style="font-size:11px;text-transform:uppercase;">Document Back</div>
                        @php $ext2 = pathinfo($idVerification->document_back, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext2), ['jpg','jpeg','png']))
                        <img src="{{ Storage::url($idVerification->document_back) }}" class="img-fluid rounded-3 border" style="max-height:200px;object-fit:cover;width:100%;">
                        @else
                        <a href="{{ Storage::url($idVerification->document_back) }}" target="_blank" class="btn btn-outline-primary w-100"><i class="bi bi-file-earmark-pdf me-1"></i>View PDF</a>
                        @endif
                    </div>
                    @endif
                    @if($idVerification->selfie)
                    <div class="col-md-4">
                        <div class="text-muted fw-600 mb-2" style="font-size:11px;text-transform:uppercase;">Selfie</div>
                        <img src="{{ Storage::url($idVerification->selfie) }}" class="img-fluid rounded-3 border" style="max-height:200px;object-fit:cover;width:100%;">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Update Status</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('id-verification.status', $idVerification) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-600">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $idVerification->status==='pending'?'selected':'' }}>Pending</option>
                            <option value="under_review" {{ $idVerification->status==='under_review'?'selected':'' }}>Under Review</option>
                            <option value="verified" {{ $idVerification->status==='verified'?'selected':'' }}>Verified</option>
                            <option value="rejected" {{ $idVerification->status==='rejected'?'selected':'' }}>Rejected</option>
                            <option value="expired" {{ $idVerification->status==='expired'?'selected':'' }}>Expired</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Risk Level</label>
                        <select name="risk_level" class="form-select">
                            <option value="low" {{ $idVerification->risk_level==='low'?'selected':'' }}>Low</option>
                            <option value="medium" {{ $idVerification->risk_level==='medium'?'selected':'' }}>Medium</option>
                            <option value="high" {{ $idVerification->risk_level==='high'?'selected':'' }}>High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Rejection Reason <span class="text-muted">(if rejected)</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain why this was rejected…">{{ $idVerification->rejection_reason }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i>Update Status</button>
                </form>
            </div>
        </div>

        @if($idVerification->contact)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Linked Contact</h6></div>
            <div class="card-body p-4">
                <div class="d-flex gap-3 align-items-center">
                    <div class="avatar-circle" style="width:40px;height:40px;font-size:15px;">{{ strtoupper(substr($idVerification->contact->first_name,0,1)) }}</div>
                    <div>
                        <div class="fw-600">{{ $idVerification->contact->first_name }} {{ $idVerification->contact->last_name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $idVerification->contact->email }}</div>
                    </div>
                </div>
                <a href="{{ route('contacts.show', $idVerification->contact) }}" class="btn btn-outline-primary btn-sm w-100 mt-3">View Contact</a>
            </div>
        </div>
        @endif

        @if($idVerification->notes)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Notes</h6></div>
            <div class="card-body p-4"><p class="mb-0" style="font-size:14px;">{{ $idVerification->notes }}</p></div>
        </div>
        @endif

        <form method="POST" action="{{ route('id-verification.destroy', $idVerification) }}" onsubmit="return confirm('Delete this verification record?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Delete Record</button>
        </form>
    </div>
</div>
@endsection
