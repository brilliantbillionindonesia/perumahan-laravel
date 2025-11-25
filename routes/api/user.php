<?php
use App\Http\Controllers\Api\UserController;

Route::middleware(['permission:manage_users'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::get('show', [UserController::class, 'show']);
        Route::post('store', [UserController::class, 'store']);
        Route::put('role', [UserController::class, 'changeRole']);
        Route::prefix('citizen')->group(function () {
            Route::post('sync', [UserController::class, 'syncCitizen']);
        });

    });
});


