@extends('app.layouts.base')
@section('title','My Courses — BERKEMAH')

@push('styles')
<style>
  /* micro polish */
  .hover-lift{ transition:transform .2s ease, box-shadow .2s ease; }
  .hover-lift:hover{ transform:translateY(-3px); box-shadow:0 16px 40px rgba(2,6,23,.10); }
</style>
@endpush

@section('content')
  <div class="flex items-end justify-between mb-6">
    <h1 class="text-2xl font-bold">Kursus Saya</h1>
    @if(isset($enrollments) && $enrollments->count())
      <div class="text-sm text-gray-600">{{ $enrollments->total() ?? $enrollments->count() }} kursus</div>
    @endif
  </div>

  @if(isset($enrollments) && $enrollments->count())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($enrollments as $enr)
        @php
          $course = $enr->course ?? null;
          $pct    = (int)($enr->progress_percent ?? 0);
          $done   = (int)($enr->done_lessons ?? 0);
          $total  = (int)($enr->total_lessons ?? 0);
          $status = $enr->status ?? 'active';
          $cover  = $course->cover_url ?? asset('assets/images/placeholder.png');
        @endphp

        <div class="group bg-white border rounded-2xl overflow-hidden hover-lift">
          <a href="{{ $course ? route('app.courses.show',$course) : '#' }}" class="block">
            <div class="relative aspect-[16/9] bg-gray-100 overflow-hidden">
              <img src="{{ $cover }}" alt="{{ $course->title ?? 'Tanpa Judul' }}"
                   class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
              <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 text-[11px] font-medium rounded-full
                  {{ $status==='active' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-gray-600/20' }}">
                  {{ ucfirst($status) }}
                </span>
              </div>
            </div>
          </a>

          <div class="p-4">
            <a href="{{ $course ? route('app.courses.show',$course) : '#' }}" class="block">
              <h3 class="font-semibold text-slate-900 line-clamp-2 group-hover:text-indigo-700 transition">
                {{ $course->title ?? 'Tanpa Judul' }}
              </h3>
            </a>

            {{-- Progress --}}
            <div class="mt-3">
              <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Progress</span>
                <span class="font-medium text-slate-800">{{ $pct }}%</span>
              </div>
              <div class="mt-1.5 h-2 w-full rounded-full bg-slate-100 overflow-hidden" aria-label="Progress">
                <div class="h-full rounded-full"
                     style="width: {{ max(0,min(100,$pct)) }}%;
                            background-image: linear-gradient(to right, #22c55e, #16a34a);">
                </div>
              </div>
              <div class="mt-1.5 text-xs text-gray-600">
                {{ $done }} / {{ $total }} pelajaran
              </div>
            </div>

            {{-- Footer actions --}}
            <div class="mt-4 flex items-center gap-2">
              <a href="{{ $course ? route('app.courses.show',$course) : '#' }}"
                 class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl
                        bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Lanjutkan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
              </a>
              <a href="{{ $course ? route('app.courses.show',$course) : '#' }}"
                 class="inline-flex px-3 py-2 text-sm font-medium rounded-xl border
                        text-slate-700 bg-white hover:bg-slate-50">
                Detail
              </a>
              @if(!empty($enr->activated_at))
                <span class="ml-auto text-[11px] text-gray-500">
                  Aktif: {{ \Illuminate\Support\Carbon::parse($enr->activated_at)->diffForHumans() }}
                </span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if(method_exists($enrollments,'links'))
      <div class="mt-8">{{ $enrollments->links() }}</div>
    @endif
  @else
    <div class="p-8 bg-white border rounded-2xl text-center">
      <div class="text-slate-800 font-semibold">Belum ada kursus yang diikuti.</div>
      <div class="text-slate-600 mt-1">Mulai belajar dari pilihan kursus terbaik kami.</div>
      <a class="inline-flex mt-4 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
         href="{{ route('app.courses.index') }}">Jelajah kursus</a>
    </div>
  @endif
@endsection
