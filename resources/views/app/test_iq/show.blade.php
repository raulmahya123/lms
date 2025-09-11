@extends('app.layouts.base')

@section('title', $test->title)

@section('content')
<div class="max-w-3xl mx-auto py-8">
  <h1 class="text-2xl font-bold mb-2">{{ $test->title }}</h1>
  <p class="text-gray-600 mb-6">{{ $test->description }}</p>

  @if(session('status'))
  <div class="mb-4 p-3 bg-green-50 text-green-800 rounded">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('user.test-iq.submit', $test) }}"
    x-data="{ start: Date.now(), dur: 0 }"
    x-on:submit="dur = Math.round((Date.now() - start)/1000)">
    @csrf
    <input type="hidden" name="duration_sec" :value="dur">

    @foreach($test->questions ?? [] as $q)
    <div class="mb-6">
      <div class="font-medium mb-2">{{ $loop->iteration }}. {{ $q['text'] ?? '' }}</div>
      <div class="grid gap-2">
        @foreach(($q['options'] ?? []) as $opt)
        <label class="flex items-center gap-2">
          <input type="radio"
            name="answers[{{ $loop->parent->index }}]"
            value="{{ $opt }}"
            class="accent-indigo-600">
          <span>{{ $opt }}</span>
        </label>
        @endforeach
      </div>
    </div>
    @endforeach
    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white">Kirim Jawaban</button>
  </form>
</div>
@endsection