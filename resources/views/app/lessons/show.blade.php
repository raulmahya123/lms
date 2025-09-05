@extends('app.layouts.base')
@section('title', $lesson->title)
@section('content')
<div class="flex items-start gap-6">
  <div class="flex-1">
    <h1 class="text-2xl font-semibold">{{ $lesson->title }}</h1>
    <div class="prose max-w-none mt-4">{!! $lesson->content !!}</div>

    @if($lesson->resources->count())
      <h2 class="mt-6 font-semibold">Resources</h2>
      <ul class="list-disc pl-5 mt-2">
        @foreach($lesson->resources as $r)
          <li><a class="text-blue-700 hover:underline" href="{{ route('app.resources.show',$r) }}">{{ $r->title }}</a></li>
        @endforeach
      </ul>
    @endif

    @if($lesson->quiz)
      <form method="POST" action="{{ route('app.quiz.start',$lesson) }}" class="mt-6">
        @csrf
        <button class="px-4 py-2 bg-purple-600 text-white rounded">Mulai Kuis</button>
      </form>
    @endif

    <div class="mt-8 flex items-center gap-2">
      @if($prev)<a class="px-3 py-2 border rounded" href="{{ route('app.lessons.show',$prev) }}">← Sebelumnya</a>@endif
      @if($next)<a class="px-3 py-2 border rounded" href="{{ route('app.lessons.show',$next) }}">Berikutnya →</a>@endif
    </div>
  </div>

  <div class="w-72">
    <div class="p-4 bg-white border rounded">
      <div class="font-semibold mb-2">Progress</div>
      <form method="POST" action="{{ route('app.lessons.progress',$lesson) }}">
        @csrf
        <input type="hidden" name="progress[watched]" value="1">
        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="completed" value="1" @checked($progress && $progress->completed_at)>
          Tandai selesai
        </label>
        <button class="mt-3 w-full px-3 py-2 bg-gray-900 text-white rounded">Simpan</button>
      </form>
    </div>
  </div>
</div>
@endsection
