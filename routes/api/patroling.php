<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatrolingController;

// Grup utama dengan prefix dan middleware
Route::prefix('patrols')
    ->group(function () {

        // Route untuk warga (tanpa middleware admin)
        Route::get('/me', [PatrolingController::class, 'me']);

        // Grup admin
        Route::middleware(['permission:manage_patrols'])->group(function () {
            Route::get('/list', [PatrolingController::class, 'list']);
            Route::get('/show', [PatrolingController::class, 'show']);
            Route::post('/store', [PatrolingController::class, 'store']);
            Route::put('/update', [PatrolingController::class, 'update']);
            Route::post('/presence', [PatrolingController::class, 'presence']);
        });
    });
