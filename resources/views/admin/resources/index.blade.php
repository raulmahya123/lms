@extends('layouts.admin')

@section('title','Resources — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters:false }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- book icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 4.5A2.25 2.25 0 0 0 3.75 6.75v11.5A2.25 2.25 0 0 0 6 20.5h12a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 18 4.5H6Zm0 1.5h12c.414 0 .75.336.75.75v10.25a.75.75 0 0 1-.75.75H6a.75.75 0 0 1-.75-.75V6.75c0-.414.336-.75.75-.75Z"/>
        </svg>
        Resources
      </h1>
      <p class="text-sm opacity-70">Kelola resource tambahan per lesson. Bisa berupa file, link, atau dokumen referensi.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.resources.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New Resource
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter icon --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTER FORM --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Lesson</label>
      <div class="relative">
        <select name="lesson_id" class="w-full border rounded-xl pl-3 pr-3 py-2">
          <option value="">— All Lessons —</option>
          @php
            $__lessons = \App\Models\Lesson::orderBy('title')->get();
          @endphp
          @foreach($__lessons as $l)
            <option value="{{ $l->id }}" @selected(request('lesson_id')==$l->id)>
              {{ $l->title }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Search title</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" placeholder="Cari judul resource…"
               class="w-full border rounded-xl pl-3 pr-3 py-2">
      </div>
    </div>

    <div class="flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        Apply
      </button>
      @if(request()->hasAny(['lesson_id','q']))
        <a href="{{ route('admin.resources.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- ALERT --}}
  @if(session('ok'))
    <div class="p-3 bg-green-100 text-green-700 rounded-xl">
      {{ session('ok') }}
    </div>
  @endif

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    {{-- header strip --}}
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $resources->total() }}</span>
        <span class="opacity-70">resources found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $resources->currentPage() }} / {{ $resources->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-14">#</th>
            <th class="p-3 text-left">Lesson</th>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">URL</th>
            <th class="p-3 text-left w-32">Type</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($resources as $res)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $res->id }}</td>
              <td class="p-3">{{ $res->lesson->title ?? '-' }}</td>
              <td class="p-3 font-medium">{{ $res->title }}</td>
              <td class="p-3">
                <a href="{{ $res->url }}" target="_blank"
                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border hover:bg-gray-50 text-xs text-blue-700">
                  Open
                </a>
              </td>
              <td class="p-3">{{ $res->type ?? '-' }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.resources.show',$res) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="View">
                    View
                  </a>
                  <a href="{{ route('admin.resources.edit',$res) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    Edit
                  </a>
                  <form action="{{ route('admin.resources.destroy',$res) }}" method="POST" class="inline"
                        onsubmit="return confirm('Hapus resource ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-10 text-center text-sm opacity-70">
                Belum ada resource.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing
        <span class="font-semibold">{{ $resources->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $resources->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $resources->total() }}</span>
        results
      </div>
      <div>
        {{ $resources->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
