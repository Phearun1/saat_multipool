<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);
Route::get('/machine', [App\Http\Controllers\InvestmentController::class, 'machine']);
Route::get('/view_all_machine', [ App\Http\Controllers\MachineController::class, 'view_all_machine']);
Route::get('/view_machine_detail', [ App\Http\Controllers\MachineController::class, 'view_machine_detail']);
Route::get('/view_list_machine_install', [ App\Http\Controllers\MachineController::class, 'view_list_machine_install']);
Route::get('/view_machine_location', [ App\Http\Controllers\MachineController::class, 'view_machine_location']);

Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'wallet']);

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);
//Language Translation

Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::post('/formsubmit', [App\Http\Controllers\HomeController::class, 'FormSubmit'])->name('FormSubmit');
