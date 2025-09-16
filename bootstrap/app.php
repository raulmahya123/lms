<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\EnsureLessonAccessible;
use App\Http\Middleware\EnsureAttemptOwner;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: null,      // kamu memang mematikan API routes
        commands: null,
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware kustom kamu
        $middleware->alias([
            'ensure.lesson.accessible'      => EnsureLessonAccessible::class,
            'app.ensure.lesson.accessible'  => EnsureLessonAccessible::class,
            'ensure.attempt.owner'          => EnsureAttemptOwner::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'midtrans/webhook',      // atau 'midtrans/*' kalau mau lebih longgar
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // default ok
    })
    ->create();
