@extends('app.layouts.base')
@section('title','Resource')
@section('content')
<h1 class="text-xl font-semibold mb-2">{{ $resource->title }}</h1>
<p class="text-sm text-gray-600 mb-4">Course: {{ $course->title }}</p>

@if($resource->type === 'link')
  <a href="{{ $resource->url }}" target="_blank" class="text-blue-700 hover:underline">Buka Link</a>
@else
  <a href="{{ $resource->url }}" class="text-blue-700 hover:underline">Download</a>
@endif
@endsection
