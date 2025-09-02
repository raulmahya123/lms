@extends('layouts.admin')

@section('title','Options')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-blue-900">Options</h1>
  <a href="{{ route('admin.options.create') }}"
     class="px-4 py-2 rounded-lg bg-blue-700 text-white hover:bg-blue-600">
    + Add Option
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
        <th class="px-4 py-3 text-left">Question</th>
        <th class="px-4 py-3 text-left">Text</th>
        <th class="px-4 py-3 text-left">Correct</th>
        <th class="px-4 py-3 text-left">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      @forelse ($options as $opt)
        <tr>
          <td class="px-4 py-3">{{ $opt->id }}</td>
          <td class="px-4 py-3">{{ Str::limit($opt->question->prompt ?? '-',40) }}</td>
          <td class="px-4 py-3">{{ Str::limit($opt->text,50) }}</td>
          <td class="px-4 py-3">
            @if($opt->is_correct)
              <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">True</span>
            @else
              <span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded">False</span>
            @endif
          </td>
          <td class="px-4 py-3 space-x-2">
            <a href="{{ route('admin.options.show',$opt) }}"
               class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-500 text-xs">View</a>
            <a href="{{ route('admin.options.edit',$opt) }}"
               class="px-3 py-1.5 rounded bg-yellow-500 text-white hover:bg-yellow-400 text-xs">Edit</a>
            <form action="{{ route('admin.options.destroy',$opt) }}"
                  method="POST" class="inline-block"
                  onsubmit="return confirm('Yakin hapus opsi ini?')">
              @csrf @method('DELETE')
              <button type="submit"
                      class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-500 text-xs">
                Delete
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada opsi.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $options->links() }}
</div>
@endsection
