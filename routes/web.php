<?php

use App\Http\Controllers\AsmimportController;
use App\Http\Controllers\AsmprogramController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeimportController;
use App\Http\Controllers\SeprogramController;
use App\Http\Controllers\SubwholesaleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use App\Models\Customer;
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

// Route::get('/get-customer-by-outlet', [ReportController::class, 'getCustomerByOutlet'])->name('report.getCustomerByOutlet');

Route::group(['middleware' => ['auth', 'isAdmin']], function () {
    Route::get('/forget-password/{id}', [AuthController::class, 'forgetPassword'])->name('forget.password');
    Route::post('/forget-password/{id}', [AuthController::class, 'forgetPasswordPost'])->name('forget.password.post');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordPost'])->name('reset.password.post');
    Route::resource('dashboard', DashboardController::class);
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Route::get('/dashboard/ticket-data', [DashboardController::class, 'getTicketData'])->name('dashboard.ticket-data');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'profile'])->name('profile');

    //User
    Route::resource('user', UserController::class)->except('show');
    Route::get('change-password', [UserController::class, 'showChangePasswordForm'])->name('change_password');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('update_password');
    Route::get('/lock', [UserController::class, 'lock'])->name('lockscreen');
    Route::post('/unlock', [UserController::class, 'unlock'])->name('unlock');
    
    Route::post('/user/disable/{id}', [UserController::class, 'disable'])->name('user.disable');
    Route::post('/user/enable/{id}', [UserController::class, 'enable'])->name('user.enable');
    Route::post('/users/{id}/update-profile-photo', [UserController::class, 'updateProfilePhoto'])
        ->name('users.updateProfilePhoto');

    Route::get('/user/fetch-managers', [UserController::class, 'fetchManagers'])
        ->name('user.fetchManagers');

    // Route for switching languages
    Route::get('/set-lang/{lang}', [UserController::class, 'setLanguage'])->name('user.set_lang');


    //role
    Route::resource('role', RoleController::class);
    Route::post('role/fetch-permissions', [RoleController::class, 'fetchPermissions'])->name('role.fetch-permissions');

    //permission
    Route::resource('permission', PermissionController::class);

    //Report
    Route::resource('report', ReportController::class);
    Route::get('/get-reports', [ReportController::class, 'getReports'])->name('get-reports');
    Route::get('/export', [ReportController::class, 'export'])->name('report.export');
    Route::post('/reports/mark-as-seen', [ReportController::class, 'markAsSeen'])->name('reports.markAsSeen');

    //customer
    Route::get('/customers/by-area', [ReportController::class, 'getCustomersByArea'])->name('customers.byArea');
    Route::resource('customer', CustomerController::class)->except(['show']);

    // SE section start
    Route::resource('sub-wholesale', SubwholesaleController::class);
    Route::get('/subwholesale-export', [SubwholesaleController::class, 'export'])->name('subwholesale.export');


    Route::resource('retail', RetailController::class);
    Route::get('/retail-export', [RetailController::class, 'export'])->name('retail.export');
    

    Route::resource('asm', AsmprogramController::class);
    Route::post('/asm-import', [AsmimportController::class, 'import'])->name('asm.import');
    Route::get('/asm-export', [AsmprogramController::class, 'export'])->name('asm.export');


    Route::resource('se', SeprogramController::class);
    Route::post('/se-import', [SeimportController::class, 'import'])->name('se.import');
    Route::get('/se-export', [SeprogramController::class, 'export'])->name('se.export');

    
// SE section end 

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