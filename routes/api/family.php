<?php
use App\Http\Controllers\Api\FamilyController;

Route::prefix('family')->group(function () {
    Route::get('card', [FamilyController::class, 'card']);
    Route::get('list', [FamilyController::class, 'list']);
    Route::get('my-list', [FamilyController::class, 'myList']);
    Route::get('my-card', [FamilyController::class, 'myCard']);
});
