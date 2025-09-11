<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestIq; // <— tambahkan
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

        // Cari Test IQ aktif terbaru, lalu redirect
        $test = TestIq::query()->where('is_active', true)->latest('id')->first();

        if ($test) {
            return redirect()
                ->route('app.test-iq.show', $test) // route model binding {testIq}
                ->with('status', 'Akun berhasil dibuat. Silakan mulai Tes IQ Anda.');
        }

        // Fallback bila belum ada test aktif
        return redirect()->route('app.test-iq.show')->with('status', 'Akun berhasil dibuat.');
    }
}
