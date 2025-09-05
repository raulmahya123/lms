@extends('app.layouts.base')
@section('title','My Courses — BERKEMAH')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Kursus Saya</h1>

  @if(isset($enrollments) && $enrollments->count())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($enrollments as $enr)
        @php $course = $enr->course ?? null; @endphp
        <a href="{{ $course ? route('app.courses.show',$course) : '#' }}"
           class="group bg-white border rounded-xl overflow-hidden hover:shadow-lg transition block">
          <div class="aspect-[16/9] bg-gray-100 overflow-hidden">
            <img src="{{ $course->cover_url ?? asset('assets/images/placeholder.png') }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition" alt="">
          </div>
          <div class="p-4">
            <h3 class="font-semibold line-clamp-2">{{ $course->title ?? 'Tanpa Judul' }}</h3>
            <p class="text-xs text-gray-600 mt-1">
              Progress: {{ $enr->progress_percent ?? 0 }}%
            </p>
          </div>
        </a>
      @endforeach
    </div>

    @if(method_exists($enrollments,'links'))
      <div class="mt-6">{{ $enrollments->links() }}</div>
    @endif
  @else
    <div class="p-6 bg-white border rounded-xl">
      Belum ada kursus yang diikuti.
      <a class="text-blue-700 underline" href="{{ route('app.courses.index') }}">Jelajah kursus</a>
    </div>
  @endif
@endsection
