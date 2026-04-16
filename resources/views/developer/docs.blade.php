@extends('layouts.app')
@section('title', 'API Documentation')
@section('page-title', 'API Documentation')

@push('styles')
<style>
.docs-sidebar-sticky { position:sticky;top:80px; }
.api-sidebar { background:#fff;border:1px solid var(--color-border);border-radius:12px;padding:16px; }
.api-nav-section { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:#9ca3af;padding:10px 10px 4px; }
.api-nav-item { display:block;padding:6px 10px;border-radius:7px;text-decoration:none;color:#374151;font-size:13px;transition:all .15s;margin-bottom:1px; }
.api-nav-item:hover { background:#f0f4ff;color:#0d6efd; }
.api-nav-item.active { background:#0d6efd;color:#fff; }
.endpoint-block { border:1px solid var(--color-border);border-radius:12px;overflow:hidden;margin-bottom:20px; }
.endpoint-header { display:flex;align-items:center;gap:12px;padding:14px 20px;cursor:pointer;background:#fff;transition:background .15s; }
.endpoint-header:hover { background:#f8faff; }
.method-badge { font-family:monospace;font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;min-width:60px;text-align:center; }
.endpoint-path { font-family:monospace;font-size:14px;color:#374151; }
.endpoint-desc { font-size:13px;color:#6b7280;margin-left:auto; }
.endpoint-body { border-top:1px solid var(--color-border);background:#f8faff; }
.code-block { background:#1e293b;color:#e2e8f0;border-radius:8px;padding:16px;font-family:monospace;font-size:12px;overflow-x:auto;margin:0; }
.code-block .comment { color:#64748b; }
.code-block .key { color:#7dd3fc; }
.code-block .val { color:#86efac; }
.code-block .str { color:#fde68a; }
.code-block .kw { color:#c084fc; }
.param-table td,th { font-size:12px; }
.try-btn { font-size:11px; }
.nav-tabs .nav-link { font-size:12px;padding:4px 12px; }
</style>
@endpush

@section('content')
<div class="row g-4">
    {{-- Sidebar --}}
    <div class="col-lg-3 d-none d-lg-block">
        <div class="docs-sidebar-sticky">
            <div class="api-sidebar">
                <div class="mb-3 p-2 rounded-3" style="background:#0f172a;">
                    <div class="text-white small fw-600">Base URL</div>
                    <code style="color:#7dd3fc;font-size:11px;">{{ url('/api/v1') }}</code>
                </div>
                <div class="api-nav-section">Authentication</div>
                <a href="#auth" class="api-nav-item">API Authentication</a>

                <div class="api-nav-section">CRM Resources</div>
                <a href="#contacts" class="api-nav-item">Contacts</a>
                <a href="#companies" class="api-nav-item">Companies</a>
                <a href="#leads" class="api-nav-item">Leads</a>
                <a href="#deals" class="api-nav-item">Deals</a>
                <a href="#tasks" class="api-nav-item">Tasks</a>

                <div class="api-nav-section">Sales</div>
                <a href="#products" class="api-nav-item">Products</a>
                <a href="#quotes" class="api-nav-item">Quotes</a>
                <a href="#invoices" class="api-nav-item">Invoices</a>

                <div class="api-nav-section">Webhooks</div>
                <a href="#webhooks" class="api-nav-item">Webhook Events</a>
                <a href="#webhook-payload" class="api-nav-item">Payload Format</a>

                <div class="api-nav-section">Reference</div>
                <a href="#errors" class="api-nav-item">Error Codes</a>
                <a href="#rate-limits" class="api-nav-item">Rate Limits</a>
                <a href="#pagination" class="api-nav-item">Pagination</a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="col-lg-9">

        {{-- Your apps credentials bar --}}
        @if($apps)
        <div class="alert alert-primary d-flex align-items-center gap-3 mb-4" role="alert">
            <i class="bi bi-key-fill fs-5"></i>
            <div class="flex-fill">
                <div class="fw-600">Using application: {{ $apps->name }}</div>
                <code style="font-size:11px;">{{ $apps->client_id }}</code>
            </div>
            <a href="{{ route('developer.apps.show', $apps) }}" class="btn btn-sm btn-primary">View Keys</a>
        </div>
        @else
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            You have no active API apps.
            <a href="{{ route('developer.apps.create') }}" class="alert-link">Create one to get started.</a>
        </div>
        @endif

        {{-- Auth section --}}
        <div id="auth" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">Authentication</h4>
            <p>All API requests must include your <code>client_id</code> and <code>client_secret</code> as Bearer credentials. Every request is scoped to your tenant automatically.</p>

            <ul class="nav nav-tabs mb-2" id="authTabs">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#authCurl">cURL</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#authJs">JavaScript</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#authPhp">PHP</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="authCurl">
<pre class="code-block"><span class="comment"># Set your credentials</span>
<span class="kw">export</span> CLIENT_ID=<span class="str">"crm_your_client_id"</span>
<span class="kw">export</span> SECRET=<span class="str">"sk_your_secret_key"</span>

<span class="comment"># Make an authenticated request</span>
curl -X GET <span class="str">"{{ url('/api/v1/contacts') }}"</span> \
  -H <span class="str">"Authorization: Bearer $CLIENT_ID:$SECRET"</span> \
  -H <span class="str">"Accept: application/json"</span></pre>
                </div>
                <div class="tab-pane fade" id="authJs">
<pre class="code-block"><span class="kw">const</span> <span class="key">CRM_URL</span> = <span class="str">'{{ url('/api/v1') }}'</span>;
<span class="kw">const</span> <span class="key">token</span> = btoa(<span class="str">`${CLIENT_ID}:${SECRET}`</span>);

<span class="kw">const</span> <span class="key">res</span> = <span class="kw">await</span> fetch(<span class="str">`${CRM_URL}/contacts`</span>, {
  headers: {
    <span class="str">'Authorization'</span>: <span class="str">`Bearer ${token}`</span>,
    <span class="str">'Accept'</span>: <span class="str">'application/json'</span>,
  }
});
<span class="kw">const</span> <span class="key">data</span> = <span class="kw">await</span> res.json();</pre>
                </div>
                <div class="tab-pane fade" id="authPhp">
<pre class="code-block"><span class="kw">$client</span> = <span class="kw">new</span> \GuzzleHttp\Client();
<span class="kw">$response</span> = <span class="kw">$client</span>->get(<span class="str">'{{ url('/api/v1/contacts') }}'</span>, [
    <span class="str">'headers'</span> => [
        <span class="str">'Authorization'</span> => <span class="str">'Bearer '</span> . base64_encode(<span class="str">"$clientId:$secret"</span>),
        <span class="str">'Accept'</span>         => <span class="str">'application/json'</span>,
    ],
]);
<span class="kw">$contacts</span> = json_decode(<span class="kw">$response</span>->getBody(), true);</pre>
                </div>
            </div>
        </div>

        @php
        $endpoints = [
            'contacts' => [
                'title' => 'Contacts',
                'icon' => 'bi-people-fill',
                'color' => 'primary',
                'endpoints' => [
                    ['GET', '/contacts', 'List all contacts', [['page','integer','Page number (default: 1)'],['per_page','integer','Results per page (max: 100)'],['search','string','Search by name or email'],['status','string','Filter by status']]],
                    ['POST', '/contacts', 'Create a contact', [['first_name','string','Required. First name'],['last_name','string','Last name'],['email','string','Email address'],['phone','string','Phone number'],['company_id','integer','Linked company ID']]],
                    ['GET', '/contacts/{id}', 'Get a contact', []],
                    ['PUT', '/contacts/{id}', 'Update a contact', []],
                    ['DELETE', '/contacts/{id}', 'Delete a contact', []],
                ],
            ],
            'companies' => [
                'title' => 'Companies',
                'icon' => 'bi-building-fill',
                'color' => 'secondary',
                'endpoints' => [
                    ['GET', '/companies', 'List all companies', []],
                    ['POST', '/companies', 'Create a company', [['name','string','Required. Company name'],['industry','string','Industry category'],['website','string','Company website']]],
                    ['GET', '/companies/{id}', 'Get a company with contacts & deals', []],
                    ['PUT', '/companies/{id}', 'Update a company', []],
                    ['DELETE', '/companies/{id}', 'Delete a company', []],
                ],
            ],
            'leads' => [
                'title' => 'Leads',
                'icon' => 'bi-funnel-fill',
                'color' => 'warning',
                'endpoints' => [
                    ['GET', '/leads', 'List all leads', [['status','string','Filter by status'],['stage_id','integer','Filter by pipeline stage']]],
                    ['POST', '/leads', 'Create a lead', [['title','string','Required. Lead title'],['contact_id','integer','Contact ID'],['stage_id','integer','Pipeline stage ID'],['value','numeric','Estimated value']]],
                    ['GET', '/leads/{id}', 'Get a lead', []],
                    ['PATCH', '/leads/{id}/stage', 'Move lead to a new stage', [['stage_id','integer','Required. Target stage ID']]],
                    ['POST', '/leads/{id}/convert', 'Convert lead to deal', []],
                ],
            ],
            'deals' => [
                'title' => 'Deals',
                'icon' => 'bi-briefcase-fill',
                'color' => 'success',
                'endpoints' => [
                    ['GET', '/deals', 'List all deals', []],
                    ['POST', '/deals', 'Create a deal', [['name','string','Required. Deal name'],['contact_id','integer','Contact ID'],['stage_id','integer','Pipeline stage ID'],['value','numeric','Deal value'],['close_date','date','Expected close date']]],
                    ['GET', '/deals/{id}', 'Get a deal', []],
                    ['PATCH', '/deals/{id}/won', 'Mark deal as won', []],
                    ['PATCH', '/deals/{id}/lost', 'Mark deal as lost', [['reason','string','Loss reason']]],
                ],
            ],
            'products' => [
                'title' => 'Products',
                'icon' => 'bi-box-seam-fill',
                'color' => 'info',
                'endpoints' => [
                    ['GET', '/products', 'List all products', []],
                    ['POST', '/products', 'Create a product', [['name','string','Required'],['unit_price','numeric','Required. Unit price'],['sku','string','Stock-keeping unit'],['tax_rate','numeric','Tax rate %']]],
                    ['GET', '/products/{id}', 'Get a product', []],
                    ['PUT', '/products/{id}', 'Update a product', []],
                ],
            ],
            'invoices' => [
                'title' => 'Invoices',
                'icon' => 'bi-receipt-cutoff',
                'color' => 'danger',
                'endpoints' => [
                    ['GET', '/invoices', 'List invoices', [['status','string','Filter: draft|sent|paid|overdue']]],
                    ['POST', '/invoices', 'Create an invoice', []],
                    ['GET', '/invoices/{id}', 'Get an invoice', []],
                    ['PATCH', '/invoices/{id}/paid', 'Mark invoice as paid', [['paid_at','datetime','Payment timestamp']]],
                ],
            ],
        ];
        $methodColors = ['GET'=>'primary','POST'=>'success','PUT'=>'warning','PATCH'=>'warning','DELETE'=>'danger'];
        @endphp

        @foreach($endpoints as $key => $group)
        <div id="{{ $key }}" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">
                <i class="bi {{ $group['icon'] }} text-{{ $group['color'] }} me-2"></i>{{ $group['title'] }}
            </h4>
            @foreach($group['endpoints'] as $ep)
            @php [$method, $path, $desc, $params] = $ep; @endphp
            <div class="endpoint-block">
                <div class="endpoint-header" data-bs-toggle="collapse" data-bs-target="#ep_{{ $key }}_{{ $loop->index }}">
                    <span class="method-badge bg-{{ $methodColors[$method] ?? 'secondary' }}-subtle text-{{ $methodColors[$method] ?? 'secondary' }}">{{ $method }}</span>
                    <span class="endpoint-path">/api/v1{{ $path }}</span>
                    <span class="endpoint-desc d-none d-md-block">{{ $desc }}</span>
                    <i class="bi bi-chevron-down text-muted ms-2" style="font-size:12px;"></i>
                </div>
                <div class="endpoint-body collapse" id="ep_{{ $key }}_{{ $loop->index }}">
                    <div class="p-4">
                        <p class="text-muted mb-3">{{ $desc }}</p>
                        @if($params)
                        <h6 class="fw-600 mb-2">Parameters</h6>
                        <table class="table table-sm param-table mb-3">
                            <thead class="table-light"><tr><th>Name</th><th>Type</th><th>Description</th></tr></thead>
                            <tbody>
                                @foreach($params as $p)
                                <tr><td><code>{{ $p[0] }}</code></td><td class="text-muted">{{ $p[1] }}</td><td>{{ $p[2] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                        <h6 class="fw-600 mb-2">Example Response</h6>
<pre class="code-block">{
  <span class="key">"data"</span>: {
    <span class="key">"id"</span>: <span class="val">42</span>,
    <span class="key">"type"</span>: <span class="str">"{{ rtrim($key, 's') }}"</span>,
    <span class="key">"attributes"</span>: { <span class="comment">/* resource fields */</span> },
    <span class="key">"created_at"</span>: <span class="str">"{{ now()->toISOString() }}"</span>
  },
  <span class="key">"meta"</span>: { <span class="key">"tenant_id"</span>: <span class="val">1</span> }
}</pre>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach

        {{-- Webhooks --}}
        <div id="webhooks" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3"><i class="bi bi-broadcast text-info me-2"></i>Webhook Events</h4>
            <p>Subscribe to real-time events. We POST a signed JSON payload to your webhook URL whenever these events occur.</p>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Event</th><th>Triggered When</th></tr></thead>
                    <tbody>
                        @foreach(\App\Models\DeveloperApp::allEvents() as $slug => $label)
                        <tr>
                            <td><code>{{ $slug }}</code></td>
                            <td class="text-muted">{{ $label }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="webhook-payload" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">Webhook Payload Format</h4>
<pre class="code-block">{
  <span class="key">"event"</span>:      <span class="str">"contact.created"</span>,
  <span class="key">"timestamp"</span>: <span class="str">"{{ now()->toISOString() }}"</span>,
  <span class="key">"tenant_id"</span>: <span class="val">1</span>,
  <span class="key">"data"</span>: {
    <span class="key">"id"</span>:         <span class="val">42</span>,
    <span class="key">"first_name"</span>: <span class="str">"Jane"</span>,
    <span class="key">"last_name"</span>:  <span class="str">"Doe"</span>,
    <span class="key">"email"</span>:      <span class="str">"jane@example.com"</span>
  }
}</pre>
            <p class="text-muted small mt-2">Verify authenticity by checking the <code>X-CRM-Signature</code> header (HMAC-SHA256 of the payload body signed with your secret).</p>
        </div>

        {{-- Errors --}}
        <div id="errors" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">Error Codes</h4>
            <table class="table table-sm">
                <thead class="table-light"><tr><th>Code</th><th>Meaning</th></tr></thead>
                <tbody>
                    @foreach([[400,'Bad Request — invalid parameters'],[401,'Unauthorized — missing or invalid credentials'],[403,'Forbidden — insufficient permissions'],[404,'Not Found — resource does not exist'],[422,'Unprocessable Entity — validation failed'],[429,'Too Many Requests — rate limit exceeded'],[500,'Internal Server Error — contact support']] as [$code, $msg])
                    <tr><td><span class="badge bg-{{ $code < 500 ? 'warning' : 'danger' }}-subtle text-{{ $code < 500 ? 'warning' : 'danger' }}">{{ $code }}</span></td><td class="text-muted">{{ $msg }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Rate limits --}}
        <div id="rate-limits" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">Rate Limits</h4>
            <p>Each API application has a configurable rate limit (default: 1,000 requests/hour). Rate limit status is included in every response header:</p>
<pre class="code-block"><span class="key">X-RateLimit-Limit</span>:     <span class="val">1000</span>
<span class="key">X-RateLimit-Remaining</span>: <span class="val">984</span>
<span class="key">X-RateLimit-Reset</span>:     <span class="val">1713283200</span></pre>
        </div>

        {{-- Pagination --}}
        <div id="pagination" class="mb-5">
            <h4 class="fw-700 border-bottom pb-2 mb-3">Pagination</h4>
            <p>All list endpoints return paginated results. Use <code>page</code> and <code>per_page</code> query parameters.</p>
<pre class="code-block">{
  <span class="key">"data"</span>: [ <span class="comment">/* array of resources */</span> ],
  <span class="key">"meta"</span>: {
    <span class="key">"current_page"</span>: <span class="val">1</span>,
    <span class="key">"per_page"</span>:     <span class="val">25</span>,
    <span class="key">"total"</span>:        <span class="val">248</span>,
    <span class="key">"last_page"</span>:    <span class="val">10</span>
  },
  <span class="key">"links"</span>: {
    <span class="key">"next"</span>: <span class="str">"/api/v1/contacts?page=2"</span>,
    <span class="key">"prev"</span>: <span class="val">null</span>
  }
}</pre>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Highlight active doc section on scroll
const sections = document.querySelectorAll('[id]');
const navItems = document.querySelectorAll('.api-nav-item');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 120) current = s.id; });
    navItems.forEach(a => { a.classList.toggle('active', a.getAttribute('href') === '#' + current); });
});
</script>
@endpush
