<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PreventDuringIqTest
{
    /**
     * Blokir seluruh route non-ujian ketika sesi tes IQ sedang aktif.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $inProgressTestId = $request->session()->get('iq.in_progress');

            if ($inProgressTestId) {
                $routeName = $request->route()?->getName() ?? '';

                // Hanya rute tes IQ (user.test-iq.*) dan logout yang boleh
                $isAllowed =
                    Str::startsWith($routeName, 'user.test-iq.') ||
                    in_array($routeName, ['logout'], true);

                if (!$isAllowed) {
                    $step = (int) $request->session()->get('iq.current_step', 1);

                    // Fallback URL untuk diarahkan balik
                    $fallback = $request->session()->get('iq.return_to')
                        ?: route('user.test-iq.question', [$inProgressTestId, $step]);

                    // Untuk request JSON/AJAX
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message'  => 'Sesi tes masih berlangsung. Selesaikan terlebih dahulu.',
                            'redirect' => $fallback,
                        ], 423); // 423 Locked
                    }

                    // Flash message (bisa ditangkap SweetAlert)
                    $request->session()->flash('iq_locked', 'Sesi tes masih berlangsung. Selesaikan terlebih dahulu.');
                    return redirect()->to($fallback);
                }
            }
        }

        return $next($request);
    }
}
