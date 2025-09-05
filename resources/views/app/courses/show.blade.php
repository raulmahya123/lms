@extends('app.layouts.base')
@section('title', $course->title)
@section('content')
<div class="flex items-start gap-6">
  <div class="flex-1">
    <img src="{{ $course->cover_url }}" class="w-full rounded border">
    <h1 class="text-2xl font-semibold mt-4">{{ $course->title }}</h1>
    <p class="mt-2 text-gray-700">{{ $course->description }}</p>

    <h2 class="mt-6 font-semibold">Kurikulum</h2>
    @foreach($course->modules as $m)
      <div class="mt-3">
        <div class="font-medium">{{ $m->title }}</div>
        <ul class="mt-2 space-y-1">
        @foreach($m->lessons as $l)
          <li>
            <a class="text-blue-700 hover:underline"
               href="{{ route('app.lessons.show',$l) }}">
              {{ $l->ordering }}. {{ $l->title }}
              @if($l->is_free) <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800">Free</span>@endif
            </a>
          </li>
        @endforeach
        </ul>
      </div>
    @endforeach
  </div>

  <div class="w-72">
    <div class="p-4 bg-white border rounded">
      @if($isEnrolled)
        <div class="text-emerald-700 font-medium">Kamu sudah terdaftar</div>
      @else
        <form method="POST" action="{{ route('app.courses.enroll',$course) }}">
          @csrf
          <button class="w-full px-4 py-2 bg-blue-600 text-white rounded">Enroll Gratis</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
