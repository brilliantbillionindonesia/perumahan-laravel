<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ManageHousingController;

// Redirect ke dashboard langsung
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Test email
Route::get('test/email', function () {
    return view('emails.users.welcome', [
        'user' => \App\Models\User::first(),
        'password' => 'password',
    ]);
});

// ROUTE ADMIN TANPA LOGIN
Route::prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ManageHousingController::class, 'dashboard'])->name('admin.dashboard');

    // CRUD Housings
    Route::resource('/housings', ManageHousingController::class);
});