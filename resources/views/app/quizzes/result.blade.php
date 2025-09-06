@extends('app.layouts.base')
@section('title','Hasil Kuis')

@section('content')
  <h1 class="text-xl font-semibold">Hasil Kuis</h1>

  {{-- Ringkasan nilai --}}
  <div class="mt-3 grid gap-3 sm:grid-cols-3">
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Skor Attempt Ini</div>
      <div class="mt-1 text-xl font-semibold">{{ $attempt->score }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">% Benar Attempt Ini</div>
      <div class="mt-1 text-xl font-semibold">
        {{ isset($percent) ? number_format($percent,2) : '-' }}%
        @isset($correct)
          <span class="text-sm text-gray-500">({{ $correct }}/{{ $total }})</span>
        @endisset
      </div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">% Benar Terbaik (Semua Attempt)</div>
      <div class="mt-1 text-xl font-semibold">
        {{ isset($best_percent) ? number_format($best_percent,2) : (isset($percent) ? number_format($percent,2) : '-') }}%
        @isset($best_correct)
          <span class="text-sm text-gray-500">({{ $best_correct }}/{{ $best_total }})</span>
        @endisset
      </div>
    </div>
  </div>

  @php
    // pakai eligible_best kalau tersedia; kalau tidak, fallback ke eligible lama
    $canDownload = $eligible_best ?? $eligible ?? false;
  @endphp

  @if($canDownload)
    <div class="mt-4 p-4 rounded border bg-emerald-50 text-emerald-800">
      <div class="font-semibold">Selamat! Kamu memenuhi syarat sertifikat 🎉</div>
      <p class="text-sm mt-1">Minimal 80% benar telah tercapai.</p>
      <a href="{{ route('app.certificate.course', $course) }}"
         class="inline-block mt-3 px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
         Unduh Sertifikat
      </a>
    </div>
  @else
    <div class="mt-4 p-4 rounded border bg-rose-50 text-rose-800">
      <div class="font-semibold">Belum memenuhi syarat sertifikat</div>
      <p class="text-sm mt-1">Butuh minimal 80% jawaban benar dari soal pilihan ganda.</p>
      @isset($best_percent)
        <p class="text-xs mt-1 text-rose-700">Persentase terbaikmu saat ini: {{ number_format($best_percent,2) }}%.</p>
      @endisset
    </div>
  @endif

  {{-- Daftar jawaban --}}
  <div class="mt-4 space-y-4">
    @foreach($attempt->answers as $ans)
      <div class="p-3 bg-white border rounded">
        <div class="font-medium">
          {{ $loop->iteration }}. {{ $ans->question->prompt }}
        </div>
        <div class="mt-2 text-sm">
          @if($ans->question->type === 'mcq')
            <div>
              Jawabanmu:
              {{ optional($ans->question->options->firstWhere('id', $ans->option_id))->text ?? '—' }}
            </div>
          @else
            <div>Jawabanmu: {{ $ans->text_answer ?? '—' }}</div>
          @endif

          <span class="{{ $ans->is_correct ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $ans->question->type === 'mcq'
                ? ($ans->is_correct ? 'Benar' : 'Salah')
                : 'Perlu review (jawaban esai tidak dinilai otomatis)' }}
          </span>
        </div>
      </div>
    @endforeach
  </div>
@endsection
