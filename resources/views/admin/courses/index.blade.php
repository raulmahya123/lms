@extends('layouts.admin')

@section('title','Courses')

@section('content')
<div class="space-y-4">

  {{-- Header + Actions --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <h1 class="text-2xl font-bold">Courses</h1>
    <a href="{{ route('admin.courses.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">+ New Course</a>
  </div>

  {{-- Filter & Search --}}
  <form method="GET" action="{{ route('admin.courses.index') }}"
        class="bg-white rounded-xl shadow p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
      <label class="block text-sm font-medium mb-1">Search</label>
      <input type="text" name="q" value="{{ request('q') }}"
             placeholder="Title…"
             class="w-full border rounded p-2">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Published</label>
      <select name="published" class="w-full border rounded p-2">
        <option value="">All</option>
        <option value="1" @selected(request('published')==='1')>Yes</option>
        <option value="0" @selected(request('published')==='0')>No</option>
      </select>
    </div>
    <div class="flex items-end gap-2">
      <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded border">Reset</a>
      <button class="px-4 py-2 bg-gray-900 text-white rounded">Apply</button>
    </div>
  </form>

  {{-- Table --}}
  <div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full min-w-[720px]">
      <thead class="bg-gray-100 text-sm">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Cover</th>
          <th class="p-3 text-left">Title</th>
          <th class="p-3 text-left">Modules</th>
          <th class="p-3 text-left">Published</th>
          <th class="p-3 text-left">Updated</th>
          <th class="p-3 text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($courses as $c)
          <tr class="border-t">
            <td class="p-3 align-top">{{ $c->id }}</td>

            {{-- Cover thumb (normalize: http(s) | /storage | relative path) --}}
            <td class="p-3 align-top">
              @php
                $cover = $c->cover_url;
                if ($cover) {
                    $isFull  = \Illuminate\Support\Str::startsWith($cover, ['http://','https://']);
                    $isStor  = \Illuminate\Support\Str::startsWith($cover, ['/storage/','storage/']);
                    if (!$isFull && !$isStor) {
                        $cover = \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($cover,'/'));
                    } elseif ($isStor && \Illuminate\Support\Str::startsWith($cover,'storage/')) {
                        $cover = '/'.$cover;
                    }
                }
              @endphp

              @if(!empty($cover))
                <img src="{{ $cover }}" alt="Cover {{ $c->title }}"
                     class="h-12 w-20 object-cover rounded border">
              @else
                <div class="h-12 w-20 grid place-items-center border rounded text-xs text-gray-500">
                  No image
                </div>
              @endif
            </td>

            <td class="p-3 align-top">
              <div class="font-semibold">{{ $c->title }}</div>
              @if(!empty($c->description))
                <div class="text-sm text-gray-600 max-w-lg">
                  {{ \Illuminate\Support\Str::limit(strip_tags($c->description), 120) }}
                </div>
              @endif
            </td>

            <td class="p-3 align-top">
              {{ $c->modules_count }}
            </td>

            <td class="p-3 align-top">
              @if($c->is_published)
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                  Published
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                  Draft
                </span>
              @endif
            </td>

            <td class="p-3 align-top text-sm text-gray-600">
              {{ optional($c->updated_at)->format('Y-m-d H:i') }}
            </td>

            <td class="p-3 align-top">
              <div class="flex items-center justify-center gap-3">
                <a href="{{ route('admin.courses.edit',$c) }}"
                   class="px-2 py-1 text-blue-700 hover:underline">Edit</a>

                <form method="POST" action="{{ route('admin.courses.destroy',$c) }}"
                      onsubmit="return confirm('Delete this course?')"
                      class="inline">
                  @csrf
                  @method('DELETE')
                  <button class="px-2 py-1 text-red-600 hover:underline">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="p-8 text-center text-gray-500">
              Belum ada course yang cocok dengan filter/pencarian.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4">
    {{ $courses->withQueryString()->links() }}
  </div>
</div>
@endsection
