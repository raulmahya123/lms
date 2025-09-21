<?php

// app/Http/Middleware/EnsureSameDevice.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSameDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $uaHashSession = $request->session()->get('ua_hash');
        $uaHashNow     = hash('sha256', (string)$request->header('User-Agent'));

        if (!$uaHashSession) {
            // pertama request setelah login → set
            $request->session()->put('ua_hash', $uaHashNow);
        } elseif ($uaHashSession !== $uaHashNow) {
            // beda device/UA → tendang
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(401, 'Session tidak valid untuk device ini.');
        }

        return $next($request);
    }
}
