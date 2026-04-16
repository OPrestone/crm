<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contacts
    Route::resource('contacts', ContactController::class);

    // Companies
    Route::resource('companies', CompanyController::class);

    // Leads
    Route::resource('leads', LeadController::class);

    // Deals
    Route::resource('deals', DealController::class);

    // Tasks
    Route::resource('tasks', TaskController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    // Cards
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/cards/templates/create', [CardController::class, 'createTemplate'])->name('cards.templates.create');
    Route::post('/cards/templates', [CardController::class, 'storeTemplate'])->name('cards.templates.store');
    Route::get('/cards/create', [CardController::class, 'create'])->name('cards.create');
    Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::get('/cards/{card}/pdf', [CardController::class, 'pdf'])->name('cards.pdf');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    // Settings (Tenant Admin +)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/tenant', [SettingsController::class, 'updateTenant'])->name('settings.updateTenant');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.storeUser');
    Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.destroyUser');
    Route::post('/settings/stages', [SettingsController::class, 'storeStage'])->name('settings.storeStage');
    Route::delete('/settings/stages/{stage}', [SettingsController::class, 'destroyStage'])->name('settings.destroyStage');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Kanban AJAX update
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
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tenants', [AdminController::class, 'tenants'])->name('tenants');
        Route::get('/tenants/create', [AdminController::class, 'createTenant'])->name('tenants.create');
        Route::post('/tenants', [AdminController::class, 'storeTenant'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [AdminController::class, 'editTenant'])->name('tenants.edit');
        Route::patch('/tenants/{tenant}', [AdminController::class, 'updateTenant'])->name('tenants.update');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
    });
});
