@extends('layouts.auth-modern')

@section('title', 'Login')
@section('visual_kicker', 'Welcome back')
@section('visual_title', 'Lanjutkan Perjalanan Belajarmu')
@section('visual_text', 'Masuk ke akun BERKEMAH dan lanjutkan kelas, tes, serta progres belajar yang sudah kamu mulai.')
@section('heading', 'Selamat Datang Kembali')
@section('subheading', 'Masuk menggunakan akun BERKEMAH kamu.')

@section('visual_cards')
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur">
    <p class="text-xs font-bold uppercase tracking-wider text-white/60">Progress kelas</p>
    <p class="mt-2 text-2xl font-black">72%</p>
    <div class="mt-4 h-2 rounded-full bg-white/15"><div class="h-2 w-[72%] rounded-full bg-[#DDD6FE]"></div></div>
  </div>
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur">
    <p class="text-xs font-bold uppercase tracking-wider text-white/60">Sertifikat</p>
    <p class="mt-2 text-2xl font-black">Ready</p>
    <p class="mt-3 text-sm text-white/62">Validasi pencapaian belajar.</p>
  </div>
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur">
    <p class="text-xs font-bold uppercase tracking-wider text-white/60">Forum</p>
    <p class="mt-2 text-2xl font-black">Aktif</p>
    <p class="mt-3 text-sm text-white/62">Diskusi bersama mentor.</p>
  </div>
@endsection

@section('content')
<form method="POST" action="{{ route('login') }}" x-data="{ showPassword:false, loading:false }" @submit="loading=true" class="space-y-5">
  @csrf

  <div>
    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Email Address</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m8-4H8m12 10V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z"/></svg>
      </span>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="auth-input @error('email') auth-input-error @enderror" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
    </div>
    @error('email')
      <p id="email-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <div class="mb-2 flex items-center justify-between gap-3">
      <label for="password" class="block text-sm font-bold text-slate-700 dark:text-white/82">Password</label>
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="text-xs font-bold text-tosca-dark hover:text-tosca dark:text-[#DDD6FE]">Forgot Password?</a>
      @endif
    </div>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .895 2 2v3h-4v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z"/></svg>
      </span>
      <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" class="auth-input pr-12 @error('password') auth-input-error @enderror" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
      <button type="button" @click="showPassword=!showPassword" class="absolute right-3 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-xl text-slate-400 hover:bg-tosca-light hover:text-tosca-dark dark:hover:bg-white/10" aria-label="Toggle password visibility">
        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
        <svg x-cloak x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58M9.9 4.24A10.7 10.7 0 0112 4c6.5 0 10 8 10 8a18.5 18.5 0 01-3.03 4.25M6.1 6.1C3.45 8.08 2 12 2 12s3.5 8 10 8a10.8 10.8 0 004.04-.78"/></svg>
      </button>
    </div>
    @error('password')
      <p id="password-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
    @enderror
  </div>

  <label class="flex items-center gap-3 text-sm font-semibold text-slate-600 dark:text-white/70">
    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-tosca focus:ring-tosca">
    Remember Me
  </label>

  <button type="submit" :disabled="loading" class="flex h-13 min-h-[52px] w-full items-center justify-center gap-3 rounded-2xl bg-tosca px-5 text-sm font-black text-white shadow-lg shadow-tosca/20 transition hover:bg-tosca-dark hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-75">
    <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
    <span x-text="loading ? 'Memproses...' : 'Masuk ke BERKEMAH'"></span>
  </button>
</form>

<div class="my-6 flex items-center gap-4">
  <div class="h-px flex-1 bg-slate-200 dark:bg-white/10"></div>
  <span class="text-xs font-bold text-slate-400">Atau masuk dengan</span>
  <div class="h-px flex-1 bg-slate-200 dark:bg-white/10"></div>
</div>

<button type="button" class="flex h-12 w-full items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white text-sm font-black text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/8 dark:text-white">
  <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-sm font-black text-red-500">G</span>
  Google
</button>

<p class="mt-7 text-center text-sm font-semibold text-slate-500 dark:text-white/62">
  Belum punya akun?
  <a href="{{ route('register') }}" class="font-black text-tosca-dark hover:text-tosca dark:text-[#DDD6FE]">Daftar Gratis</a>
</p>
@endsection
