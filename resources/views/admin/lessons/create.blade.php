@extends('layouts.admin')
@section('title','Create Lesson — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Play/lesson icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.5 5.75A2.75 2.75 0 0 1 7.25 3h9.5A2.75 2.75 0 0 1 19.5 5.75v12.5A2.75 2.75 0 0 1 16.75 21h-9.5A2.75 2.75 0 0 1 4.5 18.25V5.75Zm5 1.25a.75.75 0 0 0-.75.75v8.5a.75.75 0 0 0 1.14.64l6.5-4.25a.75.75 0 0 0 0-1.28l-6.5-4.25a.75.75 0 0 0-.39-.11Z"/>
        </svg>
        Create Lesson
      </h1>
      <p class="text-sm opacity-70">Tambahkan pelajaran baru ke salah satu modul.</p>
    </div>
    <a href="{{ route('admin.lessons.index') }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">← Back</a>
  </div>

  {{-- FORM CARD --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.lessons.store') }}" class="space-y-6">
      @csrf

      {{-- Module --}}
      <div>
        <label class="block text-sm font-medium mb-1">Module <span class="text-red-500">*</span></label>
        <select name="module_id" class="w-full border rounded-xl px-3 py-2" required>
          <option value="">— Select Module —</option>
          @foreach($modules as $m)
            <option value="{{ $m->id }}" @selected(old('module_id')==$m->id)>
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
               value="{{ old('title') }}"
               placeholder="Contoh: Pengenalan Variabel"
               class="w-full border rounded-xl px-3 py-2" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Content --}}
      <div>
        <label class="block text-sm font-medium mb-1">Content (HTML / Markdown / text)</label>
        <textarea name="content" rows="6"
                  placeholder="Tulis materi di sini..."
                  class="w-full border rounded-xl px-3 py-2">{{ old('content') }}</textarea>
        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Content URLs (Array) --}}
      <div x-data="{ urls: @js(old('content_url', [])) }">
        <label class="block text-sm font-medium mb-1">Content URLs (video/file)</label>

        <template x-for="(item, index) in urls" :key="index">
          <div class="flex gap-2 mb-2">
            <input type="text" :name="`content_url[${index}][title]`"
                   x-model="item.title"
                   placeholder="Judul konten"
                   class="w-1/3 border rounded-xl px-3 py-2">
            <input type="url" :name="`content_url[${index}][url]`"
                   x-model="item.url"
                   placeholder="https://..."
                   class="w-2/3 border rounded-xl px-3 py-2">
            <button type="button" @click="urls.splice(index,1)"
                    class="px-2 text-red-600">✕</button>
          </div>
        </template>

        <button type="button" @click="urls.push({title:'',url:''})"
                class="mt-2 px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600">
          + Tambah URL
        </button>

        @error('content_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Ordering + Free --}}
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Ordering</label>
          <input type="number" name="ordering" min="1"
                 value="{{ old('ordering',1) }}"
                 class="w-full border rounded-xl px-3 py-2">
          <p class="text-xs opacity-70 mt-1">Urutan tampil (angka kecil muncul duluan).</p>
          @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_free" value="1" @checked(old('is_free'))
                   class="rounded">
            <span>Mark as Free</span>
          </label>
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          Save Lesson
        </button>
        <a href="{{ route('admin.lessons.index') }}"
           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
