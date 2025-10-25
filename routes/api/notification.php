<?php

use App\Http\Controllers\Api\NotificationController;

Route::prefix('notifications')->group(function () {
    Route::get('list', [NotificationController::class, 'list']);
    Route::get('has', [NotificationController::class, 'has']);
    Route::post('read', [NotificationController::class, 'read']);
});
