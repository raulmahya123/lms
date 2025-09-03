@extends('layouts.admin')

@section('title','Quizzes · Questions — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q','quiz_id']) ? 'true' : 'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- list icon --}}
        <svg class="w-7 h-7 opacity-80" viewBox="0 0 24 24" fill="currentColor">
          <path d="M8.25 4.5h10.125M8.25 9h10.125M8.25 13.5h10.125M8.25 18h10.125M3.375 4.5h.008v.008h-.008V4.5zM3.375 9h.008v.008h-.008V9zM3.375 13.5h.008v.008h-.008v-.008zM3.375 18h.008v.008h-.008V18z" />
        </svg>
        Quizzes · Questions
      </h1>
      <p class="text-sm opacity-70">Kelola pertanyaan per quiz. Filter cepat, cari prompt, dan aksi edit/hapus.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.questions.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-700 text-white hover:bg-blue-600 shadow">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c.552 0 1 .448 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H6a1 1 0 1 1 0-2h5V6c0-.552.448-1 1-1Z"/></svg>
        New Question
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5.25A.75.75 0 0 1 3.75 4.5h16.5a.75.75 0 0 1 .6 1.2L15 12.302v5.823a.75.75 0 0 1-1.08.67l-3-1.5A.75.75 0 0 1 10.5 17v-4.698L3.15 5.7a.75.75 0 0 1-.15-.45Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTER FORM --}}
  <form id="q-filters" method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
    {{-- Quiz --}}
    <div>
      <label class="text-sm font-medium mb-1 block">Quiz</label>
      <select name="quiz_id" class="w-full rounded-xl border pl-3 pr-3 py-2">
        <option value="">— All Quizzes —</option>
        @foreach($quizzes ?? [] as $quiz)
          <option value="{{ $quiz->id }}" @selected(request('quiz_id')==$quiz->id)>{{ $quiz->title }}</option>
        @endforeach
      </select>
    </div>

    {{-- Search Prompt --}}
    <div>
      <label class="text-sm font-medium mb-1 block">Search prompt</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" value="{{ request('q') }}"
               placeholder="Cari teks prompt…"
               class="w-full rounded-xl border pl-10 pr-3 py-2">
        <svg class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3.75a6.25 6.25 0 1 1 3.938 11.171l3.07 3.07a.75.75 0 1 1-1.06 1.06l-3.07-3.07A6.25 6.25 0 0 1 10 3.75Zm-4.75 6.25a4.75 4.75 0 1 0 9.5 0 4.75 4.75 0 0 0-9.5 0Z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800">
        Apply
      </button>
      @if(request()->hasAny(['q','quiz_id']))
        <a href="{{ route('admin.questions.index') }}"
           class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- FLASH --}}
  @if(session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
      {{ session('ok') }}
    </div>
  @endif

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    {{-- header strip --}}
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $questions->total() }}</span>
        <span class="opacity-70">questions found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $questions->currentPage() }} / {{ $questions->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="px-4 py-3 text-left w-16">#</th>
            <th class="px-4 py-3 text-left">Quiz</th>
            <th class="px-4 py-3 text-left">Prompt</th>
            <th class="px-4 py-3 text-left w-28">Type</th>
            <th class="px-4 py-3 text-left w-24">Points</th>
            <th class="px-4 py-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse ($questions as $item)
            <tr class="border-t">
              <td class="px-4 py-3 font-semibold text-gray-700">#{{ $item->id }}</td>
              <td class="px-4 py-3">{{ $item->quiz->title ?? '—' }}</td>
              <td class="px-4 py-3">
                {{ \Illuminate\Support\Str::limit($item->prompt, 80) }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                  {{ strtoupper($item->type) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-800">
                  {{ $item->points }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  @if(Route::has('admin.questions.show'))
                    <a href="{{ route('admin.questions.show',$item) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50" title="View">
                      {{-- eye icon --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/></svg>
                      View
                    </a>
                  @endif
                  <a href="{{ route('admin.questions.edit',$item) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50" title="Edit">
                    Edit
                  </a>
                  <form action="{{ route('admin.questions.destroy',$item) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus pertanyaan ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50" title="Delete">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-sm opacity-70">
                Belum ada pertanyaan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
      <div class="opacity-70">
        Showing
        <span class="font-semibold">{{ $questions->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $questions->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $questions->total() }}</span>
        results
      </div>
      <div>
        {{ $questions->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
