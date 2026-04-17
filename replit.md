# Enterprise CRM — Laravel 12

## Overview
A world-class, enterprise-grade multitenant CRM platform built with Laravel 12 (PHP 8.2), Bootstrap 5 (hosted locally), SQLite, and a full plugin/module system gated by subscription plan tier.

## Public Marketing Pages
- `/` — redirects to `/pricing` when not logged in
- `/pricing` — full pricing page with Free/Starter/Pro/Enterprise plan cards, monthly/annual toggle, feature comparison table, FAQ, CTA
- `/how-to` — full documentation page with sticky sidebar nav, step-by-step guides for every module, video placeholder
- `/register?plan=starter` — pre-selects plan badge on registration; passes `plan` to tenant creation
- First login shows a 5-step onboarding walkthrough modal (auto-dismissed, stored in `users.onboarding_completed_at`)
- "Restart Tour" available in the user avatar dropdown menu

## Subdomain & Custom Domain Provisioning (`/settings/domain`)
- **Subdomain** (Starter+): claim `yourname.app-host.com` — instant, no DNS needed
- **Custom Domain** (Pro+): connect `crm.yourcompany.com` with DNS TXT record verification
- **DNS Verification**: generates `_crm-verify` TXT token, polls DNS via `dns_get_record()`, stores `domain_verified_at`
- **SMTP / Email Settings**: per-tenant SMTP config (host, port, user, pass, from name/email, encryption)
- **Admin Domain Panel** (`/admin/domains`): view all subdomains + custom domains, force-approve or revoke any domain
- Plan gates: `canUseSubdomain()` = starter+, `canUseCustomDomain()` = pro+
- `Tenant::portalUrl()` returns active URL; `domainBadgeLabel()` for UI display
- `ResolveTenantByDomain` middleware on every request: maps custom domain / subdomain to a Tenant instance

## REST API (`/api/v1/*` — authenticated via Bearer token)
- **Auth**: `Authorization: Bearer {client_secret}` (from Developer App)
- **Rate limiting**: 120 req/min per token, enforced via Laravel `RateLimiter`
- **Endpoints**: Contacts (full CRUD), Companies (CRUD), Leads (CRUD), Deals (CRUD), Products (CRUD), Invoices (read), `/me`, `/ping`
- All requests auto-logged to `api_logs` table (method, endpoint, status, response time, IP)
- **Middleware**: `ApiAuthenticate` validates token, checks `is_active`, enforces IP whitelist

## Audit Log (`/audit-log` — plugin: `audit_log`)
- Real implementation replacing stub — `AuditLog` model + migration
- Filterable by user, event type, date range
- Stats: events today, this week, all time, active users today
- Expandable rows show old/new values as JSON diff, HTTP method + URL
- `AuditLog::record($event, $model, $old, $new)` static helper for logging anywhere

## Production Hardening
- **Security Headers**: `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy`, `Permissions-Policy`, HSTS (on HTTPS) via `SecurityHeaders` middleware
- **Custom Error Pages**: styled dark-theme pages for 403, 404, 419, 500
- **Rate Limiter**: `api` limiter defined in `AppServiceProvider` (120 req/min)
- **Email Log table** (`email_logs`): tracks sent emails per tenant with status, error, related record

## Developer Portal (`/developer` — Enterprise plugin: `api_access`)
- **Hub** `/developer` — live stats (API calls, errors, webhooks), 7-day traffic chart, recent request log, app list
- **Applications** `/developer/apps` — CRUD for API apps with auto-generated `client_id` / `client_secret`
- **App Detail** `/developer/apps/{id}` — dark-themed credentials display, masked secret with copy button, 1-click regenerate, webhook event subscriptions, IP whitelist, usage stats, recent logs
- **API Logs** `/developer/logs` — paginated request log with method/status/app/date filters, expandable request+response body
- **Webhook Deliveries** `/developer/webhooks` — delivery log with event/app/status filters, expandable payload+response
- **API Docs** `/developer/docs` — interactive documentation with sticky sidebar, all endpoints (Contacts, Companies, Leads, Deals, Products, Invoices), auth guide, webhook event table, error codes, rate limit & pagination reference, cURL/JS/PHP code snippets
- Models: `DeveloperApp`, `ApiLog`, `WebhookDelivery`

## Tech Stack
- **Framework**: Laravel 12, PHP 8.2
- **Database**: SQLite (row-level multitenancy via `tenant_id`)
- **Auth & RBAC**: Laravel Breeze + Spatie Permission (`super_admin`, `tenant_admin`, `manager`, `staff`)
- **PDF**: barryvdh/laravel-dompdf
- **QR Codes**: simplesoftwareio/simple-qrcode
- **Frontend**: Bootstrap 5 (hosted locally in `public/assets/vendor/`)

## Running the Application
```
php artisan serve --host=0.0.0.0 --port=5000
```

## Demo Credentials
| Role         | Email                | Password |
|--------------|----------------------|----------|
| Super Admin  | admin@crm.io         | password |
| Tenant Admin | demo@acme.com        | password |
| Manager      | manager@acme.com     | password |
| Staff        | staff@acme.com       | password |

## Architecture

### Multitenancy
Row-level multitenancy: every table has `tenant_id`. All queries are scoped by tenant.

### Plugin System
Every CRM module is a plugin with a minimum plan tier. Plugins are gated via `plugin:{slug}` middleware on every route group.

**Plan tiers** (each includes all lower tiers):
- `free` — Contacts, Companies, Leads, Tasks, Activities, Settings
- `starter` — + Deals, Products, Quotes, Invoicing, Calendar, Help Desk, Documents, Goals, Reports, Notifications
- `pro` — + Card Generator, AI & Intelligence, Email Campaigns (stub), Web Forms (stub), Contracts (stub), Sales Forecasting (stub)
- `enterprise` — + ID Verification (KYC), Commissions (stub), Territories (stub), Audit Log (stub), API & Webhooks (stub)

**Admin controls**: Super admin can enable/disable any plugin per tenant and change plan. Plugin cache invalidated on every change.

**Key files**:
- `app/Models/Plugin.php` — plugin definitions + plan hierarchy
- `app/Models/TenantPlugin.php` — per-tenant overrides
- `app/Models/Tenant.php` — `hasPlugin()`, `enabledPluginSlugs()`, cache logic
- `app/Http/Middleware/PluginAccessMiddleware.php` — route gate (alias: `plugin`)
- `database/seeders/PluginSeeder.php` — seeds all 27 plugins

## CRM Modules (27 total)

### Free Tier
| Module | Slug | Notes |
|--------|------|-------|
| Contacts | `contacts` | Full CRUD, import/export |
| Companies | `companies` | Full CRUD |
| Leads | `leads` | Kanban pipeline |
| Tasks | `tasks` | Full CRUD, polymorphic taskable |
| Activities | `activities` | Timeline |
| Settings | `settings` | Tenant config, users, stages |

### Starter Tier
| Module | Slug | Notes |
|--------|------|-------|
| Deals | `deals` | Kanban pipeline |
| Products | `products` | Catalog with pricing, margin calc |
| Quotes | `quotes` | Line items, PDF export, status tracking |
| Invoicing | `invoicing` | PDF export, payment tracking |
| Calendar | `calendar` | Month view calendar + appointments |
| Help Desk | `helpdesk` | Ticket system with replies |
| Documents | `documents` | File upload, morphic attachment |
| Goals | `goals` | Auto-calculated from live CRM data |
| Reports | `reports` | PDF export |
| Notifications | `notifications` | In-app alerts |

### Pro Tier
| Module | Slug | Notes |
|--------|------|-------|
| Card Generator | `cards` | Digital business cards, PDF/QR |
| AI & Intelligence | `ai_tools` | Lead scoring, pipeline insights |
| Email Campaigns | `email_campaigns` | Coming-soon stub |
| Web Forms | `web_forms` | Coming-soon stub |
| Contracts | `contracts` | Coming-soon stub |
| Sales Forecasting | `forecasting` | Coming-soon stub |

### Enterprise Tier
| Module | Slug | Notes |
|--------|------|-------|
| ID Verification (KYC) | `id_verification` | Full document upload + status |
| Commissions | `commissions` | Coming-soon stub |
| Territories | `territories` | Coming-soon stub |
| Audit Log | `audit_log` | Coming-soon stub |
| API & Webhooks | `api_access` | Coming-soon stub |

## Admin Panel
- `/admin` — platform dashboard (cross-tenant stats)
- `/admin/tenants` — manage all tenants, change plans
- `/admin/tenants/{id}/plugins` — enable/disable plugins per tenant
- `/admin/plugins` — system-wide plugin overview + edit min-plan per plugin
- `/admin/users` — platform-wide user list

## Storage
- `public/storage/documents/{tenant_id}/` — uploaded documents
- `public/storage/cards/{tenant_id}/` — card photos
- `public/storage/id-docs/{tenant_id}/` — KYC document uploads
- Storage link: `php artisan storage:link`

## Key Middleware
- `auth` — Laravel authentication
- `super_admin` — restricts admin panel to super_admin role
- `plugin:{slug}` — blocks access if tenant doesn't have plugin enabled for plan

## Important Models & Relationships
- `Task` — polymorphic `taskable()` (morphTo) — no direct contact_id/deal_id
- `Contact`, `Deal`, `Lead` — `tasks()` via `morphMany(Task::class, 'taskable')`
- `Quote` → `QuoteItem` → `Product` (optional FK)
- `Ticket` → `TicketReply`
- `Document` — `documentable()` polymorphic (Contact, Deal)
- `Goal` — auto-calculates `current_value` from live Deal/Lead/Contact data
- `Appointment` — has `user_id` (owner) + optional `contact_id`

## Seeder Architecture (Modular — 26 seeders)
Run with `php artisan db:seed`. Safe to re-run (truncates all data tables first).

Execution order (each seeder queries DB for its dependencies, no shared state):

| Order | Seeder | Creates |
|-------|--------|---------|
| 1 | `RoleSeeder` | 4 roles (super_admin, tenant_admin, manager, staff) |
| 2 | `TenantSeeder` | 2 tenants + 5 users + 8 CRM settings (key-value) |
| 3 | `PipelineSeeder` | 12 pipeline stages (6 deal + 6 lead) |
| 4 | `CompanySeeder` | 12 companies |
| 5 | `ContactSeeder` | 25 contacts |
| 6 | `LeadSeeder` | 15 leads |
| 7 | `DealSeeder` | 18 deals |
| 8 | `TaskSeeder` | 20 tasks |
| 9 | `ActivitySeeder` | 20 activities (polymorphic to contacts/leads/deals) |
| 10 | `AppointmentSeeder` | 12 appointments |
| 11 | `ProductSeeder` | 15 products (CRM plans, services, add-ons) |
| 12 | `InvoiceSeeder` | 12 invoices + 30 line items |
| 13 | `QuoteSeeder` | 8 quotes + 25 line items (linked to products) |
| 14 | `TicketSeeder` | 10 tickets + 19 replies |
| 15 | `DocumentSeeder` | 10 documents (polymorphic to contacts/companies/deals) |
| 16 | `GoalSeeder` | 8 goals (revenue, deals_won, leads_created, etc.) |
| 17 | `CardSeeder` | 4 card templates + 7 cards |
| 18 | `NotificationSeeder` | 16 CRM notifications |
| 19 | `EmailCampaignSeeder` | 5 campaigns + 45 recipient records |
| 20 | `WebFormSeeder` | 3 forms + 9 submissions |
| 21 | `ContractSeeder` | 3 contract templates + 7 contracts |
| 22 | `ForecastingSeeder` | 16 sales quotas (quarterly per rep) |
| 23 | `CommissionSeeder` | 3 commission plans + 4 commissions |
| 24 | `TerritorySeeder` | 6 territories + 10 territory-user assignments |
| 25 | `DeveloperSeeder` | 3 developer API apps |
| 26 | `PluginSeeder` | 27 plugins |

Demo credentials: `admin@crm.io` / `password` (super_admin), `demo@acme.com` / `password` (enterprise tenant admin)
