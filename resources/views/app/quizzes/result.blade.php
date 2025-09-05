@extends('app.layouts.base')
@section('title','Hasil Kuis')
@section('content')
<h1 class="text-xl font-semibold">Hasil Kuis</h1>
<p class="mt-2">Skor: <span class="font-bold">{{ $attempt->score }}</span></p>
<div class="mt-4 space-y-4">
  @foreach($attempt->answers as $ans)
    <div class="p-3 bg-white border rounded">
      <div class="font-medium">{{ $loop->iteration }}. {{ $ans->question->prompt }}</div>
      <div class="mt-2 text-sm">
        @if($ans->question->type==='mcq')
          Jawabanmu: {{ optional($ans->question->options->firstWhere('id',$ans->option_id))->text ?? '—' }}<br>
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
@extends('app.layouts.base')
@section('title','Hasil Kuis')
@section('content')
<h1 class="text-xl font-semibold">Hasil Kuis</h1>
<p class="mt-2">Skor: <span class="font-bold">{{ $attempt->score }}</span></p>
<div class="mt-4 space-y-4">
  @foreach($attempt->answers as $ans)
    <div class="p-3 bg-white border rounded">
      <div class="font-medium">{{ $loop->iteration }}. {{ $ans->question->prompt }}</div>
      <div class="mt-2 text-sm">
        @if($ans->question->type==='mcq')
          Jawabanmu: {{ optional($ans->question->options->firstWhere('id',$ans->option_id))->text ?? '—' }}<br>
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
