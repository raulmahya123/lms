@extends('layouts.admin')
@section('title','Edit Membership')

@section('content')
@php($m = $membership ?? null) {{-- SAFE alias --}}

{{-- HEADER --}}
<div class="mb-6 flex items-center justify-between">
  <div class="flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h10M9 12h10M9 18h10M5 6h.01M5 12h.01M5 18h.01"/>
    </svg>
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide">Membership · Edit</h1>
      <p class="text-xs opacity-70">Perbarui status & masa aktif membership.</p>
    </div>
  </div>

  <a href="{{ route('admin.memberships.index') }}"
     class="inline-flex items-center gap-2 px-3 py-2 border rounded-xl hover:bg-gray-50 text-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Back
  </a>
</div>

{{-- FLASH --}}
@if(session('ok'))
  <div class="mb-4 border border-green-200 bg-green-50 text-green-800 rounded-2xl px-4 py-3">
    {{ session('ok') }}
  </div>
@endif
@if($errors->any())
  <div class="mb-4 border border-red-200 bg-red-50 text-red-800 rounded-2xl px-4 py-3">
    {{ $errors->first() }}
  </div>
@endif

{{-- INFO STRIP (read-only) --}}
<div class="mb-6 rounded-2xl border bg-white shadow-sm p-4 flex flex-wrap gap-4 text-sm">
  <div class="flex items-center gap-2">
    <span class="text-gray-500">User:</span>
    <span class="font-medium">{{ data_get($m,'user.name','-') }}</span>
    <span class="text-gray-400">({{ data_get($m,'user.email','-') }})</span>
  </div>
  <div class="flex items-center gap-2">
    <span class="text-gray-500">Plan:</span>
    <span class="font-medium">{{ data_get($m,'plan.name','-') }}</span>
  </div>
  <div class="flex items-center gap-2">
    <span class="text-gray-500">ID:</span>
    <span class="font-mono text-xs">#{{ data_get($m,'id','—') }}</span>
  </div>
</div>

{{-- FORM CARD --}}
<form method="POST" action="{{ isset($m) ? route('admin.memberships.update',$m) : '#' }}" class="space-y-6 max-w-3xl">
  @csrf
  @method('PUT')

  <div class="rounded-2xl border bg-white shadow p-6 space-y-5">
    {{-- Status --}}
    <div class="relative">
      <label class="block text-sm font-medium mb-1">Status</label>
      <select name="status" class="w-full pl-9 pr-3 py-2.5 border rounded-xl focus:ring-blue-600 focus:border-blue-600">
        @foreach(['pending','active','inactive'] as $st)
          <option value="{{ $st }}" @selected(old('status', data_get($m,'status')) === $st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
      <span class="absolute left-3 top-[2.15rem] text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/>
        </svg>
      </span>
    </div>

    {{-- Activated At --}}
    <div class="relative">
      <label class="block text-sm font-medium mb-1">Activated At</label>
      <input type="datetime-local" name="activated_at"
             value="{{ old('activated_at', optional(data_get($m,'activated_at'))->format('Y-m-d\TH:i')) }}"
             class="w-full pl-9 pr-3 py-2.5 border rounded-xl focus:ring-blue-600 focus:border-blue-600">
      <span class="absolute left-3 top-[2.15rem] text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </span>
    </div>

    {{-- Expires At --}}
    <div class="relative">
      <label class="block text-sm font-medium mb-1">Expires At</label>
      <input type="datetime-local" name="expires_at"
             value="{{ old('expires_at', optional(data_get($m,'expires_at'))->format('Y-m-d\TH:i')) }}"
             class="w-full pl-9 pr-3 py-2.5 border rounded-xl focus:ring-blue-600 focus:border-blue-600">
      <span class="absolute left-3 top-[2.15rem] text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22a10 10 0 110-20 10 10 0 010 20z"/>
        </svg>
      </span>
      <p class="mt-1 text-xs text-gray-500">Harus setelah “Activated At”.</p>
    </div>
  </div>

  {{-- ACTIONS --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.memberships.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border rounded-xl hover:bg-gray-50">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Cancel
    </a>
    <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      Update Membership
    </button>
  </div>
</form>
@endsection
