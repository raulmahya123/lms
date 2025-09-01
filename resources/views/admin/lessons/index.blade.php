@extends('layouts.admin')
@section('title','Lessons')

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex items-center gap-2">
    <select name="module_id" class="border rounded px-3 py-2">
      <option value="">— Filter by Module —</option>
      @php
        $__modules = \App\Models\Module::with('course:id,title')->orderBy('course_id')->orderBy('ordering')->get();
      @endphp
      @foreach($__modules as $m)
        <option value="{{ $m->id }}" @selected(request('module_id')==$m->id)>
          {{ $m->course?->title }} — {{ $m->title }}
        </option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
    @if(request('module_id')) <a href="{{ route('admin.lessons.index') }}" class="underline text-sm">Reset</a> @endif
  </form>

  <a href="{{ route('admin.lessons.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Lesson</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">#</th>
        <th class="p-2 text-left">Course</th>
        <th class="p-2 text-left">Module</th>
        <th class="p-2 text-left">Title</th>
        <th class="p-2 text-left">Ordering</th>
        <th class="p-2 text-left">Free?</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($lessons as $l)
        <tr class="border-t">
          <td class="p-2">{{ $l->id }}</td>
          <td class="p-2">{{ $l->module?->course?->title }}</td>
          <td class="p-2">{{ $l->module?->title }}</td>
          <td class="p-2">{{ $l->title }}</td>
          <td class="p-2">{{ $l->ordering }}</td>
          <td class="p-2">{{ $l->is_free ? 'Yes' : 'No' }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.lessons.edit',$l) }}" class="text-blue-600 underline">Edit</a>
            <form method="POST" action="{{ route('admin.lessons.destroy',$l) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 underline" onclick="return confirm('Delete this lesson?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="7">No lessons.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $lessons->withQueryString()->links() }}</div>
@endsection
