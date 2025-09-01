@extends('layouts.admin')
@section('title','Create Quiz')

@section('content')
<form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-5 bg-white p-6 rounded shadow max-w-2xl">
  @csrf
  <div>
    <label class="block text-sm font-medium mb-1">Lesson</label>
    <select name="lesson_id" class="w-full border rounded px-3 py-2" required>
      <option value="">— Select Lesson —</option>
      @foreach($lessons as $ls)
        <option value="{{ $ls->id }}" @selected(old('lesson_id')==$ls->id)>{{ $ls->title }}</option>
      @endforeach
    </select>
    @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" class="w-full border rounded px-3 py-2" value="{{ old('title') }}" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.quizzes.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
  </div>
</form>
@endsection
