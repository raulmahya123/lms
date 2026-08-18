@extends('layouts.app')
@section('title', 'Membership Saya')

@section('content')
@php
use Illuminate\Support\Carbon;

$calcPct = function($start, $end){
  try{
    if(!$start || !$end) return 0;
    $start = Carbon::parse($start);
    $end = Carbon::parse($end);
    $now = now();
    if($end->lessThanOrEqualTo($start)) return 0;
    $total = max(1, $start->diffInSeconds($end));
    $gone = max(0, $start->diffInSeconds(min($now,$end)));
    return (int) floor(($gone / $total) * 100);
  }catch(\Throwable $e){ return 0; }
};

$daysLeft = function($end){
  if(!$end) return null;
  $d = Carbon::parse($end);
  return $d->isPast() ? 0 : now()->diffInDays($d) + 1;
};
@endphp

<div class="max-w-7xl mx-auto space-y-8 pb-12">

  {{-- Flash Messages --}}
  @if(session('ok'))
    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 font-medium flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      {{ session('ok') }}
    </div>
  @endif
  @if(session('info'))
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 font-medium flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ session('info') }}
    </div>
  @endif
  @if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-medium flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold text-text-main">Membership Saya</h1>
      <p class="mt-1 text-text-soft">Kelola status membership, masa aktif, dan riwayat paket Anda.</p>
    </div>
    <div class="shrink-0">
      <a href="{{ route('app.memberships.plans') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-100 rounded-xl text-sm font-bold text-text-main hover:text-tosca hover:border-tosca/30 transition-colors shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V5a1 1 0 1 1 2 0v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6Z"></path></svg>
        Jelajahi Paket
      </a>
    </div>
  </div>

  {{-- Kartu Status Terkini --}}
  <section>
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8 relative overflow-hidden group hover:border-tosca/30 transition-all">
      {{-- Decorative Bg --}}
      @if($current && $current->status === 'active')
        <div class="absolute top-0 right-0 w-64 h-64 bg-tosca-light opacity-30 rounded-full blur-3xl -mt-20 -mr-20 pointer-events-none group-hover:opacity-60 transition-opacity"></div>
      @elseif($current && $current->status === 'pending')
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-50 rounded-full blur-3xl -mt-20 -mr-20 pointer-events-none"></div>
      @endif

      <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-8">
        <div class="flex-1">
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Membership Saat Ini</div>

          @if($current)
            @php
              $badgeClass = match($current->status) {
                'active' => 'bg-green-100 text-green-700 border-green-200',
                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                default => 'bg-gray-100 text-gray-700 border-gray-200',
              };
              $pct = $calcPct($current->activated_at, $current->expires_at);
              $left = $daysLeft($current->expires_at);
            @endphp

            <div class="text-3xl font-extrabold text-text-main mb-4 flex items-center gap-3">
              {{ $current->plan->name ?? 'Plan' }}
              <span class="px-3 py-1 rounded-lg border text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                {{ ucfirst($current->status) }}
              </span>
            </div>

            <div class="flex flex-wrap items-center gap-3 mb-6">
              @if($current->activated_at)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-sm font-semibold text-text-soft">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  Aktif: {{ Carbon::parse($current->activated_at)->format('d M Y, H:i') }}
                </div>
              @endif
              @if($current->expires_at)
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-sm font-semibold text-text-soft">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Berakhir: {{ Carbon::parse($current->expires_at)->format('d M Y, H:i') }}
                </div>
              @endif
              @if($current->status === 'pending')
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-100 text-sm font-bold text-amber-700">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Menunggu Konfirmasi Pembayaran
                </div>
              @endif
            </div>

            @if($current->status === 'active' && $current->expires_at)
              <div class="max-w-md bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <div class="flex items-center justify-between text-sm font-bold mb-2">
                  <span class="text-text-main">Masa Aktif Berjalan</span>
                  <span class="text-tosca">{{ $pct }}%</span>
                </div>
                <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-tosca rounded-full" style="width: {{ max(0,min(100,$pct)) }}%"></div>
                </div>
                <div class="mt-2 text-xs font-bold text-right">
                  @if($left !== null)
                    @if($left <= 0)
                      <span class="text-red-500">Telah Berakhir</span>
                    @elseif($left === 1)
                      <span class="text-amber-500">1 hari tersisa</span>
                    @else
                      <span class="text-text-soft">{{ $left }} hari tersisa</span>
                    @endif
                  @else
                    <span class="text-text-soft">—</span>
                  @endif
                </div>
              </div>
            @endif

          @else
            <div class="text-2xl font-extrabold text-text-main mb-2">Belum Memiliki Paket Aktif</div>
            <p class="text-text-soft">Anda saat ini menggunakan versi gratis. Tingkatkan pengalaman belajar Anda dengan memilih paket membership premium.</p>
          @endif
        </div>

        {{-- Actions (current) --}}
        <div class="shrink-0 w-full md:w-64 flex flex-col gap-3">
          @if($current)
            @if($current->status === 'pending')
              <a href="{{ route('app.memberships.checkout', $current) }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full">
                Lanjut Bayar
              </a>
              <button type="button" class="flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors w-full" onclick="location.reload()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Cek Status
              </button>

              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}" class="js-cancel-form">
                @csrf
                <input type="hidden" name="ack" value="0">
                <button type="submit" class="flex items-center justify-center gap-2 px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-colors w-full">
                  Batalkan Transaksi
                </button>
              </form>

            @elseif($current->status === 'active')
              @php $left = $daysLeft($current->expires_at); @endphp
              @if(($left ?? 0) <= 7 && $left !== null)
                <a href="{{ route('app.memberships.plans') }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full">
                  Perpanjang Paket
                </a>
              @endif

              <form method="POST" action="{{ route('app.memberships.cancel', $current) }}" class="js-cancel-form">
                @csrf
                <input type="hidden" name="ack" value="0">
                <button type="submit" class="flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-gray-100 text-gray-600 font-bold rounded-xl hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors w-full">
                  Batalkan Langganan
                </button>
              </form>
            @else
              <a href="{{ route('app.memberships.plans') }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full">
                Pilih Paket Baru
              </a>
            @endif
          @else
            <a href="{{ route('app.memberships.plans') }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full">
              Pilih Paket Premium
            </a>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- Riwayat --}}
  <section>
    <h2 class="text-xl font-bold text-text-main mb-4 flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-softbg text-tosca-dark flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      Riwayat Membership
    </h2>

    @if($history->count())
      {{-- Mobile: cards --}}
      <div class="grid gap-4 sm:hidden">
        @foreach($history as $m)
          @php
            $badgeClass = match($m->status) {
              'active' => 'bg-green-100 text-green-700',
              'pending' => 'bg-amber-100 text-amber-700',
              default => 'bg-gray-100 text-gray-700',
            };
          @endphp
          <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex justify-between items-start mb-3">
              <div class="font-bold text-lg text-text-main">{{ $m->plan->name ?? 'Plan' }}</div>
              <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">{{ ucfirst($m->status) }}</span>
            </div>
            
            <div class="space-y-2 mb-4 text-sm font-medium text-text-soft">
              <div class="flex justify-between border-b border-gray-50 pb-2">
                <span>Diaktifkan:</span>
                <span class="text-text-main">{{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y') : '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span>Berakhir:</span>
                <span class="text-text-main">{{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y') : '—' }}</span>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">
              @if($m->status === 'pending')
                <a href="{{ route('app.memberships.checkout', $m) }}" class="flex-1 text-center py-2 bg-tosca text-white font-bold rounded-lg text-sm">Checkout</a>
                <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form flex-1">
                  @csrf
                  <input type="hidden" name="ack" value="0">
                  <button type="submit" class="w-full py-2 bg-red-50 text-red-600 font-bold rounded-lg text-sm">Batal</button>
                </form>
              @elseif($m->status === 'active')
                <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form w-full">
                  @csrf
                  <input type="hidden" name="ack" value="0">
                  <button type="submit" class="w-full py-2 bg-gray-100 text-gray-600 font-bold rounded-lg text-sm">Nonaktifkan</button>
                </form>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      {{-- Desktop: table --}}
      <div class="hidden sm:block bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
              <th class="px-6 py-4 font-bold">Paket</th>
              <th class="px-6 py-4 font-bold">Status</th>
              <th class="px-6 py-4 font-bold">Tanggal Aktif</th>
              <th class="px-6 py-4 font-bold">Tanggal Berakhir</th>
              <th class="px-6 py-4 font-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @foreach($history as $m)
              @php
                $badgeClass = match($m->status) {
                  'active' => 'bg-green-100 text-green-700 border-green-200',
                  'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                  default => 'bg-gray-100 text-gray-700 border-gray-200',
                };
              @endphp
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-bold text-text-main whitespace-nowrap">
                  {{ $m->plan->name ?? 'Plan' }}
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                    {{ ucfirst($m->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-text-soft whitespace-nowrap">
                  {{ $m->activated_at ? Carbon::parse($m->activated_at)->format('d M Y, H:i') : '—' }}
                </td>
                <td class="px-6 py-4 text-sm font-medium text-text-soft whitespace-nowrap">
                  {{ $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y, H:i') : '—' }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    @if($m->status === 'pending')
                      <a href="{{ route('app.memberships.checkout', $m) }}" class="px-4 py-2 bg-tosca text-white font-bold rounded-lg text-xs hover:bg-tosca-dark transition-colors">Checkout</a>
                      
                      <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                        @csrf
                        <input type="hidden" name="ack" value="0">
                        <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 font-bold rounded-lg text-xs hover:bg-red-100 transition-colors">Batal</button>
                      </form>
                    @elseif($m->status === 'active')
                      <form method="POST" action="{{ route('app.memberships.cancel', $m) }}" class="js-cancel-form">
                        @csrf
                        <input type="hidden" name="ack" value="0">
                        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg text-xs hover:bg-gray-200 transition-colors">Nonaktifkan</button>
                      </form>
                    @else
                      <span class="text-text-soft font-medium text-sm">—</span>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-6 flex justify-center">
        {{ $history->links() }}
      </div>
    @else
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-12 text-center flex flex-col items-center justify-center">
        <div class="w-16 h-16 rounded-2xl bg-softbg text-tosca flex items-center justify-center mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="text-xl font-bold text-text-main mb-2">Belum Ada Riwayat</div>
        <p class="text-text-soft mb-6">Mulai berlangganan untuk mendapatkan akses penuh ke materi pembelajaran premium.</p>
        <a href="{{ route('app.memberships.plans') }}" class="px-6 py-3 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">Jelajahi Paket</a>
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
          title: 'Batalkan Membership?',
          html: `
            <div class="text-left mt-4 bg-gray-50 p-4 rounded-xl text-sm border border-gray-200">
              <ul class="space-y-2 font-medium text-gray-700">
                <li class="flex items-start gap-2 text-red-600">
                  <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  Membership akan langsung dinonaktifkan.
                </li>
                <li class="flex items-start gap-2">
                  <svg class="w-5 h-5 shrink-0 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Akses materi premium akan dicabut.
                </li>
                <li class="flex items-start gap-2">
                  <svg class="w-5 h-5 shrink-0 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Sisa masa aktif hangus dan pembayaran bersifat non-refundable.
                </li>
              </ul>
            </div>
            <label class="mt-4 flex items-center gap-3 text-sm font-bold text-gray-800 bg-white border border-gray-200 p-3 rounded-lg cursor-pointer hover:bg-gray-50">
              <input type="checkbox" id="ack-cancel" class="w-5 h-5 rounded text-tosca focus:ring-tosca">
              <span class="text-left">Saya memahami dan setuju dengan konsekuensi ini.</span>
            </label>
          `,
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#9ca3af',
          confirmButtonText: 'Ya, Batalkan',
          cancelButtonText: 'Kembali',
          reverseButtons: true,
          focusConfirm: false,
          customClass: {
            title: 'font-bold text-gray-900',
            confirmButton: 'font-bold rounded-xl px-6 py-3',
            cancelButton: 'font-bold rounded-xl px-6 py-3',
            popup: 'rounded-3xl'
          },
          preConfirm: () => {
            const ok = document.getElementById('ack-cancel')?.checked;
            if (!ok) {
              Swal.showValidationMessage('Anda harus menyetujui konsekuensi di atas terlebih dahulu.');
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