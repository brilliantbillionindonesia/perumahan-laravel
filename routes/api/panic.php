<?php
use App\Http\Controllers\Api\PanicController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('panics')->group(function () {
        Route::get('show', [PanicController::class, 'show']);
        Route::post('store', [PanicController::class, 'store']);
        Route::post('handle', [PanicController::class, 'handle']);
        Route::get('notified-to-me', [PanicController::class, 'panicNotifiedToMe']);
    });
});
