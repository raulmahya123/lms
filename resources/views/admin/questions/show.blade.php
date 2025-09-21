@extends('layouts.admin')

@section('title', 'Question Detail — BERKEMAH')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-blue-900">Question Detail</h1>
      <p class="text-sm text-blue-700/70">Detail pertanyaan untuk quiz ini.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.questions.index') }}"
         class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        ← Back
      </a>
      <a href="{{ route('admin.questions.edit', $question) }}"
         class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        Edit
      </a>
      <form action="{{ route('admin.questions.destroy', $question) }}" method="POST"
            onsubmit="return confirm('Yakin hapus question ini?')">
        @csrf @method('DELETE')
        <button type="submit"
                class="px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 transition">
          Delete
        </button>
      </form>
    </div>
  </div>

  {{-- Summary Card --}}
  <div class="rounded-2xl border border-blue-100 bg-white/90 shadow-lg backdrop-blur">
    <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-white rounded-t-2xl flex flex-wrap items-center gap-3">
      {{-- Quiz badge --}}
      <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-blue-100">
        <svg class="w-4 h-4 text-blue-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
        <span class="text-blue-900 font-medium">{{ $question->quiz->title ?? '— No Quiz —' }}</span>
      </span>

      {{-- Ordering --}}
      <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-amber-100">
        <svg class="w-4 h-4 text-amber-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a.75.75 0 0 1 .75.75V18a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Z"/></svg>
        <span class="text-amber-900">Ordering: {{ $question->ordering ?? 0 }}</span>
      </span>

      {{-- ID --}}
      <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white border border-gray-200">
        <svg class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="currentColor"><path d="M4.5 6A1.5 1.5 0 0 1 6 4.5h12A1.5 1.5 0 0 1 19.5 6v12A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V6Z"/></svg>
        <span class="text-gray-800">ID: <span class="font-mono">{{ Str::limit($question->id, 10, '…') }}</span></span>
      </span>
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
          <a href="{{ route('admin.options.create', ['question_id' => $question->id]) }}"
             class="text-sm px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            Add Option
          </a>
        </div>

        @php
          $opts = $question->relationLoaded('options') ? $question->options : $question->options()->get();
          $opts = $opts->sortBy('ordering')->values();
        @endphp

        @if($opts->isEmpty())
          <div class="rounded-xl border border-blue-100 bg-white p-4 text-sm text-blue-700/70">
            Belum ada opsi jawaban.
          </div>
        @else
          <div class="rounded-2xl border overflow-hidden">
            <table class="min-w-full text-sm">
              <thead class="bg-blue-50 text-blue-900">
                <tr>
                  <th class="p-3 text-left w-16">#</th>
                  <th class="p-3 text-left">Text</th>
                  <th class="p-3 text-center w-28">Correct</th>
                  <th class="p-3 text-right w-24">Score</th>
                  <th class="p-3 text-center w-40">Actions</th>
                </tr>
              </thead>
              <tbody class="[&>tr:hover]:bg-blue-50/40">
                @foreach($opts as $opt)
                  <tr class="border-t align-top">
                    <td class="p-3 font-semibold text-blue-900">{{ $opt->ordering ?? $loop->iteration }}</td>
                    <td class="p-3">
                      <div class="whitespace-pre-line">{{ $opt->text ?? $opt->label ?? '—' }}</div>
                    </td>
                    <td class="p-3 text-center">
                      @if($opt->is_correct ?? false)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800">
                          ✓ Correct
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                          — 
                        </span>
                      @endif
                    </td>
                    <td class="p-3 text-right tabular-nums">
                      {{ $opt->score ?? $opt->weight ?? '0' }}
                    </td>
                    <td class="p-3">
                      <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.options.edit', $opt) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
                          Edit
                        </a>
                        <form method="POST" action="{{ route('admin.options.destroy', $opt) }}"
                              onsubmit="return confirm('Hapus opsi ini?')" class="inline">
                          @csrf @method('DELETE')
                          <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition">
                            Delete
                          </button>
                        </form>
                      </div>
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
          <div class="text-xs text-blue-700/70 mb-1">Created</div>
          <div class="text-sm font-medium text-blue-900">{{ $question->created_at?->format('d M Y H:i') ?? '—' }}</div>
        </div>
        <div class="rounded-xl border border-blue-100 bg-white p-4">
          <div class="text-xs text-blue-700/70 mb-1">Updated</div>
          <div class="text-sm font-medium text-blue-900">{{ $question->updated_at?->format('d M Y H:i') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
