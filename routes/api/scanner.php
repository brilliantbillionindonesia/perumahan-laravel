<?php

use App\Http\Controllers\Api\Scanner\FamilyCardScannerController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('scanners')->group(function () {
        Route::post('family-card', [FamilyCardScannerController::class, 'store']);
    });
});
