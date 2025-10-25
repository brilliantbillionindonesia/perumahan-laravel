<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatrolingController;

Route::prefix('patrols')
    ->group(function () {

        Route::get('/me', [PatrolingController::class, 'me']);
        Route::get('/today', [PatrolingController::class, 'today']);
        Route::get('/list', [PatrolingController::class, 'list']);
        Route::get('/show', [PatrolingController::class, 'show']);

        Route::middleware(['permission:manage_patrols'])->group(function () {
            Route::post('/store', [PatrolingController::class, 'store']);
            Route::put('/update', [PatrolingController::class, 'update']);
            Route::post('/presence', [PatrolingController::class, 'presence']);
        });
    });
