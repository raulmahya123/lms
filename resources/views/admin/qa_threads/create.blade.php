@extends('layouts.admin')
@section('title','New Q&A Thread — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">New Thread</h1>
    <a href="{{ route('admin.qa-threads.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back</a>
  </div>

  <form method="POST" action="{{ route('admin.qa-threads.store') }}" class="bg-white border rounded-2xl p-6 space-y-5">
    @csrf

    {{-- User --}}
    <div>
      <label class="block text-sm font-medium mb-1">User <span class="text-red-500">*</span></label>
      <input type="number" name="user_id" value="{{ old('user_id') }}" placeholder="User ID"
             class="w-full border rounded-xl p-2" required>
      @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Course --}}
    <div>
      <label class="block text-sm font-medium mb-1">Course</label>
      <select name="course_id" class="w-full border rounded-xl p-2">
        <option value="">— None —</option>
        @foreach($courses as $c)
          <option value="{{ $c->id }}" @selected(old('course_id')==$c->id)>{{ $c->title }}</option>
        @endforeach
      </select>
      @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Lesson --}}
    <div>
      <label class="block text-sm font-medium mb-1">Lesson</label>
      <select name="lesson_id" class="w-full border rounded-xl p-2">
        <option value="">— None —</option>
        @foreach($lessons as $l)
          <option value="{{ $l->id }}" @selected(old('lesson_id')==$l->id)>{{ $l->title }}</option>
        @endforeach
      </select>
      @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Title --}}
    <div>
      <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-xl p-2" required>
      @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Body --}}
    <div>
      <label class="block text-sm font-medium mb-1">Body <span class="text-red-500">*</span></label>
      <textarea name="body" rows="6" class="w-full border rounded-xl p-3" required>{{ old('body') }}</textarea>
      @error('body') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <select name="status" class="w-full border rounded-xl p-2">
        @php($st = old('status','open'))
        <option value="open" @selected($st==='open')>Open</option>
        <option value="resolved" @selected($st==='resolved')>Resolved</option>
        <option value="closed" @selected($st==='closed')>Closed</option>
      </select>
      @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="pt-2">
      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">Create Thread</button>
    </div>
  </form>
</div>
@endsection
