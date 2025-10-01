



<?php

use App\Http\Controllers\Api\HousingController;
use App\Http\Controllers\ApI\AuthController;

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

    Route::prefix('citizens')->group(function () {
        Route::get('list', [CitizenController::class, 'list']);
        Route::get('show', [CitizenController::class, 'show']);
    });

    Route::prefix('family')->group(function () {
        Route::get('card', [FamilyController::class, 'card']);
        Route::get('list', [FamilyController::class, 'list']);
        Route::get('my-list', [FamilyController::class, 'myList']);
        Route::get('my-card', [FamilyController::class, 'myCard']);
    });

    Route::prefix('option')->group(function () {
        Route::get('list', [OptionController::class, 'index']);
        Route::get('show/{constant}', [OptionController::class, 'show']);
        Route::post('store/{constant}', [OptionController::class, 'store']);
        Route::put('update/{constant}', [OptionController::class, 'update']);
    });

    Route::prefix('complaint')->group(function () {
        Route::get('/list', [ComplaintController::class, 'list']);
        Route::get('/show/{id}', [ComplaintController::class, 'show']);
        Route::post('/store', [ComplaintController::class, 'store']);
        Route::put('/update/{id}', [ComplaintController::class, 'update']);
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
    Route::prefix('option')->group(function () {
        Route::get('list', [OptionController::class, 'index']);
        Route::get('show/{constant}', [OptionController::class, 'show']);
        Route::post('store/{constant}', [OptionController::class, 'store']);
        Route::put('update/{constant}', [OptionController::class, 'update']);
    });
    require __DIR__.'/api/user.php';
    require __DIR__.'/api/citizens.php';
    require __DIR__.'/api/complaints.php';
    require __DIR__.'/api/family.php';
});

require __DIR__.'/api/master.php';
