<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\QuizAttempt; // pastikan model ini ada & benar namespace-nya

class EnsureAttemptOwner
{
    /**
     * Pastikan user yang mengakses adalah pemilik attempt
     * atau punya hak admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dengan implicit binding, param {attempt} otomatis jadi instance QuizAttempt
        /** @var QuizAttempt|null $attempt */
        $attempt = $request->route('attempt');

        if (! $attempt instanceof QuizAttempt) {
            abort(404, 'Attempt tidak ditemukan.');
        }

        // Seharusnya middleware 'auth' sudah dijalankan lebih dulu di route.
        $user = $request->user();
        if (! $user) {
            // kalau tetap mau jaga-jaga:
            abort(401, 'Silakan login untuk melihat hasil.');
        }

        // Izinkan jika pemilik, atau punya ability/role admin
        $isOwner = ($attempt->user_id === $user->id);

        // Sesuaikan sesuai sistem auth kamu:
        // 1) kalau pakai Gate ability 'admin' (Gate::define('admin', ...))
        $isAdmin = $user->can('admin');

        // 2) atau kalau pakai kolom is_admin:
        // $isAdmin = (bool) $user->is_admin;

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'Anda tidak berhak melihat hasil ini.');
        }

        return $next($request);
    }
}
