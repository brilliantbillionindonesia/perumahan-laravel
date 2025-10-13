<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('test/email', function () {
//     return view('emails.users.welcome', [
//         'user' => \App\Models\User::first(),
//         'password' => 'password',
//     ]);
// });

Route::middleware(['web_token'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    Route::get('/citizen', function () {
        dd('citizennnnnn');
    });
});
