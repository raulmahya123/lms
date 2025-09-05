@extends('app.layouts.base')
@section('title','Kuis: '.$quiz->title)
@section('content')
<h1 class="text-xl font-semibold mb-4">{{ $quiz->title }}</h1>

<form method="POST" action="{{ route('app.quiz.submit',$quiz) }}" class="space-y-6">
  @csrf
  <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

  @foreach($quiz->questions as $q)
    <div class="p-4 bg-white border rounded">
      <div class="font-medium">{{ $loop->iteration }}. {{ $q->prompt }} ({{ $q->points }} pts)</div>
      <div class="mt-3">
        @if($q->type==='mcq')
          @foreach($q->options as $opt)
            <label class="block">
              <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}" class="mr-2"> {{ $opt->text }}
            </label>
          @endforeach
        @else
          <textarea name="answers[{{ $q->id }}]" class="w-full border rounded px-3 py-2" rows="3" placeholder="Jawaban singkat/essay..."></textarea>
        @endif
      </div>
    </div>
  @endforeach

  <button class="px-4 py-2 bg-emerald-600 text-white rounded">Kirim Jawaban</button>
</form>
@endsection
