@extends('layouts.admin')
@section('title','Create Lesson')

@section('content')
<form method="POST" action="{{ route('admin.lessons.store') }}" class="space-y-5 bg-white p-6 rounded shadow max-w-3xl">
  @csrf
  <div>
    <label class="block text-sm font-medium mb-1">Module</label>
    <select name="module_id" class="w-full border rounded px-3 py-2" required>
      <option value="">— Select Module —</option>
      @foreach($modules as $m)
        <option value="{{ $m->id }}" @selected(old('module_id')==$m->id)>
          {{ $m->course?->title }} — {{ $m->title }}
        </option>
      @endforeach
    </select>
    @error('module_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" class="w-full border rounded px-3 py-2" value="{{ old('title') }}" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Content (HTML / Markdown / text)</label>
    <textarea name="content" rows="6" class="w-full border rounded px-3 py-2">{{ old('content') }}</textarea>
    @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Content URL (video/file)</label>
    <input type="url" name="content_url" class="w-full border rounded px-3 py-2" value="{{ old('content_url') }}">
    @error('content_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Ordering</label>
      <input type="number" name="ordering" class="w-full border rounded px-3 py-2" value="{{ old('ordering',1) }}" min="1">
      @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="flex items-end">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_free" value="1" @checked(old('is_free'))>
        <span>Mark as Free</span>
      </label>
    </div>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.lessons.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
  </div>
</form>
@endsection
