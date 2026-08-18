@extends('layouts.app')
@section('title','Riwayat Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Pembayaran
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Riwayat Pembayaran</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('app.memberships.plans') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Membership Baru
      </a>
    </div>
  </div>

  {{-- Toolbar: Search + Filter (client-side) --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      
      <div class="flex-1 flex flex-col sm:flex-row gap-4 w-full">
        <div class="relative flex-1 max-w-sm">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input type="text" id="q" class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-500" placeholder="Cari ref / item...">
        </div>
        
        <div class="relative w-full sm:w-48">
          <select id="status" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
            <option value="">Semua Status</option>
            <option value="paid">Selesai (Paid)</option>
            <option value="pending">Menunggu (Pending)</option>
            <option value="failed">Gagal (Failed)</option>
            <option value="refunded">Dikembalikan (Refunded)</option>
          </select>
          <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>

        <button type="button" id="btnClear" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors shrink-0">
          Reset Filter
        </button>
      </div>

      {{-- Legenda visual --}}
      <div class="flex items-center gap-4 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 shrink-0">
        <div class="text-sm font-bold text-text-soft">Total: {{ $items->total() }}</div>
        <div class="flex gap-2">
          <div class="w-3 h-3 rounded-full bg-green-500" title="Paid"></div>
          <div class="w-3 h-3 rounded-full bg-amber-500" title="Pending"></div>
          <div class="w-3 h-3 rounded-full bg-red-500" title="Failed"></div>
        </div>
      </div>
    </div>
  </div>

  @php
    $statusClass = function($s){
      return match(strtolower((string)$s)){
        'paid','success','settlement' => 'bg-green-100 text-green-700 border-green-200',
        'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
        'failed','deny','cancel' => 'bg-red-100 text-red-700 border-red-200',
        'refunded','refund' => 'bg-blue-100 text-blue-700 border-blue-200',
        default    => 'bg-gray-100 text-gray-700 border-gray-200'
      };
    };
  @endphp

  @if($items->count())
    {{-- Mobile cards --}}
    <div class="grid gap-4 sm:hidden" id="list-cards">
      @foreach($items as $p)
        @php
          $itemLabel = $p->plan ? "Membership: {$p->plan->name}" : ($p->course ? "Course: {$p->course->title}" : '—');
          $pill = $statusClass($p->status);
          $statusVal = strtoupper($p->status);
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 js-row" data-ref="{{ $p->reference }}" data-item="{{ $itemLabel }}" data-status="{{ strtolower($p->status) }}">
          <div class="flex justify-between items-start mb-3">
            <div class="font-bold text-text-main leading-tight max-w-[70%] line-clamp-2">{{ $itemLabel }}</div>
            <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider border {{ $pill }}">{{ $statusVal }}</span>
          </div>
          
          <div class="space-y-2 mb-4 text-sm font-medium text-text-soft">
            <div class="flex justify-between border-b border-gray-50 pb-2">
              <span>Tanggal:</span>
              <span class="text-text-main">{{ $p->created_at?->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-50 pb-2">
              <span>No. Ref:</span>
              <div class="flex items-center gap-2">
                <span class="font-mono text-text-main text-xs truncate max-w-[100px]" id="ref-{{ $p->id }}">{{ $p->reference }}</span>
                <button class="text-tosca hover:text-tosca-dark js-copy p-1" data-target="#ref-{{ $p->id }}" title="Salin">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>
              </div>
            </div>
            <div class="flex justify-between items-center pt-1">
              <span>Total Bayar:</span>
              <span class="text-lg font-bold text-text-main">Rp {{ number_format($p->amount,0,',','.') }}</span>
            </div>
          </div>

          <a href="{{ route('app.payments.show',$p) }}" class="flex items-center justify-center w-full py-2 bg-gray-50 hover:bg-gray-100 text-text-main font-bold rounded-lg border border-gray-200 transition-colors text-sm">
            Lihat Detail
          </a>
        </div>
      @endforeach
    </div>

    {{-- Desktop table --}}
    <div class="hidden sm:block bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
      <table class="w-full text-left border-collapse" id="table">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
            <th class="px-6 py-4 font-bold">Tanggal</th>
            <th class="px-6 py-4 font-bold">No. Referensi</th>
            <th class="px-6 py-4 font-bold">Item Pesanan</th>
            <th class="px-6 py-4 font-bold text-right">Total Bayar</th>
            <th class="px-6 py-4 font-bold text-center">Status</th>
            <th class="px-6 py-4 font-bold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($items as $p)
            @php
              $itemLabel = $p->plan ? "Membership: {$p->plan->name}" : ($p->course ? "Course: {$p->course->title}" : '—');
              $pill = $statusClass($p->status);
              $statusVal = strtoupper($p->status);
            @endphp
            <tr class="hover:bg-gray-50/50 transition-colors js-row" data-ref="{{ $p->reference }}" data-item="{{ $itemLabel }}" data-status="{{ strtolower($p->status) }}">
              <td class="px-6 py-4 text-sm font-medium text-text-soft whitespace-nowrap">
                {{ $p->created_at?->format('d M Y, H:i') }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span class="font-mono text-sm font-bold text-text-main" id="refd-{{ $p->id }}">{{ $p->reference }}</span>
                  <button class="text-gray-400 hover:text-tosca transition-colors js-copy" data-target="#refd-{{ $p->id }}" title="Salin Referensi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                  </button>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="font-bold text-text-main line-clamp-1 max-w-xs">{{ $itemLabel }}</div>
              </td>
              <td class="px-6 py-4 text-right">
                <span class="font-extrabold text-text-main whitespace-nowrap">Rp {{ number_format($p->amount,0,',','.') }}</span>
              </td>
              <td class="px-6 py-4 text-center">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $pill }}">
                  {{ $statusVal }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <a href="{{ route('app.payments.show',$p) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-xs hover:bg-gray-50 hover:border-gray-300 transition-colors">
                  Detail
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-6 flex justify-center">
      {{ $items->withQueryString()->links() }}
    </div>
  @else
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-12 text-center flex flex-col items-center justify-center">
      <div class="w-20 h-20 rounded-3xl bg-softbg text-tosca flex items-center justify-center mb-6">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
      </div>
      <h3 class="text-2xl font-bold text-text-main mb-2">Belum Ada Transaksi</h3>
      <p class="text-text-soft mb-8">Riwayat pembayaran Anda akan muncul di sini setelah Anda melakukan pembelian atau berlangganan membership.</p>
      
      <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('app.memberships.plans') }}" class="px-8 py-3.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
          Lihat Paket Membership
        </a>
        <a href="{{ route('app.courses.index') }}" class="px-8 py-3.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
          Cari Kursus
        </a>
      </div>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Copy reference (mobile + desktop)
  document.querySelectorAll('.js-copy').forEach(btn => {
    btn.addEventListener('click', async () => {
      const sel = btn.getAttribute('data-target');
      const el = document.querySelector(sel);
      if (!el) return;
      const text = el.textContent.trim();
      try {
        await navigator.clipboard.writeText(text);
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        setTimeout(() => btn.innerHTML = originalHtml, 1500);
      } catch (e) { alert('Gagal menyalin.'); }
    });
  });

  // Client-side filter
  const qEl = document.getElementById('q');
  const statusEl = document.getElementById('status');
  const btnClear = document.getElementById('btnClear');

  function match(row, q, s) {
    const ref  = (row.dataset.ref || '').toLowerCase();
    const item = (row.dataset.item || '').toLowerCase();
    const st   = (row.dataset.status || '').toLowerCase();
    const okQ  = !q || ref.includes(q) || item.includes(q);
    const okS  = !s || st === s;
    return okQ && okS;
  }

  function applyFilter(){
    const q = (qEl.value || '').toLowerCase().trim();
    const s = (statusEl.value || '').toLowerCase().trim();

    let visibleCount = 0;
    document.querySelectorAll('.js-row').forEach(row => {
      const isVisible = match(row, q, s);
      row.style.display = isVisible ? '' : 'none';
      if(isVisible) visibleCount++;
    });
  }

  qEl?.addEventListener('input', applyFilter);
  statusEl?.addEventListener('change', applyFilter);
  btnClear?.addEventListener('click', () => {
    qEl.value = ''; statusEl.value = ''; applyFilter();
  });
});
</script>
@endpush
