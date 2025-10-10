<?php
use App\Http\Controllers\Api\PanicController;

Route::prefix('panics')->group(function () {
    Route::get('show', [PanicController::class, 'show']);
    Route::post('store', [PanicController::class, 'store']);
});
