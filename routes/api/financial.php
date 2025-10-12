<?php

use App\Http\Controllers\Api\Financial\CashBalanceController;
use App\Http\Controllers\Api\Financial\CategoryController;
use App\Http\Controllers\Api\Financial\DueController;
use App\Http\Controllers\Api\Financial\FeeController;
use App\Http\Controllers\Api\Financial\TransactionController;

Route::prefix('financial')->group(function () {
    Route::middleware(['permission:manage_transactions'])->group(function () {
        Route::prefix('dues')->group(function () {
            Route::get('list', [DueController::class, 'list']);
            Route::get('show', [DueController::class, 'show']);
            Route::get('detail', [DueController::class, 'detail']);
            Route::post('pay', [DueController::class, 'pay']);
        });
        Route::prefix('categories')->group(function () {
            Route::get('list', [CategoryController::class, 'list']);
            Route::get('show', [CategoryController::class, 'show']);
            Route::post('store', [CategoryController::class, 'store']);
            Route::put('update', [CategoryController::class, 'update']);
            Route::delete('delete', [CategoryController::class, 'delete']);
            Route::post('restore', [CategoryController::class, 'restore']);
        });
        Route::prefix('fees')->group(function () {
            Route::get('list', [FeeController::class, 'list']);
            Route::get('show', [FeeController::class, 'show']);
            Route::post('store', [FeeController::class, 'store']);
            Route::put('update', [FeeController::class, 'update']);
            Route::delete('delete', [FeeController::class, 'delete']);
            Route::post('restore', [FeeController::class, 'restore']);
        });
        Route::prefix('transactions')->group(function () {
            Route::post('store', [TransactionController::class, 'store']);
            Route::put('update', [TransactionController::class, 'update']);
            Route::delete('delete', [TransactionController::class, 'delete']);
        });
    });

    Route::prefix('cash-balances')->group(function () {
        Route::get('list', [CashBalanceController::class, 'list']);
        Route::get('show', [CashBalanceController::class, 'show']);
        Route::get('latest', [CashBalanceController::class, 'latest']);
    });
    Route::prefix('transactions')->group(function () {
        Route::get('list', [TransactionController::class, 'list']);
        Route::get('show', [TransactionController::class, 'show']);
        Route::get('category', [TransactionController::class, 'byCategory']);
    });

    Route::prefix('payment-proofs')->group(function () {
        Route::get('list', [TransactionController::class, 'proofPayment']);
        Route::get('show', [TransactionController::class, 'proofPaymentDetail']);
    });




});
