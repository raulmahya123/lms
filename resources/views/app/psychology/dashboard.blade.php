@extends('layouts.app')
@section('title','Dashboard Psikologi')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-8">

  {{-- Header --}}
  <div>
    <h1 class="text-2xl font-semibold">Dashboard Psikologi</h1>
    <p class="text-slate-500">Semua tes yang kamu bisa ambil, progres, dan rekomendasi profil.</p>
  </div>

  {{-- Rekomendasi --}}
  @if($recommendation)
    <div class="rounded-2xl p-5 bg-gradient-to-r from-indigo-50 to-sky-50 border border-slate-200">
      <div class="font-semibold">Profil Terbaru: {{ $recommendation['title'] }}</div>
      <p class="text-slate-700">{{ $recommendation['desc'] }}</p>
    </div>
  @endif

  {{-- Ringkasan cepat --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-2xl border p-5 bg-white shadow-sm text-center">
      <div class="text-2xl font-semibold">{{ \App\Models\PsyAttempt::where('user_id',auth()->id())->count() }}</div>
      <div class="text-slate-500 text-sm">Total Percobaan</div>
    </div>
    <div class="rounded-2xl border p-5 bg-white shadow-sm text-center">
      <div class="text-2xl font-semibold">{{ \App\Models\PsyAttempt::where('user_id',auth()->id())->distinct('test_id')->count('test_id') }}</div>
      <div class="text-slate-500 text-sm">Tes Berbeda</div>
    </div>
    <div class="rounded-2xl border p-5 bg-white shadow-sm text-center">
      <div class="text-2xl font-semibold">
        {{ optional(\App\Models\PsyAttempt::where('user_id',auth()->id())->latest()->first())->total_score ?? 0 }}
      </div>
      <div class="text-slate-500 text-sm">Skor Terakhir</div>
    </div>
  </div>

  {{-- Tes tersedia --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-lg">Tes Tersedia</h2>
      @if(Route::has('psy-tests.index'))
        <a href="{{ route('psy-tests.index') }}" class="text-sky-600 hover:underline">Kelola (Admin) →</a>
      @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      @forelse($tests as $t)
        <div class="rounded-2xl border border-slate-200 p-5 bg-white shadow-sm relative">
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">Soal: {{ $t->questions_count }}</span>
            @if($t->is_premium)
              <span class="text-xs px-2 py-0.5 rounded-full bg-violet-50 text-violet-700">Premium</span>
            @endif
          </div>
          <h3 class="font-semibold">{{ $t->title }}</h3>
          <p class="text-sm text-slate-600 line-clamp-2">{{ $t->description }}</p>

          @php $s = $stats[$t->id] ?? null; @endphp
          <div class="mt-3 grid grid-cols-3 text-center text-xs text-slate-500">
            <div><div class="font-semibold text-slate-900">{{ $s->attempts ?? 0 }}</div>attempt</div>
            <div><div class="font-semibold text-slate-900">{{ $s ? round($s->avg_score,1) : 0 }}</div>rata2</div>
            <div><div class="font-semibold text-slate-900">{{ $s->best ?? 0 }}</div>terbaik</div>
          </div>

          <div class="mt-4">
            @if($t->locked)
              @if(Route::has('memberships.index'))
                <a href="{{ route('memberships.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-900 text-white hover:opacity-90">
                  Buka Akses (Paket)
                </a>
              @else
                <span class="inline-flex items-center px-3 py-2 rounded-xl bg-slate-200 text-slate-600">Terkunci</span>
              @endif
              <span class="absolute top-3 right-3 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded">Locked</span>
            @else
              @if($routeNames['take_show'])
                <a href="{{ route($routeNames['take_show'], $t) }}"
                   class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700">
                  Mulai / Ulangi Tes
                </a>
              @else
                <a href="{{ url('/psy-tests/'.$t->id) }}"
                   class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-sky-600 text-white hover:bg-sky-700">
                  Mulai / Ulangi Tes
                </a>
              @endif
            @endif
          </div>
        </div>
      @empty
        <p class="text-slate-500">Belum ada tes.</p>
      @endforelse
    </div>
  </div>

  {{-- Riwayat --}}
  <div>
    <h2 class="font-semibold text-lg mb-3">Riwayat Terbaru</h2>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left px-4 py-2 font-medium text-slate-600">Tanggal</th>
            <th class="text-left px-4 py-2 font-medium text-slate-600">Tes</th>
            <th class="text-left px-4 py-2 font-medium text-slate-600">Skor</th>
            <th class="text-left px-4 py-2 font-medium text-slate-600">Profil</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($attempts as $a)
            @php $profile = \App\Services\PsyAccess::findProfile($a->test_id, (int) $a->total_score); @endphp
            <tr class="border-t">
              <td class="px-4 py-2">{{ $a->created_at->format('d M Y, H:i') }}</td>
              <td class="px-4 py-2">{{ $a->test->title }}</td>
              <td class="px-4 py-2 font-semibold">{{ $a->total_score }}</td>
              <td class="px-4 py-2">
                @if($profile)
                  <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700">{{ $profile->name }}</span>
                @else
                  <span class="text-slate-400">-</span>
                @endif
              </td>
              <td class="px-4 py-2 text-right">
                @if($routeNames['attempt_show'])
                  <a href="{{ route($routeNames['attempt_show'], $a) }}" class="text-sky-600 hover:underline">Detail</a>
                @else
                  {{-- fallback: tidak ada route detail khusus user --}}
                  <span class="text-slate-400">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada riwayat.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $attempts->links() }}</div>
  </div>

</div>
@endsection
