@extends('layouts.admin')

@section('title','Resources')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-blue-900">Resources</h1>
  <a href="{{ route('admin.resources.create') }}"
     class="px-4 py-2 rounded bg-blue-700 text-white hover:bg-blue-600">
    + Add Resource
  </a>
</div>

@if(session('ok'))
  <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
    {{ session('ok') }}
  </div>
@endif

<div class="bg-white shadow rounded-lg overflow-hidden">
  <table class="min-w-full text-sm">
    <thead class="bg-blue-800 text-white">
      <tr>
        <th class="px-4 py-3 text-left">#</th>
        <th class="px-4 py-3 text-left">Lesson</th>
        <th class="px-4 py-3 text-left">Title</th>
        <th class="px-4 py-3 text-left">URL</th>
        <th class="px-4 py-3 text-left">Type</th>
        <th class="px-4 py-3 text-left">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      @forelse($resources as $res)
        <tr>
          <td class="px-4 py-3">{{ $res->id }}</td>
          <td class="px-4 py-3">{{ $res->lesson->title ?? '-' }}</td>
          <td class="px-4 py-3">{{ $res->title }}</td>
          <td class="px-4 py-3">
            <a href="{{ $res->url }}" class="text-blue-600 underline" target="_blank">Open</a>
          </td>
          <td class="px-4 py-3">{{ $res->type ?? '-' }}</td>
          <td class="px-4 py-3 space-x-2">
            <a href="{{ route('admin.resources.show',$res) }}"
               class="px-3 py-1.5 bg-blue-600 text-white rounded text-xs">View</a>
            <a href="{{ route('admin.resources.edit',$res) }}"
               class="px-3 py-1.5 bg-yellow-500 text-white rounded text-xs">Edit</a>
            <form action="{{ route('admin.resources.destroy',$res) }}" method="POST" class="inline"
                  onsubmit="return confirm('Hapus resource ini?')">
              @csrf @method('DELETE')
              <button type="submit"
                      class="px-3 py-1.5 bg-red-600 text-white rounded text-xs">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada resource.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $resources->links() }}
</div>
@endsection
