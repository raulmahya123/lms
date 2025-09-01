@extends('layouts.admin')

@section('title','Edit Module')

@section('content')
<form method="POST" action="{{ route('admin.modules.update',$module) }}" class="space-y-5 bg-white p-6 rounded shadow max-w-2xl">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Course</label>
    <select name="course_id" class="w-full border rounded px-3 py-2" required>
      @foreach($courses as $c)
        <option value="{{ $c->id }}" @selected(old('course_id',$module->course_id)==$c->id)>{{ $c->title }}</option>
      @endforeach
    </select>
    @error('course_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title" class="w-full border rounded px-3 py-2" value="{{ old('title',$module->title) }}" required>
    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Ordering</label>
    <input type="number" name="ordering" class="w-full border rounded px-3 py-2" value="{{ old('ordering',$module->ordering) }}" min="1">
    @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.modules.index') }}" class="px-4 py-2 rounded border">Back</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
  </div>
</form>

{{-- Opsional: quick list lesson milik module ini --}}
@if($module->relationLoaded('lessons') || $module->lessons()->exists())
  <div class="mt-8">
    <h2 class="text-lg font-semibold mb-2">Lessons in this Module</h2>
    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 text-left">#</th>
            <th class="p-2 text-left">Title</th>
            <th class="p-2 text-left">Ordering</th>
            <th class="p-2 text-left">Free?</th>
            <th class="p-2"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($module->lessons()->orderBy('ordering')->get() as $l)
            <tr class="border-t">
              <td class="p-2">{{ $l->id }}</td>
              <td class="p-2">{{ $l->title }}</td>
              <td class="p-2">{{ $l->ordering }}</td>
              <td class="p-2">{{ $l->is_free ? 'Yes' : 'No' }}</td>
              <td class="p-2">
                <a href="{{ route('admin.lessons.edit',$l) }}" class="text-blue-600 underline">Edit</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endif
@endsection
