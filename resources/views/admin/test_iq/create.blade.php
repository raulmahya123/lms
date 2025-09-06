@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-semibold mb-4">Buat Test IQ Baru</h1>

@if($errors->any())
  <div class="mb-3 p-3 bg-red-50 text-red-700 rounded">
    <ul class="list-disc ml-5">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.test-iq.store') }}" class="grid gap-4 max-w-3xl">
  @csrf

  <label class="grid gap-1">
    <span class="font-medium">Judul</span>
    <input type="text" name="title" value="{{ old('title') }}" class="border rounded px-3 py-2" required>
  </label>

  <label class="grid gap-1">
    <span class="font-medium">Deskripsi</span>
    <textarea name="description" rows="3" class="border rounded px-3 py-2">{{ old('description') }}</textarea>
  </label>

  <div class="grid grid-cols-2 gap-4">
    <label class="grid gap-1">
      <span class="font-medium">Durasi (menit)</span>
      <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" class="border rounded px-3 py-2" min="0">
    </label>

    <label class="flex items-center gap-2 mt-6">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
      <span>Aktif</span>
    </label>
  </div>

  <label class="grid gap-1">
    <span class="font-medium">Questions (JSON)</span>
    <textarea name="questions_json" rows="12" class="font-mono text-sm border rounded px-3 py-2" placeholder='[
  {"id":1,"text":"2+2=?","options":["2","3","4","5"],"answer":"4"}
]'>{{ old('questions_json') }}</textarea>
    <small class="text-gray-500">Format: array objek {id, text, options[], answer}</small>
  </label>

  <div class="flex gap-2">
    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Simpan</button>
    <a href="{{ route('admin.test-iq.index') }}" class="px-4 py-2 rounded border">Batal</a>
  </div>
</form>
@endsection
