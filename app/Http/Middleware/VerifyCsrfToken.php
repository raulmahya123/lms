<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Daftar URI yang dikecualikan dari verifikasi CSRF.
     */
    protected $except = [
        'midtrans/webhook',   // biarkan Midtrans POST tanpa CSRF
        // 'midtrans/*',       // opsional jika ingin lebih longgar
    ];
}
