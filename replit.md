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
