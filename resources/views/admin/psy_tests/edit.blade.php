{{-- resources/views/admin/psy_tests/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit Test — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-blue-900">Edit Test</h1>
    <a href="{{ route('admin.psy-tests.show', $psy_test) }}"
       class="px-3 py-2 border rounded-xl hover:bg-gray-50">← Back</a>
  </div>

  @if($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded">
      <strong>Validation Error:</strong>
      <ul class="mt-1 list-disc pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.psy-tests.update', $psy_test) }}" class="space-y-6">
    @csrf @method('PUT')

    {{-- Name (wajib) --}}
    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input type="text" name="name" value="{{ old('name', $psy_test->name) }}" required
             class="w-full border rounded-xl px-3 py-2">
    </div>

    {{-- Slug (opsional) --}}
    <div>
      <label class="block text-sm font-medium mb-1">Slug</label>
      <input type="text" name="slug" value="{{ old('slug', $psy_test->slug) }}"
             class="w-full border rounded-xl px-3 py-2">
      <p class="text-xs text-gray-500 mt-1">Kosongkan untuk auto-generate dari Name.</p>
    </div>

    {{-- Track --}}
    <div>
      <label class="block text-sm font-medium mb-1">Track</label>
      <select name="track" required class="w-full border rounded-xl px-3 py-2">
        @foreach($tracks as $t)
          <option value="{{ $t }}" @selected(old('track', $psy_test->track) === $t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>

    {{-- Type --}}
    <div>
      <label class="block text-sm font-medium mb-1">Type</label>
      <select name="type" required class="w-full border rounded-xl px-3 py-2">
        @foreach($types as $t)
          <option value="{{ $t }}" @selected(old('type', $psy_test->type) === $t)>{{ strtoupper($t) }}</option>
        @endforeach
      </select>
    </div>

    {{-- Time Limit (menit, opsional) --}}
    <div>
      <label class="block text-sm font-medium mb-1">Time Limit (minutes)</label>
      <input type="number" name="time_limit_min" min="1" max="600"
             value="{{ old('time_limit_min', $psy_test->time_limit_min) }}"
             class="w-full border rounded-xl px-3 py-2" placeholder="Optional">
    </div>

    {{-- Status --}}
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <select name="is_active" class="w-full border rounded-xl px-3 py-2">
        <option value="1" @selected(old('is_active', (int)$psy_test->is_active) == 1)>Active</option>
        <option value="0" @selected(old('is_active', (int)$psy_test->is_active) == 0)>Inactive</option>
      </select>
    </div>

    <div class="flex items-center gap-3">
      <button class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
      <a href="{{ route('admin.psy-tests.show', $psy_test) }}"
         class="px-5 py-2 border rounded-xl hover:bg-gray-50">Cancel</a>
    </div>
  </form>
</div>
@endsection
