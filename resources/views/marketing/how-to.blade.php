@extends('layouts.marketing')
@section('title', 'How-To Guide & Docs')

@push('styles')
<style>
.docs-hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); color: #fff; padding: 60px 0; text-align: center; }
.docs-hero h1 { font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 800; margin-bottom: 12px; }
.docs-sidebar { position: sticky; top: 80px; background: #f8faff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; }
.docs-sidebar .docs-nav-item { display: block; padding: 7px 12px; border-radius: 7px; text-decoration: none; color: #374151; font-size: 13px; transition: all .15s; margin-bottom: 2px; }
.docs-sidebar .docs-nav-item:hover { background: #e8f0fe; color: #0d6efd; }
.docs-sidebar .docs-nav-item.active { background: #0d6efd; color: #fff; font-weight: 600; }
.docs-sidebar .docs-nav-section { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; padding: 10px 12px 4px; margin-top: 4px; }
.docs-content { max-width: 800px; }
.docs-section { margin-bottom: 56px; }
.docs-section h2 { font-size: 1.6rem; font-weight: 700; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 24px; }
.docs-section h3 { font-size: 1.1rem; font-weight: 700; margin-top: 28px; margin-bottom: 12px; color: #1a202c; }
.step-card { background: #f8faff; border: 1px solid #e8eef8; border-radius: 12px; padding: 20px 24px; margin-bottom: 16px; }
.step-num { width: 32px; height: 32px; background: #0d6efd; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; }
.tip-box { background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 12px 16px; margin: 16px 0; font-size: 13px; }
.info-box { background: #eff6ff; border-left: 4px solid #0d6efd; border-radius: 0 8px 8px 0; padding: 12px 16px; margin: 16px 0; font-size: 13px; }
.module-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin: 16px 0; }
.module-item { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; }
.module-item i { font-size: 18px; }
.video-placeholder { background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%); border-radius: 12px; padding: 48px 24px; text-align: center; color: #fff; cursor: pointer; transition: all .2s; margin: 16px 0; }
.video-placeholder:hover { opacity: .9; transform: translateY(-1px); }
.video-placeholder .play-btn { width: 64px; height: 64px; background: rgba(255,255,255,.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 28px; border: 2px solid rgba(255,255,255,.4); }
kbd { background: #f0f0f0; border: 1px solid #ccc; border-radius: 4px; padding: 1px 6px; font-size: 12px; }
</style>
@endpush

@section('content')
<div class="docs-hero">
    <div class="container">
        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 rounded-pill px-3 py-1 mb-3" style="font-size:13px;color:rgba(255,255,255,.8);">
            <i class="bi bi-book-fill text-warning"></i> Complete guide — updated regularly
        </div>
        <h1>How-To Guide &amp; Documentation</h1>
        <p style="color:rgba(255,255,255,.7);font-size:16px;max-width:560px;margin:0 auto 24px;">Everything you need to get the most out of your CRM. From first login to advanced workflows.</p>
        <a href="#getting-started" class="btn btn-light btn-lg me-2"><i class="bi bi-play-circle me-2"></i>Start Here</a>
        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg"><i class="bi bi-rocket-takeoff me-2"></i>Try It Free</a>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">

        {{-- Sidebar navigation --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="docs-sidebar">
                <div class="docs-nav-section">Getting Started</div>
                <a href="#getting-started"   class="docs-nav-item">Quick Start</a>
                <a href="#first-login"        class="docs-nav-item">First Login Walkthrough</a>
                <a href="#account-setup"      class="docs-nav-item">Account Setup</a>

                <div class="docs-nav-section">Core Modules</div>
                <a href="#contacts"           class="docs-nav-item">Contacts</a>
                <a href="#companies"          class="docs-nav-item">Companies</a>
                <a href="#leads"              class="docs-nav-item">Leads Pipeline</a>
                <a href="#deals"              class="docs-nav-item">Deals</a>
                <a href="#tasks"              class="docs-nav-item">Tasks</a>

                <div class="docs-nav-section">Sales Tools</div>
                <a href="#products"           class="docs-nav-item">Products</a>
                <a href="#quotes"             class="docs-nav-item">Quotes &amp; Proposals</a>
                <a href="#invoicing"          class="docs-nav-item">Invoicing</a>

                <div class="docs-nav-section">Operations</div>
                <a href="#calendar"           class="docs-nav-item">Calendar</a>
                <a href="#helpdesk"           class="docs-nav-item">Help Desk</a>
                <a href="#documents"          class="docs-nav-item">Documents</a>
                <a href="#goals"              class="docs-nav-item">Goals</a>

                <div class="docs-nav-section">Advanced</div>
                <a href="#ai-tools"           class="docs-nav-item">AI &amp; Intelligence</a>
                <a href="#reports"            class="docs-nav-item">Reports</a>
                <a href="#settings"           class="docs-nav-item">Settings &amp; Team</a>
                <a href="#plans"              class="docs-nav-item">Plans &amp; Plugins</a>
            </div>
        </div>

        {{-- Main content --}}
        <div class="col-lg-9">
            <div class="docs-content">

                {{-- Getting Started --}}
                <div class="docs-section" id="getting-started">
                    <h2><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>Quick Start</h2>
                    <p>Get your CRM up and running in under 5 minutes. Follow these steps to go from sign-up to your first deal in the pipeline.</p>

                    <div class="video-placeholder" onclick="alert('Video tutorial coming soon!')">
                        <div class="play-btn"><i class="bi bi-play-fill"></i></div>
                        <div class="fw-600 fs-5">Watch: Complete CRM Setup in 5 Minutes</div>
                        <div style="color:rgba(255,255,255,.7);font-size:13px;margin-top:4px;">2 min 47 sec · No audio required</div>
                    </div>

                    @php $steps = [
                        ['Choose your plan', 'Visit the Pricing page and select the plan that fits your team size. You can start free — no credit card needed.'],
                        ['Create your account', 'Enter your company name, your name, email, and password. Your CRM workspace is created instantly.'],
                        ['Complete the onboarding walkthrough', 'When you first log in, an interactive guide will walk you through the key features step by step.'],
                        ['Add your first contact', 'Head to Contacts → Add Contact. Import a CSV or add manually. This is the foundation of your CRM.'],
                        ['Create a deal in the pipeline', 'Go to Deals and drag your first opportunity through the pipeline stages. Watch your revenue forecast update live.'],
                    ]; @endphp

                    @foreach($steps as $i => $step)
                    <div class="step-card d-flex gap-3 align-items-start">
                        <div class="step-num">{{ $i + 1 }}</div>
                        <div>
                            <div class="fw-600 mb-1">{{ $step[0] }}</div>
                            <div class="text-muted small">{{ $step[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- First Login --}}
                <div class="docs-section" id="first-login">
                    <h2><i class="bi bi-hand-wave text-warning me-2"></i>First Login Walkthrough</h2>
                    <p>When you log in for the first time, an <strong>interactive onboarding tour</strong> will automatically appear. It guides you through:</p>
                    <div class="row g-3 mb-3">
                        @foreach(['Dashboard overview','Adding your first contact','Setting up the pipeline','Creating your first deal','Inviting team members'] as $step)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span class="small fw-600">{{ $step }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="tip-box">
                        <strong><i class="bi bi-lightbulb me-1 text-warning"></i>Tip:</strong>
                        You can re-trigger the walkthrough at any time by clicking <strong>Help → Restart Tour</strong> in the top navigation.
                    </div>
                </div>

                {{-- Account Setup --}}
                <div class="docs-section" id="account-setup">
                    <h2><i class="bi bi-gear-fill text-secondary me-2"></i>Account Setup</h2>
                    <h3>Company Settings</h3>
                    <p>Go to <strong>Settings</strong> in the sidebar to configure your company name, email, logo, timezone, and currency. These details appear on your invoices and quotes.</p>
                    <h3>Invite Team Members</h3>
                    <p>Under <strong>Settings → Users</strong>, enter the email of each team member and assign a role:</p>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light"><tr><th>Role</th><th>Can Do</th></tr></thead>
                            <tbody>
                                <tr><td><strong>Tenant Admin</strong></td><td>Full access, manage settings, invite users</td></tr>
                                <tr><td><strong>Manager</strong></td><td>View all records, assign tasks, approve deals</td></tr>
                                <tr><td><strong>Staff</strong></td><td>Manage their own contacts, deals, and tasks</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <h3>Pipeline Stages</h3>
                    <p>Customise your <strong>deal pipeline stages</strong> under Settings → Pipeline. Default stages include Prospecting, Qualification, Proposal, Negotiation, Closed Won, and Closed Lost.</p>
                </div>

                {{-- Contacts --}}
                <div class="docs-section" id="contacts">
                    <h2><i class="bi bi-people-fill text-primary me-2"></i>Contacts</h2>
                    <p>Contacts are the foundation of your CRM. Every person you interact with — leads, customers, partners — lives here.</p>
                    <h3>Adding Contacts</h3>
                    <p>Click <strong>Contacts → Add Contact</strong>. Fill in the name, email, phone, company, and source. You can also add custom notes and link to a company record.</p>
                    <h3>Importing Contacts</h3>
                    <p>To bulk-import, go to <strong>Contacts → Import</strong> and upload a CSV. The required columns are <code>first_name</code>, <code>last_name</code>, and <code>email</code>. All other fields are optional.</p>
                    <div class="info-box"><i class="bi bi-info-circle me-1 text-primary"></i>
                        <strong>Pro tip:</strong> After importing, use the filter to segment contacts by source, status, or tag — then create targeted follow-up tasks in bulk.
                    </div>
                    <h3>Activities Timeline</h3>
                    <p>Every contact has an <strong>activity timeline</strong> showing calls, emails, meetings, tasks, and deals — so your whole team has full context.</p>
                </div>

                {{-- Companies --}}
                <div class="docs-section" id="companies">
                    <h2><i class="bi bi-building-fill text-secondary me-2"></i>Companies</h2>
                    <p>Companies represent the organisations your contacts work at. Link multiple contacts to a single company to see a full account view.</p>
                    <h3>Company 360° View</h3>
                    <p>Open any company to see all linked contacts, deals, tasks, invoices, and documents in one place. This is your account management hub.</p>
                </div>

                {{-- Leads --}}
                <div class="docs-section" id="leads">
                    <h2><i class="bi bi-funnel-fill text-warning me-2"></i>Leads Pipeline</h2>
                    <p>Leads are your inbound inquiries and early-stage prospects. The visual Kanban board lets you drag leads through your qualification stages.</p>
                    <h3>Kanban Board</h3>
                    <p>Drag and drop lead cards between columns. Each stage is colour-coded and shows the count and total value. You can customise stages under <strong>Settings → Pipeline</strong>.</p>
                    <h3>Converting a Lead</h3>
                    <p>When a lead is qualified, open it and click <strong>Convert to Deal</strong>. This creates a deal record linked to the same contact and company.</p>
                </div>

                {{-- Deals --}}
                <div class="docs-section" id="deals">
                    <h2><i class="bi bi-briefcase-fill text-success me-2"></i>Deals</h2>
                    <p>Deals track revenue opportunities from proposal to close. Use the pipeline view to see forecast value at each stage.</p>
                    <h3>Deal Stages</h3>
                    <p>Your default stages are: <strong>Prospecting → Qualification → Proposal → Negotiation → Closed Won / Lost</strong>. Customise these under Settings.</p>
                    <h3>Attaching Quotes</h3>
                    <p>From any deal, click <strong>Create Quote</strong> to generate a professional proposal linked to this deal. The quote PDF can be sent directly to the prospect.</p>
                </div>

                {{-- Products --}}
                <div class="docs-section" id="products">
                    <h2><i class="bi bi-box-seam-fill text-info me-2"></i>Products &amp; Services</h2>
                    <p>The Products catalog stores your pricing, SKUs, and descriptions. Products can be added to quotes and invoices as line items.</p>
                    <h3>Setting Up Your Catalog</h3>
                    <p>Go to <strong>Products → Add Product</strong>. Enter the name, unit price, cost price, tax rate, and category. The system automatically calculates your gross margin.</p>
                    <div class="tip-box"><i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Tip:</strong> Set up your full product catalog before creating quotes — this lets you pick items from a dropdown and auto-fills the description and price.
                    </div>
                </div>

                {{-- Quotes --}}
                <div class="docs-section" id="quotes">
                    <h2><i class="bi bi-file-earmark-ruled-fill text-warning me-2"></i>Quotes &amp; Proposals</h2>
                    <p>Create professional quotes with line items, discounts, tax, and your company's branding. Export to PDF in one click.</p>
                    <h3>Creating a Quote</h3>
                    <p>Go to <strong>Quotes → New Quote</strong>. Add line items by selecting from your product catalog or typing a custom description. The total updates in real time.</p>
                    <h3>Sending a Quote</h3>
                    <p>Change the status to <strong>Sent</strong> and click the PDF button to download a branded PDF. When the client accepts, update the status to <strong>Accepted</strong> — the value rolls up to your revenue reporting.</p>
                </div>

                {{-- Invoicing --}}
                <div class="docs-section" id="invoicing">
                    <h2><i class="bi bi-receipt-cutoff text-danger me-2"></i>Invoicing</h2>
                    <p>Generate, track, and manage invoices. Mark them as paid and export to PDF for your accounting records.</p>
                    <h3>Invoice Workflow</h3>
                    <p>Draft → Sent → Paid / Overdue. The dashboard shows your outstanding receivables and overdue amounts at a glance.</p>
                </div>

                {{-- Calendar --}}
                <div class="docs-section" id="calendar">
                    <h2><i class="bi bi-calendar3 text-primary me-2"></i>Calendar &amp; Appointments</h2>
                    <p>Schedule calls, demos, meetings, and follow-ups. The month-view calendar gives your whole team visibility on upcoming activities.</p>
                    <h3>Scheduling an Appointment</h3>
                    <p>Click <strong>Schedule</strong> or click any day on the calendar. Set the type (call, meeting, demo), assign a contact, set the location (Zoom link, office address), and pick a colour for visual grouping.</p>
                </div>

                {{-- Help Desk --}}
                <div class="docs-section" id="helpdesk">
                    <h2><i class="bi bi-headset text-info me-2"></i>Help Desk</h2>
                    <p>Manage customer support requests with a full ticketing system. Assign tickets to agents, add internal notes, and track resolution times.</p>
                    <h3>Ticket Lifecycle</h3>
                    <p>Open → In Progress → Pending → Resolved → Closed. You can change the status from within the reply interface — no need to leave the conversation.</p>
                    <h3>Internal Notes</h3>
                    <p>Use <strong>Internal Note</strong> to add comments visible only to your team — perfect for escalation context or client background notes.</p>
                </div>

                {{-- Documents --}}
                <div class="docs-section" id="documents">
                    <h2><i class="bi bi-folder2-open text-warning me-2"></i>Documents</h2>
                    <p>Upload and organise files — contracts, NDAs, proposals, images — and link them directly to contacts or deals.</p>
                    <h3>Uploading a Document</h3>
                    <p>Click <strong>Upload</strong>, select a file (up to 20 MB), add a title and category, and optionally link it to a contact or deal. The system auto-detects the file type and shows the appropriate icon.</p>
                </div>

                {{-- Goals --}}
                <div class="docs-section" id="goals">
                    <h2><i class="bi bi-bullseye text-success me-2"></i>Goals &amp; Targets</h2>
                    <p>Set revenue targets, deal quotas, lead goals, and contact acquisition targets for your team. The system automatically tracks progress from live CRM data.</p>
                    <h3>How Progress is Calculated</h3>
                    <ul class="text-muted">
                        <li><strong>Revenue goal:</strong> sum of won deals within the date range</li>
                        <li><strong>Deals won:</strong> count of deals marked as won</li>
                        <li><strong>Leads created:</strong> count of new leads in the period</li>
                        <li><strong>Contacts added:</strong> count of new contacts in the period</li>
                    </ul>
                </div>

                {{-- AI Tools --}}
                <div class="docs-section" id="ai-tools">
                    <h2><i class="bi bi-stars text-purple me-2" style="color:#6f42c1"></i>AI &amp; Intelligence <span class="badge bg-primary ms-1" style="font-size:10px;">Pro</span></h2>
                    <p>The AI module gives your team powerful insights without leaving the CRM.</p>
                    <h3>Features</h3>
                    <div class="module-grid">
                        <div class="module-item"><i class="bi bi-graph-up-arrow text-success"></i>Pipeline Intelligence</div>
                        <div class="module-item"><i class="bi bi-robot text-primary"></i>AI Assistant</div>
                        <div class="module-item"><i class="bi bi-envelope-paper text-info"></i>Email Composer</div>
                        <div class="module-item"><i class="bi bi-shield-fill-check text-danger"></i>Lead Scoring</div>
                    </div>
                </div>

                {{-- Reports --}}
                <div class="docs-section" id="reports">
                    <h2><i class="bi bi-bar-chart-fill text-primary me-2"></i>Reports &amp; Analytics</h2>
                    <p>Get a bird's-eye view of your sales performance. All reports can be exported to PDF.</p>
                    <h3>Available Reports</h3>
                    <ul>
                        <li>Revenue by period and stage</li>
                        <li>Lead conversion funnel</li>
                        <li>Deal win/loss analysis</li>
                        <li>Team activity summary</li>
                        <li>Top contacts by deal value</li>
                    </ul>
                </div>

                {{-- Settings --}}
                <div class="docs-section" id="settings">
                    <h2><i class="bi bi-gear-fill text-secondary me-2"></i>Settings &amp; Team</h2>
                    <p>Configure your workspace, invite users, and manage pipeline stages all from the Settings page.</p>
                    <div class="info-box"><i class="bi bi-shield me-1 text-primary"></i>
                        Only <strong>Tenant Admins</strong> can access Settings. Regular staff can only see their own records unless a manager grants broader access.
                    </div>
                </div>

                {{-- Plans --}}
                <div class="docs-section" id="plans">
                    <h2><i class="bi bi-puzzle-fill text-success me-2"></i>Plans &amp; Plugins</h2>
                    <p>Every module in the CRM is a <strong>plugin</strong> that can be enabled or disabled per account. Your plan determines which plugins are available.</p>
                    <p>To see what's included on your plan, visit the <a href="{{ route('pricing') }}" class="text-primary">Pricing page</a>. To upgrade, contact your admin or visit Settings.</p>
                    <div class="tip-box"><i class="bi bi-lightbulb text-warning me-1"></i>
                        <strong>Super Admins</strong> can toggle individual plugins per tenant through the Admin Panel — allowing custom configurations beyond standard plan tiers.
                    </div>
                </div>

                <div class="text-center p-5 bg-light rounded-3">
                    <h4 class="fw-700">Still have questions?</h4>
                    <p class="text-muted">Can't find what you're looking for? We're here to help.</p>
                    <a href="{{ route('register') }}" class="btn btn-primary me-2"><i class="bi bi-rocket-takeoff me-1"></i>Start Free</a>
                    <a href="{{ route('pricing') }}" class="btn btn-outline-secondary">View Plans</a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Highlight active doc section in sidebar on scroll
const sections = document.querySelectorAll('.docs-section[id]');
const navItems = document.querySelectorAll('.docs-nav-item');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => { if (window.scrollY >= s.offsetTop - 120) current = s.id; });
    navItems.forEach(a => {
        a.classList.toggle('active', a.getAttribute('href') === '#' + current);
    });
});
</script>
@endpush
