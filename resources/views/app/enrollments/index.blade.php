@extends('app.layouts.base')
@section('title','My Courses — BERKEMAH')

@push('styles')
<style>
  :root{
    --indigo:#4f46e5;      /* indigo-600 */
    --indigo-700:#4338ca;  /* indigo-700 */
    --ring:#c7d2fe;        /* indigo-200 */
  }
  .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
  .hover-lift:hover{transform:translateY(-3px); box-shadow:0 18px 50px rgba(2,6,23,.10)}
  .btn{border-radius:12px;padding:.55rem .9rem;font-weight:600}
  .btn-primary{background:var(--indigo);color:#fff}
  .btn-primary:hover{background:var(--indigo-700)}
  .btn-muted{background:#fff;border:1px solid #e5e7eb}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .55rem;border-radius:999px;border:1px solid #e5e7eb;background:#f8fafc;font-size:.72rem}
  .progress{height:10px;border-radius:999px;background:#eef2f7;overflow:hidden}
  .progress>span{display:block;height:100%;background:linear-gradient(90deg,#60a5fa,#4f46e5)}
  .cover{position:relative;aspect-ratio:16/9;background:#eef2ff;overflow:hidden}
  .cover img{width:100%;height:100%;object-fit:cover;transition:transform .3s ease}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden}
  .title{font-weight:700}
  .pill{padding:.25rem .6rem;border-radius:999px;font-size:.68rem;font-weight:600}
  .pill-live{background:#dcfce7;color:#166534}
  .pill-idle{background:#f3f4f6;color:#374151}
  .meta{font-size:.78rem;color:#64748b}
</style>
@endpush

@section('content')
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
    <div>
      <h1 class="text-2xl font-bold">Kursus Saya</h1>
      @if(isset($enrollments) && $enrollments->count())
        <div class="text-sm text-gray-600 mt-1">{{ $enrollments->total() ?? $enrollments->count() }} kursus aktif</div>
      @endif
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('app.courses.index') }}" class="btn btn-muted">Jelajah Kursus</a>
    </div>
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
          $pctClamped = max(0, min(100, $pct));
        @endphp

        <div class="card hover-lift group">
          <a href="{{ $course ? route('app.courses.show',$course) : '#' }}" class="block">
            <div class="cover">
              <img src="{{ $cover }}" alt="{{ $course->title ?? 'Tanpa Judul' }}" class="group-hover:scale-105">
              <div class="absolute top-3 left-3 flex items-center gap-2">
                <span class="pill {{ $status==='active' ? 'pill-live' : 'pill-idle' }}">{{ ucfirst($status) }}</span>
                @if($total>0)
                  <span class="chip">{{ $pctClamped }}%</span>
                @endif
              </div>
            </div>
          </a>

          <div class="p-4">
            <a href="{{ $course ? route('app.courses.show',$course) : '#' }}" class="block">
              <h3 class="title text-slate-900 line-clamp-2 group-hover:text-indigo-700 transition">
                {{ $course->title ?? 'Tanpa Judul' }}
              </h3>
            </a>

            {{-- Progress --}}
            <div class="mt-3">
              <div class="flex items-center justify-between text-xs meta">
                <span>Progress</span>
                <span class="text-slate-800 font-medium">{{ $pctClamped }}%</span>
              </div>
              <div class="progress mt-1.5" aria-label="Progress">
                <span style="width: {{ $pctClamped }}%"></span>
              </div>
              <div class="mt-1.5 text-xs meta">{{ $done }} / {{ $total }} pelajaran</div>
            </div>

            {{-- Footer actions --}}
            <div class="mt-4 flex items-center gap-2">
              <a href="{{ $course ? route('app.courses.show',$course) : '#' }}"
                 class="btn btn-primary inline-flex items-center gap-1.5">
                Lanjutkan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </a>
              <a href="{{ $course ? route('app.courses.show',$course) : '#' }}" class="btn btn-muted">Detail</a>

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
    <div class="p-10 bg-white border rounded-2xl text-center">
      <div class="text-slate-900 font-semibold text-lg">Belum ada kursus yang diikuti</div>
      <div class="text-slate-600 mt-1">Yuk mulai belajar dari pilihan kursus terbaik kami.</div>
      <a class="inline-flex mt-4 px-4 py-2 rounded-xl btn btn-primary" href="{{ route('app.courses.index') }}">
        Jelajah Kursus
      </a>
    </div>
  @endif
@endsection
