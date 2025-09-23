@extends('app.layouts.base')
@section('title','Payments')

@push('styles')
<style>
  :root{
    --indigo:#2563eb;      /* blue-600 elegan */
    --indigo-700:#1d4ed8;  /* blue-700 */
    --soft:#e5e7eb;        /* gray-200 */
    --muted:#6b7280;       /* gray-500 */
  }
  .card{background:#fff;border:1px solid var(--soft);border-radius:16px;box-shadow:0 2px 6px rgba(0,0,0,.05)}
  .btn{border-radius:12px;padding:.55rem .9rem;font-weight:700}
  .btn-muted{background:#fff;border:1px solid var(--soft);color:#111827}
  .btn-muted:hover{background:#f9fafb}
  .btn-primary{background:linear-gradient(90deg,var(--indigo),#4f46e5);color:#fff}
  .btn-primary:hover{background:linear-gradient(90deg,var(--indigo-700),#4338ca)}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .55rem;border-radius:999px;border:1px solid var(--soft);background:#f8fafc;font-size:.72rem}
  .pill{padding:.2rem .55rem;border-radius:999px;font-size:.72rem;font-weight:800;letter-spacing:.2px;border:1px solid transparent}
  .pill-paid{background:#dcfce7;color:#166534;border-color:#bbf7d0}
  .pill-pending{background:#fef3c7;color:#92400e;border-color:#fde68a}
  .pill-failed{background:#fee2e2;color:#991b1b;border-color:#fecaca}
  .pill-refunded{background:#e0f2ff;color:#075985;border-color:#bae6fd}
  .table{min-width:100%}
  .table th{font-size:.75rem;font-weight:800;color:#6b7280;text-transform:none;white-space:nowrap}
  .table td{font-size:.92rem}
  .hover-row:hover{background:#f8fafc}
  .field{border:1px solid var(--soft);border-radius:12px;padding:.55rem .8rem}
  .field:focus{outline:none;box-shadow:0 0 0 4px #bfdbfe;border-color:var(--indigo)}
  .search-wrap{gap:.5rem}
  @media (min-width:640px){ .search-wrap{gap:.75rem} }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Riwayat Pembayaran</h1>
      <p class="text-sm text-slate-600 mt-1">Lihat semua transaksi yang pernah kamu lakukan.</p>
    </div>
    <a href="{{ route('home') }}" class="text-sm text-indigo-700 hover:underline">← Beranda</a>
  </div>

  {{-- Toolbar: Search + Filter (client-side, halaman aktif) --}}
  <div class="card p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex items-center search-wrap">
        <input id="q" class="field w-full sm:w-72" placeholder="Cari ref / item di halaman ini…">
        <select id="status" class="field w-full sm:w-40">
          <option value="">Semua status</option>
          <option value="paid">Paid</option>
          <option value="pending">Pending</option>
          <option value="failed">Failed</option>
          <option value="refunded">Refunded</option>
        </select>
        <button id="btnClear" class="btn btn-muted">Reset</button>
      </div>

      {{-- Quick legend --}}
      <div class="flex flex-wrap items-center gap-2 text-xs">
        <span class="chip">Total: {{ $items->total() }}</span>
        {{-- Legenda visual (tanpa fungsi klik) --}}
        <span class="pill pill-paid">PAID</span>
        <span class="pill pill-pending">PENDING</span>
        <span class="pill pill-failed">FAILED</span>
        <span class="pill pill-refunded">REFUNDED</span>
      </div>
    </div>
  </div>

  @php
    $statusClass = function($s){
      return match(strtolower((string)$s)){
        'paid','success','settlement' => 'pill-paid',
        'pending'  => 'pill-pending',
        'failed','deny','cancel' => 'pill-failed',
        'refunded','refund' => 'pill-refunded',
        default    => 'pill-pending'
      };
    };
  @endphp

  @if($items->count())
    {{-- Mobile cards --}}
    <div class="grid gap-3 sm:hidden" id="list-cards">
      @foreach($items as $p)
        @php
          $itemLabel = $p->plan ? "Plan: {$p->plan->name}" : ($p->course ? "Course: {$p->course->title}" : '—');
          $pill = $statusClass($p->status);
          $statusVal = strtoupper($p->status);
        @endphp
        <div class="card p-4 js-row" data-ref="{{ $p->reference }}" data-item="{{ $itemLabel }}" data-status="{{ strtolower($p->status) }}">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm text-slate-500">{{ $p->created_at?->format('d M Y, H:i') }}</div>
              <div class="font-semibold text-slate-900 mt-0.5 truncate" title="{{ $itemLabel }}">{{ $itemLabel }}</div>

              <div class="mt-1 text-sm text-slate-600 flex items-center gap-2">
                <span>Ref:</span>
                <span class="font-mono break-all" id="ref-{{ $p->id }}">{{ $p->reference }}</span>
                <button class="text-indigo-700 hover:underline text-xs js-copy" data-target="#ref-{{ $p->id }}">Salin</button>
              </div>

              <div class="mt-2 flex items-center gap-2">
                <span class="pill {{ $pill }}">{{ $statusVal }}</span>
                <span class="chip">Rp {{ number_format($p->amount,0,',','.') }}</span>
              </div>
            </div>
            <div class="shrink-0">
              <a href="{{ route('app.payments.show',$p) }}" class="btn btn-muted text-sm">Detail</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Desktop table --}}
    <div class="hidden sm:block card overflow-hidden">
      <table class="table" id="table">
        <thead class="bg-gray-50">
          <tr class="text-left">
            <th class="p-3">Tanggal</th>
            <th class="p-3">Ref</th>
            <th class="p-3">Item</th>
            <th class="p-3 text-right">Jumlah</th>
            <th class="p-3 text-center">Status</th>
            <th class="p-3 text-right"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($items as $p)
            @php
              $itemLabel = $p->plan ? "Plan: {$p->plan->name}" : ($p->course ? "Course: {$p->course->title}" : '—');
              $pill = $statusClass($p->status);
              $statusVal = strtoupper($p->status);
            @endphp
            <tr class="hover-row js-row" data-ref="{{ $p->reference }}" data-item="{{ $itemLabel }}" data-status="{{ strtolower($p->status) }}">
              <td class="p-3 text-slate-600 whitespace-nowrap">{{ $p->created_at?->format('d M Y, H:i') }}</td>
              <td class="p-3 font-mono text-slate-800 break-all">
                <span id="refd-{{ $p->id }}">{{ $p->reference }}</span>
                <button class="ml-2 text-indigo-700 hover:underline text-xs js-copy" data-target="#refd-{{ $p->id }}">Salin</button>
              </td>
              <td class="p-3 text-slate-900">{{ $itemLabel }}</td>
              <td class="p-3 text-right font-semibold">Rp {{ number_format($p->amount,0,',','.') }}</td>
              <td class="p-3 text-center">
                <span class="pill {{ $pill }}">{{ $statusVal }}</span>
              </td>
              <td class="p-3 text-right">
                <a href="{{ route('app.payments.show',$p) }}" class="btn btn-muted">Detail</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $items->withQueryString()->links() }}
    </div>
  @else
    <div class="p-10 text-center card">
      <div class="text-slate-900 font-semibold text-lg">Belum ada pembayaran</div>
      <div class="text-slate-600 mt-1">Transaksi kamu akan tampil di sini setelah checkout.</div>
      <div class="mt-4 flex items-center justify-center gap-2">
        <a href="{{ route('app.memberships.plans') }}" class="btn btn-primary">Lihat Paket</a>
        <a href="{{ route('app.courses.index') }}" class="btn btn-muted">Cari Kursus</a>
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
        const old = btn.textContent;
        btn.textContent = 'Disalin ✓';
        setTimeout(() => btn.textContent = old, 1200);
      } catch (e) { alert('Gagal menyalin.'); }
    });
  });

  // Client-side filter (pada halaman aktif)
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

    document.querySelectorAll('.js-row').forEach(row => {
      row.style.display = match(row, q, s) ? '' : 'none';
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
