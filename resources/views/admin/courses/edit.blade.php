@extends('layouts.admin')

@section('title','Edit Course')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">Edit Course</h1>

  <form method="POST" action="{{ route('admin.courses.update',$course) }}"
        enctype="multipart/form-data"
        class="space-y-6 bg-white p-6 rounded-xl shadow">
    @csrf
    @method('PUT')

    {{-- Title --}}
    <div>
      <label class="block font-semibold">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" 
             value="{{ old('title',$course->title) }}" 
             class="w-full border rounded p-2" required>
      @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block font-semibold">Description</label>
      <textarea name="description" rows="4" 
                class="w-full border rounded p-2">{{ old('description',$course->description) }}</textarea>
      @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Cover Upload (utama) --}}
    <div x-data="{ preview: null }">
      <label class="block font-semibold">Upload Cover</label>
      <input type="file" name="cover" accept="image/*"
             @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
             class="w-full border rounded p-2">
      @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

      {{-- preview baru --}}
      <template x-if="preview">
        <img :src="preview" alt="Preview" class="mt-3 h-40 w-auto rounded border object-cover">
      </template>

      {{-- preview lama --}}
      @if($course->cover_url)
        <img src="{{ $course->cover_url }}" alt="Current Cover"
             class="mt-3 h-40 w-auto rounded border object-cover">
      @endif
    </div>

    {{-- (Opsional) Cover URL --}}
    <div>
      <label class="block font-semibold">Atau pakai Cover URL (opsional)</label>
      <input type="url" name="cover_url" 
             value="{{ old('cover_url',$course->cover_url) }}"
             placeholder="https://example.com/image.jpg"
             class="w-full border rounded p-2">
      @error('cover_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      <p class="text-xs opacity-70 mt-1">Kalau upload file & URL dua-duanya diisi, sistem pakai file upload.</p>
    </div>

    {{-- Published --}}
    <div class="flex items-center space-x-2">
      <input type="checkbox" name="is_published" id="is_published" class="h-4 w-4"
             {{ old('is_published',$course->is_published) ? 'checked' : '' }}>
      <label for="is_published" class="font-semibold">Published</label>
    </div>

    {{-- Actions --}}
    <div class="flex justify-end space-x-3">
      <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded border">Cancel</a>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
    </div>
  </form>
</div>
@endsection
