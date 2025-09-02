@extends('layouts.admin')

@section('title','Edit Module — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide">Edit Module</h1>
      <p class="text-sm opacity-70">Perbarui judul, urutan, atau pindahkan ke course lain.</p>
    </div>
    <a href="{{ route('admin.modules.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back</a>
  </div>

  {{-- Card --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="space-y-6">
      @csrf
      @method('PUT')

      {{-- Course --}}
      <div>
        <label class="block text-sm font-medium mb-1">Course <span class="text-red-500">*</span></label>
        <select name="course_id" class="w-full border rounded-xl px-3 py-2" required>
          <option value="">— pilih course —</option>
          @foreach($courses as $c)
            <option value="{{ $c->id }}" @selected(old('course_id',$module->course_id)==$c->id)>{{ $c->title }}</option>
          @endforeach
        </select>
        @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title',$module->title) }}"
               class="w-full border rounded-xl px-3 py-2" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Ordering --}}
      <div class="max-w-xs">
        <label class="block text-sm font-medium mb-1">Ordering</label>
        <input type="number" name="ordering" min="0"
               value="{{ old('ordering',$module->ordering) }}"
               class="w-full border rounded-xl px-3 py-2">
        <p class="text-xs opacity-70 mt-1">Angka urutan tampil (kecil → muncul duluan).</p>
        @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          Update Module
        </button>
        <a href="{{ route('admin.modules.index') }}"
           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
