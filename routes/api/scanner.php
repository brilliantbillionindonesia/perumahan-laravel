<?php

use App\Http\Controllers\Api\Scanner\FamilyCardScannerController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('scanners')->group(function () {
        Route::prefix('family-card')->group(function () {
            Route::get('list', [FamilyCardScannerController::class, 'list']);
            Route::get('show', [FamilyCardScannerController::class, 'show']);
            Route::get('get-file', [FamilyCardScannerController::class, 'getFile']);
            Route::post('store', [FamilyCardScannerController::class, 'store']);
            Route::post('update', [FamilyCardScannerController::class, 'update']);
            Route::post('verify', [FamilyCardScannerController::class, 'verify']);
            Route::post('store/json', [FamilyCardScannerController::class, 'storeWithJsonFile']);
        });
    });
});
