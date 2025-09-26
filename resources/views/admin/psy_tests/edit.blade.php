{{-- resources/views/admin/psy_tests/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit Test — BERKEMAH')

@section('content')
@php
  /** @var \App\Models\PsyTest $psy_test */
@endphp

<div class="max-w-3xl mx-auto space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-blue-900">Edit Test</h1>
    <a href="{{ route('admin.psy-tests.show', $psy_test) }}"
       class="px-3 py-2 border rounded-xl hover:bg-gray-50">← Back</a>
  </div>

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.psy-tests.update', $psy_test) }}" class="space-y-6">
    @csrf @method('PUT')

    {{-- Name --}}
    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input type="text" name="name" value="{{ old('name',$psy_test->name) }}" required
             class="w-full border rounded-xl px-3 py-2">
    </div>

    {{-- Slug --}}
    <div>
      <label class="block text-sm font-medium mb-1">Slug</label>
      <input type="text" name="slug" value="{{ old('slug',$psy_test->slug) }}"
             class="w-full border rounded-xl px-3 py-2">
    </div>

    {{-- Description --}}
    <div>
      <label class="block text-sm font-medium mb-1">Description</label>
      <textarea name="description" rows="3"
                class="w-full border rounded-xl px-3 py-2">{{ old('description',$psy_test->description) }}</textarea>
    </div>

    {{-- Status --}}
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <select name="is_active" class="w-full border rounded-xl px-3 py-2">
        <option value="1" @selected($psy_test->is_active)>Active</option>
        <option value="0" @selected(!$psy_test->is_active)>Inactive</option>
      </select>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
      <button class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
      <a href="{{ route('admin.psy-tests.show', $psy_test) }}"
         class="px-5 py-2 border rounded-xl hover:bg-gray-50">Cancel</a>
    </div>
  </form>
</div>
@endsection
