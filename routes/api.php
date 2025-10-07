<?php

use App\Http\Controllers\Api\AuthController;

require __DIR__.'/api/auth.php';
require __DIR__.'/api/housing.php';

Route::middleware(['auth:sanctum', 'profile'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    require __DIR__.'/api/user.php';
    require __DIR__.'/api/citizens.php';
    require __DIR__.'/api/complaints.php';
    require __DIR__.'/api/family.php';
    require __DIR__.'/api/financial.php';
});

require __DIR__.'/api/master.php';
