@extends('layouts.admin')
@section('title','New Certificate Template — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">New Certificate Template</h1>
    <a href="{{ route('admin.certificate-templates.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back</a>
  </div>

  <form method="POST" action="{{ route('admin.certificate-templates.store') }}" class="bg-white border rounded-2xl p-6 space-y-5">
    @csrf

    {{-- Name --}}
    <div>
      <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
      <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-xl p-2" required>
      @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Background URL --}}
    <div>
      <label class="block text-sm font-medium mb-1">Background URL</label>
      <input type="url" name="background_url" value="{{ old('background_url') }}" placeholder="https://..."
             class="w-full border rounded-xl p-2">
      @error('background_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Fields JSON --}}
    <div>
      <label class="block text-sm font-medium mb-1">Fields JSON</label>
      <textarea name="fields_json" rows="5" class="w-full border rounded-xl p-3" placeholder='{"name":"{{ "{name}" }}","course":"{{ "{course}" }}","date":"{{ "{date}" }}"}'>{{ old('fields_json') }}</textarea>
      <p class="text-xs text-gray-500 mt-1">Isikan JSON mapping untuk teks dinamis (akan di-cast array di model).</p>
      @error('fields_json') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- SVG JSON --}}
    <div>
      <label class="block text-sm font-medium mb-1">SVG JSON (opsional)</label>
      <textarea name="svg_json" rows="5" class="w-full border rounded-xl p-3" placeholder='{"layers":[...]}'>{{ old('svg_json') }}</textarea>
      @error('svg_json') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Active --}}
    <div class="flex items-center gap-3">
      <input type="hidden" name="is_active" value="0">
      <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded" @checked(old('is_active',1))>
      <label for="is_active" class="text-sm">Active</label>
      @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="pt-2">
      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Create Template</button>
    </div>
  </form>
</div>
@endsection
