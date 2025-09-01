@extends('layouts.admin')
@section('title','Edit Quiz')

@section('content')
<form method="POST" action="{{ route('admin.quizzes.update',$quiz) }}" class="space-y-5 bg-white p-6 rounded shadow max-w-2xl">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm font-medium mb-1">Lesson</label>
    <select name="lesson_id" class="w-full border rounded px-3 py-2" required>
      @foreach($lessons as $ls)
        <option value="{{ $ls->id }}" @selected(old('lesson_id',$quiz->lesson_id)==$ls->id)>{{ $ls->title }}</option>
      @endforeach
    </select>
    @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" class="w-full border rounded px-3 py-2" value="{{ old('title',$quiz->title) }}" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.quizzes.index') }}" class="px-4 py-2 rounded border">Back</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
  </div>
</form>

{{-- Questions & Options --}}
<div class="mt-10 grid gap-6 md:grid-cols-2">
  {{-- Create Question --}}
  <div class="bg-white p-6 rounded shadow">
    <h2 class="font-semibold mb-4">Add Question</h2>
    <form method="POST" action="{{ route('admin.questions.store') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
      <div>
        <label class="block text-sm">Type</label>
        <select name="type" class="w-full border rounded px-3 py-2">
          <option value="mcq">Multiple Choice</option>
          <option value="short">Short Answer</option>
          <option value="long">Long Answer</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Prompt</label>
        <textarea name="prompt" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
      </div>
      <div>
        <label class="block text-sm">Points</label>
        <input type="number" name="points" class="w-full border rounded px-3 py-2" value="1" min="1">
      </div>
      <button class="px-3 py-2 bg-gray-900 text-white rounded">Add Question</button>
    </form>
  </div>

  {{-- Questions List --}}
  <div class="bg-white p-6 rounded shadow">
    <h2 class="font-semibold mb-4">Questions</h2>

    @forelse($quiz->questions as $q)
      <div class="border rounded mb-4">
        <div class="p-3 flex items-start justify-between bg-gray-50">
          <div>
            <div class="text-xs uppercase text-gray-500">{{ strtoupper($q->type) }} • {{ $q->points }} pts</div>
            <div class="font-medium">{{ $q->prompt }}</div>
          </div>
          <div class="flex items-center gap-2">
            {{-- Edit Question (inline) --}}
            <details>
              <summary class="text-sm underline cursor-pointer">Edit</summary>
              <form method="POST" action="{{ route('admin.questions.update',$q) }}" class="mt-2 space-y-2">
                @csrf @method('PUT')
                <input type="hidden" name="type" value="{{ $q->type }}">
                <textarea name="prompt" class="w-full border rounded px-3 py-2" rows="2">{{ $q->prompt }}</textarea>
                <input type="number" name="points" class="w-full border rounded px-3 py-2" value="{{ $q->points }}" min="1">
                <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Save</button>
              </form>
            </details>

            {{-- Delete Question --}}
            <form method="POST" action="{{ route('admin.questions.destroy',$q) }}">
              @csrf @method('DELETE')
              <button class="text-red-600 text-sm underline" onclick="return confirm('Delete question?')">Delete</button>
            </form>
          </div>
        </div>

        {{-- Options (only for MCQ) --}}
        @if($q->type === 'mcq')
          <div class="p-3 space-y-3">
            {{-- Add Option --}}
            <form method="POST" action="{{ route('admin.options.store') }}" class="flex items-center gap-2">
              @csrf
              <input type="hidden" name="question_id" value="{{ $q->id }}">
              <input name="text" class="flex-1 border rounded px-3 py-2" placeholder="New option..." required>
              <label class="inline-flex items-center gap-1 text-sm">
                <input type="checkbox" name="is_correct" value="1"> correct
              </label>
              <button class="px-3 py-2 bg-gray-900 text-white rounded">Add</button>
            </form>

            {{-- Options List --}}
            @foreach($q->options as $op)
              <div class="flex items-center justify-between border rounded px-3 py-2">
                <div>
                  <span class="font-medium">{{ $op->text }}</span>
                  @if($op->is_correct)
                    <span class="text-green-700 text-xs font-semibold ml-2">✔ correct</span>
                  @endif
                </div>
                <div class="flex items-center gap-2">
                  {{-- Edit Option --}}
                  <details>
                    <summary class="text-sm underline cursor-pointer">Edit</summary>
                    <form method="POST" action="{{ route('admin.options.update',$op) }}" class="mt-2 flex items-center gap-2">
                      @csrf @method('PUT')
                      <input name="text" class="border rounded px-3 py-2" value="{{ $op->text }}" required>
                      <label class="inline-flex items-center gap-1 text-sm">
                        <input type="checkbox" name="is_correct" value="1" @checked($op->is_correct)> correct
                      </label>
                      <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Save</button>
                    </form>
                  </details>

                  {{-- Delete Option --}}
                  <form method="POST" action="{{ route('admin.options.destroy',$op) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-600 text-sm underline" onclick="return confirm('Delete option?')">Delete</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @empty
      <p class="text-gray-500">No questions yet.</p>
    @endforelse
  </div>
</div>
@endsection
