<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

require __DIR__.'/api/auth.php';
require __DIR__.'/api/housing.php';
require __DIR__.'/api/device.php';
require __DIR__.'/api/panic.php';

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('change-password', [UserController::class, 'changePassword']);
    });
});
Route::prefix('user')->group(function () {
    Route::post('generate-password', [UserController::class, 'generatePassword']);
});


Route::middleware(['auth:sanctum', 'profile'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    require __DIR__.'/api/user.php';
    require __DIR__.'/api/citizens.php';
    require __DIR__.'/api/complaints.php';
    require __DIR__.'/api/family.php';
    require __DIR__.'/api/financial.php';
    require __DIR__.'/api/patroling.php';
});

require __DIR__.'/api/master.php';
