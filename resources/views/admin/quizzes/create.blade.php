@extends('layouts.admin')
@section('title','Create Quiz — BERKEMAH')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Quiz/Clipboard icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 3.75A2.25 2.25 0 0 1 11.25 1.5h1.5A2.25 2.25 0 0 1 15 3.75h1.5A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75H9Zm1.5 0A.75.75 0 0 0 9.75 4.5h4.5a.75.75 0 0 0-.75-.75h-3Z"/>
        </svg>
        Create Quiz
      </h1>
      <p class="text-sm opacity-70">Buat kuis baru untuk salah satu lesson.</p>
    </div>
    <a href="{{ route('admin.quizzes.index') }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">← Back</a>
  </div>

  {{-- FORM CARD --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.quizzes.store') }}" class="space-y-6">
      @csrf

      {{-- Lesson --}}
      <div>
        <label class="block text-sm font-medium mb-1">Lesson <span class="text-red-500">*</span></label>
        <div class="relative">
          <select name="lesson_id" class="w-full border rounded-xl pl-10 pr-3 py-2" required>
            <option value="">— Select Lesson —</option>
            @foreach($lessons as $ls)
              <option value="{{ $ls->id }}" @selected(old('lesson_id')==$ls->id)>{{ $ls->title }}</option>
            @endforeach
          </select>
          {{-- list icon --}}
          <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
          </svg>
        </div>
        @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title"
               value="{{ old('title') }}"
               placeholder="Contoh: Kuis Dasar HTML"
               class="w-full border rounded-xl px-3 py-2" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          {{-- save icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5.25 4.5A2.25 2.25 0 0 1 7.5 2.25h7.19a2.25 2.25 0 0 1 1.59.66l2.81 2.81a2.25 2.25 0 0 1 .66 1.59v9.75A2.25 2.25 0 0 1 17.5 19.5h-10A2.25 2.25 0 0 1 5.25 17.25v-12.75Z"/>
          </svg>
          Save Quiz
        </button>
        <a href="{{ route('admin.quizzes.index') }}"
           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
