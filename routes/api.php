<?php

use App\Http\Controllers\Api\HousingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('check', [AuthController::class, 'checkToken'])->name('check-token');

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
    Route::middleware(['role:admin'])->group(function () {
        Route::get('admin/dashboard', fn() => 'Welcome admin');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('list', [UserController::class, 'list']);
            Route::post('store', [UserController::class, 'store']);
            Route::put('role', [UserController::class, 'changeRole']);
        });
    });

    require __DIR__.'/api/user.php';
    require __DIR__.'/api/citizens.php';
    require __DIR__.'/api/complaints.php';
    require __DIR__.'/api/family.php';
    require __DIR__.'/api/financial.php';
    require __DIR__.'/api/patroling.php';
});

require __DIR__.'/api/master.php';


