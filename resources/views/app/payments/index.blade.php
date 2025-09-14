@extends('app.layouts.base')
@section('title','Payments')

@push('styles')
<style>
  :root{
    --indigo:#4f46e5;      /* indigo-600 */
    --indigo-700:#4338ca;  /* indigo-700 */
    --soft:#e5e7eb;
  }
  .card{background:#fff;border:1px solid var(--soft);border-radius:16px}
  .btn{border-radius:12px;padding:.55rem .9rem;font-weight:600}
  .btn-muted{background:#fff;border:1px solid var(--soft)}
  .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .55rem;border-radius:999px;border:1px solid var(--soft);background:#f8fafc;font-size:.72rem}
  .pill{padding:.2rem .55rem;border-radius:999px;font-size:.72rem;font-weight:700}
  .pill-paid{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
  .pill-pending{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
  .pill-failed{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
  .pill-refunded{background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe}
  .table{min-width:100%}
  .table th{font-size:.75rem;font-weight:700;color:#6b7280;text-transform:none}
  .table td{font-size:.9rem}
  .hover-row:hover{background:#f8fafc}
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-4">
    <h1 class="text-2xl font-bold text-slate-900">Riwayat Pembayaran</h1>
    <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline">← Beranda</a>
  </div>

  {{-- Quick legend --}}
  <div class="flex flex-wrap items-center gap-2 text-xs">
    <span class="chip">Total: {{ $items->total() }}</span>
    @php
      $statusClass = function($s){
        return match($s){
          'paid'     => 'pill-paid',
          'pending'  => 'pill-pending',
          'failed'   => 'pill-failed',
          'refunded' => 'pill-refunded',
          default    => 'pill-pending'
        };
      };
    @endphp
  </div>

  @if($items->count())
    {{-- Mobile cards --}}
    <div class="grid gap-3 sm:hidden">
      @foreach($items as $p)
        @php
          $itemLabel = $p->plan ? "Plan: {$p->plan->name}" : ($p->course ? "Course: {$p->course->title}" : '—');
          $pill = $statusClass($p->status);
        @endphp
        <div class="card p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm text-slate-500">{{ $p->created_at?->format('d M Y, H:i') }}</div>
              <div class="font-semibold text-slate-900 mt-0.5">{{ $itemLabel }}</div>
              <div class="mt-1 text-sm text-slate-600">Ref: <span class="font-mono">{{ $p->reference }}</span></div>
              <div class="mt-2 flex items-center gap-2">
                <span class="pill {{ $pill }}">{{ strtoupper($p->status) }}</span>
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
      <table class="table">
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
            @endphp
            <tr class="hover-row">
              <td class="p-3 text-slate-600 whitespace-nowrap">{{ $p->created_at?->format('d M Y, H:i') }}</td>
              <td class="p-3 font-mono text-slate-800">{{ $p->reference }}</td>
              <td class="p-3 text-slate-900">{{ $itemLabel }}</td>
              <td class="p-3 text-right font-semibold">Rp {{ number_format($p->amount,0,',','.') }}</td>
              <td class="p-3 text-center">
                <span class="pill {{ $pill }}">{{ strtoupper($p->status) }}</span>
              </td>
              <td class="p-3 text-right">
                <a href="{{ route('app.payments.show',$p) }}" class="text-indigo-700 hover:underline">Detail</a>
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
        <a href="{{ route('app.memberships.plans') }}" class="btn btn-muted">Lihat Paket</a>
        <a href="{{ route('app.courses.index') }}" class="btn btn-muted">Cari Kursus</a>
      </div>
    </div>
  @endif
</div>
@endsection
