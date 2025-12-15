<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\CheckRole; // Add this import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register your middleware alias here
        $middleware->alias([
            'checkRole' => CheckRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Update project statuses daily at midnight
        $schedule->command('projects:update-statuses')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();