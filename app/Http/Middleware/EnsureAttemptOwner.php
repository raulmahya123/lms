<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAttemptOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user    = $request->user();
        $attempt = $request->route('attempt'); // implicit binding: App\Models\QuizAttempt

        // === 404: Attempt tidak ditemukan ===
        if (!$attempt) {
            abort(404, 'Attempt tidak ditemukan.');
        }

        // === 422: User tidak valid / tidak login ===
        if (!$user) {
            abort(422, 'User tidak valid atau belum login.');
        }

        // === 403: User bukan pemilik attempt dan bukan admin ===
        if ($attempt->user_id !== $user->id && !$user->can('admin')) {
            abort(403, 'Anda tidak berhak melihat hasil ini.');
        }

        return $next($request);
    }
}
