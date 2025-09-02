@extends('layouts.admin')
@section('title','Edit Lesson — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Pencil/Edit icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/>
        </svg>
        Edit Lesson
      </h1>
      <p class="text-sm opacity-70">Perbarui judul, konten, urutan, atau status gratis pelajaran.</p>
    </div>
    <a href="{{ route('admin.lessons.index') }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">← Back</a>
  </div>

  {{-- FORM CARD --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.lessons.update',$lesson) }}" class="space-y-6">
      @csrf @method('PUT')

      {{-- Module --}}
      <div>
        <label class="block text-sm font-medium mb-1">Module <span class="text-red-500">*</span></label>
        <select name="module_id" class="w-full border rounded-xl px-3 py-2" required>
          @foreach($modules as $m)
            <option value="{{ $m->id }}" @selected(old('module_id',$lesson->module_id)==$m->id)>
              {{ $m->course?->title }} — {{ $m->title }}
            </option>
          @endforeach
        </select>
        @error('module_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title"
               value="{{ old('title',$lesson->title) }}"
               class="w-full border rounded-xl px-3 py-2" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Content --}}
      <div>
        <label class="block text-sm font-medium mb-1">Content (HTML / Markdown / text)</label>
        <textarea name="content" rows="6"
                  class="w-full border rounded-xl px-3 py-2">{{ old('content',$lesson->content) }}</textarea>
        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Content URL --}}
      <div>
        <label class="block text-sm font-medium mb-1">Content URL (video/file)</label>
        <input type="url" name="content_url"
               value="{{ old('content_url',$lesson->content_url) }}"
               placeholder="https://..."
               class="w-full border rounded-xl px-3 py-2">
        @error('content_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Ordering + Free --}}
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Ordering</label>
          <input type="number" name="ordering" min="1"
                 value="{{ old('ordering',$lesson->ordering) }}"
                 class="w-full border rounded-xl px-3 py-2">
          <p class="text-xs opacity-70 mt-1">Urutan tampil (angka kecil muncul duluan).</p>
          @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_free" value="1"
                   @checked(old('is_free',$lesson->is_free))
                   class="rounded">
            <span>Mark as Free</span>
          </label>
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          Update Lesson
        </button>
        <a href="{{ route('admin.lessons.index') }}"
           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
