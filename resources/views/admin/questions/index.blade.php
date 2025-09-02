@extends('layouts.admin')

@section('title','Questions')

@section('content')
{{-- HEADER --}}
<div class="flex items-start justify-between mb-4">
  <div>
    <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
      {{-- icon --}}
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M8.25 4.5h10.125M8.25 9h10.125M8.25 13.5h10.125M8.25 18h10.125M3.375 4.5h.008v.008h-.008V4.5zM3.375 9h.008v.008h-.008V9zM3.375 13.5h.008v.008h-.008v-.008zM3.375 18h.008v.008h-.008V18z" />
      </svg>
      Quizzes · Questions
    </h1>
    <p class="text-sm opacity-70 mt-1">Kelola pertanyaan per quiz. Filter cepat, cari prompt, dan aksi edit/hapus.</p>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.questions.create') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-700 text-white hover:bg-blue-600 shadow">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c.552 0 1 .448 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H6a1 1 0 1 1 0-2h5V6c0-.552.448-1 1-1Z"/></svg>
      New Question
    </a>
    <button type="button"
            onclick="document.getElementById('q-filters').scrollIntoView({behavior:'smooth'})"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-50">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5.25A.75.75 0 0 1 3.75 4.5h16.5a.75.75 0 0 1 .6 1.2L15 12.302v5.823a.75.75 0 0 1-1.08.67l-3-1.5A.75.75 0 0 1 10.5 17v-4.698L3.15 5.7a.75.75 0 0 1-.15-.45Z"/></svg>
      Filters
    </button>
  </div>
</div>

{{-- FILTERS CARD --}}
<form id="q-filters" method="GET" class="rounded-2xl border border-gray-200 bg-white p-4 md:p-5 mb-5">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 items-end">
    {{-- Quiz --}}
    <div>
      <label class="text-xs font-semibold opacity-70 block mb-1">Quiz</label>
      <select name="quiz_id" class="w-full rounded-xl border-gray-300 focus:ring-0">
        <option value="">— All Quizzes —</option>
        @foreach($quizzes ?? [] as $quiz)
          <option value="{{ $quiz->id }}" @selected(request('quiz_id')==$quiz->id)>{{ $quiz->title }}</option>
        @endforeach
      </select>
    </div>

    {{-- Search Prompt --}}
    <div class="md:col-span-1">
      <label class="text-xs font-semibold opacity-70 block mb-1">Search prompt</label>
      <div class="relative">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Cari teks prompt…"
               class="w-full rounded-xl border-gray-300 pl-9 focus:ring-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3.75a6.25 6.25 0 1 1 3.938 11.171l3.07 3.07a.75.75 0 1 1-1.06 1.06l-3.07-3.07A6.25 6.25 0 0 1 10 3.75Zm-4.75 6.25a4.75 4.75 0 1 0 9.5 0 4.75 4.75 0 0 0-9.5 0Z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0f172a] text-white hover:opacity-90">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 5.75a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 .53 1.28l-5.47 5.47V18a.75.75 0 0 1-1.2.6l-3-2.25a.75.75 0 0 1-.3-.6v-3.75L4.22 6.28A.75.75 0 0 1 3.75 5.75Z"/></svg>
        Apply
      </button>
      @if(request()->hasAny(['q','quiz_id']))
        <a href="{{ route('admin.questions.index') }}"
           class="px-4 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-50">
          Reset
        </a>
      @endif
    </div>
  </div>
</form>

{{-- FLASH --}}
@if(session('ok'))
  <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl mb-4 text-sm">
    {{ session('ok') }}
  </div>
@endif

{{-- TABLE CARD --}}
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-700">
        <tr>
          <th class="px-4 py-3 text-left w-16">#</th>
          <th class="px-4 py-3 text-left">Quiz</th>
          <th class="px-4 py-3 text-left">Prompt</th>
          <th class="px-4 py-3 text-left">Type</th>
          <th class="px-4 py-3 text-left">Points</th>
          <th class="px-4 py-3 text-left w-40">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($questions as $q)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3">#{{ $q->id }}</td>
            <td class="px-4 py-3">{{ $q->quiz->title ?? '—' }}</td>
            <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($q->prompt, 64) }}</td>
            <td class="px-4 py-3 uppercase text-xs font-semibold text-gray-700">{{ $q->type }}</td>
            <td class="px-4 py-3">{{ $q->points }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.questions.edit',$q) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M16.98 3.978a2.5 2.5 0 0 1 3.535 3.535L9.56 18.47a3 3 0 0 1-1.237.742l-3.33 1.041a.75.75 0 0 1-.94-.94l1.04-3.33a3 3 0 0 1 .742-1.237L16.98 3.978Z"/></svg>
                  Edit
                </a>
                <form action="{{ route('admin.questions.destroy',$q) }}" method="POST"
                      onsubmit="return confirm('Yakin hapus pertanyaan ini?')">
                  @csrf @method('DELETE')
                  <button type="submit"
                          class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 4.5a.75.75 0 0 0-.75.75V6H6a.75.75 0 0 0 0 1.5h.75v10.125A2.625 2.625 0 0 0 9.375 20.25h5.25A2.625 2.625 0 0 0 17.25 17.625V7.5H18a.75.75 0 0 0 0-1.5h-3V5.25a.75.75 0 0 0-.75-.75h-4.5Z"/></svg>
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
              Belum ada pertanyaan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- FOOTER TABLE --}}
  <div class="flex items-center justify-between px-4 py-3 bg-gray-50 text-xs text-gray-600">
    <div>
      Showing
      <span class="font-semibold">{{ $questions->firstItem() ?: 0 }}</span>
      to
      <span class="font-semibold">{{ $questions->lastItem() ?: 0 }}</span>
      of
      <span class="font-semibold">{{ $questions->total() }}</span>
      results
    </div>
    <div class="text-sm">
      {{ $questions->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection
