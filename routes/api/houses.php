<?php
use App\Http\Controllers\Api\HouseController;

Route::prefix('houses')->group(function () {
    Route::get('list', [HouseController::class, 'list']);
    Route::get('show', [HouseController::class, 'show']);
    Route::get('me', [HouseController::class, 'me']);
    Route::get('show/family-card', [HouseController::class, 'showByFamilyCard']);
    Route::prefix('statistics')->group(function () {
        Route::get('owners', [HouseController::class, 'statisticOwnerRenter']);
    });

});
