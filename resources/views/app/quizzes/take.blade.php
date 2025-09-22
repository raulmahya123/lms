@extends('app.layouts.base')

@section('title','Kuis: '.$quiz->title)

@section('content')
<h1 class="text-xl font-semibold mb-4">{{ $quiz->title }}</h1>

{{-- 
  Asumsi: $attempt ada & $answers adalah array map [question_id => jawaban]
  Kalau belum ada $answers, cukup biarkan kosong: []
--}}
@php
  /** @var array<string,string> $answers */
  $answers = $answers ?? [];
@endphp

<form id="quizForm" method="POST" action="{{ route('app.quiz.submit', $quiz) }}" class="space-y-6">
  @csrf

  <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

  {{-- kirim daftar question_ids untuk validasi server-side (opsional, tapi bagus) --}}
  @foreach($quiz->questions as $q)
    <input type="hidden" name="question_ids[]" value="{{ $q->id }}">
  @endforeach

  @foreach($quiz->questions as $q)
    @php
      $fieldName = "answers.{$q->id}";
      $oldValue  = old("answers.{$q->id}", $answers[$q->id] ?? null);
    @endphp

    <div class="p-4 bg-white border rounded">
      <div class="font-medium">
        {{ $loop->iteration }}. {{ $q->prompt }}
        <span class="text-sm text-gray-500">({{ $q->points }} pts)</span>
      </div>

      <div class="mt-3 space-y-2">
        @if($q->type === 'mcq')
          @forelse($q->options as $opt)
            @php
              $id = "q{$q->id}_opt{$opt->id}";
            @endphp
            <div class="flex items-start gap-2">
              <input
                id="{{ $id }}"
                type="radio"
                name="answers[{{ $q->id }}]"
                value="{{ $opt->id }}"
                class="mt-1"
                {{-- HTML requires ‘required’ di semua radio/salah satu group untuk enforce minimal 1 --}}
                required
                @checked((string)$oldValue === (string)$opt->id)
              >
              <label for="{{ $id }}" class="select-none">
                {{ $opt->text }}
              </label>
            </div>
          @empty
            <p class="text-sm text-amber-600">Tidak ada opsi untuk soal ini.</p>
          @endforelse
        @else
          <textarea
            name="answers[{{ $q->id }}]"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
            rows="3"
            placeholder="Jawaban singkat/essay..."
            required
          >{{ old($fieldName, $oldValue) }}</textarea>
        @endif

        @error("answers.{$q->id}")
          <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
    </div>
  @endforeach

  <div class="flex items-center gap-3">
    <button
      type="submit"
      class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 disabled:opacity-60"
    >
      Kirim Jawaban
    </button>
    <span class="text-sm text-gray-500">Pastikan semua soal sudah terjawab.</span>
  </div>
</form>

{{-- Cegah double-submit --}}
<script>
  document.getElementById('quizForm')?.addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
  });
</script>
@endsection
