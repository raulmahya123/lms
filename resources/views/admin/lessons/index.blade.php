@extends('layouts.admin')

@section('title','Lessons — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters:false }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Lesson/Play icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.5 5.75A2.75 2.75 0 0 1 7.25 3h9.5A2.75 2.75 0 0 1 19.5 5.75v12.5A2.75 2.75 0 0 1 16.75 21h-9.5A2.75 2.75 0 0 1 4.5 18.25V5.75Zm5 1.25a.75.75 0 0 0-.75.75v8.5a.75.75 0 0 0 1.14.64l6.5-4.25a.75.75 0 0 0 0-1.28l-6.5-4.25a.75.75 0 0 0-.39-.11Z"/>
        </svg>
        Lessons
      </h1>
      <p class="text-sm opacity-70">Kelola pelajaran per modul. Filter cepat, cari judul, dan aksi edit/hapus.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.lessons.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New Lesson
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter icon --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTERS / SEARCH --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Module</label>
      <div class="relative">
        <select name="module_id" class="w-full border rounded-xl pl-10 pr-3 py-2">
          <option value="">— All Modules —</option>
          @php
            $__modules = \App\Models\Module::with('course:id,title')
              ->orderBy('course_id')->orderBy('ordering')->get();
          @endphp
          @foreach($__modules as $m)
            <option value="{{ $m->id }}" @selected(request('module_id')==$m->id)>
              {{ $m->course?->title }} — {{ $m->title }}
            </option>
          @endforeach
        </select>
        {{-- list icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Search title</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" placeholder="Cari judul lesson…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        {{-- search icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
        </svg>
      </div>
    </div>

    <div class="flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        {{-- funnel icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Apply
      </button>
      @if(request()->hasAny(['module_id','q']))
        <a href="{{ route('admin.lessons.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          {{-- reset icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 5.25a6.75 6.75 0 1 0 6.53 8.4.75.75 0 1 1 1.46.3 8.25 8.25 0 1 1-1.92-7.17V5.25a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5h1.86A6.73 6.73 0 0 0 12 5.25Z"/>
          </svg>
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    {{-- header strip --}}
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $lessons->total() }}</span>
        <span class="opacity-70">lessons found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $lessons->currentPage() }} / {{ $lessons->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-16">#</th>
            <th class="p-3 text-left">Course</th>
            <th class="p-3 text-left">Module</th>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">Content URLs</th>
            <th class="p-3 text-left w-28">Ordering</th>
            <th class="p-3 text-left w-24">Free?</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($lessons as $l)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $l->id }}</td>
              <td class="p-3">{{ $l->module?->course?->title ?? '-' }}</td>
              <td class="p-3">{{ $l->module?->title ?? '-' }}</td>
              <td class="p-3 font-medium">{{ $l->title }}</td>
              <td class="p-3">
                @php
                  $videos = $l->content_url;
                  if (is_string($videos)) {
                      $decoded = json_decode($videos, true);
                      $videos = is_array($decoded) ? $decoded : [];
                  }
                @endphp
                @if(!empty($videos))
                  <div class="flex flex-wrap gap-1.5 max-w-[420px]">
                    @foreach($videos as $i => $video)
                      <a href="{{ route('admin.lessons.show', [$l, 'v' => $i]) }}"
                         class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border hover:bg-gray-50 text-xs"
                         title="Play: {{ $video['title'] ?? 'Untitled' }}">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
                        <span class="truncate max-w-[160px]">{{ $video['title'] ?? 'Untitled' }}</span>
                      </a>
                    @endforeach
                  </div>
                @else
                  <span class="text-xs opacity-60">-</span>
                @endif
              </td>
              <td class="p-3">{{ $l->ordering }}</td>
              <td class="p-3">
                @if($l->is_free)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Yes</span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">No</span>
                @endif
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.lessons.show',$l) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                     title="View / Play">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/>
                    </svg>
                    View
                  </a>
                  <a href="{{ route('admin.lessons.edit',$l) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.lessons.destroy',$l) }}"
                        onsubmit="return confirm('Delete this lesson?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-10 text-center text-sm opacity-70">
                Belum ada lesson.
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
        <span class="font-semibold">{{ $lessons->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $lessons->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $lessons->total() }}</span>
        results
      </div>
      <div>
        {{ $lessons->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
