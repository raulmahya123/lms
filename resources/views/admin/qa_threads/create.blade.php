@extends('layouts.admin')
@section('title','New Q&A Thread — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h1 class="text-3xl font-bold tracking-tight text-blue-900">New Thread</h1>
    <a href="{{ route('admin.qa-threads.index') }}"
       class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
      ← Back
    </a>
  </div>

  {{-- Card --}}
  <form method="POST" action="{{ route('admin.qa-threads.store') }}"
        class="bg-white/90 border border-blue-100 rounded-2xl p-6 space-y-5 shadow-lg backdrop-blur">
    @csrf

    {{-- User (UUID-safe) --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">
        User <span class="text-red-500">*</span>
      </label>
      <select name="user_id"
              class="w-full border border-blue-200 rounded-xl p-2 focus:ring-2 focus:ring-blue-500"
              required>
        <option value="">— pilih user —</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>
            {{ $u->name }} ({{ Str::limit($u->id, 8, '') }})
          </option>
        @endforeach
      </select>
      @error('user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      <p class="text-xs text-blue-700/70 mt-1">Gunakan dropdown agar aman untuk UUID.</p>
    </div>

    {{-- Course --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">Course</label>
      <select name="course_id"
              class="w-full border border-blue-200 rounded-xl p-2 focus:ring-2 focus:ring-blue-500">
        <option value="">— None —</option>
        @foreach($courses as $c)
          <option value="{{ $c->id }}" @selected(old('course_id')==$c->id)>{{ $c->title }}</option>
        @endforeach
      </select>
      @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Lesson --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">Lesson</label>
      <select name="lesson_id"
              class="w-full border border-blue-200 rounded-xl p-2 focus:ring-2 focus:ring-blue-500">
        <option value="">— None —</option>
        @foreach($lessons as $l)
          <option value="{{ $l->id }}" @selected(old('lesson_id')==$l->id)>{{ $l->title }}</option>
        @endforeach
      </select>
      @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Title --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">
        Title <span class="text-red-500">*</span>
      </label>
      <input type="text" name="title" value="{{ old('title') }}"
             class="w-full border border-blue-200 rounded-xl p-2 focus:ring-2 focus:ring-blue-500" required>
      @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Body --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">
        Body <span class="text-red-500">*</span>
      </label>
      <textarea name="body" rows="6"
                class="w-full border border-blue-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500"
                required>{{ old('body') }}</textarea>
      @error('body') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
      <label class="block text-sm font-semibold mb-1 text-blue-900">Status</label>
      @php($st = old('status','open'))
      <select name="status"
              class="w-full border border-blue-200 rounded-xl p-2 focus:ring-2 focus:ring-blue-500">
        <option value="open" @selected($st==='open')>Open</option>
        <option value="resolved" @selected($st==='resolved')>Resolved</option>
        <option value="closed" @selected($st==='closed')>Closed</option>
      </select>
      @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Actions --}}
    <div class="pt-2">
      <button
        class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-600 text-white font-semibold hover:from-blue-800 hover:to-blue-700 shadow-md transition">
        Create Thread
      </button>
    </div>
  </form>
</div>
@endsection
