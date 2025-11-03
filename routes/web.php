<?php

use App\Http\Controllers\Web\Management\CitizenController;
use App\Http\Controllers\Web\Management\HousingController;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
});

Route::get('/qr-drive', function () {
    // $link = 'https://drive.google.com/file/d/1WkE7tduTGaSVHocBMDv55C_x_5GcmlGi/view?usp=drive_link';
    // return response(QrCode::format('png')->size(300)->generate($link))
    //     ->header('Content-Type', 'image/png');
    return view('qr-code');
});
