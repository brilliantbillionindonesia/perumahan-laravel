<?php

use App\Http\Controllers\Web\Management\CitizenController;
use App\Http\Controllers\Web\Management\HousingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('test/email', function () {
    return view('emails.users.generated-password', [
        'user' => \App\Models\User::first(),
        'password' => 'password',
    ]);
});

Route::middleware(['web_token'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [HousingController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('/housings', HousingController::class);
    Route::prefix('citizen')->group(function(){
        Route::post('import', [CitizenController::class, 'import'])->name('citizen.import');
    });
});
