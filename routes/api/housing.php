<?php

use App\Http\Controllers\Api\HousingController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('housing')->group(function () {
        Route::get('list', [HousingController::class, 'list']);
        Route::get('show', [HousingController::class, 'show']);
    });
});
