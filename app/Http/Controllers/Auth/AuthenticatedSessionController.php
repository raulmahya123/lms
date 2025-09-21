<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * - Regenerate session
     * - Set device fingerprint (opsional tambahan keamanan)
     * - Tandai current_session_id di users
     * - Hapus semua session lain milik user (single device)
     * - Isi metadata di tabel sessions (debug/monitoring)
     */

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // simpan UA-hash ke session (untuk middleware EnsureSameDevice)
        $uaHash = hash('sha256', (string)$request->header('User-Agent'));
        $request->session()->put('ua_hash', $uaHash);

        $user = auth()->user();
        $sid  = $request->session()->getId();

        // tandai session aktif di tabel users
        $user->forceFill([
            'current_session_id' => $sid,
            'current_login_at'   => now(),
            'current_ip'         => $request->ip(),
            'current_ua_hash'    => $uaHash,
        ])->save();

        // isi metadata baris session (opsional, buat monitoring)
        DB::table('sessions')->where('id', $sid)->update([
            'user_id'       => $user->id,
            'ip_address'    => $request->ip(),
            'user_agent'    => (string)$request->header('User-Agent'),
            'last_activity' => time(),
        ]);

        // kill semua sesi user lain → cookie copas jadi invalid
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $sid)
            ->delete();

        // redirect
        $role = optional($user->role)->name;
        return in_array($role, ['admin', 'mentor'], true)
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
