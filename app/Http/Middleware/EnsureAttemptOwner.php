<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAttemptOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $attempt = $request->route('attempt'); // implicit binding: App\Models\QuizAttempt

        if (!$user || !$attempt) {
            abort(403, 'Unauthorized.');
        }

        if ($attempt->user_id !== $user->id && !$user->can('admin')) {
            abort(403, 'Anda tidak berhak melihat hasil ini.');
        }

        return $next($request);
    }
}
