<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Management\CitizenController;
use App\Http\Controllers\Web\Management\HousingController;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
>>>>>>> b3d629c1d79767cf8b58264debce1682206cd50d

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
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Dashboard admin
    Route::get('/dashboard', [HousingController::class, 'dashboard'])->name('admin.dashboard');

    // CRUD data perumahan
    Route::resource('/housings', HousingController::class);

    // Daftar penghuni (residents) untuk perumahan tertentu
    Route::get('/housings/{id}/residents', [HousingController::class, 'residents'])
        ->name('admin.housings.residents');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (WEB TOKEN)
|--------------------------------------------------------------------------
*/
Route::middleware(['web_token'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('user.dashboard');

    Route::get('/citizen', [CitizenController::class, 'index'])->name('citizen.index');
});

Route::get('/qr-drive', function () {
    // $link = 'https://drive.google.com/file/d/1WkE7tduTGaSVHocBMDv55C_x_5GcmlGi/view?usp=drive_link';
    // return response(QrCode::format('png')->size(300)->generate($link))
    //     ->header('Content-Type', 'image/png');
    return view('qr-code');
});
