<?php

use App\Http\Controllers\Api\V1\CompanyApiController;
use App\Http\Controllers\Api\V1\ContactApiController;
use App\Http\Controllers\Api\V1\DealApiController;
use App\Http\Controllers\Api\V1\InvoiceApiController;
use App\Http\Controllers\Api\V1\LeadApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM REST API v1
|
| Auth: Bearer token (client_secret from your Developer App)
| Base URL: /api/v1
| Rate limit: per-app limit stored on developer_apps.rate_limit
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['throttle:api', \App\Http\Middleware\ApiAuthenticate::class])->group(function () {

    // ── Contacts ────────────────────────────────────────────────────────────
    Route::get   ('/contacts',       [ContactApiController::class, 'index']);
    Route::post  ('/contacts',       [ContactApiController::class, 'store']);
    Route::get   ('/contacts/{id}',  [ContactApiController::class, 'show']);
    Route::patch ('/contacts/{id}',  [ContactApiController::class, 'update']);
    Route::delete('/contacts/{id}',  [ContactApiController::class, 'destroy']);

    // ── Companies ───────────────────────────────────────────────────────────
    Route::get   ('/companies',      [CompanyApiController::class, 'index']);
    Route::post  ('/companies',      [CompanyApiController::class, 'store']);
    Route::patch ('/companies/{id}', [CompanyApiController::class, 'update']);
    Route::delete('/companies/{id}', [CompanyApiController::class, 'destroy']);

    // ── Leads ───────────────────────────────────────────────────────────────
    Route::get   ('/leads',          [LeadApiController::class, 'index']);
    Route::post  ('/leads',          [LeadApiController::class, 'store']);
    Route::patch ('/leads/{id}',     [LeadApiController::class, 'update']);
    Route::delete('/leads/{id}',     [LeadApiController::class, 'destroy']);

    // ── Deals ───────────────────────────────────────────────────────────────
    Route::get   ('/deals',          [DealApiController::class, 'index']);
    Route::post  ('/deals',          [DealApiController::class, 'store']);
    Route::get   ('/deals/{id}',     [DealApiController::class, 'show']);
    Route::patch ('/deals/{id}',     [DealApiController::class, 'update']);
    Route::delete('/deals/{id}',     [DealApiController::class, 'destroy']);

    // ── Products ────────────────────────────────────────────────────────────
    Route::get   ('/products',       [ProductApiController::class, 'index']);
    Route::post  ('/products',       [ProductApiController::class, 'store']);
    Route::patch ('/products/{id}',  [ProductApiController::class, 'update']);
    Route::delete('/products/{id}',  [ProductApiController::class, 'destroy']);

    // ── Invoices ────────────────────────────────────────────────────────────
    Route::get   ('/invoices',       [InvoiceApiController::class, 'index']);
    Route::get   ('/invoices/{id}',  [InvoiceApiController::class, 'show']);

    // ── Meta ────────────────────────────────────────────────────────────────
    Route::get('/me', function (Request $request) {
        $app    = $request->attributes->get('api_app');
        $tenant = $request->attributes->get('api_tenant');
        return response()->json([
            'data' => [
                'app'    => ['id' => $app->id, 'name' => $app->name, 'active' => (bool) $app->is_active, 'rate_limit' => $app->rate_limit],
                'tenant' => ['id' => $tenant->id, 'name' => $tenant->name, 'plan' => $tenant->plan],
            ],
        ]);
    });

    Route::get('/ping', fn() => response()->json(['pong' => true, 'timestamp' => now()->toIso8601String()]));
});

// ── 404 fallback for API ──────────────────────────────────────────────────
Route::fallback(fn() => response()->json(['error' => 'Not Found', 'message' => 'API endpoint not found.'], 404));
