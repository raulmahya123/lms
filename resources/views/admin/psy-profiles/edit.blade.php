@extends('layouts.admin')

@section('title','Edit Psy Profile')

@section('content')
<h1 class="text-xl font-bold mb-4">Edit Psy Profile</h1>

<form method="POST" action="{{ route('admin.psy-profiles.update',$psyProfile) }}" class="space-y-4">
  @csrf @method('PUT')

  <div>
    <label class="block mb-1">Tes</label>
    <select name="test_id" class="border rounded px-3 py-2 w-full">
      @foreach($tests as $id=>$title)
        <option value="{{ $id }}" @selected($psyProfile->test_id==$id)>{{ $title }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block mb-1">Key</label>
    <input type="text" name="key" class="border rounded px-3 py-2 w-full" value="{{ old('key',$psyProfile->key) }}">
  </div>

  <div>
    <label class="block mb-1">Nama</label>
    <input type="text" name="name" class="border rounded px-3 py-2 w-full" value="{{ old('name',$psyProfile->name) }}">
  </div>

  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block mb-1">Min Total</label>
      <input type="number" name="min_total" class="border rounded px-3 py-2 w-full" value="{{ old('min_total',$psyProfile->min_total) }}">
    </div>
    <div>
      <label class="block mb-1">Max Total</label>
      <input type="number" name="max_total" class="border rounded px-3 py-2 w-full" value="{{ old('max_total',$psyProfile->max_total) }}">
    </div>
  </div>

  <div>
    <label class="block mb-1">Deskripsi</label>
    <textarea name="description" class="border rounded px-3 py-2 w-full">{{ old('description',$psyProfile->description) }}</textarea>
  </div>

  <button class="px-4 py-2 rounded bg-indigo-600 text-white">Update</button>
  <a href="{{ route('admin.psy-profiles.index') }}" class="px-4 py-2 rounded border">Batal</a>
</form>
@endsection
