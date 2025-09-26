{{-- resources/views/admin/psy_questions/index.blade.php --}}
@extends('layouts.admin')
@section('title','Psych Questions' . ($currentTest ? ' — '.$currentTest->name : ''))

@section('content')
@php
  $testId = request('psy_test_id');
  $q      = request('q');
  $qtype  = request('qtype');
  $trait  = request('trait');

  $__tests  = $tests ?? \App\Models\PsyTest::select('id','name')->orderBy('name')->get();
  $__types  = \App\Models\PsyQuestion::query()->select('qtype')->distinct()->pluck('qtype')->filter()->values();
  $__traits = \App\Models\PsyQuestion::query()->select('trait_key')->distinct()->pluck('trait_key')->filter()->values();
@endphp

<div x-data="{
      q:@js($q ?? ''),
      showFilters: {{ request()->hasAny(['q','psy_test_id','qtype','trait'])?'true':'false' }}
    }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2h12v20l-6-3-6 3V2Z"/></svg>
        Questions
        @if($currentTest) • {{ $currentTest->name }} @endif
      </h1>
      <p class="text-sm opacity-70">
        Kelola pertanyaan & opsi {{ $currentTest ? 'untuk test ini' : 'untuk semua test' }}.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.psy-tests.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 6.75a.75.75 0 0 1 0 1.5H8.56l2.97 2.97a.75.75 0 1 1-1.06 1.06L7.5 9.31v4.94a.75.75 0 0 1-1.5 0V8.25c0-.41.34-.75.75-.75h6.75Z"/></svg>
        Back to Tests
      </a>

      <a href="{{ route('admin.psy-questions.create', ['psy_test_id'=>$currentTest?->id]) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        Add Question
      </a>

      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTERS PANEL --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search prompt</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" placeholder="Cari teks pertanyaan…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Test</label>
      <div class="relative">
        <select name="psy_test_id" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="">— Semua Test —</option>
          @foreach($__tests as $t)
            <option value="{{ $t->id }}" @selected($testId == $t->id)>{{ $t->name }}</option>
          @endforeach
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3.75 5.25A2.25 2.25 0 0 1 6 3h4.5A2.25 2.25 0 0 1 12.75 5.25v13.5A2.25 2.25 0 0 0 10.5 16.5H6A2.25 2.25 0 0 0 3.75 18.75V5.25Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Type</label>
      <div class="relative">
        <select name="qtype" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="">All</option>
          @foreach($__types as $t)
            <option value="{{ $t }}" @selected($qtype===$t)>{{ strtoupper($t) }}</option>
          @endforeach
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M7 7.5h10a4.5 4.5 0 1 1 0 9H7a4.5 4.5 0 1 1 0-9Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Trait</label>
      <div class="relative">
        <select name="trait" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="">All</option>
          @foreach($__traits as $t)
            <option value="{{ $t }}" @selected($trait===$t)>{{ $t }}</option>
          @endforeach
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3.75 10.5 10.5 3.75l9.75 9.75-6.75 6.75H3.75V10.5Z"/>
        </svg>
      </div>
    </div>

    <div class="md:col-span-4 flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Apply
      </button>

      @if(request()->hasAny(['q','psy_test_id','qtype','trait']))
        <a href="{{ route('admin.psy-questions.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a6.75 6.75 0 1 0 6.53 8.4.75.75 0 1 1 1.46.3 8.25 8.25 0 1 1-1.92-7.17V5.25a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0 1.5h1.86A6.73 6.73 0 0 0 12 5.25Z"/></svg>
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
      <div class="text-sm">
        <span class="font-semibold">{{ $questions->total() }}</span>
        <span class="opacity-70">questions</span>

        @if($testId)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Test filter active
          </span>
        @endif

        @if($q)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
            “{{ $q }}”
          </span>
        @endif

        @if($qtype)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-violet-50 text-violet-700 border border-violet-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Type: {{ strtoupper($qtype) }}
          </span>
        @endif

        @if($trait)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 12a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9A.75.75 0 0 1 6.75 12Z"/></svg>
            Trait: {{ $trait }}
          </span>
        @endif
      </div>
      <div class="text-xs opacity-70">
        Page {{ $questions->currentPage() }} / {{ $questions->lastPage() }}
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-16">#</th>
            @if(!$currentTest)
              <th class="p-3 text-left w-56">Test</th>
            @endif
            <th class="p-3 text-left">Prompt</th>
            <th class="p-3 text-left w-28">Trait</th>
            <th class="p-3 text-left w-20">Type</th>
            <th class="p-3 text-left w-24">Options</th>
            <th class="p-3 text-center w-48">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($questions as $item)
            <tr class="border-t align-top">
              <td class="p-3 font-semibold text-gray-700">{{ $item->ordering }}</td>

              @if(!$currentTest)
                <td class="p-3">
                  <div class="truncate max-w-[320px]" title="{{ $item->test->name ?? '' }}">
                    {{ $item->test->name ?? '—' }}
                  </div>
                </td>
              @endif

              <td class="p-3">
                <div class="line-clamp-2" title="{{ $item->prompt }}">{{ $item->prompt }}</div>
              </td>

              <td class="p-3">{{ $item->trait_key ?? '—' }}</td>

              <td class="p-3 uppercase">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-violet-100 text-violet-800">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 12a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9A.75.75 0 0 1 6.75 12Z"/></svg>
                  {{ $item->qtype }}
                </span>
              </td>

              <td class="p-3">
                <div class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                  <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.75 2.75 7.5 12 12.25 21.25 7.5 12 2.75Zm0 9.5L2.75 17l9.25 4.75L21.25 17 12 12.25Z"/></svg>
                  <span class="tabular-nums">{{ $item->options->count() }}</span>
                </div>
              </td>

              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  {{-- View --}}
                  @if(isset($currentTest))
                    <a href="{{ route('admin.psy-tests.questions.show', [$currentTest, $item]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="View">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 6.75c-5.25 0-8.25 5.25-8.25 5.25S6.75 17.25 12 17.25 20.25 12 20.25 12 17.25 6.75 12 6.75Zm0 7.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z"/></svg>
                      View
                    </a>
                  @else
                    <a href="{{ route('admin.psy-questions.show', $item) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="View">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 6.75c-5.25 0-8.25 5.25-8.25 5.25S6.75 17.25 12 17.25 20.25 12 20.25 12 17.25 6.75 12 6.75Zm0 7.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z"/></svg>
                      View
                    </a>
                  @endif

                  {{-- Edit (flat) --}}
                  <a href="{{ route('admin.psy-questions.edit', $item) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/></svg>
                    Edit
                  </a>

                  {{-- Delete --}}
                  @if(isset($currentTest))
                    <form method="POST" action="{{ route('admin.psy-tests.questions.destroy', [$currentTest, $item]) }}"
                          onsubmit="return confirm('Delete question?')" class="inline">
                      @csrf @method('DELETE')
                      <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                        Delete
                      </button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.psy-questions.destroy', $item) }}"
                          onsubmit="return confirm('Delete question?')" class="inline">
                      @csrf @method('DELETE')
                      <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                        Delete
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ $currentTest ? 6 : 7 }}" class="p-10 text-center text-sm opacity-70">
                Belum ada pertanyaan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing <span class="font-semibold">{{ $questions->firstItem() ?? 0 }}</span>
        to <span class="font-semibold">{{ $questions->lastItem() ?? 0 }}</span>
        of <span class="font-semibold">{{ $questions->total() }}</span> results
      </div>
      <div>{{ $questions->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
