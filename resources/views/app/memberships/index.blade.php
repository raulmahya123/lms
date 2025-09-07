@extends('app.layouts.base')
@section('title', 'Membership Saya')

@push('styles')
<style>
  .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
  .hover-lift:hover{transform:translateY(-2px); box-shadow:0 14px 40px rgba(2,6,23,.12)}
  .soft-border{border:1px solid rgba(2,6,23,.08)}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Carbon;

  // Progress masa aktif (0-100, integer)
  $calcPct = function($start, $end){
      try{
        if(!$start || !$end) return 0;
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);
        $now   = now();
        if($end->lessThanOrEqualTo($start)) return 0;
        $total = max(1, $start->diffInSeconds($end));
        $gone  = max(0, $start->diffInSeconds(min($now,$end)));
        return (int) floor(($gone / $total) * 100);
      }catch(\Throwable $e){ return 0; }
  };

  // Sisa hari sebagai INTEGER (tanpa desimal). Termasuk hari ini.
  $daysLeft = function($end){
      if(!$end) return null;
      $d = Carbon::parse($end);
      return $d->isPast() ? 0 : now()->diffInDays($d) + 1;
  };
@endphp

<div class="max-w-6xl mx-auto">
  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded soft-border bg-green-50 text-green-800 px-3 py-2 text-sm">{{ session('ok') }}</div>
  @endif
  @if(session('info'))
    <div class="mb-4 rounded soft-border bg-blue-50 text-blue-800 px-3 py-2 text-sm">{{ session('info') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded soft-border bg-red-50 text-red-800 px-3 py-2 text-sm">{{ $errors->first() }}</div>
  @endif

  <header class="mb-6">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Membership Saya</h1>
      <a href="{{ route('app.memberships.plans') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-lg soft-border bg-white hover:bg-gray-50">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 11V5a1 1 0 1 1 2 0v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6Z"/></svg>
        Lihat Paket
      </a>
    </div>
    <p class="mt-1 text-sm text-gray-600">Kelola status membership, lihat masa aktif & riwayat paket.</p>
  </header>

  {{-- Kartu status terkini --}}
  <section class="mb-8">
    <div class="p-5 rounded-2xl soft-border bg-white hover-lift">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
        <div class="min-w-0">
          <div class="text-sm text-gray-500">Membership aktif/terbaru</div>

          @if($current)
            @php
              $badge = match($current->status) {
                'active'  => 'bg-green-50 text-green-700 soft-border border-green-200',
                'pending' => 'bg-amber-50 text-amber-700 soft-border border-amber-200',
                default   => 'bg-gray-50 text-gray-700 soft-border border-gray-200'
              };
              $pct     = $calcPct($current->activated_at, $current->expires_at);
              $left    = $daysLeft($current->expires_at); // INTEGER
            @endphp

            <div class="mt-1 text-lg md:text-xl font-semibold truncate">
              {{ $current->plan->name ?? 'Plan' }}
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center text-xs px-2 py-0.5 rounded {{ $badge }}">
                {{ ucfirst($current->status) }}
              </span>

              @if($current->activated_at)
                <span class="text-xs text-gray-600">
                  Aktif: {{ Carbon::parse($current->activated_at)->format('d M Y H:i') }}
                </span>
              @endif
              @if($current->expires_at)
                <span class="text-xs text-gray-600">
                  Berakhir: {{ Carbon::parse($current->expires_at)->format('d M Y H:i') }}
                </span>
              @endif
            </div>

            {{-- Progress masa aktif --}}
            @if($current->status === 'active' && $current->expires_at)
              <div class="mt-4">
                <div class="flex items-center justify-between text-xs">
                  <span class="text-gray-600">Masa aktif</span>
                  <span class="font-medium text-gray-900">{{ $pct }}%</span>
                </div>
                <div class="mt-1.5 h-2 w-full rounded-full bg-gray-100 overflow-hidden" aria-label="Masa aktif">
                  <div class="h-full rounded-full"
                       style="width: {{ max(0,min(100,$pct)) }}%;
                              background-image: linear-gradient(to right,#22c55e,#16a34a)">
                  </div>
                </div>
                <div class="mt-1.5 text-xs text-gray-600">
                  @if($left !== null)
                    @if($left <= 0)
                      <span class="text-red-600 font-medium">Berakhir</span>
                    @elseif($left === 1)
                      1 hari tersisa
                    @else
                      {{ $left }} hari tersisa
                    @endif
                  @else
                    —
                  @endif
                </div>
              </div>
            @endif
          @else
            <div class="mt-1 text-lg font-semibold text-gray-700">Belum ada membership</div>
            <p class="text-sm text-gray-500">Mulai dengan memilih paket di halaman Paket.</p>
          @endif
        </div>

        <div class="shrink-0 flex flex-col gap-2">
          @if($current)
            @if($current->status === 'pending')
              <a href="{{ route('app.memberships.checkout', $current) }}"
                 class="px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-sm text-center">
                Lanjutkan Pembayaran
              </a>
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
                @csrf
                <button class="w-full px-3 py-2 rounded-lg soft-border bg-white hover:bg-gray-50 text-sm"
                        onclick="return confirm('Batalkan membership ini?')">
                  Batalkan
                </button>
              </form>
            @elseif($current->status === 'active')
              @if($left !== null && $left <= 7)
                <a href="{{ route('app.memberships.plans') }}"
                   class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm text-center">
                  Perpanjang / Upgrade
                </a>
              @endif
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
                @csrf
                <button class="px-3 py-2 rounded-lg soft-border bg-white hover:bg-gray-50 text-sm"
                        onclick="return confirm('Nonaktifkan membership sekarang?')">
                  Nonaktifkan
                </button>
              </form>
            @else
              <a href="{{ route('app.memberships.plans') }}"
                 class="px-3 py-2 rounded-lg soft-border bg-white hover:bg-gray-50 text-sm text-center">
                Pilih Paket
              </a>
            @endif
          @else
            <a href="{{ route('app.memberships.plans') }}"
               class="px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 text-sm text-center">
              Pilih Paket
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- Riwayat --}}
  <section>
    <div class="mb-3 flex items-center justify-between">
      <h2 class="text-base font-semibold text-gray-900">Riwayat Membership</h2>
    </div>

    @if($history->count())

      {{-- Mobile: cards --}}
      <div class="grid gap-3 sm:hidden">
        @foreach($history as $m)
          @php
            $badge = match($m->status) {
              'active'  => 'bg-green-50 text-green-700 soft-border border-green-200',
              'pending' => 'bg-amber-50 text-amber-700 soft-border border-amber-200',
              default   => 'bg-gray-50 text-gray-700 soft-border border-gray-200'
            };
          @endphp
          <div class="rounded-xl soft-border bg-white p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="font-medium text-gray-900">{{ $m->plan->name ?? 'Plan' }}</div>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                  <span class="inline-flex items-center text-xs px-2 py-0.5 rounded {{ $badge }}">
                    {{ ucfirst($m->status) }}
                  </span>
                  <span class="text-xs text-gray-600">
                    Aktif: {{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y H:i') : '—' }}
                  </span>
                  <span class="text-xs text-gray-600">
                    Berakhir: {{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y H:i') : '—' }}
                  </span>
                </div>
              </div>
              <div class="shrink-0">
                @if($m->status === 'pending')
                  <a href="{{ route('app.memberships.checkout', $m) }}"
                     class="text-sm text-blue-700 hover:underline">Checkout</a>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Desktop: table --}}
      <div class="hidden sm:block overflow-x-auto rounded-2xl soft-border bg-white">
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold text-gray-600">
              <th class="px-4 py-3">Plan</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aktif</th>
              <th class="px-4 py-3">Berakhir</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-sm">
            @foreach($history as $m)
              @php
                $badge = match($m->status) {
                  'active'  => 'bg-green-50 text-green-700 soft-border border-green-200',
                  'pending' => 'bg-amber-50 text-amber-700 soft-border border-amber-200',
                  default   => 'bg-gray-50 text-gray-700 soft-border border-gray-200'
                };
              @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $m->plan->name ?? 'Plan' }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center text-xs px-2 py-0.5 rounded {{ $badge }}">
                    {{ ucfirst($m->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-700">
                  {{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-700">
                  {{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-right">
                  @if($m->status === 'pending')
                    <a href="{{ route('app.memberships.checkout', $m) }}"
                       class="text-blue-700 hover:underline">Checkout</a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $history->links() }}
      </div>
    @else
      <div class="rounded-2xl soft-border bg-white p-8 text-center">
        <div class="text-slate-800 font-semibold">Belum ada riwayat membership.</div>
        <div class="text-slate-600 mt-1">Mulai berlangganan untuk akses penuh materi.</div>
        <a class="inline-flex mt-4 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
           href="{{ route('app.memberships.plans') }}">Lihat Paket</a>
      </div>
    @endif
  </section>
</div>
@endsection
