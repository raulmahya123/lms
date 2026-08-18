@extends('layouts.auth-modern')

@section('title', 'Lupa Password')
@section('visual_kicker', 'Recovery')
@section('visual_title', 'Akses akunmu kembali dengan aman')
@section('visual_text', 'Kami bantu kirim tautan reset agar kamu bisa melanjutkan kelas, forum, dan progress belajar di BERKEMAH.')
@section('heading', 'Lupa Password?')
@section('subheading', 'Masukkan email akun BERKEMAH kamu. Kami akan mengirimkan tautan untuk mengatur ulang password.')

@section('visual_cards')
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur xl:col-span-3">
    <p class="text-sm font-black">Reset cepat dan aman</p>
    <p class="mt-3 text-sm leading-6 text-white/64">Gunakan email yang terdaftar supaya link reset dapat dikirim ke inbox kamu.</p>
  </div>
@endsection

@section('content')
<form method="POST" action="{{ route('password.email') }}" x-data="{ loading:false }" @submit="loading=true" class="space-y-5">
  @csrf
  <div>
    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Email</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m8-4H8m12 10V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z"/></svg>
      </span>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="auth-input @error('email') auth-input-error @enderror" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
    </div>
    @error('email')<p id="email-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>
  <button type="submit" :disabled="loading" class="flex min-h-[52px] w-full items-center justify-center gap-3 rounded-2xl bg-tosca px-5 text-sm font-black text-white shadow-lg shadow-tosca/20 transition hover:bg-tosca-dark disabled:opacity-75">
    <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
    <span x-text="loading ? 'Memproses...' : 'Kirim Link Reset Password'"></span>
  </button>
</form>

<p class="mt-7 text-center text-sm font-semibold">
  <a href="{{ route('login') }}" class="text-tosca-dark hover:text-tosca dark:text-[#9df4e7]">Kembali ke Login</a>
</p>
@endsection
