@extends('layouts.admin')

@section('title','Create Course')

@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-4 bg-white p-6 rounded shadow">
  @csrf
  <div>
    <label class="block">Title</label>
    <input name="title" class="w-full border p-2" required>
  </div>
  <div>
    <label class="block">Description</label>
    <textarea name="description" class="w-full border p-2"></textarea>
  </div>
  <div>
    <label class="block">Cover URL</label>
    <input name="cover_url" class="w-full border p-2">
  </div>
  <div>
    <label><input type="checkbox" name="is_published"> Published</label>
  </div>
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
</form>
@endsection
