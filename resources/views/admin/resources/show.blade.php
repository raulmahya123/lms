@extends('layouts.admin')

@section('title','View Resource')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-blue-900">Resource Detail</h1>

<div class="bg-white shadow rounded p-6 space-y-4">
  <div><strong>Lesson:</strong> {{ $resource->lesson->title ?? '-' }}</div>
  <div><strong>Title:</strong> {{ $resource->title }}</div>
  <div><strong>URL:</strong> <a href="{{ $resource->url }}" target="_blank" class="text-blue-600 underline">{{ $resource->url }}</a></div>
  <div><strong>Type:</strong> {{ $resource->type ?? '-' }}</div>
</div>

<div class="mt-6 flex space-x-2">
  <a href="{{ route('admin.resources.edit',$resource) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
  <form action="{{ route('admin.resources.destroy',$resource) }}" method="POST" onsubmit="return confirm('Hapus resource ini?')">
    @csrf @method('DELETE')
    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
  </form>
  <a href="{{ route('admin.resources.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
</div>
@endsection
