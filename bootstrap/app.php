<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\EnsureLessonAccessible;
use App\Http\Middleware\EnsureAttemptOwner;
use App\Http\Middleware\PreventDuringIqTest;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',   // <- Nyalakan API routes
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware kustom
        $middleware->alias([
            'ensure.lesson.accessible'      => EnsureLessonAccessible::class,
            'app.ensure.lesson.accessible'  => EnsureLessonAccessible::class,
            'ensure.attempt.owner'          => EnsureAttemptOwner::class,
        ]);
        $middleware->web(append: [
            PreventDuringIqTest::class,
        ]);
        // CSRF hanya berlaku untuk group "web".
        // Jika webhook Midtrans kamu pakai route API, ini SEBENARNYA tidak perlu.
        // Boleh kamu hapus. Kalau ingin tetap ada, biarkan saja—tidak berpengaruh ke /api.
        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',      // hanya berguna jika webhook ada di routes/web.php
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // default
    })
    ->create();
