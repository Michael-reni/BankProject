<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountHistoryController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/account/create_account', [AccountController::class, 'create_account']);
Route::get('/account/check_balance/{uuid}', [AccountController::class, 'check_balance']);
Route::put('/account/add_to_balance/{uuid}', [AccountController::class, 'add_to_balance']);
Route::put('/account/withdraw_from_balance/{uuid}', [AccountController::class, 'withdraw_from_balance']);
Route::get('/account/check_history/{uuid}', [AccountHistoryController::class, 'check_history']);