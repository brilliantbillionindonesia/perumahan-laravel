<?php

use App\Http\Controllers\Api\ComplaintController;

Route::prefix('complaints')->group(function () {
    Route::get('/list', [ComplaintController::class, 'list']);
    Route::get('/show', [ComplaintController::class, 'show']);
    Route::post('/store', [ComplaintController::class, 'store']);
    Route::put('/update', [ComplaintController::class, 'update']);
    Route::delete('/delete', [ComplaintController::class, 'destroy']);
    route::get('/history', [ComplaintController::class, 'history']);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('action', [ComplaintController::class, 'action']);
    });
});
