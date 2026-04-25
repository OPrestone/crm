@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('content')
@php $activeTab = request('tab', 'company'); @endphp
<ul class="nav nav-tabs mb-4" id="settingsTabs">
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'company'  ? 'active' : '' }}" data-bs-toggle="tab" href="#s-company"><i class="bi bi-building me-1"></i>Company</a></li>
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'branding' ? 'active' : '' }}" data-bs-toggle="tab" href="#s-branding"><i class="bi bi-palette me-1"></i>Branding</a></li>
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'users'    ? 'active' : '' }}" data-bs-toggle="tab" href="#s-users"><i class="bi bi-people me-1"></i>Users</a></li>
    <li class="nav-item"><a class="nav-link {{ $activeTab === 'pipeline' ? 'active' : '' }}" data-bs-toggle="tab" href="#s-pipeline"><i class="bi bi-funnel me-1"></i>Pipeline Stages</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade {{ $activeTab === 'company' ? 'show active' : '' }}" id="s-company">
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

    {{-- ── Branding Tab ──────────────────────────────────────────────── --}}
    <div class="tab-pane fade {{ $activeTab === 'branding' ? 'show active' : '' }}" id="s-branding">
        <form method="POST" action="{{ route('settings.branding') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- Logo --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-image me-2"></i>Logo</h5></div>
                    <div class="card-body px-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-2 text-center">
                                @if($tenant->logo)
                                    <img src="{{ Storage::url($tenant->logo) }}" alt="Logo" style="max-height:80px;max-width:160px;object-fit:contain;border-radius:8px;border:1px solid #e5e7eb;padding:8px;">
                                @else
                                    <div style="width:80px;height:80px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                        <i class="bi bi-building" style="font-size:32px;color:#94a3b8;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-600">Upload Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp">
                                <div class="form-text">PNG, JPG, SVG or WEBP — max 2 MB. Recommended: 200 × 60 px, transparent background.</div>
                            </div>
                            @if($tenant->logo)
                            <div class="col-md-3">
                                <form method="POST" action="{{ route('settings.branding.removeLogo') }}" onsubmit="return confirm('Remove logo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Remove Logo</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colors --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-palette me-2"></i>Brand Colors</h5></div>
                    <div class="card-body px-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600">Primary Color</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" name="primary_color" id="primaryColor" class="form-control form-control-color" value="{{ $tenant->primary_color ?? '#0d6efd' }}" style="width:48px;height:42px;">
                                    <input type="text" id="primaryHex" class="form-control" value="{{ $tenant->primary_color ?? '#0d6efd' }}" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#0d6efd" oninput="syncColor(this,'primaryColor')">
                                </div>
                                <div class="form-text">Buttons, links, highlights</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Accent Color</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" name="accent_color" id="accentColor" class="form-control form-control-color" value="{{ $tenant->accent_color ?? '#0d6efd' }}" style="width:48px;height:42px;">
                                    <input type="text" id="accentHex" class="form-control" value="{{ $tenant->accent_color ?? '#0d6efd' }}" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#0d6efd" oninput="syncColor(this,'accentColor')">
                                </div>
                                <div class="form-text">Secondary highlights, badges</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600 mb-2">Quick Presets</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['#0d6efd'=>'Blue','#7c3aed'=>'Purple','#059669'=>'Green','#dc2626'=>'Red','#d97706'=>'Amber','#0891b2'=>'Cyan','#db2777'=>'Pink','#1e293b'=>'Slate'] as $hex => $label)
                                    <button type="button" class="color-preset-btn" style="width:30px;height:30px;border-radius:50%;background:{{ $hex }};border:2px solid transparent;cursor:pointer;transition:all .15s;" title="{{ $label }}" onclick="applyPreset('{{ $hex }}')"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Style --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-layout-sidebar me-2"></i>Appearance</h5></div>
                    <div class="card-body px-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-600">Sidebar Style</label>
                                <div class="d-flex gap-3">
                                    <label class="sidebar-style-option {{ ($tenant->sidebar_style ?? 'dark') === 'dark' ? 'selected' : '' }}" for="sidebar_dark">
                                        <input type="radio" name="sidebar_style" id="sidebar_dark" value="dark" class="d-none" {{ ($tenant->sidebar_style ?? 'dark') === 'dark' ? 'checked' : '' }}>
                                        <div class="sidebar-preview sidebar-preview-dark">
                                            <div class="sp-side"></div><div class="sp-main"></div>
                                        </div>
                                        <span style="font-size:12px;font-weight:600;">Dark</span>
                                    </label>
                                    <label class="sidebar-style-option {{ ($tenant->sidebar_style ?? 'dark') === 'light' ? 'selected' : '' }}" for="sidebar_light">
                                        <input type="radio" name="sidebar_style" id="sidebar_light" value="light" class="d-none" {{ ($tenant->sidebar_style ?? 'dark') === 'light' ? 'checked' : '' }}>
                                        <div class="sidebar-preview sidebar-preview-light">
                                            <div class="sp-side"></div><div class="sp-main"></div>
                                        </div>
                                        <span style="font-size:12px;font-weight:600;">Light</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600">Font Family</label>
                                <select name="font_family" class="form-select">
                                    <option value="system"  {{ ($tenant->font_family ?? 'system')  === 'system'  ? 'selected' : '' }}>System Default</option>
                                    <option value="inter"   {{ ($tenant->font_family ?? 'system')  === 'inter'   ? 'selected' : '' }}>Inter (Modern Sans)</option>
                                    <option value="poppins" {{ ($tenant->font_family ?? 'system')  === 'poppins' ? 'selected' : '' }}>Poppins (Friendly)</option>
                                    <option value="georgia" {{ ($tenant->font_family ?? 'system')  === 'georgia' ? 'selected' : '' }}>Georgia (Classic Serif)</option>
                                </select>
                                <div class="form-text">Applied across all CRM pages for your team</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview strip --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-eye me-2"></i>Live Preview</h5></div>
                    <div class="card-body px-4">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <button type="button" class="btn btn-primary preview-btn">Primary Button</button>
                            <button type="button" class="btn btn-outline-primary preview-btn-outline">Outline Button</button>
                            <span class="badge preview-badge" style="padding:6px 12px;border-radius:20px;font-size:13px;color:#fff;">Badge</span>
                            <a href="#" class="preview-link text-primary text-decoration-none fw-600">Sample Link</a>
                            <div class="progress flex-grow-1" style="height:8px;border-radius:8px;min-width:120px;">
                                <div class="progress-bar preview-bar" role="progressbar" style="width:65%;background:var(--bs-primary,#0d6efd);border-radius:8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-2"></i>Save Branding</button>
                <span class="text-muted ms-3" style="font-size:13px;">Changes take effect immediately for all team members.</span>
            </div>
        </div>
        </form>

        <style>
            .sidebar-style-option {
                display: flex; flex-direction: column; align-items: center; gap: 6px;
                cursor: pointer; padding: 8px; border-radius: 10px; border: 2px solid #e5e7eb; transition: all .15s;
            }
            .sidebar-style-option.selected, .sidebar-style-option:hover { border-color: #0d6efd; background: #eff6ff; }
            .sidebar-preview { display: flex; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; width: 80px; height: 52px; }
            .sidebar-preview-dark .sp-side  { width: 24px; background: #0f172a; }
            .sidebar-preview-dark .sp-main  { flex: 1; background: #f0f4f8; }
            .sidebar-preview-light .sp-side { width: 24px; background: #ffffff; border-right: 1px solid #e5e7eb; }
            .sidebar-preview-light .sp-main { flex: 1; background: #f0f4f8; }
            .color-preset-btn:hover { transform: scale(1.2); border-color: #333 !important; }
        </style>
        <script>
            function syncColor(textInput, colorId) {
                const val = textInput.value;
                if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                    document.getElementById(colorId).value = val;
                    updatePreview();
                }
            }
            document.getElementById('primaryColor').addEventListener('input', function() {
                document.getElementById('primaryHex').value = this.value;
                updatePreview();
            });
            document.getElementById('accentColor').addEventListener('input', function() {
                document.getElementById('accentHex').value = this.value;
            });
            function applyPreset(hex) {
                document.getElementById('primaryColor').value = hex;
                document.getElementById('primaryHex').value  = hex;
                updatePreview();
            }
            function updatePreview() {
                const color = document.getElementById('primaryColor').value;
                document.querySelectorAll('.preview-btn').forEach(el => el.style.background = color);
                document.querySelectorAll('.preview-btn').forEach(el => el.style.borderColor = color);
                document.querySelectorAll('.preview-btn-outline').forEach(el => { el.style.color = color; el.style.borderColor = color; });
                document.querySelectorAll('.preview-badge').forEach(el => el.style.background = color);
                document.querySelectorAll('.preview-link').forEach(el => el.style.color = color);
                document.querySelectorAll('.preview-bar').forEach(el => el.style.background = color);
            }
            document.querySelectorAll('.sidebar-style-option').forEach(opt => {
                opt.addEventListener('click', () => {
                    document.querySelectorAll('.sidebar-style-option').forEach(o => o.classList.remove('selected'));
                    opt.classList.add('selected');
                    opt.querySelector('input').checked = true;
                });
            });
        </script>
    </div>

    <div class="tab-pane fade {{ $activeTab === 'users' ? 'show active' : '' }}" id="s-users">
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

    <div class="tab-pane fade {{ $activeTab === 'pipeline' ? 'show active' : '' }}" id="s-pipeline">
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
