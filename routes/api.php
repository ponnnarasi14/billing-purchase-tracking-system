<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InsightController;

Route::prefix('insights')->group(function () {
    Route::get('/high-variety', [InsightController::class, 'highVarietyCustomers']);
    Route::get('/stock-forecast', [InsightController::class, 'stockForecast']);
    Route::get('/repeat-customers', [InsightController::class, 'repeatCustomers']);
    Route::get('/high-demand-orders', [InsightController::class, 'highDemandOrders']);
});