@extends('app.layouts.base')
@section('title','Courses')

@push('styles')
<style>
  :root{
    --primary:#1d4ed8;
    --primary-700:#1e40af;
    --ring:#bfdbfe;
  }
  .btn{border-radius:12px;padding:.6rem 1rem;font-weight:600;transition:.15s ease}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-primary:hover{background:var(--primary-700)}
  .btn-muted{background:#fff;border:1px solid #e5e7eb}
  .field{border:1px solid #e5e7eb;border-radius:12px;padding:.6rem .9rem}
  .field:focus{outline:none;box-shadow:0 0 0 4px var(--ring);border-color:var(--primary)}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;transition:.18s ease}
  .card:hover{transform:translateY(-2px);box-shadow:0 18px 50px rgba(2,6,23,.08)}
  .cover{background:#eef2ff;height:170px}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .6rem;border-radius:999px;border:1px solid #e5e7eb;background:#f8fafc;font-size:.75rem}
  .badge-enrolled{display:inline-flex;align-items:center;gap:.4rem;font-size:.72rem;background:#dcfce7;color:#065f46;border-radius:10px;padding:.25rem .5rem}
  .title{font-weight:700}
  .grad{background:linear-gradient(135deg,var(--primary),#60a5fa)}
  .reset-link{font-size:.85rem}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Facades\Auth;
  use App\Models\Enrollment;

  $q = request('q');

  // daftar course yang sudah user miliki (untuk badge "Enrolled")
  $myIds = Enrollment::where('user_id', Auth::id())
      ->pluck('course_id')->all();
@endphp

<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

  <div class="flex items-center justify-between gap-4">
    <h1 class="text-2xl font-semibold">Courses</h1>
    <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline">← Home</a>
  </div>

  <form method="GET" class="flex flex-col sm:flex-row items-stretch gap-3">
    <div class="flex-1 relative">
      <input name="q" value="{{ $q }}" placeholder="Cari judul kursus…"
             class="field w-full pl-10" autocomplete="off">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"/>
      </svg>
    </div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary">Search</button>
      @if($q)
        <a href="{{ route('app.courses.index') }}" class="btn btn-muted reset-link">Reset</a>
      @endif
    </div>
  </form>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($courses as $c)
      @php
        $img = $c->cover_url ?: 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?q=80&w=1200&auto=format&fit=crop';
      @endphp

      <a href="{{ route('app.courses.show',$c) }}" class="card group">
        <div class="cover relative" style="background-image:url('{{ $img }}');background-size:cover;background-position:center">
          <div class="absolute inset-0 grad opacity-0 group-hover:opacity-20 transition"></div>

          {{-- hanya badge Enrolled; tidak ada penanda "Terkunci" --}}
          @if(in_array($c->id, $myIds))
            <span class="badge-enrolled absolute top-3 left-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75" /></svg>
              Enrolled
            </span>
          @endif
        </div>

        <div class="p-4 space-y-2">
          <div class="title text-lg group-hover:text-indigo-700 transition">{{ $c->title }}</div>

          <div class="flex flex-wrap items-center gap-2">
            <span class="chip">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3h14v2H5zM5 7h14v2H5zM5 11h14v2H5zM5 15h14v2H5zM5 19h14v2H5z"/></svg>
              {{ $c->modules_count }} modules
            </span>
            <span class="chip">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/></svg>
              {{ $c->enrollments_count }} enrolled
            </span>
          </div>

          @if($c->short_description ?? false)
            <p class="text-sm text-gray-600 line-clamp-2">{{ $c->short_description }}</p>
          @endif
        </div>
      </a>
    @empty
      <div class="col-span-full p-8 text-center text-gray-600 bg-white border rounded-2xl">
        Belum ada course yang cocok.
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $courses->withQueryString()->links() }}
  </div>
</div>
@endsection
