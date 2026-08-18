@extends('layouts.auth-modern')

@section('title', 'Konfirmasi Password')
@section('visual_kicker', 'Area aman')
@section('visual_title', 'Konfirmasi identitas sebelum lanjut')
@section('visual_text', 'Masukkan password akun BERKEMAH kamu untuk mengakses area yang membutuhkan verifikasi ulang.')
@section('heading', 'Konfirmasi Password')
@section('subheading', 'Ini adalah area aman. Konfirmasi password sebelum melanjutkan.')

@section('visual_cards')
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur xl:col-span-3">
    <p class="text-sm font-black">Proteksi tambahan</p>
    <p class="mt-3 text-sm leading-6 text-white/64">Langkah ini menjaga data profil dan akun kamu tetap aman.</p>
  </div>
@endsection

@section('content')
<form method="POST" action="{{ route('password.confirm') }}" x-data="{ showPassword:false, loading:false }" @submit="loading=true" class="space-y-5">
  @csrf
  <div>
    <label for="password" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Password</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .895 2 2v3h-4v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z"/></svg>
      </span>
      <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" class="auth-input pr-12 @error('password') auth-input-error @enderror">
      <button type="button" @click="showPassword=!showPassword" class="absolute right-3 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-xl text-slate-400 hover:bg-tosca-light hover:text-tosca-dark dark:hover:bg-white/10" aria-label="Toggle password visibility">
        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
        <svg x-cloak x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58M9.9 4.24A10.7 10.7 0 0112 4c6.5 0 10 8 10 8a18.5 18.5 0 01-3.03 4.25M6.1 6.1C3.45 8.08 2 12 2 12s3.5 8 10 8a10.8 10.8 0 004.04-.78"/></svg>
      </button>
    </div>
    @error('password')<p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>
  <button type="submit" :disabled="loading" class="flex min-h-[52px] w-full items-center justify-center gap-3 rounded-2xl bg-tosca px-5 text-sm font-black text-white shadow-lg shadow-tosca/20 transition hover:bg-tosca-dark disabled:opacity-75">
    <span x-text="loading ? 'Memproses...' : 'Konfirmasi'"></span>
  </button>
</form>
@endsection
