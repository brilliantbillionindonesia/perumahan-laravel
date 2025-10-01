<?php
use App\Http\Controllers\Api\CitizenController;

Route::prefix('citizens')->group(function () {
    Route::get('list', [CitizenController::class, 'list']);
    Route::get('show', [CitizenController::class, 'show']);
});
