@extends('layouts.admin')

@section('title','Create Option — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- plus icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3.75a8.25 8.25 0 1 1 0 16.5 8.25 8.25 0 0 1 0-16.5Zm.75 3.75a.75.75 0 0 0-1.5 0v4.5H6.75a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5V7.5Z"/>
        </svg>
        Create Option
      </h1>
      <p class="text-sm opacity-70">Tambahkan opsi baru untuk sebuah pertanyaan.</p>
    </div>
    <a href="{{ route('admin.options.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition">
      {{-- back icon --}}
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.28 6.22a.75.75 0 0 1 0 1.06L6.56 11h11.19a.75.75 0 0 1 0 1.5H6.56l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z"/></svg>
      Back
    </a>
  </div>

  {{-- FLASH --}}
  @if(session('ok'))
    <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('ok') }}
    </div>
  @endif

  {{-- CARD FORM --}}
  <div class="rounded-2xl border bg-white p-6">
    <form action="{{ route('admin.options.store') }}" method="POST" class="space-y-6">
      @csrf

      {{-- Question --}}
      <div>
        <label class="block text-sm font-medium mb-1">Question <span class="text-red-500">*</span></label>
        <div class="relative">
          <select name="question_id" class="w-full border rounded-xl pl-10 pr-3 py-2" required>
            <option value="" disabled selected>— pilih pertanyaan —</option>
            @foreach($questions as $q)
              <option value="{{ $q->id }}" {{ old('question_id') == $q->id ? 'selected' : '' }}>
                {{ \Illuminate\Support\Str::limit($q->prompt,60) }}
              </option>
            @endforeach
          </select>
          {{-- list icon --}}
          <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
          </svg>
        </div>
        @error('question_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Option Text --}}
      <div>
        <label class="block text-sm font-medium mb-1">Option Text <span class="text-red-500">*</span></label>
        <div class="relative">
          <textarea name="text" rows="4" class="w-full border rounded-xl pl-10 pr-3 py-2" required>{{ old('text') }}</textarea>
          {{-- text icon --}}
          <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4.5 6.75A.75.75 0 0 1 5.25 6h13.5a.75.75 0 0 1 0 1.5H5.25A.75.75 0 0 1 4.5 6.75ZM5.25 10.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5Zm0 4.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z"/>
          </svg>
        </div>
        @error('text') <p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Is Correct (toggle) --}}
      <div>
        <label class="block text-sm font-medium mb-2">Correct?</label>
        <label class="inline-flex items-center gap-3 select-none">
          <input type="checkbox" id="is_correct" name="is_correct" value="1"
                 class="peer sr-only" {{ old('is_correct') ? 'checked' : '' }}>
          <span class="w-11 h-6 rounded-full border relative
                       transition peer-checked:bg-green-500 peer-checked:border-green-500
                       peer-checked:shadow-inner bg-gray-200 border-gray-300">
            <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition
                         peer-checked:translate-x-5"></span>
          </span>
          <span class="text-sm">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full
                         bg-gray-200 text-gray-700 peer-checked:bg-green-100 peer-checked:text-green-800">
              {{-- check/x icon --}}
              <svg class="w-4 h-4 hidden peer-checked:inline" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 1 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 1 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z"/>
              </svg>
              <svg class="w-4 h-4 peer-checked:hidden" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.59l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.65l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.715-4.715-4.715-4.715a.75.75 0 0 1 0-1.06Z"/>
              </svg>
              <span class="hidden peer-checked:inline">True</span>
              <span class="inline peer-checked:hidden">False</span>
            </span>
          </span>
        </label>
        @error('is_correct') <p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center justify-end gap-2">
        <a href="{{ route('admin.options.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          {{-- cancel icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.59l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.65l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.715-4.715-4.715-4.715a.75.75 0 0 1 0-1.06Z"/></svg>
          Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          {{-- save icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5.25 3A2.25 2.25 0 0 0 3 5.25v13.5A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V8.56a.75.75 0 0 0-.22-.53l-4.81-4.8A.75.75 0 0 0 15.44 3H5.25Zm2.5 3h5.5a.75.75 0 0 1 .75.75V9a.75.75 0 0 1-.75.75h-5.5A.75.75 0 0 1 7.75 9V6.75Z"/>
          </svg>
          Create
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
