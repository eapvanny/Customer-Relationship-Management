<?php

use App\Http\Controllers\API\CustomerController as APICustomer;
use App\Http\Controllers\API\DashboardController as APIDashboard;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ReportController as APIReport;
use App\Http\Controllers\API\UserController;
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
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/dashboard', [APIDashboard::class, 'index']);

    Route::get('/customers', [APICustomer::class, 'index']); // Existing endpoint
    Route::get('/depos', [APICustomer::class,'getDeposByArea']);
    Route::get('/customers/create', [APICustomer::class, 'create']); // New endpoint
    Route::post('/customers', [APICustomer::class, 'store']); // New endpoint

    Route::get('/reports', [APIReport::class, 'index']); // New endpoint: /api/reports
    Route::get('/reports/{id}', [APIReport::class, 'show']); // New endpoint
});
