@extends('app.layouts.base')
@section('title','Hasil Kuis')

@section('content')
  <h1 class="text-xl font-semibold">Hasil Kuis</h1>
  <p class="mt-2">Skor: <span class="font-bold">{{ $attempt->score }}</span></p>

  @if($eligible)
    <div class="mt-4 p-4 rounded border bg-emerald-50 text-emerald-800">
      <div class="font-semibold">Selamat! Kamu memenuhi syarat sertifikat 🎉</div>
      <p class="text-sm mt-1">Skor minimal 80 tercapai.</p>
      <a href="{{ route('app.certificate.course', $course) }}"
         class="inline-block mt-3 px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
         Unduh Sertifikat
      </a>
    </div>
  @else
    <div class="mt-4 p-4 rounded border bg-rose-50 text-rose-800">
      <div class="font-semibold">Belum memenuhi syarat sertifikat</div>
      <p class="text-sm mt-1">Butuh skor minimal 80 untuk mendapatkan sertifikat.</p>
    </div>
  @endif

  <div class="mt-4 space-y-4">
    @foreach($attempt->answers as $ans)
      <div class="p-3 bg-white border rounded">
        <div class="font-medium">{{ $loop->iteration }}. {{ $ans->question->prompt }}</div>
        <div class="mt-2 text-sm">
          @if($ans->question->type === 'mcq')
            Jawabanmu:
            {{ optional($ans->question->options->firstWhere('id', $ans->option_id))->text ?? '—' }}<br>
          @else
            Jawabanmu: {{ $ans->text_answer ?? '—' }}<br>
          @endif
          <span class="{{ $ans->is_correct ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $ans->is_correct ? 'Benar' : 'Salah/Perlu review' }}
          </span>
        </div>
      </div>
    @endforeach
  </div>
@endsection
