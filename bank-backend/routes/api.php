<?php

use App\Http\Controllers\Api\Balance\BalanceTransactionController;
use App\Http\Controllers\Api\Balance\BalanceTransactionQueryController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

// routes without authentication
Route::controller(UserController::class)->prefix('auth')->group(function() {
    Route::post('/login', 'login')->name('user-login');
    Route::post('/register', 'create')->name('user-register');
});


// routes with authentication
Route::middleware('auth:sanctum')->group(function(){

    // create or update routes
    Route::controller(BalanceTransactionController::class)->prefix('transaction')->group(function(){
        Route::post('/create', 'store')->middleware('can:create_balance_transaction')->name('balance-transaction-create');
        Route::get('/accept/{transactionId}', 'accept')->middleware('can:accept_balance_transaction')->name('balance-transaction-accept');
        Route::get('/reject/{transactionId}', 'reject')->middleware('can:accept_balance_transaction')->name('balance-transaction-reject');
        Route::get('/getbyid/{transactionId}', 'getTransaction')->name('balance-transaction-getbyid');
    });

    // listing of transactions
    Route::controller(BalanceTransactionQueryController::class)->prefix('transaction')->group(function(){
        Route::get('/lasts', 'getLastTransactions')->middleware('can:last_balance_transaction')->name('balance-transaction-last');
        Route::get('/credit', 'getCreditTransactions')->middleware('can:credit_balance_transaction')->name('balance-transaction-credit');
        Route::get('/debit', 'getDebitTransactions')->middleware('can:debit_balance_transaction')->name('balance-transaction-debit');
        Route::get('/total-transaction-type', 'getTotalIncomingExpenses')->middleware('can:total_amount_balance_by_transaction_type')->name('balance-transaction-total-transaction-type');
        Route::get('/pending', 'getPedingTransactions')->middleware('can:pending_balance_transaction')->name('balance-transaction-pending');
    });

    Route::controller(UserController::class)->prefix('user')->group(function() {
        Route::get('/balance', 'getBalance')->middleware('can:get_balance')->name('user-balance');
        Route::get('/logout', 'logout')->name('user-logout');
    });
});
