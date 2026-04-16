# Enterprise CRM — Laravel 12

## Overview
A world-class, enterprise-grade multitenant CRM platform built with Laravel 12 (PHP 8.2), Bootstrap 5 (hosted locally), SQLite, and a full plugin/module system.

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
Row-level multitenancy: every table has `tenant_id`. The `TenantMiddleware` enforces isolation on all queries.

### Plugin System
Every CRM module is a plugin with a minimum plan tier. Plugins are gated via `plugin:{slug}` middleware on every route group.

**Plan tiers** (each includes all lower tiers):
- `free` — Contacts, Companies, Leads, Tasks, Settings
- `starter` — + Deals, Invoicing, Reports, Notifications
- `pro` — + Card Generator, AI & Intelligence
- `enterprise` — + ID Verification (KYC)

**Admin controls**: Super admin can enable/disable any plugin per tenant (overrides the plan default) and change a tenant's plan. Cache is invalidated on every change.

**Key files**:
- `app/Models/Plugin.php` — plugin definitions + plan hierarchy
- `app/Models/TenantPlugin.php` — per-tenant overrides
- `app/Models/Tenant.php` — `hasPlugin()`, `enabledPluginSlugs()`, cache logic
- `app/Http/Middleware/PluginAccessMiddleware.php` — route gate
- `database/seeders/PluginSeeder.php` — seeds all 12 plugins

## CRM Modules
| Module | Slug | Min Plan |
|--------|------|----------|
| Contacts | `contacts` | free |
| Companies | `companies` | free |
| Leads (Kanban) | `leads` | free |
| Tasks | `tasks` | free |
| Deals (Kanban) | `deals` | starter |
| Invoicing | `invoicing` | starter |
| Reports | `reports` | starter |
| Notifications | `notifications` | starter |
| Card Generator | `cards` | pro |
| AI & Intelligence | `ai_tools` | pro |
| ID Verification (KYC) | `id_verification` | enterprise |
| Settings | `settings` | free |

## Admin Panel
- `/admin` — platform dashboard (cross-tenant stats)
- `/admin/tenants` — manage all tenants, change plans
- `/admin/tenants/{id}/plugins` — enable/disable plugins per tenant
- `/admin/plugins` — system-wide plugin overview + edit min-plan per plugin
- `/admin/users` — platform-wide user list

## Storage
- `public/storage/cards/{tenant_id}/` — card photos
- `public/storage/id-docs/{tenant_id}/` — KYC document uploads
- Storage link: `php artisan storage:link`

## Middleware
- `auth` — Laravel authentication
- `super_admin` — restricts admin panel to super_admin role
- `plugin:{slug}` — blocks access if tenant doesn't have plugin enabled
