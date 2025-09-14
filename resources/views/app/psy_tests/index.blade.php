@extends('app.layouts.base')

@section('title','Tes Psikologi')

@push('styles')
<style>
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .55rem;border-radius:999px;background:#f8fafc;border:1px solid #e5e7eb;font-size:.75rem}
  .btn{border-radius:12px;padding:.55rem .9rem;font-weight:600}
  .btn-primary{background:#2563eb;color:#fff}
  .btn-muted{background:#fff;border:1px solid #e5e7eb}
  .btn:disabled{opacity:.6;cursor:not-allowed}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;transition:.15s ease}
  .card:hover{box-shadow:0 14px 40px rgba(2,6,23,.06);transform:translateY(-1px)}
  .bar{height:6px;border-radius:999px;background:#eef2f7;overflow:hidden}
  .bar>span{display:block;height:100%;background:linear-gradient(90deg,#2563eb,#22c55e)}
  .line-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
</style>
@endpush

@section('content')
@php
  $hasFilters = request()->hasAny(['q','track','type','sort','per_page']);
  $sort = $sort ?? request('sort');      // dari controller versi advanced (opsional)
  $perPage = $perPage ?? request('per_page');
@endphp

<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-4">
    <h1 class="text-2xl font-semibold">Tes Psikologi</h1>
    <a href="{{ route('home') }}" class="text-sm text-blue-600">← Beranda</a>
  </div>

  {{-- Filter Bar --}}
  <form method="GET" class="grid md:grid-cols-5 gap-3">
    <input name="q" value="{{ $q }}" placeholder="Cari tes…"
           class="border rounded-lg px-3 py-2 md:col-span-2">

    <select name="track" class="border rounded-lg px-3 py-2">
      <option value="">Semua Track</option>
      @foreach($tracks as $t)
        <option value="{{ $t }}" @selected($track===$t)>{{ ucfirst($t) }}</option>
      @endforeach
    </select>

    <select name="type" class="border rounded-lg px-3 py-2">
      <option value="">Semua Tipe</option>
      @foreach($types as $t)
        <option value="{{ $t }}" @selected($type===$t)>{{ strtoupper($t) }}</option>
      @endforeach
    </select>

    {{-- Optional: sorting & per_page (kalau controllernya support) --}}
    <div class="flex gap-3">
      <select name="sort" class="border rounded-lg px-3 py-2">
        <option value="">Urutkan</option>
        <option value="latest" @selected($sort==='latest')>Terbaru</option>
        <option value="name" @selected($sort==='name')>Nama A→Z</option>
        <option value="questions" @selected($sort==='questions')>Banyak Soal</option>
      </select>
      <select name="per_page" class="border rounded-lg px-3 py-2">
        <option value="">/Hal</option>
        @foreach([12,20,30,50] as $pp)
          <option value="{{ $pp }}" @selected((int)$perPage === $pp)>{{ $pp }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-5 flex gap-2">
      <button class="btn btn-primary">Terapkan</button>
      @if($hasFilters)
        <a href="{{ route('app.psytests.index') }}" class="btn btn-muted">Reset</a>
      @endif
    </div>
  </form>

  {{-- Grid Cards --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($tests as $t)
      @php
        $slugId = $t->slug ?: $t->id;
        $hasQ   = ($t->questions_count ?? 0) > 0;

        // Opsi progres (jika controller kirim attemptByTest & answerCountsByAttempt)
        $attempt   = isset($attemptByTest) ? ($attemptByTest[$t->id] ?? null) : null;
        $answered  = 0;
        if ($attempt && isset($answerCountsByAttempt)) {
            $answered = (int) ($answerCountsByAttempt[$attempt->id] ?? 0);
        }
        $totalQ = (int) ($t->questions_count ?? 0);
        $pct    = $totalQ > 0 ? (int) floor(($answered / $totalQ) * 100) : 0;
      @endphp

      <div class="card p-4 flex flex-col">
        <div class="text-xs text-gray-500 mb-1 flex flex-wrap gap-2">
          <span class="chip">{{ strtoupper($t->type) }}</span>
          <span class="chip">{{ ucfirst($t->track) }}</span>
          <span class="chip">{{ $t->questions_count }} soal</span>
          @if(!empty($t->time_limit_min))
            <span class="chip">⏳ {{ (int)$t->time_limit_min }} menit</span>
          @endif
        </div>

        <a href="{{ route('app.psytests.show', $slugId) }}" class="font-semibold text-lg hover:underline">
          {{ $t->name }}
        </a>

        @if($t->description)
          <p class="text-gray-600 mt-1 line-2">{{ $t->description }}</p>
        @endif

        {{-- Progress (jika ada attempt berjalan) --}}
        @if($attempt)
          <div class="mt-3">
            <div class="bar"><span style="width: {{ $pct }}%"></span></div>
            <div class="text-xs text-gray-500 mt-1">
              Progres: {{ $answered }}/{{ $totalQ }} ({{ $pct }}%)
            </div>
          </div>
        @endif

        <div class="mt-4 flex items-center justify-between gap-2">
          <a href="{{ route('app.psytests.show', $slugId) }}" class="btn btn-muted">Detail</a>

          @if($hasQ)
            <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}">
              @csrf
              <button class="btn btn-primary">
                {{ $attempt ? 'Lanjutkan' : 'Mulai' }}
              </button>
            </form>
          @else
            <button class="btn btn-muted" disabled>Tidak tersedia</button>
          @endif
        </div>
      </div>
    @empty
      <div class="col-span-full p-6 text-center text-gray-600 bg-white border rounded-xl">
        Tidak ada tes ditemukan.
      </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $tests->links() }}</div>
</div>
@endsection
