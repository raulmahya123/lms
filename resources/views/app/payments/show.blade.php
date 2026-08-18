@extends('layouts.app')
@section('title','Detail Pembayaran '.$payment->reference)

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        Detail Transaksi
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Invoice Pembayaran</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
    </div>
  </div>

  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col md:flex-row">
    {{-- Bagian Kiri: Info Utama --}}
    <div class="flex-1 p-8 md:p-10 border-b md:border-b-0 md:border-r border-gray-50">
      
      @php
        $status = strtolower($payment->status ?? 'pending');
        $map = [
          'paid'       => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'],
          'success'    => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'],
          'settlement' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'],
          'pending'    => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
          'failed'     => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'],
          'deny'       => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'],
          'cancel'     => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'],
          'expired'    => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'],
          'refund'     => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>'],
          'refunded'   => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>'],
        ];
        $style = $map[$status] ?? ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'];
      @endphp

      <div class="flex items-center justify-between mb-8">
        <div>
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Status Tagihan</div>
          <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border {{ $style['bg'] }} {{ $style['border'] }} {{ $style['text'] }} font-bold text-sm uppercase tracking-wider">
            {!! $style['icon'] !!}
            {{ ucfirst($payment->status) }}
          </div>
        </div>
        <div class="text-right">
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Total Nominal</div>
          <div class="text-3xl font-extrabold text-tosca-dark">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Nomor Referensi</div>
          <div class="flex items-center justify-between gap-2">
            <div id="refText" class="font-mono text-sm font-bold text-text-main break-all">{{ $payment->reference }}</div>
            <button type="button" id="btnCopyRef" class="shrink-0 p-2 text-gray-400 hover:text-tosca hover:bg-white rounded-lg transition-colors border border-transparent hover:border-gray-200" title="Salin">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </button>
          </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Waktu Transaksi</div>
          <div class="font-bold text-text-main">
            {{ optional($payment->paid_at)->format('d M Y, H:i') ?? 'Belum Dibayar' }}
          </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 sm:col-span-2">
          <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Item Pesanan</div>
          <div class="font-bold text-text-main text-lg">
            @if($payment->plan)
              Paket Membership: <strong>{{ $payment->plan->name }}</strong>
            @elseif($payment->course)
              Kursus: <strong>{{ $payment->course->title }}</strong>
            @else
              —
            @endif
          </div>
        </div>
      </div>

      {{-- Info Tambahan (Gateway/Method) --}}
      @if(isset($payment->method) || isset($payment->gateway) || isset($payment->order_id) || isset($payment->va_number))
        <h3 class="text-sm font-bold text-text-main mt-8 mb-4 border-b border-gray-100 pb-2">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          @isset($payment->method)
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Metode Bayar</div>
              <div class="font-bold text-text-main">{{ $payment->method }}</div>
            </div>
          @endisset

          @isset($payment->gateway)
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Gateway</div>
              <div class="font-bold text-text-main">{{ $payment->gateway }}</div>
            </div>
          @endisset

          @isset($payment->order_id)
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Order ID</div>
              <div class="font-bold text-text-main font-mono text-sm break-all">{{ $payment->order_id }}</div>
            </div>
          @endisset

          @isset($payment->va_number)
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Nomor Virtual Account</div>
              <div class="font-bold text-text-main font-mono text-sm break-all">{{ $payment->va_number }}</div>
            </div>
          @endisset
        </div>
      @endif
    </div>

    {{-- Bagian Kanan: Aksi --}}
    <div class="w-full md:w-80 p-8 bg-softbg/30 flex flex-col justify-between">
      <div class="space-y-4">
        <h3 class="font-bold text-text-main mb-6">Tindakan</h3>
        
        @if(in_array($status,['pending']))
          @if(isset($payment->pay_url))
            <a href="{{ $payment->pay_url }}" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full text-center">
              Lanjutkan Pembayaran
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
          @elseif(Route::has('app.payments.resume'))
            <a href="{{ route('app.payments.resume', $payment) }}" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 w-full text-center">
              Lanjutkan Pembayaran
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
          @endif
          <button type="button" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors w-full" onclick="location.reload()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Cek Status Terbaru
          </button>
        @endif

        @if(in_array($status,['paid','success','settlement','refunded']))
          @if(Route::has('app.payments.invoice'))
            <a href="{{ route('app.payments.invoice', $payment) }}" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-white border-2 border-tosca text-tosca font-bold rounded-xl hover:bg-tosca hover:text-white transition-colors shadow-sm w-full text-center group">
              <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              Unduh Invoice PDF
            </a>
          @endif
        @endif

        @if(Route::has('app.payments.index'))
          <a href="{{ route('app.payments.index') }}" class="flex items-center justify-center gap-2 px-6 py-3.5 bg-transparent text-text-soft hover:text-tosca font-bold rounded-xl transition-colors w-full text-center">
            Ke Riwayat Pembayaran
          </a>
        @endif
      </div>

      <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-xs font-medium text-text-soft leading-relaxed flex items-start gap-2">
          <svg class="w-5 h-5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Simpan nomor referensi transaksi ini. Sertakan referensi tersebut jika Anda membutuhkan bantuan dari layanan bantuan kami.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnCopyRef');
  const refText = document.getElementById('refText')?.textContent?.trim() || '';
  if (btn && refText) {
    btn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(refText);
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        btn.classList.add('bg-green-50', 'border-green-200');
        setTimeout(() => {
          btn.innerHTML = originalHtml;
          btn.classList.remove('bg-green-50', 'border-green-200');
        }, 1500);
      } catch (e) {
        console.error(e);
        alert('Gagal menyalin referensi.');
      }
    });
  }
});
</script>
@endpush
