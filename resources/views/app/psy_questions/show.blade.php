@extends('app.layouts.base')

@section('title', 'Soal — '.$test->name)

@section('content')
@php
  // hitung progress sederhana
  $ids = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
  $pos = array_search($question->id, $ids, true);
  $current = $pos === false ? 1 : ($pos + 1);
  $total = count($ids);
  $pct = $total ? intval($current / $total * 100) : 0;
@endphp

<div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
  <div class="flex items-center justify-between">
    <a href="{{ route('app.psytests.show', $test->slug ?: $test->id) }}" class="text-blue-600">← {{ $test->name }}</a>
    <div class="text-sm text-gray-600">Soal {{ $current }} / {{ $total }}</div>
  </div>

  <div>
    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
      <div class="h-full bg-blue-600" style="width: {{ $pct }}%"></div>
    </div>
    <div class="text-xs text-gray-500 mt-1">{{ $pct }}% selesai</div>
  </div>

  <div class="bg-white border rounded-xl p-5">
    <h2 class="text-xl font-semibold">{{ $question->text }}</h2>

    <form method="POST" action="{{ route('app.psy.attempts.answer', [$test, $question]) }}" class="mt-5 space-y-4">
      @csrf

      @if($question->options->count())
        {{-- MCQ / Likert dengan opsi --}}
        <div class="space-y-3">
          @foreach($question->options as $op)
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="radio" name="option_id" value="{{ $op->id }}" class="h-4 w-4" required>
              <span>{{ $op->label }}</span>
            </label>
          @endforeach
        </div>
      @else
        {{-- Likert tanpa opsi (nilai integer) --}}
        <div>
          <label class="block text-sm font-medium mb-1">Nilai (integer)</label>
          <input type="number" name="value" class="border rounded-lg px-3 py-2 w-40" required>
          @error('value') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      @endif

      <div class="flex items-center justify-between pt-4">
        <div>
          @if($prevId)
            <a class="px-3 py-2 border rounded-lg"
               href="{{ route('app.psytests.questions.show', [$test->slug ?: $test->id, $prevId]) }}">← Sebelumnya</a>
          @endif
        </div>

        <div class="flex gap-3">
          <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            {{ $nextId ? 'Simpan & Lanjut' : 'Simpan' }}
          </button>
        </div>
      </div>
    </form>

    @if(!$nextId)
      <div class="mt-6 border-t pt-4">
        <a href="{{ route('app.psy.attempts.submit', $test) }}"
           class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg">
          Selesai & Hitung Hasil
        </a>
      </div>
    @endif
  </div>

  <div class="flex justify-between text-sm">
    @if($prevId)
      <a class="text-gray-600"
         href="{{ route('app.psytests.questions.show', [$test->slug ?: $test->id, $prevId]) }}">← Soal sebelumnya</a>
    @else <span></span>
    @endif

    @if($nextId)
      <a class="text-gray-600"
         href="{{ route('app.psytests.questions.show', [$test->slug ?: $test->id, $nextId]) }}">Soal berikutnya →</a>
    @endif
  </div>
</div>
@endsection
