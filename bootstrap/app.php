<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\EnsureLessonAccessible;
use App\Http\Middleware\EnsureAttemptOwner;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
         api: null,          // <— matikan API routes
        commands: null,     // <— matikan console routes kalau ga ada
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware — cukup daftar di sini, JANGAN di routes/web.php
        $middleware->alias([
            'ensure.lesson.accessible'      => EnsureLessonAccessible::class,
            'app.ensure.lesson.accessible'  => EnsureLessonAccessible::class, // biar dua-duanya valid
            'ensure.attempt.owner'          => EnsureAttemptOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // biarkan default; penting blok ini ADA
    })
    ->create();
