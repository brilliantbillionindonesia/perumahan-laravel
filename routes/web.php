<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test/email', function () {
    return view('emails.users.welcome', [
        'user' => \App\Models\User::first(),
        'password' => 'password',
    ]);
});
