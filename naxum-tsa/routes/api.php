<?php

declare(strict_types=1);

use App\Http\Controllers\CommissionReportController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\TopDistributorsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API endpoints for the Naxum TSA Backend application.
| All routes are prefixed with /api automatically.
|
*/

// Health check endpoint
Route::get('/health', HealthCheckController::class);

// Commission Report Routes (Task 1)
// GET /api/commission-report - Get paginated commission report with optional filters
// GET /api/commission-report/order/{orderId}/items - Get items for a specific order
Route::prefix('commission-report')->group(function (): void {
    Route::get('/', [CommissionReportController::class, 'index'])
        ->name('commission-report.index');

    Route::get('/order/{orderId}/items', [CommissionReportController::class, 'orderItems'])
        ->where('orderId', '[0-9]+')
        ->name('commission-report.order-items');
});

// Top Distributors Routes (Task 2)
// GET /api/top-distributors - Get top 200 distributors by total sales
Route::get('/top-distributors', [TopDistributorsController::class, 'index'])
    ->name('top-distributors.index');
