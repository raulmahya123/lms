@extends('app.layouts.base')
@section('title','Courses')
@section('content')
<form method="GET" class="mb-4">
  <input name="q" value="{{ request('q') }}" placeholder="Cari judul..." class="border rounded px-3 py-2">
  <button class="px-3 py-2 bg-gray-900 text-white rounded">Search</button>
</form>

<div class="grid md:grid-cols-3 gap-4">
@foreach($courses as $c)
  <a href="{{ route('app.courses.show',$c) }}" class="block bg-white border rounded overflow-hidden">
    <div class="h-36 bg-gray-100" style="background-image:url('{{ $c->cover_url }}'); background-size:cover"></div>
    <div class="p-3">
      <div class="font-semibold">{{ $c->title }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ $c->modules_count }} modules · {{ $c->enrollments_count }} enrolled</div>
      @if(in_array($c->id, $myIds)) <span class="inline-block mt-2 text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Enrolled</span> @endif
    </div>
  </a>
@endforeach
</div>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
