<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestIq;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Ambil Tes IQ aktif terbaru (stabil untuk UUID)
        $test = TestIq::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->first();

        // Bangun URL tujuan; untuk route param gunakan route key (mendukung UUID/ULID/ID)
        if ($test) {
            $nextUrl = route('user.test-iq.show', [
                'testIq' => $test->getRouteKey(), // pakai key sesuai model (uuid/id)
            ]);
        } else {
            $nextUrl = route('dashboard'); // route tanpa parameter
        }

        // Jika perlu verifikasi email, arahkan ke notice dan simpan intended URL
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            session(['url.intended' => $nextUrl]);

            return redirect()
                ->route('verification.notice')
                ->with('status', 'Akun berhasil dibuat. Silakan verifikasi email Anda terlebih dahulu.');
        }

        // Tidak perlu verifikasi / sudah terverifikasi
        return redirect()->intended($nextUrl)
            ->with('status', $test
                ? 'Akun berhasil dibuat. Silakan mulai Tes IQ Anda.'
                : 'Akun berhasil dibuat.');
    }
}
