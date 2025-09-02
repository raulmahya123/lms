@extends('layouts.admin')

@section('title','View Option')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-blue-900">Option Detail</h1>

<div class="bg-white shadow rounded-lg p-6 space-y-4">
  <div>
    <h2 class="text-sm font-medium text-gray-600">ID</h2>
    <p class="text-lg">{{ $option->id }}</p>
  </div>

  <div>
    <h2 class="text-sm font-medium text-gray-600">Question</h2>
    <p class="text-lg">{{ $option->question->prompt ?? '-' }}</p>
  </div>

  <div>
    <h2 class="text-sm font-medium text-gray-600">Option Text</h2>
    <p class="text-lg">{{ $option->text }}</p>
  </div>

  <div>
    <h2 class="text-sm font-medium text-gray-600">Correct?</h2>
    <p class="text-lg">
      @if($option->is_correct)
        ✅ Yes
      @else
        ❌ No
      @endif
    </p>
  </div>
</div>

<div class="mt-6 flex space-x-3">
  <a href="{{ route('admin.options.edit',$option) }}"
     class="px-4 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-400">Edit</a>
  <form action="{{ route('admin.options.destroy',$option) }}" method="POST" onsubmit="return confirm('Yakin hapus opsi ini?')">
    @csrf @method('DELETE')
    <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-500">Delete</button>
  </form>
  <a href="{{ route('admin.options.index') }}" class="px-4 py-2 rounded bg-gray-200">Back</a>
</div>
@endsection
