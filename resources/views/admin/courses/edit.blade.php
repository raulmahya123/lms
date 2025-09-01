@extends('layouts.admin')

@section('title','Edit Course')

@section('content')
<form method="POST" action="{{ route('admin.courses.update',$course) }}" class="space-y-4 bg-white p-6 rounded shadow">
  @csrf @method('PUT')
  <div>
    <label class="block">Title</label>
    <input name="title" value="{{ old('title',$course->title) }}" class="w-full border p-2" required>
  </div>
  <div>
    <label class="block">Description</label>
    <textarea name="description" class="w-full border p-2">{{ old('description',$course->description) }}</textarea>
  </div>
  <div>
    <label class="block">Cover URL</label>
    <input name="cover_url" value="{{ old('cover_url',$course->cover_url) }}" class="w-full border p-2">
  </div>
  <div>
    <label><input type="checkbox" name="is_published" value="1" @checked($course->is_published)> Published</label>
  </div>
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
</form>
@endsection
