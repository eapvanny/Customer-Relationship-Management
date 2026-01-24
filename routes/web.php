<?php

use App\Http\Controllers\AjaxBackendController;
use App\Http\Controllers\AsmimportController;
use App\Http\Controllers\AsmprogramController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProvinceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepoController;
use App\Http\Controllers\DisplaysubwholesaleController;
use App\Http\Controllers\ExclusiveController;
use App\Http\Controllers\GetmanagerController;
use App\Http\Controllers\MCustomerController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PosmController;
use App\Http\Controllers\ProvinceReportController;
use App\Http\Controllers\RegionsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantimportController;
use App\Http\Controllers\RetailController;
use App\Http\Controllers\RetailimportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolimportController;
use App\Http\Controllers\SeimportController;
use App\Http\Controllers\SeprogramController;
use App\Http\Controllers\SportclubController;
use App\Http\Controllers\SportclubimportController;
use App\Http\Controllers\SubwholesaleController;
use App\Http\Controllers\SubwholesaleImportController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WholesaleController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
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
    if(auth()->check()) {
        return redirect()->back();
    }else{
        return view('backend.login');
    }
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
    Route::resource('user', UserController::class)->except(['show']);

    Route::get('change-password', [UserController::class, 'showChangePasswordForm'])->name('change_password');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('update_password');
    Route::get('/lock', [UserController::class, 'lock'])->name('lockscreen');
    Route::post('/unlock', [UserController::class, 'unlock'])->name('unlock');

    Route::post('/user/disable/{id}', [UserController::class, 'disable'])->name('user.disable');
    Route::post('/user/enable/{id}', [UserController::class, 'enable'])->name('user.enable');
    Route::post('/users/{id}/update-profile-photo', [UserController::class, 'updateProfilePhoto'])
        ->name('users.updateProfilePhoto');

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
    Route::get('/export-report', [ReportController::class, 'export'])->name('report.export');
    Route::post('/reports/mark-as-seen', [ReportController::class, 'markAsSeen'])->name('reports.markAsSeen');

    //customer
    Route::get('/customers/outlet', [ReportController::class, 'getOutlets'])->name('customers.outlet');
    Route::get('/customers/getName', [ReportController::class, 'getCustomers'])->name('customers.getNames');
    Route::get('/customers/customer-types', [ReportController::class, 'getCustomerType'])->name('customers.getCustomerTypes');
    Route::resource('customer', CustomerController::class)->except(['show']);
    Route::get('/export-customer', [CustomerController::class, 'export'])->name('customer.export');
    Route::get('/get-depos-by-area', [CustomerController::class, 'getDeposByArea'])->name('get-depos-by-area');
    //Depo
    Route::resource('depo', DepoController::class);

    Route::post('ajax/get-user-area', [AjaxBackendController::class, 'getUserArea'])->name('ajax.getUserArea');


    // SE section start


    // SUB WHOLESALE Route
    // Route::resource('sub-wholesale', SubwholesaleController::class);
    // Route::get('/sub-wholesale/export', [SubwholesaleController::class, 'export'])->name('sub-wholesale.export');
    // Route::get('/sub-wholesale/excel/import', [SubwholesaleController::class, 'import'])->name('sub-wholesale.import');
    // Route::post('/subwholesale/import/save', [SubwholesaleController::class, 'saveImport'])->name('import.save');
    // Route::get('/sub-wholesale/by-area', [SubwholesaleController::class, 'getCustomersByArea'])->name('subwholesale.byArea');
    // Route::get('sub-wholesale/{id}/pictures', [SubwholesaleController::class, 'getPictures'])->name('sub-wholesale.picture');
    // Route::post('sub-wholesale/{id}/pictures/save', [SubwholesaleController::class, 'storePicture'])->name('sub-wholesale.storePicture');
    // Route::get('/subwholesale-export', [SubwholesaleController::class, 'export'])->name('sub-wholesale.export');



    // Route::resource('sub-wholesale-import', SubwholesaleImportController::class);
    // Route::get('/subwholesale-import-export', [SubwholesaleImportController::class, 'export'])->name('subwholesaleimport.export');


    // ASM Program
    Route::resource('asm', AsmprogramController::class);
    Route::post('/asm-import', [AsmimportController::class, 'import'])->name('asm.import');
    Route::get('/asm-export', [AsmprogramController::class, 'export'])->name('asm.export');
    // Route::get('/asm-get/by-area', [AsmprogramController::class, 'getCustomersByArea'])->name('asm.byArea');

    Route::get('/customers/outlet/asm', [AsmprogramController::class, 'getOutlets'])->name('asm_customers.outlet');
    Route::get('/customers/get/asm', [AsmprogramController::class, 'getCustomers'])->name('asm_customers.getName');
    Route::get('/customers/customer-type/asm', [AsmprogramController::class, 'getCustomerType'])->name('asm_customers.getCustomerType');
    Route::get('/get-reports/asm', [AsmprogramController::class, 'getReports'])->name('asm.getreport');


    // SE Program
    Route::resource('se', SeprogramController::class);
    Route::post('/se-import', [SeimportController::class, 'import'])->name('se.import');
    Route::get('/se-export', [SeprogramController::class, 'export'])->name('se.export');

    Route::get('/customers/outlet/se', [SeprogramController::class, 'getOutlets'])->name('se_customers.outlet');
    Route::get('/customers/get/se', [SeprogramController::class, 'getCustomers'])->name('se_customers.getName');
    Route::get('/customers/customer-type/se', [SeprogramController::class, 'getCustomerType'])->name('se_customers.getCustomerType');



    // Route::get('/user/fetch-managers', [UserController::class, 'fetchManagers'])->name('user.fetchManagers');
    Route::get('/user/fetch-hierarchy-users', [UserController::class, 'fetchHierarchyUsers'])->name('user.fetchHierarchyUsers');
    Route::get('/users/fetch-roles', [UserController::class, 'fetchRolesByType'])->name('user.fetchRolesByType');
    Route::get('user/fetch-asms', [UserController::class, 'fetchAsms'])->name('user.fetchAsms');
    Route::get('user/fetch-sups', [UserController::class, 'fetchSupervisors'])->name('user.fetchSupervisors');
    Route::get('user/fetch-rsms', [UserController::class, 'fetchRsms'])->name('user.fetchRsms');
    Route::get('user/fetch-managers', [UserController::class, 'fetchManagers'])->name('user.fetchManagers');

    Route::get('/users/fetch-manager', [UserController::class, 'fetchManagersOnly'])->name('user.fetchManagersOnly');

    // Report Province
    Route::resource('reports', ProvinceReportController::class);
    Route::get('/get-reports-pro', [ProvinceReportController::class, 'getReports'])->name('get-reports-pro');
    Route::get('/export-report-pro', [ProvinceReportController::class, 'export'])->name('report-pro.export');
    Route::get('/customers/outlet/province', [ProvinceReportController::class, 'getOutletsProvince'])->name('customers.outlet-pro');
    Route::get('/customers/get', [ProvinceReportController::class, 'getCustomersProvince'])->name('customers_pro.getName');
    Route::get('/customers/customer-type', [ProvinceReportController::class, 'getCustomerTypeProvince'])->name('customers.getCustomerType-pro');
    // Manager customer for marketing
    // Manager customer for marketing
    Route::resource('mcustomer', MCustomerController::class);
    Route::get('/mcustomer/province/by-area', [MCustomerController::class, 'getCustomersByArea'])->name('mcustomer.byArea');
    Route::get('/mcustomer/province/export', [MCustomerController::class, 'export'])->name('mcustomer.export');


    // Display program subwholesale route
    Route::resource('/display/sub-wholesale', DisplaysubwholesaleController::class)->names('displaysub');
    Route::get('/display/sub-wholesale/file/import', [DisplaysubwholesaleController::class, 'import'])->name('displaysub.import');
    Route::post('/display/sub-wholesale/file/import/save', [DisplaysubwholesaleController::class, 'saveImport'])->name('displaysub.import.save');

    Route::get('/sub-wholesale/by-area', [DisplaysubwholesaleController::class, 'getCustomersByArea'])->name('subwholesale.byArea');
    Route::get('/display/sub-wholesale/{id}/take/pictures', [DisplaysubwholesaleController::class, 'getPictures'])->name('displaysub.takePicture');
    Route::post('/display/sub-wholesale/{id}/take/pictures/save', [DisplaysubwholesaleController::class, 'storePicture'])->name('displaysub.storePicture');
    Route::get('/display/sub-wholesale/export/file/excel', [DisplaysubwholesaleController::class, 'export'])->name('displaysub.export');


     // Wholesale
    Route::resource('/display/wholesale', WholesaleController::class)->names('wholesale');
    Route::get('/display/wholesale/file/import', [WholesaleController::class, 'import'])->name('wholesale.import');
    Route::post('/display/wholesale/file/import/save', [WholesaleController::class, 'saveImport'])->name('wholesale.import.save');
    Route::get('/display/wholesale/{id}/take/picture', [WholesaleController::class, 'takePicture'])->name('wholesale.takePicture');
    Route::post('/display/wholesale/{id}/take/picture', [WholesaleController::class, 'savePicture'])->name('wholesale.savePicture');
    Route::get('/display/wholesale/file/export', [WholesaleController::class, 'export'])->name('wholesale.export');


    // RETAILs
    Route::resource('/display/retail', RetailController::class)->names('retail');
    Route::get('/display/retail/file/import', [RetailController::class, 'import'])->name('retail.import');
    Route::post('/display/retail/file/import/save', [RetailController::class, 'saveImport'])->name('retail.import.save');

    Route::get('/display/retail/{id}/take/picture', [RetailController::class, 'takePicture'])->name('retail.takePicture');
    Route::post('/display/retail/{id}/take/picture', [RetailController::class, 'savePicture'])->name('retail.savePicture');
    Route::get('/display/retail/file/export', [RetailController::class, 'export'])->name('retail.export');

    // Master data
        // 1. Region management route
        Route::resource('region', RegionsController::class);
        Route::get('region/list/export', [RegionsController::class, 'export'])->name('region.export');

        // 2. Outlet management route
        Route::resource('depot', OutletController::class)->names('outlet');
        Route::get('depot/export/excel', [OutletController::class, 'export'])->name('outlet.export');

        // 3. POSM Management
        Route::resource('posm/management', PosmController::class)->names('posm');
        Route::get('posm/list/export', [PosmController::class, 'export'])->name('posm.export');

        // 4. Customer province management
        Route::resource('/province/customer', CustomerProvinceController::class)->names('cp');
        Route::get('/customer/province/list/export', [CustomerProvinceController::class, 'export'])->name('cp.export');
        Route::get('/customer/get/outlet/region', [CustomerProvinceController::class, 'getDeposByArea'])->name('get-depos-by-region');
    // Master data


    // Exclusive start
        // School
        Route::resource('/exclusive/customer', ExclusiveController::class)->names('exclusive');
        Route::get('/exclusive/customer/file/export', [ExclusiveController::class, 'export'])->name('exclusive.export');

        Route::get('/exclusive/customer/outlet', [ExclusiveController::class, 'getOutlets'])->name('exclusive.outlet');
        Route::get('/exclusive/customer/get', [ExclusiveController::class, 'getCustomers'])->name('exclusive.getName');
        Route::get('/exclusive/customer/customer-type', [ExclusiveController::class, 'getCustomerType'])->name('exclusive.getCustomerType');


        // School
        Route::resource('/exclusive/school', SchoolController::class)->names('school');
        Route::get('/exclusive/school/file/export', [SchoolController::class, 'export'])->name('school.export');

        Route::get('/exclusive/school/outlet', [SchoolController::class, 'getOutlets'])->name('school.outlet');
        Route::get('/exclusive/school/get', [SchoolController::class, 'getCustomers'])->name('school.getName');
        Route::get('/exclusive/school/customer-type', [SchoolController::class, 'getCustomerType'])->name('school.getCustomerType');


            // Restaurant
        Route::resource('/exclusive/restaurant', RestaurantController::class);
        Route::get('/exclusive/restaurant/file/export', [RestaurantController::class, 'export'])->name('restaurant.export');
        Route::get('/exclusive/restaurant/outlet', [RestaurantController::class, 'getOutlets'])->name('restaurant.outlet');
        Route::get('/exclusive/restaurant/get', [RestaurantController::class, 'getCustomers'])->name('restaurant.getName');
        Route::get('/exclusive/restaurant/customer-type', [RestaurantController::class, 'getCustomerType'])->name('restaurant.getCustomerType');


            // sport club
        Route::resource('/exclusive/sport-club', SportclubController::class)->names('sportClub');
        Route::get('/exclusive/sport-club/file/export', [SportclubController::class, 'export'])->name('sportClub.export');
        Route::get('/exclusive/sport-club/outlet', [SportclubController::class, 'getOutlets'])->name('sportClub.outlet');
        Route::get('/exclusive/sport-club/get', [SportclubController::class, 'getCustomers'])->name('sportClub.getName');
        Route::get('/exclusive/sport-club/customer-type', [SportclubController::class, 'getCustomerType'])->name('sportClub.getCustomerType');

        // ------------ redo --------
        // Route::resource('/exclusive/{type}/customer', ExclusiveController::class)->names('exclusive');
        // Route::get('/exclusive/{type}/customer/export', [ExclusiveController::class, 'export'])->name('exclusive.export');

    // Exclusive end




    //photo preview
    Route::get('/photo/{encryptedPath}', function ($encryptedPath) {
        try {
            $key = substr(hash('sha256', config('app.key')), 0, 32);
            $data = base64_decode(strtr($encryptedPath, '-_', '+/'));
            $iv = substr($data, 0, 16);
            $encrypted = substr($data, 16);
            $decryptedPath = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

            $filePath = storage_path('app/public/' . $decryptedPath);

            if (file_exists($filePath)) {
                return response()->file($filePath);
            } else {
                abort(404, 'Image not found');
            }
        } catch (\Exception $e) {
            abort(403, 'Invalid or corrupted photo URL');
        }
    })->name('photo.view');


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
