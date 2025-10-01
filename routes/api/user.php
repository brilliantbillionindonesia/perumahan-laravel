<?php
use App\Http\Controllers\Api\UserController;

Route::middleware(['role:admin'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::post('store', [UserController::class, 'store']);
        Route::put('role', [UserController::class, 'changeRole']);
    });
});
