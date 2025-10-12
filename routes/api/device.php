<?php
use App\Http\Controllers\Api\DeviceController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('devices')->group(function () {
        Route::get('list', [DeviceController::class, 'list']);
        Route::get('show', [DeviceController::class, 'show']);
        Route::post('register', [DeviceController::class, 'register']);
    });
});
