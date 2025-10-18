<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ManageHousingController;

// Redirect ke dashboard langsung
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Test email
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
    Route::get('/citizen', function () {
        dd('citizennnnnn');
    });
});

// ROUTE ADMIN TANPA LOGIN
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ManageHousingController::class, 'dashboard'])->name('admin.dashboard');

    // CRUD Housings
    Route::resource('/housings', ManageHousingController::class);
});
