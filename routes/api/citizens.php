<?php
use App\Http\Controllers\Api\CitizenController;

Route::prefix('citizens')->group(function () {
    Route::get('list', [CitizenController::class, 'list']);
    Route::get('show', [CitizenController::class, 'show']);
    Route::prefix('statistics')->group(function () {
        Route::get('head-citizen', [CitizenController::class, 'statisticHeadCitizen']);
        Route::get('citizen', [CitizenController::class, 'statisticCitizen']);
        Route::get('age', [CitizenController::class, 'statisticCitizenAge']);
        Route::get('subdist-match', [CitizenController::class, 'statisticSubdistrictMatch']);
    });
});
