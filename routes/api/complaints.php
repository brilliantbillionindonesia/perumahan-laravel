<?php

use App\Http\Controllers\Api\ComplaintController;

Route::prefix('complaint')->group(function () {
    Route::get('/list', [ComplaintController::class, 'list']);
    Route::get('/show/{id}', [ComplaintController::class, 'show']);
    Route::post('/store', [ComplaintController::class, 'store']);
    Route::put('/update/{id}', [ComplaintController::class, 'update']);
});
