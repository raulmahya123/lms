@extends('layouts.admin')

@section('title','Modules')

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex items-center gap-2">
    <select name="course_id" class="border rounded px-3 py-2">
      <option value="">— Filter by Course —</option>
      @php
        // ambil list course untuk filter cepat
        $__courses = \App\Models\Course::select('id','title')->orderBy('title')->get();
      @endphp
      @foreach($__courses as $c)
        <option value="{{ $c->id }}" @selected(request('course_id')==$c->id)>{{ $c->title }}</option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
    @if(request('course_id'))
      <a href="{{ route('admin.modules.index') }}" class="underline text-sm">Reset</a>
    @endif
  </form>

  <a href="{{ route('admin.modules.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Module</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">#</th>
        <th class="p-2 text-left">Course</th>
        <th class="p-2 text-left">Title</th>
        <th class="p-2 text-left">Ordering</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($modules as $m)
        <tr class="border-t">
          <td class="p-2">{{ $m->id }}</td>
          <td class="p-2">{{ $m->course?->title }}</td>
          <td class="p-2">{{ $m->title }}</td>
          <td class="p-2">{{ $m->ordering }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.modules.edit',$m) }}" class="text-blue-600 underline">Edit</a>
            <form method="POST" action="{{ route('admin.modules.destroy',$m) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 underline" onclick="return confirm('Delete this module?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="5">No modules.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $modules->withQueryString()->links() }}
</div>
@endsection
