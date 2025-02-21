<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('backend.login');
})->middleware('auth');
Route::get('/login', function () {
    return view('backend.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('user.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget.password');
Route::post('/forget-password', [AuthController::class, 'forgetPasswordPost'])->name('forget.password.post');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset.password');
Route::post('/reset-password', [AuthController::class, 'resetPasswordPost'])->name('reset.password.post');

Route::group(['middleware' => 'auth'], function() {

    Route::resource('dashboard', DashboardController::class);
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Route::get('/dashboard/ticket-data', [DashboardController::class, 'getTicketData'])->name('dashboard.ticket-data');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'profile'])->name('profile');

    //User
    Route::resource('user', UserController::class);
    Route::get('change-password', [UserController::class, 'showChangePasswordForm'])->name('change_password');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('update_password');
    Route::get('/lock', [UserController::class, 'lock'])->name('lockscreen');

    // Route::get('/user', [UserController::class, 'index'])->name('user.index');
    // Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    // Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    // Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    // Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    // Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
    Route::post('/users/{id}/update-profile-photo', [UserController::class, 'updateProfilePhoto'])
        ->name('users.updateProfilePhoto');

    // Route for switching languages
    Route::get('/set-lang/{lang}', [UserController::class, 'setLanguage'])->name('user.set_lang');


    //role
    Route::resource('role', RoleController::class);

    // Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    // Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    // Route::post('/role/store', [RoleController::class, 'store'])->name('role.store');
    // Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    // Route::put('/role/update/{id}', [RoleController::class, 'update'])->name('role.update');
    // Route::delete('/role/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete');

    //permission
    Route::resource('permission', PermissionController::class);
    
    // Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    // Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
    // Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');
    // Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
    // Route::put('/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update');
    // Route::delete('/permission/delete/{id}', [PermissionController::class, 'destroy'])->name('permission.delete');

    // Translation Routes
    Route::prefix('translations')->name('translation.')->group(function () {
        // List all translations
        Route::get('/', [TranslationController::class, 'index'])->name('index');

        // Store translation
        Route::post('/store', [TranslationController::class, 'store'])->name('store');

        // Update translation
        Route::put('/{id}', [TranslationController::class, 'update'])->name('update');

        // Delete translation
        Route::delete('/{id}', [TranslationController::class, 'destroy'])->name('destroy');

        // Import translations
        Route::post('/import', [TranslationController::class, 'import'])->name('import');

        //Setting Import
        Route::get('/export', [TranslationController::class, 'export'])->name('export');
    });
});