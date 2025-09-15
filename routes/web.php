<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\LocationRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\AbaPayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Auth::routes();

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Shared route for all users
    Route::get('/',  [PoolController::class, 'viewAllPool'])->name('admin.view_all_pool');
    Route::get('/view_all_machine', [MachineController::class, 'view_all_machine'])->name('machines.all');
    Route::get('/view_machine_detail/{machine_id}', [MachineController::class, 'view_machine_detail'])->name('machines.detail');
    Route::get('/view_profile', [ProfileController::class, 'view_profile'])->name('profile.view');

    Route::get('/view_all_pool', [PoolController::class, 'viewAllPool'])->name('admin.view_all_pool');
        Route::get('/view_pool_detail/{id}', [PoolController::class, 'viewPoolDetail'])->name('view_pool_detail');

    // Routes for Investors (user_type = 1)
    Route::middleware(['role:1'])->group(function () {
        Route::get('/wallet', [WalletController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');




        Route::get('/sponsor_account', [SponsorController::class, 'view_sponsor_account'])->name('investment.sponsor_account');
        Route::post('/sponsor_account/sponsor', [SponsorController::class, 'sponsor'])->name('investment.sponsor_account.sponsor');
        Route::post('/sponsor_account/withdraw', [SponsorController::class, 'withdraw'])->name('investment.sponsor_account.withdraw');

        Route::get('/view_new_location_request', [LocationRequestController::class, 'viewNewLocationRequest'])->name('view_new_location_request');
        Route::post('/create_new_location_request', [LocationRequestController::class, 'createNewLocationRequest'])->name('create_new_location_request');
        Route::put('/edit_location_request/{id}', [LocationRequestController::class, 'updateLocationRequest'])->name('edit_location_request');
        Route::delete('/delete_location_request/{id}', [LocationRequestController::class, 'deleteLocationRequest'])->name('delete_location_request');

        Route::get('/view_referral', [ProfileController::class, 'viewReferral'])->name('view_referral');
        Route::post('/create_referral', [ProfileController::class, 'createReferral'])->name('create_referral');

        Route::post('/wallet/deposit', [AbaPayController::class, 'deposit'])->name('wallet.deposit')->middleware('auth');
        

        Route::get('/wallet/balance', [WalletController::class, 'getBalance'])->name('wallet.getBalance');


        Route::post('/wallet/transaction/update-status', [WalletController::class, 'updateTransactionStatus'])->name('wallet.updateTransactionStatus');
        Route::get('/wallet/transaction-status/{transactionId}', [WalletController::class, 'checkTransactionStatus']);

        Route::post('/invest_pool/{pool_id}', [PoolController::class, 'investPool'])->name('invest_pool');
    });

    // Routes for Admins (user_type = 5)
    Route::middleware(['auth', 'role:5'])->group(function () {
        Route::post('/admin/create_user', [AdminController::class, 'createUser'])->name('admin.user.create');
        // Route for updating a user
        Route::put('/admin/update_user/{userId}', [AdminController::class, 'updateUser'])->name('admin.update_user');
        // Route for deleting a user
        Route::delete('/admin/delete_user/{userId}', [AdminController::class, 'deleteUser'])->name('admin.delete_user');

        Route::get('/admin/view_all_users', [AdminController::class, 'userList'])->name('admin.users');
        Route::get('/admin/ajax_search_users', [AdminController::class, 'ajaxSearchUsers'])->name('admin.ajax_search_users');
        Route::get('/admin/view_users_detail/{id}', [AdminController::class, 'userDetails'])->name('admin.user.details');
        Route::get('/admin/view_maintenance_record', [AdminController::class, 'ViewAllMaintenanceRecords'])->name('admin.view.maintenance.record');
        Route::get('/admin/view_operational_partner_account', [AdminController::class, 'viewallOperationalPartnerAccount'])->name('admin.view_operational_partner_account');
        Route::get('/admin/view_operational_partner_account_detail/{id}', [AdminController::class, 'viewOperationalPartnerAccountDetail'])->name('admin.view_operational_partner_account_detail');
        Route::get('/admin/view_all_profit_distribution', [AdminController::class, 'viewProfitDistribution'])->name('admin.view_profit_distribution');
        Route::post('/machines/add', [MachineController::class, 'add_machine'])->name('machines.add');
        Route::put('/machines/update/{machine_id}', [MachineController::class, 'updateMachine'])->name('machines.update');
        Route::post('/admin/assign_profit', [AdminController::class, 'assignProfit'])->name('admin.assignProfit');
        Route::post('/admin/add_user_profit', [AdminController::class, 'addUserProfit'])->name('admin.addUserProfit');
        Route::get('/admin/search-users', [AdminController::class, 'searchUsers'])->name('admin.searchUsers');


        Route::delete('/admin/delete_user_profit/{user_id}/{machine_id}', [AdminController::class, 'deleteUserProfit'])->name('admin.deleteUserProfit');

        // Admin routes for location requests
        Route::get('/admin/view_new_location_requests', [AdminController::class, 'viewNewLocationRequests'])->name('admin.view_new_location_requests');
        Route::post('/admin/update_location_request_status/{id}', [AdminController::class, 'updateLocationRequestStatus'])->name('admin.update_location_request_status');
        Route::delete('/admin/delete_location_request/{id}', [AdminController::class, 'deleteLocationRequest'])->name('admin.delete_location_request');


        // Admin routes for referral requests
        Route::get('/admin/view_all_account_referral_requests', [AdminController::class, 'viewAllAccountReferralRequests'])->name('admin.view_all_account_referral_requests');
        Route::get('/admin/approve_referral/{id}', [AdminController::class, 'approveReferral'])->name('admin.approve_referral');
        Route::get('/admin/reject_referral/{id}', [AdminController::class, 'rejectReferral'])->name('admin.reject_referral');

        
        Route::post('/create_pool', [PoolController::class, 'createPool'])->name('admin.create_pool');
        Route::put('/update_pool/{id}', [PoolController::class, 'updatePool'])->name('admin.update_pool');
        Route::delete('/delete_pool/{id}', [PoolController::class, 'deletePool'])->name('admin.delete_pool');
    });
});


// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Language Translation
Route::get('set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'km'])) { // Only allow valid locales
        session()->put('locale', $locale);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('setLocale');

// Form submission route
Route::post('/formsubmit', [HomeController::class, 'FormSubmit'])->name('FormSubmit');
