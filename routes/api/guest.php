<?php

use App\Http\Controllers\Api\GuestController;

Route::prefix('guests')->group(function () {
    Route::get('list', [GuestController::class, 'list']);
    Route::post('store', [GuestController::class, 'store']);
    Route::get('show', [GuestController::class, 'show']);
    Route::get('identification', [GuestController::class, 'getIdentification']);
});
