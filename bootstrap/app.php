<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your custom middleware aliases for Laravel 12
        $middleware->alias([
            'user.active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'valid.otp' => \App\Http\Middleware\EnsureValidOtp::class,
            'require.otp' => \App\Http\Middleware\RequireOtpAccess::class,  // ← ADD THIS LINE
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();