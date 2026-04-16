<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPluginController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\IdVerificationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return auth()->user()->hasRole('super_admin')
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    // Dashboard (always accessible)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (always accessible)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Plugin-gated modules ---

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

    // Invoicing
    Route::middleware('plugin:invoicing')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
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
    });

    // Notifications
    Route::middleware('plugin:notifications')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
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
    });
});
