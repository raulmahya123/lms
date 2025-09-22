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
  .acts a, .acts button{font-size:.82rem}
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
                <span class="chip">Menunggu konfirmasi pembayaran</span>
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

        {{-- Actions (current) --}}
        <div class="shrink-0 flex flex-col gap-2">
          @if($current)
            @if($current->status === 'pending')
              <a href="{{ route('app.memberships.checkout', $current) }}" class="btn btn-primary text-center">
                Lanjutkan Pembayaran
              </a>
              <button type="button" id="btnRefresh" class="btn btn-muted" onclick="location.reload()">Cek Status Sekarang</button>

              {{-- FORM BATALKAN (dengan modal izin) --}}
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}" class="js-cancel-form">
                @csrf
                <input type="hidden" name="ack" value="0">
                <button type="submit" class="btn btn-muted w-full">Batalkan</button>
              </form>

            @elseif($current->status === 'active')
              @php $left = $daysLeft($current->expires_at); @endphp
              @if(($left ?? 0) <= 7 && $left !== null)
                <a href="{{ route('app.memberships.plans') }}" class="btn btn-primary text-center">
                  Perpanjang / Upgrade
                </a>
              @endif

              {{-- FORM NONAKTIFKAN (pakai modal izin yang sama) --}}
              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}" class="js-cancel-form">
                @csrf
                <input type="hidden" name="ack" value="0">
                <button type="submit" class="btn btn-muted">Nonaktifkan</button>
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

                {{-- actions (mobile) --}}
                <div class="mt-3 flex flex-wrap gap-2 acts">
                  @if($m->status === 'pending')
                    <a href="{{ route('app.memberships.checkout', $m) }}" class="text-indigo-700 hover:underline">Checkout</a>
                    <button type="button" class="text-slate-700 hover:underline" onclick="location.reload()">Cek Status</button>

                    <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                      @csrf
                      <input type="hidden" name="ack" value="0">
                      <button class="text-red-700 hover:underline" type="submit">Batalkan</button>
                    </form>

                  @elseif($m->status === 'active')
                    <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                      @csrf
                      <input type="hidden" name="ack" value="0">
                      <button class="text-slate-700 hover:underline" type="submit">Nonaktifkan</button>
                    </form>
                  @endif
                </div>
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
              <th class="px-4 py-3 text-right">Aksi</th>
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
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2 justify-end acts">
                    @if($m->status === 'pending')
                      <a href="{{ route('app.memberships.checkout', $m) }}" class="btn btn-primary">Checkout</a>
                      <button type="button" class="btn btn-muted" onclick="location.reload()">Cek Status</button>

                      <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                        @csrf
                        <input type="hidden" name="ack" value="0">
                        <button class="btn btn-muted" type="submit">Batalkan</button>
                      </form>

                    @elseif($m->status === 'active')
                      <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                        @csrf
                        <input type="hidden" name="ack" value="0">
                        <button class="btn btn-muted" type="submit">Nonaktifkan</button>
                      </form>
                    @else
                      <span class="text-slate-400">—</span>
                    @endif
                  </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form.js-cancel-form').forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      Swal.fire({
        icon: 'warning',
        title: 'Batalkan membership?',
        html: `
          <div class="text-left leading-relaxed">
            <ul class="list-disc pl-5 space-y-1">
              <li>Membership akan <b>langsung dinonaktifkan</b>.</li>
              <li>Akses materi premium <b>dicabut</b>.</li>
              <li>Sisa masa aktif <b>hangus</b>.</li>
              <li>Pembayaran bersifat <b>non-refundable</b>.</li>
            </ul>
            <label class="mt-4 flex items-start gap-2">
              <input type="checkbox" id="ack-cancel" class="mt-1">
              <span>Saya memahami dan setuju dengan semua konsekuensi di atas.</span>
            </label>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Saya setuju & batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusConfirm: false,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
          const ok = document.getElementById('ack-cancel')?.checked;
          if (!ok) {
            Swal.showValidationMessage('Centang persetujuan terlebih dahulu.');
            return false;
          }
          return true;
        }
      }).then((res) => {
        if (res.isConfirmed) {
          const ackInput = form.querySelector('input[name="ack"]');
          if (ackInput) ackInput.value = '1';
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush
