@extends('layouts.app')

@section('title', 'Tes Psikologi')

@section('content')
@php
  $q       = $q       ?? request('q');
  $track   = $track   ?? request('track');
  $type    = $type    ?? request('type');
  $sort    = $sort    ?? request('sort');
  $perPage = $perPage ?? request('per_page');
  $hasFilters = request()->hasAny(['q','track','type','sort','per_page']);
@endphp

<div class="max-w-7xl mx-auto space-y-8">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold text-text-main">Tes Psikologi</h1>
      <p class="text-text-soft mt-1">Jelajahi dan ikuti berbagai tes psikologi untuk mengetahui potensimu.</p>
    </div>
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-100 rounded-xl text-sm font-semibold text-text-main hover:text-tosca hover:border-tosca/30 transition-colors shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      Beranda
    </a>
  </div>

  {{-- Filter Bar --}}
  <form method="GET" action="{{ route('app.psy.tests.index') }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="md:col-span-2">
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Tes</label>
        <div class="relative">
          <input type="text" name="q" value="{{ $q }}" placeholder="Ketik nama tes..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-tosca focus:ring focus:ring-tosca/20 text-sm transition-shadow">
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Track</label>
        <select name="track" class="w-full py-2.5 rounded-xl border-gray-200 focus:border-tosca focus:ring focus:ring-tosca/20 text-sm transition-shadow">
          <option value="">Semua Track</option>
          @foreach($tracks as $t)
            <option value="{{ $t }}" @selected($track===$t)>{{ ucfirst($t) }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Tipe</label>
        <select name="type" class="w-full py-2.5 rounded-xl border-gray-200 focus:border-tosca focus:ring focus:ring-tosca/20 text-sm transition-shadow">
          <option value="">Semua Tipe</option>
          @foreach($types as $t)
            <option value="{{ $t }}" @selected($type===$t)>{{ strtoupper($t) }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Urutkan</label>
        <select name="sort" class="w-full py-2.5 rounded-xl border-gray-200 focus:border-tosca focus:ring focus:ring-tosca/20 text-sm transition-shadow">
          <option value="">Urutkan Berdasarkan</option>
          <option value="latest" @selected($sort==='latest')>Terbaru</option>
          <option value="name" @selected($sort==='name')>Nama A→Z</option>
          <option value="questions" @selected($sort==='questions')>Banyak Soal</option>
        </select>
      </div>
    </div>

    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-50 pt-4">
      <div class="flex items-center gap-2">
        <label class="text-sm text-text-soft">Tampilkan:</label>
        <select name="per_page" class="py-1.5 rounded-lg border-gray-200 focus:border-tosca focus:ring focus:ring-tosca/20 text-sm transition-shadow">
          @foreach([12,20,30,50] as $pp)
            <option value="{{ $pp }}" @selected((int)($perPage ?? 0) === $pp)>{{ $pp }}</option>
          @endforeach
        </select>
      </div>
      
      <div class="flex gap-3 w-full sm:w-auto">
        @if($hasFilters)
          <a href="{{ route('app.psy.tests.index') }}" class="flex-1 sm:flex-none px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-colors text-center">
            Reset
          </a>
        @endif
        <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 text-center">
          Terapkan Filter
        </button>
      </div>
    </div>
  </form>

  {{-- Grid Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($tests as $t)
      @php
        $slugId = $t->slug ?: $t->id;
        $hasQ   = (int)($t->questions_count ?? 0) > 0;

        $attempt   = isset($attemptByTest) ? ($attemptByTest[$t->id] ?? null) : null;
        $answered  = 0;
        if ($attempt && isset($answerCountsByAttempt)) {
            $answered = (int) ($answerCountsByAttempt[$attempt->id] ?? 0);
        }
        $totalQ = (int) ($t->questions_count ?? 0);
        $pct    = $totalQ > 0 ? (int) floor(($answered / $totalQ) * 100) : 0;
      @endphp

      <div class="group bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 flex flex-col hover:border-tosca/30 hover:shadow-tosca/10 transition-all h-full">
        <div class="flex flex-wrap gap-2 mb-4">
          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-softbg text-tosca-dark uppercase tracking-wider">{{ strtoupper($t->type) }}</span>
          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-700 uppercase tracking-wider">{{ ucfirst($t->track) }}</span>
          <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wider">{{ $t->questions_count }} Soal</span>
          @if(!empty($t->time_limit_min))
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 uppercase tracking-wider">⏳ {{ (int)$t->time_limit_min }} Min</span>
          @endif
        </div>

        <a href="{{ route('app.psy.tests.show', $slugId) }}" class="block mb-2">
          <h3 class="font-bold text-lg text-text-main group-hover:text-tosca transition-colors leading-tight">{{ $t->name }}</h3>
        </a>

        @if(!empty(optional($t)->description))
          <p class="text-sm text-text-soft line-clamp-2 mb-6 flex-1">{{ $t->description }}</p>
        @else
          <div class="flex-1 mb-6"></div>
        @endif

        {{-- Progress (jika ada attempt berjalan) --}}
        @if($attempt)
          <div class="mb-5 p-3 rounded-2xl bg-gray-50 border border-gray-100">
            <div class="flex justify-between items-center text-xs font-semibold text-text-main mb-2">
              <span>Progres Pengerjaan</span>
              <span>{{ $pct }}%</span>
            </div>
            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-tosca rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
            </div>
            <div class="text-[10px] text-text-soft uppercase tracking-wider mt-2 font-semibold">
              {{ $answered }} dari {{ $totalQ }} soal terjawab
            </div>
          </div>
        @endif

        <div class="grid grid-cols-2 gap-3 mt-auto">
          <a href="{{ route('app.psy.tests.show', $slugId) }}" class="flex items-center justify-center px-4 py-2.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:border-gray-200 hover:bg-gray-50 transition-colors">
            Detail
          </a>

          @if($hasQ)
            <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}" class="w-full">
              @csrf
              <button class="w-full flex items-center justify-center px-4 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                {{ $attempt ? 'Lanjutkan' : 'Mulai Tes' }}
              </button>
            </form>
          @else
            <button disabled class="flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed">
              Kosong
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="col-span-full py-16 flex flex-col items-center justify-center bg-white rounded-3xl border border-gray-50 text-center shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
        <div class="w-16 h-16 rounded-2xl bg-softbg text-tosca flex items-center justify-center mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-text-main">Tidak Ditemukan</h3>
        <p class="text-text-soft mt-1">Maaf, kami tidak menemukan tes yang sesuai dengan pencarian atau filter Anda.</p>
        <a href="{{ route('app.psy.tests.index') }}" class="mt-6 px-6 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-colors">Reset Filter</a>
      </div>
    @endforelse
  </div>

  @if(method_exists($tests,'links'))
    <div class="mt-8 flex justify-center">
      {{ $tests->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
  @endif

</div>
@endsection
