@extends('layouts.auth-modern')

@section('title', 'Verifikasi Email')
@section('visual_kicker', 'Aktivasi akun')
@section('visual_title', 'Satu langkah lagi untuk mulai belajar')
@section('visual_text', 'Verifikasi email kamu agar akun BERKEMAH aktif dan fitur belajar bisa digunakan penuh.')
@section('heading', 'Verifikasi Email Kamu')
@section('subheading', 'Kami sudah mengirimkan tautan verifikasi ke email kamu. Buka email tersebut untuk mengaktifkan akun BERKEMAH.')

@section('visual_cards')
  <div class="rounded-3xl border border-white/12 bg-white/12 p-5 backdrop-blur xl:col-span-3">
    <p class="text-sm font-black">Cek inbox atau spam</p>
    <p class="mt-3 text-sm leading-6 text-white/64">Jika belum ada email masuk, kirim ulang link verifikasi dari halaman ini.</p>
  </div>
@endsection

@section('content')
@if (session('status') == 'verification-link-sent')
  <div class="mb-5 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-800 dark:border-violet-400/20 dark:bg-violet-400/10 dark:text-violet-100">
    Email verifikasi berhasil dikirim ulang.
  </div>
@endif

<div class="space-y-4">
  <form method="POST" action="{{ route('verification.send') }}" x-data="{ loading:false }" @submit="loading=true">
    @csrf
    <button type="submit" :disabled="loading" class="flex min-h-[52px] w-full items-center justify-center gap-3 rounded-2xl bg-tosca px-5 text-sm font-black text-white shadow-lg shadow-tosca/20 transition hover:bg-tosca-dark disabled:opacity-75">
      <svg x-show="loading" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
      <span x-text="loading ? 'Memproses...' : 'Kirim Ulang Email Verifikasi'"></span>
    </button>
  </form>

  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/8 dark:text-white">
      Ganti Email
    </button>
  </form>
</div>
@endsection
