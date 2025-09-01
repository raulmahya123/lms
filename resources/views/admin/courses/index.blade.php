@extends('layouts.admin')

@section('title','Courses')

@section('content')
<div class="mb-4">
  <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Course</a>
</div>

<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-200">
    <tr>
      <th class="p-2 text-left">ID</th>
      <th class="p-2 text-left">Title</th>
      <th class="p-2 text-left">Modules</th>
      <th class="p-2 text-left">Published</th>
      <th class="p-2">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($courses as $c)
    <tr class="border-t">
      <td class="p-2">{{ $c->id }}</td>
      <td class="p-2">{{ $c->title }}</td>
      <td class="p-2">{{ $c->modules_count }}</td>
      <td class="p-2">{{ $c->is_published ? 'Yes' : 'No' }}</td>
      <td class="p-2">
        <a href="{{ route('admin.courses.edit',$c) }}" class="text-blue-600 underline">Edit</a>
        <form method="POST" action="{{ route('admin.courses.destroy',$c) }}" class="inline">
          @csrf @method('DELETE')
          <button class="text-red-600 underline" onclick="return confirm('Delete?')">Delete</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="mt-4">{{ $courses->links() }}</div>
@endsection
