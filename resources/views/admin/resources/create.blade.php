@extends('layouts.admin')

@section('title','Add Resource')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-blue-900">Add Resource</h1>

<form action="{{ route('admin.resources.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-6">
  @csrf

  <div>
    <label class="block text-sm font-medium mb-1">Lesson</label>
    <select name="lesson_id" class="w-full border rounded px-3 py-2" required>
      <option value="">— Select Lesson —</option>
      @foreach($lessons as $lesson)
        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">URL</label>
    <input type="url" name="url" class="w-full border rounded px-3 py-2" required>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Type</label>
    <input type="text" name="type" class="w-full border rounded px-3 py-2" placeholder="pdf, video, link">
  </div>

  <div class="flex justify-end">
    <a href="{{ route('admin.resources.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
    <button type="submit" class="ml-3 px-4 py-2 bg-blue-700 text-white rounded">Save</button>
  </div>
</form>
@endsection
