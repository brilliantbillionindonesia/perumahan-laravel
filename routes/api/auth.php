<?php

use App\Http\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('check', [AuthController::class, 'checkToken'])->name('check-token');
    Route::post('logout', [AuthController::class, 'logout']);
});
