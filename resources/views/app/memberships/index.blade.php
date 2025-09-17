@extends('app.layouts.base')
@section('title', 'Membership Saya')

@push('styles')
<style>
  :root{
    --indigo:#4f46e5;      /* indigo-600 */
    --indigo-700:#4338ca;  /* indigo-700 */
    --ring:#c7d2fe;        /* indigo-200 */
    --soft:#e5e7eb;
  }
  .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
  .hover-lift:hover{transform:translateY(-3px); box-shadow:0 18px 50px rgba(2,6,23,.10)}
  .soft-border{border:1px solid var(--soft)}
  .btn{border-radius:12px;padding:.55rem .9rem;font-weight:600}
  .btn-primary{background:var(--indigo);color:#fff}
  .btn-primary:hover{background:var(--indigo-700)}
  .btn-muted{background:#fff;border:1px solid var(--soft)}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .55rem;border-radius:999px;border:1px solid var(--soft);background:#f8fafc;font-size:.72rem}
  .progress{height:10px;border-radius:999px;background:#eef2f7;overflow:hidden}
  .progress>span{display:block;height:100%;background:linear-gradient(90deg,#60a5fa,#4f46e5)}
  .pill{padding:.25rem .6rem;border-radius:999px;font-size:.68rem;font-weight:600}
  .pill-live{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
  .pill-warn{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
  .pill-idle{background:#f3f4f6;color:#374151;border:1px solid #e5e7eb}
  .table{min-width:100%}
  .table th{font-size:.75rem;font-weight:700;color:#6b7280;text-transform:none}
  .table td{font-size:.9rem}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Carbon;

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

  $daysLeft = function($end){
      if(!$end) return null;
      $d = Carbon::parse($end);
      return $d->isPast() ? 0 : now()->diffInDays($d) + 1;
  };
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">

  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded soft-border bg-green-50 text-green-800 px-3 py-2 text-sm">{{ session('ok') }}</div>
  @endif
  @if(session('info'))
    <div class="mb-4 rounded soft-border bg-indigo-50 text-indigo-800 px-3 py-2 text-sm">{{ session('info') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded soft-border bg-red-50 text-red-800 px-3 py-2 text-sm">{{ $errors->first() }}</div>
  @endif

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Membership Saya</h1>
      <p class="mt-1 text-sm text-slate-600">Kelola status membership, lihat masa aktif & riwayat paket.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('app.memberships.plans') }}" class="btn btn-muted inline-flex items-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 11V5a1 1 0 1 1 2 0v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6Z"/></svg>
        Lihat Paket
      </a>
    </div>
  </div>

  {{-- Kartu status terkini --}}
  <section class="mb-8">
    <div class="p-5 rounded-2xl soft-border bg-white hover-lift">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
        <div class="min-w-0">
          <div class="text-sm text-slate-500">Membership aktif/terbaru</div>

          @if($current)
            @php
              $badgeClass = match($current->status) {
                'active'  => 'pill-live',
                'pending' => 'pill-warn',
                default   => 'pill-idle',
              };
              $pct     = $calcPct($current->activated_at, $current->expires_at);
              $left    = $daysLeft($current->expires_at);
            @endphp

            <div class="mt-1 text-lg md:text-xl font-semibold truncate">
              {{ $current->plan->name ?? 'Plan' }}
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2">
              <span class="pill {{ $badgeClass }}">{{ ucfirst($current->status) }}</span>

              @if($current->activated_at)
                <span class="chip">Aktif: {{ Carbon::parse($current->activated_at)->format('d M Y H:i') }}</span>
              @endif
              @if($current->expires_at)
                <span class="chip">Berakhir: {{ Carbon::parse($current->expires_at)->format('d M Y H:i') }}</span>
              @endif
              @if($current->status === 'pending')
                <span class="chip">Menunggu konfirmasi pembayaran (otomatis via webhook)</span>
              @endif
            </div>

            @if($current->status === 'active' && $current->expires_at)
              <div class="mt-4">
                <div class="flex items-center justify-between text-xs text-slate-600">
                  <span>Masa aktif</span>
                  <span class="font-medium text-slate-900">{{ $pct }}%</span>
                </div>
                <div class="progress mt-1.5" aria-label="Masa aktif">
                  <span style="width: {{ max(0,min(100,$pct)) }}%"></span>
                </div>
                <div class="mt-1.5 text-xs text-slate-600">
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
            <div class="mt-1 text-lg font-semibold text-slate-800">Belum ada membership</div>
            <p class="text-sm text-slate-600">Mulai dengan memilih paket di halaman Paket.</p>
          @endif
        </div>

        {{-- Actions --}}
        <div class="shrink-0 flex flex-col gap-2">
          @if($current)
            @if($current->status === 'pending')
      <a href="{{ route('app.memberships.checkout', $current) }}" class="btn btn-primary text-center">
        Lanjutkan Pembayaran
      </a>
      <button type="button" id="btnRefresh" class="btn btn-muted" onclick="location.reload()">Cek Status Sekarang</button>
      <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
        @csrf
        <button class="btn btn-muted w-full" onclick="return confirm('Batalkan membership ini?')">
          Batalkan
        </button>
      </form>
    @elseif($current->status === 'active')
              @if(($left ?? 0) <= 7 && $left !== null)
                <a href="{{ route('app.memberships.plans') }}" class="btn btn-primary text-center">
                  Perpanjang / Upgrade
                </a>
              @endif
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}">
                @csrf
                <button class="btn btn-muted" onclick="return confirm('Nonaktifkan membership sekarang?')">
                  Nonaktifkan
                </button>
              </form>
            @else
              <a href="{{ route('app.memberships.plans') }}" class="btn btn-muted text-center">
                Pilih Paket
              </a>
            @endif
          @else
            <a href="{{ route('app.memberships.plans') }}" class="btn btn-primary text-center">
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
      <h2 class="text-base font-semibold text-slate-900">Riwayat Membership</h2>
    </div>

    @if($history->count())

      {{-- Mobile: cards --}}
      <div class="grid gap-3 sm:hidden">
        @foreach($history as $m)
          @php
            $badgeClass = match($m->status) {
              'active'  => 'pill-live',
              'pending' => 'pill-warn',
              default   => 'pill-idle',
            };
          @endphp
          <div class="rounded-2xl soft-border bg-white p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="font-medium text-slate-900">{{ $m->plan->name ?? 'Plan' }}</div>
                <div class="mt-1 flex flex-wrap items-center gap-2">
                  <span class="pill {{ $badgeClass }}">{{ ucfirst($m->status) }}</span>
                  <span class="chip">Aktif: {{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y H:i') : '—' }}</span>
                  <span class="chip">Berakhir: {{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y H:i') : '—' }}</span>
                </div>
              </div>
              <div class="shrink-0">
                @if($m->status === 'pending')
                  <a href="{{ route('app.memberships.checkout', $m) }}" class="text-sm text-indigo-700 hover:underline">Checkout</a>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Desktop: table --}}
      <div class="hidden sm:block overflow-x-auto rounded-2xl soft-border bg-white">
        <table class="table divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr class="text-left">
              <th class="px-4 py-3">Plan</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aktif</th>
              <th class="px-4 py-3">Berakhir</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($history as $m)
              @php
                $badgeClass = match($m->status) {
                  'active'  => 'pill-live',
                  'pending' => 'pill-warn',
                  default   => 'pill-idle',
                };
              @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium text-slate-900">{{ $m->plan->name ?? 'Plan' }}</td>
                <td class="px-4 py-3">
                  <span class="pill {{ $badgeClass }}">{{ ucfirst($m->status) }}</span>
                </td>
                <td class="px-4 py-3 text-slate-700">
                  {{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-slate-700">
                  {{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y H:i') : '—' }}
                </td>
                <td class="px-4 py-3 text-right">
                  @if($m->status === 'pending')
                    <a href="{{ route('app.memberships.checkout', $m) }}" class="text-indigo-700 hover:underline">Checkout</a>
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
      <div class="rounded-2xl soft-border bg-white p-10 text-center">
        <div class="text-slate-900 font-semibold text-lg">Belum ada riwayat membership</div>
        <div class="text-slate-600 mt-1">Mulai berlangganan untuk akses penuh materi.</div>
        <a class="inline-flex mt-4 btn btn-primary" href="{{ route('app.memberships.plans') }}">Lihat Paket</a>
      </div>
    @endif
  </section>
</div>
@endsection
