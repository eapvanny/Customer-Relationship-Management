<?php

use App\Http\Controllers\API\CustomerController as APICustomer;
use App\Http\Controllers\API\DashboardController as APIDashboard;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ReportController as APIReport;
use App\Http\Controllers\API\UserController as APIUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [APIUserController::class, 'index']);
    Route::get('/users/{id}', [APIUserController::class, 'show']);
    Route::put('/profile', [APIUserController::class, 'updateUserProfile']);
    // Optional for multipart/form-data
    Route::post('/profile', [APIUserController::class, 'updateUserProfile']);
    Route::get('/dashboard', [APIDashboard::class, 'index']);

    Route::get('/customers', [APICustomer::class, 'index']); // Existing endpoint
    Route::get('/areas', [APICustomer::class, 'getAreas']);
    Route::get('/depos', [APICustomer::class, 'getDeposByArea']);
    Route::post('/customers', [APICustomer::class, 'store']); // New endpoint
    Route::put('/customers/{id}', [APICustomer::class, 'update']);
    Route::post('/customers/{id}', [APICustomer::class, 'update']); // Optional for Flutter if using multipart/form-data

    Route::get('/reports', [APIReport::class, 'index']); // New endpoint: /api/reports
    Route::get('/getCustomerReport', [APIReport::class, 'getCustomerReport']); // New endpoint: /api/getCustomerReport
    Route::get('/getCustomerType', [APIReport::class, 'getCustomerType']); // New endpoint: /api/getCustomerType
    Route::post('/reports', [APIReport::class, 'store']); // New endpoint
    Route::get('/reports/{id}', [APIReport::class, 'show']); // New endpoint
});
