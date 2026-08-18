@extends('layouts.auth-modern')

@section('title', 'Register')
@section('visual_order', 'lg:order-2')
@section('visual_kicker', 'Akun gratis')
@section('visual_title', 'Mulai Belajar dan Tingkatkan Skill Kamu')
@section('visual_text', 'Buat akun gratis dan dapatkan akses ke kelas, forum, tes psikologi, progress belajar, serta berbagai fitur BERKEMAH lainnya.')
@section('heading', 'Buat Akun BERKEMAH')
@section('subheading', 'Daftar gratis dan mulai perjalanan belajarmu.')

@section('visual_cards')
  @foreach ([
    ['title' => 'Belajar Terarah', 'body' => 'Materi disusun agar lebih mudah diikuti.'],
    ['title' => 'Pantau Progres', 'body' => 'Lihat perkembangan pembelajaran kamu.'],
    ['title' => 'Sertifikat & Assessment', 'body' => 'Ukur kemampuan dan dokumentasikan pencapaian.'],
  ] as $item)
    <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur">
      <p class="text-base font-black">{{ $item['title'] }}</p>
      <p class="mt-3 text-sm leading-6 text-white/64">{{ $item['body'] }}</p>
    </div>
  @endforeach
@endsection

@section('content')
<form method="POST" action="{{ route('register') }}" x-data="passwordStrength()" @submit="loading=true" class="space-y-5">
  @csrf

  <div>
    <label for="name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Nama Lengkap</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      </span>
      <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" class="auth-input @error('name') auth-input-error @enderror" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
    </div>
    @error('name')<p id="name-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>

  <div>
    <label for="email" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Email</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m8-4H8m12 10V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z"/></svg>
      </span>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="auth-input @error('email') auth-input-error @enderror" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
    </div>
    @error('email')<p id="email-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>

  <div>
    <label for="password" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Password</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.105 0 2 .895 2 2v3h-4v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z"/></svg>
      </span>
      <input id="password" name="password" x-model="password" :type="showPassword ? 'text' : 'password'" required autocomplete="new-password" class="auth-input pr-12 @error('password') auth-input-error @enderror" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
      <button type="button" @click="showPassword=!showPassword" class="absolute right-3 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-xl text-slate-400 hover:bg-tosca-light hover:text-tosca-dark dark:hover:bg-white/10" aria-label="Toggle password visibility">
        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg>
        <svg x-cloak x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58M9.9 4.24A10.7 10.7 0 0112 4c6.5 0 10 8 10 8a18.5 18.5 0 01-3.03 4.25M6.1 6.1C3.45 8.08 2 12 2 12s3.5 8 10 8a10.8 10.8 0 004.04-.78"/></svg>
      </button>
    </div>
    <div class="mt-3">
      <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
        <div class="h-full rounded-full transition-all" :class="barClass" :style="`width:${strengthWidth}%`"></div>
      </div>
      <div class="mt-2 flex items-center justify-between text-xs font-bold">
        <span :class="labelClass" x-text="strengthLabel"></span>
        <span class="text-slate-400">Minimal 8 karakter, huruf dan angka</span>
      </div>
    </div>
    @error('password')<p id="password-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>

  <div>
    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700 dark:text-white/82">Konfirmasi Password</label>
    <div class="relative">
      <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5-2v6a8 8 0 11-16 0V8l8-4 8 4z"/></svg>
      </span>
      <input id="password_confirmation" name="password_confirmation" :type="showPassword ? 'text' : 'password'" required autocomplete="new-password" class="auth-input @error('password_confirmation') auth-input-error @enderror" @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror>
    </div>
    @error('password_confirmation')<p id="password_confirmation-error" class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>@enderror
  </div>

  <label class="flex items-start gap-3 rounded-2xl bg-tosca-light/60 p-4 text-xs font-semibold leading-6 text-slate-600 dark:bg-white/8 dark:text-white/68">
    <input type="checkbox" required class="mt-1 h-4 w-4 rounded border-slate-300 text-tosca focus:ring-tosca">
    <span>Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi BERKEMAH.</span>
  </label>

  <button type="submit" :disabled="loading" class="flex min-h-[52px] w-full items-center justify-center gap-3 rounded-2xl bg-tosca px-5 text-sm font-black text-white shadow-lg shadow-tosca/20 transition hover:bg-tosca-dark hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-75">
    <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
    <span x-text="loading ? 'Memproses...' : 'Buat Akun Gratis'"></span>
  </button>
</form>

<div class="my-6 flex items-center gap-4">
  <div class="h-px flex-1 bg-slate-200 dark:bg-white/10"></div>
  <span class="text-xs font-bold text-slate-400">Atau daftar dengan</span>
  <div class="h-px flex-1 bg-slate-200 dark:bg-white/10"></div>
</div>

<button type="button" class="flex h-12 w-full items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white text-sm font-black text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/8 dark:text-white">
  <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-sm font-black text-red-500">G</span>
  Google
</button>

<p class="mt-7 text-center text-sm font-semibold text-slate-500 dark:text-white/62">
  Sudah punya akun?
  <a href="{{ route('login') }}" class="font-black text-tosca-dark hover:text-tosca dark:text-[#DDD6FE]">Masuk</a>
</p>
@endsection

@push('scripts')
<script>
  function passwordStrength() {
    return {
      password: '',
      showPassword: false,
      loading: false,
      get score() {
        let score = 0;
        if (this.password.length >= 8) score++;
        if (/[A-Za-z]/.test(this.password) && /\d/.test(this.password)) score++;
        if (/[^A-Za-z0-9]/.test(this.password) || this.password.length >= 12) score++;
        return score;
      },
      get strengthLabel() {
        if (!this.password) return 'Belum diisi';
        return this.score <= 1 ? 'Lemah' : (this.score === 2 ? 'Cukup' : 'Kuat');
      },
      get strengthWidth() {
        if (!this.password) return 8;
        return this.score <= 1 ? 34 : (this.score === 2 ? 67 : 100);
      },
      get barClass() {
        if (!this.password) return 'bg-slate-300';
        return this.score <= 1 ? 'bg-red-400' : (this.score === 2 ? 'bg-amber-400' : 'bg-tosca');
      },
      get labelClass() {
        if (!this.password) return 'text-slate-400';
        return this.score <= 1 ? 'text-red-500' : (this.score === 2 ? 'text-amber-500' : 'text-tosca-dark dark:text-[#DDD6FE]');
      }
    };
  }
</script>
@endpush
