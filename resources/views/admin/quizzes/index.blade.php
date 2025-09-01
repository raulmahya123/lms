@extends('layouts.admin')
@section('title','Quizzes')

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex items-center gap-2">
    <select name="lesson_id" class="border rounded px-3 py-2">
      <option value="">— Filter by Lesson —</option>
      @php
        $__lessons = \App\Models\Lesson::select('id','title')->orderBy('id','desc')->get();
      @endphp
      @foreach($__lessons as $ls)
        <option value="{{ $ls->id }}" @selected(request('lesson_id')==$ls->id)>{{ $ls->title }}</option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
    @if(request('lesson_id')) <a href="{{ route('admin.quizzes.index') }}" class="underline text-sm">Reset</a> @endif
  </form>

  <a href="{{ route('admin.quizzes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Quiz</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">#</th>
        <th class="p-2 text-left">Lesson</th>
        <th class="p-2 text-left">Title</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($quizzes as $q)
        <tr class="border-t">
          <td class="p-2">{{ $q->id }}</td>
          <td class="p-2">{{ $q->lesson?->title }}</td>
          <td class="p-2 font-medium">{{ $q->title }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.quizzes.edit',$q) }}" class="text-blue-600 underline">Edit</a>
            <form method="POST" action="{{ route('admin.quizzes.destroy',$q) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 underline" onclick="return confirm('Delete this quiz?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="4">No quizzes.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $quizzes->withQueryString()->links() }}</div>
@endsection
