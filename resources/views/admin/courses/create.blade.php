@extends('layouts.admin')

@section('title','Create Course')

@section('content')
<div
  x-data
  class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8"
>
  <!-- Page Header -->
  <div class="mb-6 sm:mb-8">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-coal-900">Create Course</h1>
        <p class="mt-1 text-sm text-coal-500">Lengkapi detail kursus di bawah. Anda bisa unggah cover atau pakai URL.</p>
      </div>
      <a href="{{ route('admin.courses.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-ivory-100">
        <!-- back icon -->
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Kembali
      </a>
    </div>
  </div>

  <!-- Error Summary -->
  @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
      <div class="flex items-start gap-3">
        <svg class="mt-0.5 h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div>
          <p class="font-semibold text-red-800">Silakan periksa kembali isian Anda:</p>
          <ul class="mt-2 list-disc pl-5 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.courses.store') }}"
        enctype="multipart/form-data"
        class="space-y-8 rounded-2xl border bg-white p-6 shadow-sm">
    @csrf

    {{-- Hidden: publish state (disinkronkan dari toggle / tombol aksi) --}}
    <input type="hidden" name="is_published" id="publishField" value="{{ old('is_published') ? 1 : 0 }}">

    {{-- Basic Info --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}"
               class="w-full rounded-xl border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-600"
               required>
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="mb-1.5 block text-sm font-medium">Description</label>
        <textarea name="description" rows="4"
                  class="w-full rounded-xl border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-600"
        >{{ old('description') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Media & Cover --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Upload: drag & drop -->
      <div
        x-data="{
          preview: null,
          isDropping:false,
          handleFiles(files){
            if (!files?.length) return;
            const file = files[0];
            $refs.input.files = files;
            this.preview = URL.createObjectURL(file);
          }
        }"
      >
        <label class="mb-1.5 block text-sm font-medium">Upload Cover</label>

        <div
          @dragover.prevent="isDropping = true"
          @dragleave.prevent="isDropping = false"
          @drop.prevent="isDropping=false; handleFiles($event.dataTransfer.files)"
          class="relative rounded-2xl border-2 border-dashed p-4 transition
                 hover:border-blue-500 hover:bg-ivory-50"
          :class="isDropping ? 'border-blue-600 bg-blue-50' : 'border-coal-200'"
        >
          <input type="file" name="cover" accept="image/*" x-ref="input"
                 @change="handleFiles($event.target.files)"
                 class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />

          <div class="pointer-events-none flex items-center gap-3">
            <div class="rounded-xl border p-2">
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M16 12l-4-4-4 4M12 8v9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="text-sm">
              <p class="font-medium">Seret & letakkan gambar di sini, atau klik untuk pilih</p>
              <p class="text-coal-500">PNG/JPG, max sesuai konfigurasi server</p>
            </div>
          </div>
        </div>

        <template x-if="preview">
          <div class="mt-3">
            <img :src="preview" alt="Preview" class="h-44 w-full rounded-xl border object-cover">
          </div>
        </template>
        @error('cover') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <!-- URL fallback -->
      <div>
        <label class="mb-1.5 block text-sm font-medium">Atau pakai Cover URL (opsional)</label>
        <input type="url" name="cover_url" value="{{ old('cover_url') }}"
               placeholder="https://example.com/image.jpg"
               class="w-full rounded-xl border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-600">
        <p class="mt-1 text-xs text-coal-500">Jika upload file & URL diisi, sistem akan memakai file upload.</p>
        @error('cover_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Status & Pricing --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Published -->
      <div class="rounded-2xl border p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium">Published</p>
            <p class="text-xs text-coal-500">Tampilkan kursus ke publik setelah siap.</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            {{-- Checkbox visual tanpa name; sinkron ke hidden #publishField --}}
            <input type="checkbox" class="peer sr-only"
                   @change="document.getElementById('publishField').value = $event.target.checked ? 1 : 0"
                   {{ old('is_published') ? 'checked' : '' }}>
            <div class="peer h-6 w-11 rounded-full bg-coal-300 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-5"></div>
          </label>
        </div>
      </div>

      <!-- Pricing -->
      <div
        x-data="{ isFree: {{ old('is_free', 1) ? 'true' : 'false' }} }"
        class="rounded-2xl border p-4"
      >
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-sm font-medium">Pricing</p>
            <p class="text-xs text-coal-500">Centang “Gratis” atau tentukan harga.</p>
          </div>

          <!-- hidden agar 0 terkirim saat unchecked -->
          <input type="hidden" name="is_free" value="0">

          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_free" value="1" class="h-4 w-4 rounded"
                   @change="isFree = !isFree" :checked="isFree">
            <span class="text-sm font-medium">Gratis</span>
          </label>
        </div>

        <div class="mt-4">
          <label class="mb-1 block text-sm font-medium">Harga (Rp)</label>
          <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-coal-500">Rp</span>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                   :disabled="isFree"
                   class="w-full rounded-xl border pl-10 pr-3 py-2 outline-none focus:ring-2 focus:ring-blue-600 disabled:bg-ivory-100 disabled:text-coal-500">
          </div>
          <p class="mt-1 text-xs text-coal-500">Kosongkan jika kursus gratis. Jika berbayar, harga wajib diisi.</p>
          @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-end gap-3">
      <a href="{{ route('admin.courses.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm hover:bg-ivory-100">
        Batal
      </a>

      {{-- Save as Draft (set is_published = 0) --}}
      <button type="submit"
              @click="document.getElementById('publishField').value = 0"
              class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium hover:bg-ivory-100">
        Save Draft
      </button>

      {{-- Publish Now (set is_published = 1) --}}
      <button type="submit"
              @click="document.getElementById('publishField').value = 1"
              class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
        <!-- bolt icon -->
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M13 2L3 14h7l-1 8 11-14h-7l1-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Publish Now
      </button>
    </div>
  </form>
</div>
@endsection
