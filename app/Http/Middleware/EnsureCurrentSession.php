<?php

// app/Http/Middleware/EnsureCurrentSession.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $sid = $request->session()->getId();
            if ($user->current_session_id && $user->current_session_id !== $sid) {
                // sesi ini bukan yang resmi → tendang
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                abort(401, 'Session tidak valid (bukan sesi aktif).');
            }
        }
        return $next($request);
    }
}
