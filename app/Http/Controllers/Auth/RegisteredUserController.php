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
use Illuminate\Validation\Rule;
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
        // Validasi:
        // - name: wajib string, max 255, dan TIDAK BOLEH pola email (not_regex)
        //   Pola email sederhana: ^[^@\s]+@[^@\s]+\.[^@\s]+$
        $request->validate([
            'name'     => [
                'required',
                'string',
                'max:255',
                'not_regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/i',
            ],
            'email'    => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.not_regex' => 'Nama tidak boleh berformat email.',
            'email.unique'   => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
        ]);

        $user = User::create([
            'name'     => $request->string('name'),
            'email'    => $request->string('email')->lower(),
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Ambil Tes IQ aktif terbaru
        $test = TestIq::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->first();

        // Tujuan berikutnya
        $nextUrl = $test
            ? route('user.test-iq.show', ['testIq' => $test->getRouteKey()])
            : route('dashboard');

        // Jika butuh verifikasi email
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
