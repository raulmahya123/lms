@extends('layouts.admin')

@section('title','Options — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q','quiz_id','is_correct']) ? 'true' : 'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- list icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
        Options
      </h1>
      <p class="text-sm opacity-70">Kelola opsi jawaban untuk setiap question.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.options.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        Add Option
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter icon --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FLASH MESSAGE --}}
  @if(session('ok'))
    <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('ok') }}
    </div>
  @endif

  {{-- FILTER FORM --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    {{-- Quiz --}}
    <div>
      <label class="block text-sm font-medium mb-1">Quiz</label>
      <div class="relative">
        <select name="quiz_id" class="w-full border rounded-xl pl-3 pr-8 py-2">
          <option value="">— All Quizzes —</option>
          @php
            $quizList = $quizzes ?? \App\Models\Quiz::orderBy('title')->get(['id','title']);
          @endphp
          @foreach($quizList as $quiz)
            <option value="{{ $quiz->id }}" @selected(request('quiz_id')==$quiz->id)>{{ $quiz->title }}</option>
          @endforeach
        </select>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Search text --}}
    <div>
      <label class="block text-sm font-medium mb-1">Search text</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" value="{{ request('q') }}" placeholder="Cari isi opsi…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
        </svg>
      </div>
    </div>

    {{-- Correct? --}}
    @php $ic = request('is_correct'); @endphp
    <div>
      <label class="block text-sm font-medium mb-1">Correct?</label>
      <div class="relative">
        <select name="is_correct" class="w-full border rounded-xl pl-3 pr-8 py-2">
          <option value="" @selected($ic === null || $ic === '')>All</option>
          <option value="1" @selected($ic==='1')>True</option>
          <option value="0" @selected($ic==='0')>False</option>
        </select>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60 pointer-events-none" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Actions --}}
    <div class="md:col-span-3 flex items-center gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        Apply
      </button>
      @if(request()->hasAny(['q','quiz_id','is_correct']))
        <a href="{{ route('admin.options.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
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
        <span class="font-semibold">{{ $options->total() }}</span>
        <span class="opacity-70">options found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $options->currentPage() }} / {{ $options->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left">Question</th>
            <th class="p-3 text-left">Text</th>
            <th class="p-3 text-left w-28">Correct</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse ($options as $opt)
            <tr class="border-t">
              <td class="p-3">
                {{ \Illuminate\Support\Str::limit($opt->question->prompt ?? '—', 60) }}
              </td>
              <td class="p-3">
                {{ \Illuminate\Support\Str::limit($opt->text, 80) }}
              </td>
              <td class="p-3">
                @if($opt->is_correct)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 0 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 0 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z"/></svg>
                    True
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-700">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.59l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.65l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.715-4.715-4.715-4.715a.75.75 0 0 1 0-1.06Z"/></svg>
                    False
                  </span>
                @endif
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  @if(Route::has('admin.options.show'))
                    <a href="{{ route('admin.options.show',$opt) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                       title="View option">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5c-7 0-10 7.5-10 7.5s3 7.5 10 7.5 10-7.5 10-7.5-3-7.5-10-7.5Zm0 12a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/></svg>
                      View
                    </a>
                  @endif
                  <a href="{{ route('admin.options.edit',$opt) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                     title="Edit option">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/></svg>
                    Edit
                  </a>
                  <form action="{{ route('admin.options.destroy',$opt) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus opsi ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition"
                            title="Delete option">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="p-10">
                <div class="flex flex-col items-center justify-center gap-3 text-center">
                  <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                    {{-- empty icon --}}
                    <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 3A2.75 2.75 0 0 0 4 5.75v12.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25V9.5a.75.75 0 0 0-.22-.53l-5.75-5.75A.75.75 0 0 0 13.5 3h-6.75Z"/></svg>
                  </div>
                  <div class="text-lg font-semibold">Belum ada option</div>
                  <p class="text-sm opacity-70">Tambahkan opsi jawaban pertama untuk question.</p>
                  <a href="{{ route('admin.options.create') }}"
                     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
                    Add Option
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
        <span class="font-semibold">{{ $options->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $options->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $options->total() }}</span>
        results
      </div>
      <div>
        {{ $options->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
