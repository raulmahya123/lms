@extends('layouts.app')
@section('title','Kelola Profil')

@push('styles')
<style>
  /* anim panah halus saat hover/focus */
  .card-arrow{ transition: transform .15s ease; }
  .card:hover .card-arrow,.card:focus-visible .card-arrow{ transform: translateX(.25rem); }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl">
  <div class="mb-8">
    <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Kelola Profil</h1>
    <p class="mt-1 text-slate-600 dark:text-slate-400">Pilih salah satu tindakan di bawah ini.</p>
  </div>

  {{-- dekorasi lembut --}}
  <div class="relative isolate">
    <div aria-hidden="true"
         class="pointer-events-none absolute -top-8 -left-6 h-40 w-40 rounded-full bg-blue-200/40 blur-3xl dark:bg-blue-900/30"></div>
    <div aria-hidden="true"
         class="pointer-events-none absolute -bottom-8 -right-6 h-40 w-40 rounded-full bg-indigo-200/40 blur-3xl dark:bg-indigo-900/30"></div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

      {{-- 1) Profile Information --}}
      <a href="{{ route('profile.info.edit') }}"
         class="card group relative z-10 block rounded-2xl border border-slate-200/70 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 dark:from-slate-800 dark:to-slate-900 dark:border-slate-700 pointer-events-auto">
        <div class="flex items-start gap-4">
          {{-- icon --}}
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40">
            {{-- id-card --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M3.75 5.25h16.5a.75.75 0 01.75.75v12a.75.75 0 01-.75.75H3.75A.75.75 0 013 18V6a.75.75 0 01.75-.75zm3 3.75h6m-6 3h4M7.5 15h3" />
            </svg>
          </span>
          <div class="min-w-0">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Profile Information</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Ubah nama &amp; email.</p>
          </div>
          <span class="ml-auto text-slate-400 group-hover:text-blue-500 card-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
          </span>
        </div>
      </a>

      {{-- 2) Update Password --}}
      <a href="{{ route('profile.pass.edit') }}"
         class="card group relative z-10 block rounded-2xl border border-slate-200/70 bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 dark:from-slate-800 dark:to-slate-900 dark:border-slate-700 pointer-events-auto">
        <div class="flex items-start gap-4">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40">
            {{-- key --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M21 7a5 5 0 10-9.584 2.083L3 17.5V21h3.5l2.002-2.002H12v-3.498l2.083-2.084A5.002 5.002 0 0021 7zm-5 2a2 2 0 110-4 2 2 0 010 4z"/>
            </svg>
          </span>
          <div class="min-w-0">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Update Password</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Ganti kata sandi akun.</p>
          </div>
          <span class="ml-auto text-slate-400 group-hover:text-indigo-500 card-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
          </span>
        </div>
      </a>

      {{-- 3) Delete Account --}}
      <a href="{{ route('profile.delete.confirm') }}"
         class="card group relative z-10 block rounded-2xl border border-slate-200/70 bg-gradient-to-br from-rose-50 to-white p-5 shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 dark:from-slate-800 dark:to-slate-900 dark:border-slate-700 pointer-events-auto">
        <div class="flex items-start gap-4">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/40">
            {{-- user-minus --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
              <path d="M15 14a5 5 0 00-10 0v1a1 1 0 001 1h6.126A6.5 6.5 0 0115 14z"/>
              <path d="M8 11a4 4 0 100-8 4 4 0 000 8zM16 16h6v2h-6z"/>
            </svg>
          </span>
          <div class="min-w-0">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Delete Account</h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Hapus akun &amp; data.</p>
          </div>
          <span class="ml-auto text-slate-400 group-hover:text-rose-500 card-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
          </span>
        </div>
      </a>

    </div>
  </div>
</div>
@endsection
