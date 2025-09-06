@extends('app.layouts.base')

@section('title','Edit Thread')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">Edit Thread</h1>

  <form method="POST" action="{{ route('app.qa-threads.update',$thread) }}" class="bg-white rounded-xl shadow p-6 space-y-5">
    @csrf @method('PUT')

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-1">Kursus (opsional)</label>
        <select name="course_id" class="w-full border rounded-lg px-3 py-2">
          <option value="">— Pilih kursus —</option>
          @foreach($courses as $c)
            <option value="{{ $c->id }}" @selected(old('course_id',$thread->course_id)==$c->id)>{{ $c->title }}</option>
          @endforeach
        </select>
        @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="block font-semibold mb-1">Pelajaran (opsional)</label>
        <select name="lesson_id" class="w-full border rounded-lg px-3 py-2">
          <option value="">— Pilih pelajaran —</option>
          @foreach($lessons as $l)
            <option value="{{ $l->id }}" @selected(old('lesson_id',$thread->lesson_id)===$l->id)>{{ $l->title }}</option>
          @endforeach
        </select>
        @error('lesson_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    <div>
      <label class="block font-semibold mb-1">Judul</label>
      <input name="title" value="{{ old('title',$thread->title) }}" class="w-full border rounded-lg px-3 py-2" required>
      @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block font-semibold mb-1">Pertanyaan / Deskripsi</label>
      <textarea name="body" rows="8" class="w-full border rounded-lg px-3 py-2" required>{{ old('body',$thread->body) }}</textarea>
      @error('body') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Simpan</button>
      <a href="{{ route('app.qa-threads.show',$thread) }}" class="px-4 py-2 rounded-lg bg-gray-100">Batal</a>
    </div>
  </form>
</div>
@endsection
