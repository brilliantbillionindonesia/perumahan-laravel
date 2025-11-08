<?php

use App\Console\Commands\RemindActivePanics;
use App\Http\Middleware\ForceJsonMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 🔥 Aktifkan agar Sanctum bisa baca token Bearer dari request API
        $middleware->statefulApi();

        // 🔥 Tambahkan alias middleware Sanctum dan lainnya
        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\Authenticate::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'profile' => \App\Http\Middleware\ProfileMiddleware::class,
            'web_token' => \App\Http\Middleware\WebTokenMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withCommands([
            RemindActivePanics::class,   // daftar command kamu
        ])->withSchedule(function (Schedule $schedule) {
            $schedule->command('panic:remind')
                ->everyMinute()
                ->withoutOverlapping()
                ->onOneServer();

            $schedule->command('app:generate-balance')
                ->monthlyOn(1, '00:05')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:generate-fee')
                ->dailyAt('00:05')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:reminder-patrol-housing')
                ->dailyAt('19:05')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:reminder-patrol')
                ->dailyAt('16:15')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:reminder-patrol')
                ->dailyAt('20:15')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:reminder-due-payment')
                ->dailyAt('07:00')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();

            $schedule->command('app:reminder-due-payment')
                ->dailyAt('19:00')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->onOneServer()
                ->runInBackground();
        })
    ->create();
