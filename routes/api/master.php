<?php
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\OptionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('master')->group(function () {
        Route::get('list', [MasterController::class, 'list']);
        Route::get('show', [MasterController::class, 'show']);
        Route::post('/store', [MasterController::class, 'store'])->middleware('role:admin');
        Route::put('/update', [MasterController::class, 'update'])->middleware('role:admin');
        Route::delete('/delete', [MasterController::class, 'delete'])->middleware('role:admin');
        Route::put('/restore', [MasterController::class, 'restore'])->middleware('role:admin');
    });
    Route::prefix('option')->group(function () {
        Route::get('list', [OptionController::class, 'index']);
        Route::get('show/{constant}', [OptionController::class, 'show']);
    });
});
