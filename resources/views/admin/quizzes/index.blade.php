@extends('layouts.admin')
@section('title','Quizzes — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters:false }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Quiz/Clipboard icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 3.75A2.25 2.25 0 0 1 11.25 1.5h1.5A2.25 2.25 0 0 1 15 3.75h1.5A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75H9Zm1.5 0A.75.75 0 0 0 9.75 4.5h4.5a.75.75 0 0 0-.75-.75h-3Z"/>
        </svg>
        Quizzes
      </h1>
      <p class="text-sm opacity-70">Kelola kuis per lesson. Filter cepat, cari judul, dan aksi edit/hapus.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.quizzes.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New Quiz
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
      <label class="block text-sm font-medium mb-1">Lesson</label>
      <div class="relative">
        <select name="lesson_id" class="w-full border rounded-xl pl-10 pr-3 py-2">
          <option value="">— All Lessons —</option>
          @php
            $__lessons = \App\Models\Lesson::select('id','title')->orderBy('id','desc')->get();
          @endphp
          @foreach($__lessons as $ls)
            <option value="{{ $ls->id }}" @selected(request('lesson_id')==$ls->id)>{{ $ls->title }}</option>
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
        <input type="text" name="q" x-model="q" placeholder="Cari judul quiz…"
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
      @if(request()->hasAny(['lesson_id','q']))
        <a href="{{ route('admin.quizzes.index') }}"
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
        <span class="font-semibold">{{ $quizzes->total() }}</span>
        <span class="opacity-70">quizzes found</span>
        @if(request('lesson_id'))
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
            {{-- badge icon --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Lesson filter active
          </span>
        @endif
        @if(request('q'))
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
            {{-- search badge icon --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
            “{{ request('q') }}”
          </span>
        @endif
      </div>
      <div class="text-xs opacity-70">Page {{ $quizzes->currentPage() }} / {{ $quizzes->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-16">#</th>
            <th class="p-3 text-left">Lesson</th>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-center w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($quizzes as $qz)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $qz->id }}</td>
              <td class="p-3">
                <div class="truncate max-w-[420px]" title="{{ $qz->lesson?->title }}">
                  {{ $qz->lesson?->title ?? '-' }}
                </div>
              </td>
              <td class="p-3">
                <div class="truncate max-w-[520px] font-medium" title="{{ $qz->title }}">{{ $qz->title }}</div>
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.quizzes.edit',$qz) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    {{-- pencil icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/></svg>
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.quizzes.destroy',$qz) }}"
                        onsubmit="return confirm('Delete this quiz?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                      {{-- trash icon --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="p-10">
                <div class="flex flex-col items-center justify-center text-center gap-3">
                  <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                    {{-- empty icon --}}
                    <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 3A2.75 2.75 0 0 0 4 5.75v12.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25V9.5a.75.75 0 0 0-.22-.53l-5.75-5.75A.75.75 0 0 0 13.5 3h-6.75Z"/></svg>
                  </div>
                  <div class="text-lg font-semibold">Belum ada quiz</div>
                  <p class="text-sm opacity-70 max-w-md">Tambahkan kuis untuk evaluasi pembelajaran di setiap lesson.</p>
                  <a href="{{ route('admin.quizzes.create') }}"
                     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
                    Create Quiz
                  </a>
                </div>
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
        <span class="font-semibold">{{ $quizzes->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $quizzes->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $quizzes->total() }}</span>
        results
      </div>
      <div>
        {{ $quizzes->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
