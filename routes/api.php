<?php

use App\Http\Controllers\API\AppVersionController;
use App\Http\Controllers\API\CustomerController as APICustomer;
use App\Http\Controllers\API\DashboardController as APIDashboard;
use App\Http\Controllers\API\LiveLocationController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ReportController as APIReport;
use App\Http\Controllers\API\SlowController;
use App\Http\Controllers\API\UserController as APIUserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/users', [APIUserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

    // Users
    Route::get('/users', [APIUserController::class, 'index']);
    Route::get('/users/{id}', [APIUserController::class, 'show']);
    Route::put('/profile', [APIUserController::class, 'updateUserProfile']);
    Route::post('/profile', [APIUserController::class, 'updateUserProfile']);
    Route::put('/password', [APIUserController::class, 'updateUserPassword']);

    // Dashboard
    Route::get('/dashboard', [APIDashboard::class, 'index']);
    Route::get('/maindashboard', [APIDashboard::class, 'secondindex']);

    // Customers
    Route::get('/customers', [APICustomer::class, 'index']);
    Route::get('/areas', [APICustomer::class, 'getAreas']);
    Route::get('/depos', [APICustomer::class, 'getDeposByArea']);
    Route::get('/areaDepo', [APICustomer::class, 'getAllDepoArea']);
    Route::post('/customers', [APICustomer::class, 'store']);
    Route::get('/customers/export', [APICustomer::class, 'export']);
    Route::get('/customers/{id}', [APICustomer::class, 'show']);
    Route::put('/customers/{id}', [APICustomer::class, 'update']);
    Route::post('/customers/{id}', [APICustomer::class, 'update']);
    Route::get('/customers-phone', [SlowController::class, 'index']);
    Route::get('/customers-location', [APICustomer::class, 'getCustomerLocation']);
    Route::get('/customers/{id}/detail', [APICustomer::class, 'customerDetail']);

    // Reports
    Route::get('/reports', [APIReport::class, 'index']);
    Route::get('/getCustomerReport', [APIReport::class, 'getCustomerReport']);
    Route::get('/getCustomerType', [APIReport::class, 'getCustomerType']);
    Route::post('/reports', [APIReport::class, 'store']);
    Route::post('/reports/new', [APIReport::class, 'storenew']);
    Route::post('/reports/consumer', [APIReport::class, 'storeConsumer']);
    Route::get('/reports/export', [APIReport::class, 'export']);
    Route::get('/reports/{id}', [APIReport::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Live Location Tracking
    |--------------------------------------------------------------------------
    */
    Route::prefix('live-location')->group(function () {
        // Check In and start tracking
        Route::post('/start', [LiveLocationController::class, 'start']);
        // Update GPS location every 15 seconds
        Route::post('/update', [LiveLocationController::class, 'updateLocation']);
        // Check Out and stop tracking
        Route::post('/stop', [LiveLocationController::class, 'stop']);
        // Get current tracking status
        Route::get('/status', [LiveLocationController::class, 'status']);

    });

    // App Version
    Route::get('/app-version', [AppVersionController::class, 'check']);
    Route::put('/app-version/{id}', [AppVersionController::class, 'update']);

});
