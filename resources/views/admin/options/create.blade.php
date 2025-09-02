@extends('layouts.admin')

@section('title','Add Option')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-blue-900">Add Option</h1>

<form action="{{ route('admin.options.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-6">
  @csrf

  <div>
    <label class="block text-sm font-medium mb-1">Question</label>
    <select name="question_id" class="w-full border rounded px-3 py-2" required>
      <option value="">— Select Question —</option>
      @foreach($questions as $q)
        <option value="{{ $q->id }}">{{ Str::limit($q->prompt,50) }}</option>
      @endforeach
    </select>
    @error('question_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Option Text</label>
    <textarea name="text" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('text') }}</textarea>
    @error('text') <p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
  </div>

  <div class="flex items-center space-x-2">
    <input type="checkbox" id="is_correct" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
    <label for="is_correct" class="text-sm">Is Correct?</label>
  </div>

  <div class="flex justify-end">
    <a href="{{ route('admin.options.index') }}" class="px-4 py-2 rounded bg-gray-200">Cancel</a>
    <button type="submit" class="ml-3 px-4 py-2 rounded bg-blue-700 text-white hover:bg-blue-600">Save</button>
  </div>
</form>
@endsection
