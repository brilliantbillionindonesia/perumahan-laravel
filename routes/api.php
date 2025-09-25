<?php

use App\Http\Controllers\Api\HousingController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\NgarondaController;
use App\Http\Controllers\Api\UserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('housing')->group(function () {
        Route::get('list', [HousingController::class, 'list']);
        Route::get('show', [HousingController::class, 'show']);
    });
});

Route::middleware(['auth:sanctum', 'profile'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('admin/dashboard', fn() => 'Welcome admin');
    });

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Route::middleware(['auth:sanctum', 'permission:manage_users'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('list', [UserController::class, 'list']);
            Route::post('store', [UserController::class, 'store']);
            Route::put('role', [UserController::class, 'changeRole']);
        });
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('master')->group(function () {
        Route::get('list', [MasterController::class, 'list']);
        Route::get('show', [MasterController::class, 'show']);
        Route::post('/store', [MasterController::class, 'store']);
        Route::put('/update', [MasterController::class, 'update']);
        Route::delete('/delete', [MasterController::class, 'delete']);
        Route::put('/restore', [MasterController::class, 'restore']);
    });
});
