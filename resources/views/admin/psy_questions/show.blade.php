{{-- resources/views/admin/psy_questions/show.blade.php --}}
@extends('layouts.admin')
@section('title','Question Detail — BERKEMAH')

@section('content')
@php
  /** @var \App\Models\PsyQuestion $question */
@endphp

<div class="max-w-4xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-blue-900">Question Detail</h1>
      <p class="text-sm text-blue-700/70">
        Lihat detail pertanyaan & opsi jawaban.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.psy-questions.index', request()->only(['psy_test_id','q','qtype','trait','page'])) }}"
         class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        ← Back
      </a>
      <a href="{{ route('admin.questions.edit', $question) }}"
         class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        Edit
      </a>
      <form action="{{ route('admin.questions.destroy', $question) }}" method="POST"
            onsubmit="return confirm('Delete this question?')">
        @csrf @method('DELETE')
        <button class="px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 transition">
          Delete
        </button>
      </form>
    </div>
  </div>

  {{-- Summary Card --}}
  <div class="rounded-2xl border border-blue-100 bg-white/90 shadow-lg backdrop-blur">
    <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-white rounded-t-2xl flex flex-wrap items-center gap-3">
      <div class="text-sm">
        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-blue-100">
          {{-- test badge --}}
          <svg class="w-4 h-4 text-blue-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
          <span class="text-blue-900">{{ $question->test->name ?? '— No Test —' }}</span>
        </span>
      </div>

      <div class="text-sm">
        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-violet-100">
          {{-- type badge --}}
          <svg class="w-4 h-4 text-violet-700" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 12a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9A.75.75 0 0 1 6.75 12Z"/></svg>
          <span class="text-violet-900 uppercase">{{ $question->qtype ?? '—' }}</span>
        </span>
      </div>

      <div class="text-sm">
        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-emerald-100">
          {{-- trait badge --}}
          <svg class="w-4 h-4 text-emerald-700" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 10.5 10.5 3.75l9.75 9.75-6.75 6.75H3.75V10.5Z"/></svg>
          <span class="text-emerald-900">Trait: {{ $question->trait_key ?? '—' }}</span>
        </span>
      </div>

      <div class="text-sm">
        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-amber-100">
          {{-- ordering --}}
          <svg class="w-4 h-4 text-amber-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a.75.75 0 0 1 .75.75V18a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Z"/></svg>
          <span class="text-amber-900">Ordering: {{ $question->ordering ?? 0 }}</span>
        </span>
      </div>
    </div>

    <div class="p-6 space-y-6">
      {{-- Prompt --}}
      <div>
        <h2 class="text-sm font-semibold text-blue-900 mb-1">Prompt</h2>
        <div class="rounded-xl border border-blue-100 bg-white p-4 leading-relaxed">
          {{ $question->prompt }}
        </div>
      </div>

      {{-- Options --}}
      <div>
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-sm font-semibold text-blue-900">Options</h2>
          {{-- (opsional) tombol tambah opsi kalau ada routenya --}}
          {{-- <a href="{{ route('admin.psy-options.create', ['question_id'=>$question->id]) }}"
             class="text-sm px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Add Option</a> --}}
        </div>

        @php
          $options = $question->relationLoaded('options') ? $question->options : $question->options()->get();
          // kalau punya kolom 'ordering', urutkan
          $options = $options->sortBy('ordering')->values();
        @endphp

        @if($options->isEmpty())
          <div class="rounded-xl border border-blue-100 bg-white p-4 text-sm text-blue-700/70">
            Belum ada opsi jawaban.
          </div>
        @else
          <div class="rounded-2xl border overflow-hidden">
            <table class="min-w-full text-sm">
              <thead class="bg-blue-50 text-blue-900">
                <tr>
                  <th class="p-3 text-left w-16">#</th>
                  <th class="p-3 text-left">Label / Text</th>
                  <th class="p-3 text-left w-28">Value</th>
                  <th class="p-3 text-left w-28">Score</th>
                </tr>
              </thead>
              <tbody class="[&>tr:hover]:bg-blue-50/40">
                @foreach($options as $opt)
                  <tr class="border-t">
                    <td class="p-3 font-semibold text-blue-900">{{ $opt->ordering ?? $loop->iteration }}</td>
                    <td class="p-3">
                      {{ $opt->label ?? $opt->text ?? $opt->value ?? '—' }}
                    </td>
                    <td class="p-3 tabular-nums">{{ $opt->value ?? '—' }}</td>
                    <td class="p-3 tabular-nums">
                      {{ $opt->score ?? $opt->weight ?? '—' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Meta --}}
      <div class="grid sm:grid-cols-2 gap-4">
        <div class="rounded-xl border border-blue-100 bg-white p-4">
          <div class="text-xs text-blue-700/70 mb-1">Question ID (UUID)</div>
          <div class="text-sm font-medium text-blue-900 break-all">{{ $question->id }}</div>
        </div>
        <div class="rounded-xl border border-blue-100 bg-white p-4">
          <div class="text-xs text-blue-700/70 mb-1">Created / Updated</div>
          <div class="text-sm font-medium text-blue-900">
            {{ $question->created_at?->format('d M Y H:i') ?? '—' }}
            <span class="opacity-60">·</span>
            {{ $question->updated_at?->format('d M Y H:i') ?? '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
