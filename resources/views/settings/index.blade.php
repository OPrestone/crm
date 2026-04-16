@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('content')
<ul class="nav nav-tabs mb-4" id="settingsTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#s-company">Company</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#s-users">Users</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#s-pipeline">Pipeline Stages</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="s-company">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Company Settings</h5></div>
                <div class="card-body px-4">
                <form method="POST" action="{{ route('settings.updateTenant') }}">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-600">Company Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Website</label><input type="url" name="website" class="form-control" value="{{ old('website', $tenant->website) }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Industry</label><input type="text" name="industry" class="form-control" value="{{ old('industry', $tenant->industry) }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Currency</label><select name="currency" class="form-select"><option value="USD" {{ ($tenant->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option><option value="EUR" {{ $tenant->currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option><option value="GBP" {{ $tenant->currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option><option value="KES" {{ $tenant->currency === 'KES' ? 'selected' : '' }}>KES (Ksh)</option></select></div>
                    <div class="col-md-6"><label class="form-label fw-600">Timezone</label><select name="timezone" class="form-select"><option value="UTC">UTC</option><option value="America/New_York">Eastern Time</option><option value="America/Chicago">Central Time</option><option value="America/Los_Angeles">Pacific Time</option><option value="Europe/London">London</option><option value="Africa/Nairobi">Nairobi</option></select></div>
                    <div class="col-12"><label class="form-label fw-600">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address', $tenant->address) }}</textarea></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button></div>
                </div>
                </form>
                </div></div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-transparent pt-4 px-4"><h6 class="fw-700 mb-0">Plan</h6></div>
                    <div class="card-body px-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary fs-6">{{ ucfirst($tenant->plan) }}</span>
                        </div>
                        <dl class="row mb-0" style="font-size:13px;">
                            <dt class="col-7 text-muted">Max Users</dt><dd class="col-5 fw-600">{{ $tenant->max_users }}</dd>
                            <dt class="col-7 text-muted">Max Contacts</dt><dd class="col-5 fw-600">{{ number_format($tenant->max_contacts) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="s-users">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-700 mb-0">Team Members</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-plus-lg me-1"></i>Add User</button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>User</th><th>Role</th><th>Joined</th><th></th></tr></thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width:34px;height:34px;font-size:12px;">{{ $user->initials }}</div>
                                <div><div class="fw-600" style="font-size:13px;">{{ $user->name }}</div><div class="text-muted" style="font-size:11px;">{{ $user->email }}</div></div>
                            </div></td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ $user->roles->first()?->name ?? 'staff' }}</span></td>
                            <td class="text-muted" style="font-size:12px;">{{ $user->created_at->format('M j, Y') }}</td>
                            <td>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('settings.destroyUser', $user) }}" class="d-inline" onsubmit="return confirm('Remove this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @else<span class="badge bg-success-subtle text-success">You</span>@endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Team Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('settings.storeUser') }}">
                @csrf
                <div class="modal-body"><div class="row g-3">
                    <div class="col-12"><label class="form-label fw-600">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label fw-600">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Password</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Role</label><select name="role" class="form-select"><option value="staff">Staff</option><option value="manager">Manager</option><option value="tenant_admin">Admin</option></select></div>
                    <div class="col-12"><label class="form-label fw-600">Job Title</label><input type="text" name="job_title" class="form-control"></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add User</button></div>
                </form>
            </div></div>
        </div>
    </div>

    <div class="tab-pane fade" id="s-pipeline">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-700 mb-0">Deal Stages</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStageModal" data-type="deal">Add Stage</button>
                </div>
                <div class="card-body p-0">
                    @foreach($stages->where('type', 'deal') as $stage)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:12px;height:12px;border-radius:50%;background:{{ $stage->color }};"></div>
                            <span class="fw-600">{{ $stage->name }}</span>
                            @if($stage->is_won)<span class="badge bg-success-subtle text-success ms-1">Won</span>@endif
                            @if($stage->is_lost)<span class="badge bg-danger-subtle text-danger ms-1">Lost</span>@endif
                        </div>
                        <form method="POST" action="{{ route('settings.destroyStage', $stage) }}" onsubmit="return confirm('Delete this stage?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div></div>
            </div>
            <div class="col-lg-6">
                <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-700 mb-0">Lead Stages</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStageModal" data-type="lead">Add Stage</button>
                </div>
                <div class="card-body p-0">
                    @foreach($stages->where('type', 'lead') as $stage)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:12px;height:12px;border-radius:50%;background:{{ $stage->color }};"></div>
                            <span class="fw-600">{{ $stage->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('settings.destroyStage', $stage) }}" onsubmit="return confirm('Delete this stage?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div></div>
            </div>
        </div>
        <div class="modal fade" id="addStageModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Pipeline Stage</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('settings.storeStage') }}">
                @csrf
                <div class="modal-body"><div class="row g-3">
                    <div class="col-12"><label class="form-label fw-600">Stage Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Type</label><select name="type" id="stageTypeSelect" class="form-select"><option value="deal">Deal</option><option value="lead">Lead</option></select></div>
                    <div class="col-md-6"><label class="form-label fw-600">Color</label><input type="color" name="color" class="form-control form-control-color" value="#0d6efd"></div>
                    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_won" id="is_won"><label class="form-check-label" for="is_won">Mark as Won</label></div></div>
                    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_lost" id="is_lost"><label class="form-check-label" for="is_lost">Mark as Lost</label></div></div>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Add Stage</button></div>
                </form>
            </div></div>
        </div>
    </div>
</div>
@endsection
