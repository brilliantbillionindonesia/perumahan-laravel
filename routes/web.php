<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Management\CitizenController;
use App\Http\Controllers\Web\Management\HousingController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini berisi definisi route web utama aplikasi.
| Semua route di sini akan dimuat oleh RouteServiceProvider.
|
*/

// Redirect root ke dashboard admin
Route::get('/', function () {
    return view('welcome');
});

// Route test email (tanpa login)
Route::get('test/email', function () {
    return view('emails.users.generated-password', [
        'user' => \App\Models\User::first(),
        'password' => 'password',
    ]);
});
/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (WEB TOKEN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['web_token'])
    ->group(function () {

        Route::get('/dashboard', [HousingController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::resource('/housings', HousingController::class);

        Route::get('/housings/{id}/residents', [HousingController::class, 'residents'])
            ->name('admin.housings.residents');
    });

Route::get('/qr-drive', function () {
    // $link = 'https://drive.google.com/file/d/1WkE7tduTGaSVHocBMDv55C_x_5GcmlGi/view?usp=drive_link';
    // return response(QrCode::format('png')->size(300)->generate($link))
    //     ->header('Content-Type', 'image/png');
    return view('qr-code');
});
