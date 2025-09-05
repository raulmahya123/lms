@extends('layouts.admin')
@section('title','New Psych Test — BERKEMAH')

@section('content')
@php($tracks = ['backend','frontend','fullstack','qa','devops','pm','custom'])
@php($types  = ['likert','mcq','iq','disc','big5','custom'])

<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">New Psych Test</h1>
    <a href="{{ route('admin.psy-tests.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back</a>
  </div>

  <form method="POST" action="{{ route('admin.psy-tests.store') }}" class="bg-white border rounded-2xl p-6 space-y-5">
    @csrf

    {{-- Name --}}
    <div>
      <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
      <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded-xl p-2" required>
      @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Slug --}}
    <div>
      <label class="block text-sm font-medium mb-1">Slug</label>
      <input type="text" name="slug" value="{{ old('slug') }}" placeholder="(auto from name if empty)"
             class="w-full border rounded-xl p-2">
      @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Track --}}
    <div>
      <label class="block text-sm font-medium mb-1">Track <span class="text-red-500">*</span></label>
      <select name="track" class="w-full border rounded-xl p-2" required>
        @foreach($tracks as $t)
          <option value="{{ $t }}" @selected(old('track')===$t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
      @error('track') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Type --}}
    <div>
      <label class="block text-sm font-medium mb-1">Type <span class="text-red-500">*</span></label>
      <select name="type" class="w-full border rounded-xl p-2" required>
        @foreach($types as $t)
          <option value="{{ $t }}" @selected(old('type')===$t)>{{ strtoupper($t) }}</option>
        @endforeach
      </select>
      @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Time Limit --}}
    <div>
      <label class="block text-sm font-medium mb-1">Time Limit (minutes)</label>
      <input type="number" name="time_limit_min" value="{{ old('time_limit_min') }}" min="1" max="600"
             class="w-full border rounded-xl p-2">
      @error('time_limit_min') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Active --}}
    <div class="flex items-center gap-3">
      <input type="hidden" name="is_active" value="0">
      <input id="is_active" type="checkbox" name="is_active" value="1" class="rounded" @checked(old('is_active',1))>
      <label for="is_active" class="text-sm">Active</label>
      @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="pt-2">
      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Create Test</button>
    </div>
  </form>
</div>
@endsection
