@extends('layouts.admin')

@section('title','Create Course')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">Create Course</h1>

  <form method="POST" action="{{ route('admin.courses.store') }}"
        enctype="multipart/form-data"
        class="space-y-6 bg-white p-6 rounded-xl shadow">
    @csrf

    {{-- Title --}}
    <div>
      <label class="block font-semibold">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2" required>
      @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block font-semibold">Description</label>
      <textarea name="description" rows="4" class="w-full border rounded p-2">{{ old('description') }}</textarea>
      @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Cover Upload (utama) --}}
    <div x-data="{ preview: null }">
      <label class="block font-semibold">Upload Cover</label>
      <input type="file" name="cover" accept="image/*"
             @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
             class="w-full border rounded p-2">
      @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

      <template x-if="preview">
        <img :src="preview" alt="Preview" class="mt-3 h-40 w-auto rounded border object-cover">
      </template>
    </div>

    {{-- (Opsional) Cover URL manual fallback --}}
    <div>
      <label class="block font-semibold">Atau pakai Cover URL (opsional)</label>
      <input type="url" name="cover_url" value="{{ old('cover_url') }}" placeholder="https://example.com/image.jpg"
             class="w-full border rounded p-2">
      @error('cover_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      <p class="text-xs opacity-70 mt-1">Kalau upload file & URL dua-duanya diisi, sistem pakai file upload.</p>
    </div>

    {{-- Published --}}
    <div class="flex items-center space-x-2">
      <input type="checkbox" name="is_published" id="is_published" class="h-4 w-4" {{ old('is_published') ? 'checked' : '' }}>
      <label for="is_published" class="font-semibold">Published</label>
    </div>

        {{-- Pricing --}}
    <div x-data="{
          isFree: {{ old('is_free', 1) ? 'true' : 'false' }},
          toggle(){ this.isFree = !this.isFree; }
        }">
      <label class="block font-semibold mb-2">Pricing</label>

      {{-- penting: hidden agar nilai 0 terkirim saat checkbox tidak dicentang --}}
      <input type="hidden" name="is_free" value="0">

      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_free" value="1"
               @change="toggle()"
               :checked="isFree"
               class="h-4 w-4">
        <span>Gratis</span>
      </label>
      @error('is_free') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

      <div class="mt-3">
        <label class="block text-sm font-medium mb-1">Harga (Rp)</label>
        <input type="number" step="0.01" name="price"
               value="{{ old('price') }}"
               :disabled="isFree"
               class="w-full border rounded p-2">
        <p class="text-xs opacity-70 mt-1">Kosongkan jika kursus gratis. Jika berbayar, harga wajib diisi.</p>
        @error('price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>


    {{-- Actions --}}
    <div class="flex justify-end space-x-3">
      <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded border">Cancel</a>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    </div>
  </form>
</div>
@endsection
