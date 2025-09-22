<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\EnsureAttemptOwner;
use App\Http\Middleware\EnsureCourseAccessible;
use App\Http\Middleware\PreventDuringIqTest;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware kustom
        $middleware->alias([
            // Pakai kunci akses yang memeriksa course/lesson/resource
            'app.ensure.lesson.accessible' => EnsureCourseAccessible::class,
            'ensure.attempt.owner'        => EnsureAttemptOwner::class,
        ]);

        // Middleware tambahan untuk web group
        $middleware->web(append: [
            PreventDuringIqTest::class,
        ]);

        // Contoh CSRF exception (kalau webhook Midtrans ada di web.php)
        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // default
    })
    ->create();
