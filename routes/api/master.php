<?php
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\OptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('master')->group(function () {
        Route::get('list', [MasterController::class, 'list']);
        Route::get('show', [MasterController::class, 'show']);
        Route::post('/store', [MasterController::class, 'store']);
        Route::put('/update', [MasterController::class, 'update']);
        Route::delete('/delete', [MasterController::class, 'delete']);
        Route::put('/restore', [MasterController::class, 'restore']);
    });
    Route::prefix('option')->group(function () {
        Route::get('list', [OptionController::class, 'index']);
        Route::get('show/{constant}', [OptionController::class, 'show']);
        Route::post('store/{constant}', [OptionController::class, 'store']);
        Route::put('update/{constant}', [OptionController::class, 'update']);
    });
});
