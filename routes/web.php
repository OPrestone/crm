<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPluginController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\IdVerificationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ModuleStubController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Public marketing pages
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/how-to', [MarketingController::class, 'howTo'])->name('how-to');
Route::post('/contact-sales', [MarketingController::class, 'contactSales'])->name('contact.sales');

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('pricing');
    return auth()->user()->hasRole('super_admin')
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    // Dashboard (always accessible)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Onboarding
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::post('/onboarding/restart', [OnboardingController::class, 'restart'])->name('onboarding.restart');

    // Profile (always accessible)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Plugin-gated modules ──────────────────────────────────────────────────

    // Contacts
    Route::middleware('plugin:contacts')->group(function () {
        Route::resource('contacts', ContactController::class);
    });

    // Companies
    Route::middleware('plugin:companies')->group(function () {
        Route::resource('companies', CompanyController::class);
    });

    // Leads
    Route::middleware('plugin:leads')->group(function () {
        Route::resource('leads', LeadController::class);
    });

    // Tasks
    Route::middleware('plugin:tasks')->group(function () {
        Route::resource('tasks', TaskController::class);
    });

    // Deals
    Route::middleware('plugin:deals')->group(function () {
        Route::resource('deals', DealController::class);
    });

    // Products
    Route::middleware('plugin:products')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Quotes
    Route::middleware('plugin:quotes')->group(function () {
        Route::resource('quotes', QuoteController::class);
        Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');
        Route::patch('/quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.status');
    });

    // Invoicing
    Route::middleware('plugin:invoicing')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    });

    // Help Desk / Tickets
    Route::middleware('plugin:helpdesk')->group(function () {
        Route::resource('tickets', TicketController::class);
        Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    });

    // Calendar / Appointments
    Route::middleware('plugin:calendar')->group(function () {
        Route::resource('appointments', AppointmentController::class);
    });

    // Documents
    Route::middleware('plugin:documents')->group(function () {
        Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    });

    // Goals & Targets
    Route::middleware('plugin:goals')->group(function () {
        Route::resource('goals', GoalController::class);
    });

    // Cards
    Route::middleware('plugin:cards')->group(function () {
        Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
        Route::get('/cards/templates/create', [CardController::class, 'createTemplate'])->name('cards.templates.create');
        Route::post('/cards/templates', [CardController::class, 'storeTemplate'])->name('cards.templates.store');
        Route::get('/cards/create', [CardController::class, 'create'])->name('cards.create');
        Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
        Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
        Route::get('/cards/{card}/edit', [CardController::class, 'edit'])->name('cards.edit');
        Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
        Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
        Route::get('/cards/{card}/pdf', [CardController::class, 'pdf'])->name('cards.pdf');
    });

    // AI Tools
    Route::middleware('plugin:ai_tools')->group(function () {
        Route::get('/ai', [AiController::class, 'index'])->name('ai.index');
        Route::get('/ai/insights', [AiController::class, 'insights'])->name('ai.insights');
        Route::get('/ai/email', [AiController::class, 'emailCompose'])->name('ai.email');
        Route::get('/ai/leads/{lead}/score', [AiController::class, 'leadScore'])->name('ai.lead-score');
        Route::get('/ai/deals/{deal}/insight', [AiController::class, 'dealInsight'])->name('ai.deal-insight');
        Route::get('/ai/contacts/{contact}/enrich', [AiController::class, 'contactEnrich'])->name('ai.contact-enrich');
    });

    // ID Verification (KYC)
    Route::middleware('plugin:id_verification')->group(function () {
        Route::resource('id-verification', IdVerificationController::class);
        Route::patch('/id-verification/{idVerification}/status', [IdVerificationController::class, 'updateStatus'])->name('id-verification.status');
    });

    // Reports
    Route::middleware('plugin:reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    });

    // Settings
    Route::middleware('plugin:settings')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings/tenant', [SettingsController::class, 'updateTenant'])->name('settings.updateTenant');
        Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.storeUser');
        Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.destroyUser');
        Route::post('/settings/stages', [SettingsController::class, 'storeStage'])->name('settings.storeStage');
        Route::delete('/settings/stages/{stage}', [SettingsController::class, 'destroyStage'])->name('settings.destroyStage');

        // Domain & Email settings
        Route::get('/settings/domain', [DomainController::class, 'index'])->name('settings.domain.index');
        Route::post('/settings/domain/subdomain', [DomainController::class, 'claimSubdomain'])->name('settings.domain.subdomain');
        Route::delete('/settings/domain/subdomain', [DomainController::class, 'removeSubdomain'])->name('settings.domain.subdomain.remove');
        Route::post('/settings/domain/custom', [DomainController::class, 'requestCustomDomain'])->name('settings.domain.custom');
        Route::post('/settings/domain/custom/verify', [DomainController::class, 'verifyCustomDomain'])->name('settings.domain.custom.verify');
        Route::delete('/settings/domain/custom', [DomainController::class, 'removeCustomDomain'])->name('settings.domain.custom.remove');
        Route::post('/settings/domain/smtp', [DomainController::class, 'updateSmtp'])->name('settings.domain.smtp');
    });

    // Notifications
    Route::middleware('plugin:notifications')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    });

    // ── Coming-soon stub modules (Pro / Enterprise) ───────────────────────────
    Route::middleware('plugin:email_campaigns')->get('/email-campaigns', [ModuleStubController::class, 'show'])->defaults('module', 'email_campaigns')->name('email_campaigns.index');
    Route::middleware('plugin:web_forms')->get('/web-forms', [ModuleStubController::class, 'show'])->defaults('module', 'web_forms')->name('web_forms.index');
    Route::middleware('plugin:contracts')->get('/contracts', [ModuleStubController::class, 'show'])->defaults('module', 'contracts')->name('contracts.index');
    Route::middleware('plugin:forecasting')->get('/forecasting', [ModuleStubController::class, 'show'])->defaults('module', 'forecasting')->name('forecasting.index');
    Route::middleware('plugin:commissions')->get('/commissions', [ModuleStubController::class, 'show'])->defaults('module', 'commissions')->name('commissions.index');
    Route::middleware('plugin:territories')->get('/territories', [ModuleStubController::class, 'show'])->defaults('module', 'territories')->name('territories.index');
    Route::middleware('plugin:audit_log')->get('/audit-log', [AuditController::class, 'index'])->name('audit_log.index');
    // Developer Portal (replaces stub)
    Route::middleware('plugin:api_access')->prefix('developer')->name('developer.')->group(function () {
        Route::get('/',                    [DeveloperController::class, 'index'])->name('index');
        Route::get('/apps',                [DeveloperController::class, 'apps'])->name('apps');
        Route::get('/apps/create',         [DeveloperController::class, 'createApp'])->name('apps.create');
        Route::post('/apps',               [DeveloperController::class, 'storeApp'])->name('apps.store');
        Route::get('/apps/{app}',          [DeveloperController::class, 'showApp'])->name('apps.show');
        Route::patch('/apps/{app}',        [DeveloperController::class, 'updateApp'])->name('apps.update');
        Route::patch('/apps/{app}/secret', [DeveloperController::class, 'regenerateSecret'])->name('apps.regenerate');
        Route::delete('/apps/{app}',       [DeveloperController::class, 'destroyApp'])->name('apps.destroy');
        Route::post('/apps/{app}/test-webhook', [DeveloperController::class, 'testWebhook'])->name('apps.test-webhook');
        Route::get('/logs',                [DeveloperController::class, 'logs'])->name('logs');
        Route::get('/webhooks',            [DeveloperController::class, 'webhookLogs'])->name('webhooks');
        Route::get('/docs',                [DeveloperController::class, 'docs'])->name('docs');
    });

    // Kanban AJAX (accessible if leads or deals plugin is active)
    Route::post('/kanban/update', function (\Illuminate\Http\Request $request) {
        $request->validate(['id' => 'required', 'stage_id' => 'required', 'type' => 'required|in:lead,deal']);
        $tenantId = auth()->user()->tenant_id;
        if ($request->type === 'lead') {
            $item = \App\Models\Lead::where('tenant_id', $tenantId)->findOrFail($request->id);
        } else {
            $item = \App\Models\Deal::where('tenant_id', $tenantId)->findOrFail($request->id);
        }
        $item->update(['stage_id' => $request->stage_id]);
        return response()->json(['success' => true]);
    })->name('kanban.update');

    // Admin Panel (Super Admin only)
    Route::prefix('admin')->name('admin.')->middleware('super_admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tenants', [AdminController::class, 'tenants'])->name('tenants');
        Route::get('/tenants/create', [AdminController::class, 'createTenant'])->name('tenants.create');
        Route::post('/tenants', [AdminController::class, 'storeTenant'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [AdminController::class, 'editTenant'])->name('tenants.edit');
        Route::patch('/tenants/{tenant}', [AdminController::class, 'updateTenant'])->name('tenants.update');
        Route::get('/users', [AdminController::class, 'users'])->name('users');

        // Plugin Management
        Route::get('/plugins', [AdminPluginController::class, 'index'])->name('plugins.index');
        Route::patch('/plugins/{plugin}', [AdminPluginController::class, 'update'])->name('plugins.update');
        Route::get('/tenants/{tenant}/plugins', [AdminPluginController::class, 'tenantPlugins'])->name('plugins.tenant');
        Route::post('/tenants/{tenant}/plugins/{plugin}/toggle', [AdminPluginController::class, 'toggle'])->name('plugins.toggle');
        Route::post('/tenants/{tenant}/plugins/plan', [AdminPluginController::class, 'bulkApplyPlan'])->name('plugins.plan');

        // Domain Management
        Route::get('/domains', [AdminController::class, 'domains'])->name('domains');
        Route::post('/tenants/{tenant}/domain/approve', [AdminController::class, 'approveDomain'])->name('domain.approve');
        Route::post('/tenants/{tenant}/domain/revoke', [AdminController::class, 'revokeDomain'])->name('domain.revoke');
    });
});
