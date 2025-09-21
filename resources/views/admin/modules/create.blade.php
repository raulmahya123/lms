@extends('layouts.admin')

@section('title','Create Module — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-blue-900">Create Module</h1>
      <p class="text-sm text-blue-700/70">Tambahkan modul baru ke salah satu course.</p>
    </div>
    <a href="{{ route('admin.modules.index') }}"
       class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
      ← Back
    </a>
  </div>

  {{-- Card --}}
  <div class="rounded-2xl border border-blue-100 bg-white/90 shadow-lg backdrop-blur p-6">
    <form method="POST" action="{{ route('admin.modules.store') }}" class="space-y-6">
      @csrf

      {{-- Course --}}
      <div>
        <label class="block text-sm font-semibold mb-1 text-blue-900">Course <span class="text-red-500">*</span></label>
        <select name="course_id" class="w-full border border-blue-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
          <option value="">— pilih course —</option>
          @foreach($courses as $c)
            <option value="{{ $c->id }}" @selected(old('course_id')==$c->id)>{{ $c->title }}</option>
          @endforeach
        </select>
        @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-semibold mb-1 text-blue-900">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}"
               placeholder="Contoh: Pengenalan JavaScript"
               class="w-full border border-blue-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Ordering --}}
      <div class="max-w-xs">
        <label class="block text-sm font-semibold mb-1 text-blue-900">Ordering</label>
        <input type="number" name="ordering" min="0"
               value="{{ old('ordering',0) }}"
               class="w-full border border-blue-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-blue-600/70 mt-1">Angka urutan tampil (kecil → muncul duluan).</p>
        @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Actions --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-600 text-white font-semibold hover:from-blue-800 hover:to-blue-700 shadow-md transition">
          Create Module
        </button>
        <a href="{{ route('admin.modules.index') }}"
           class="px-5 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
