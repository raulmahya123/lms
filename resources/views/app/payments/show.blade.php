@extends('app.layouts.base')
@section('title','Payment '.$payment->reference)

@push('styles')
<style>
  :root{
    --border:#e5e7eb;       /* gray-200 */
    --card:#ffffff;         /* white */
    --ink:#0f172a;          /* slate-900 */
    --muted:#6b7280;        /* gray-500 */
    --blue:#2563eb;         /* blue-600 */
    --blue-700:#1d4ed8;     /* blue-700 */
    --ring:#bfdbfe;         /* blue-200 */
  }
  .card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.25rem;box-shadow:0 2px 6px rgba(0,0,0,.05)}
  .soft{border:1px solid var(--border);border-radius:12px}
  .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem .95rem;border-radius:12px;font-weight:700}
  .btn-primary{background:linear-gradient(90deg,var(--blue),#4f46e5);color:#fff}
  .btn-primary:hover{background:linear-gradient(90deg,var(--blue-700),#4338ca)}
  .btn-muted{background:#fff;border:1px solid var(--border);color:#111827}
  .btn-muted:hover{background:#f9fafb}
  .pill{display:inline-flex;align-items:center;gap:.4rem;border-radius:999px;padding:.25rem .6rem;font-weight:700;font-size:.72rem;border:1px solid transparent}
  .pill-paid{background:#dcfce7;color:#166534;border-color:#bbf7d0}
  .pill-pending{background:#fef3c7;color:#92400e;border-color:#fde68a}
  .pill-failed{background:#fee2e2;color:#991b1b;border-color:#fecaca}
  .pill-expired{background:#f3f4f6;color:#374151;border-color:#e5e7eb}
  .pill-refunded{background:#e0f2fe;color:#075985;border-color:#bae6fd}
  /* focus ring halus */
  a:focus, button:focus {outline:none; box-shadow:0 0 0 4px var(--ring)}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-3 mb-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">Payment Detail</h1>
      <p class="text-sm text-slate-600 mt-1">Ringkasan transaksi & status pembayaran Anda.</p>
    </div>
    <div class="shrink-0">
      <a href="{{ url()->previous() }}" class="btn btn-muted">Kembali</a>
    </div>
  </div>

  {{-- Kartu Utama --}}
  <div class="card">
    <div class="grid md:grid-cols-3 gap-16">
      {{-- Kolom Kiri (informasi utama) --}}
      <div class="md:col-span-2 space-y-4">
        {{-- Reference + Copy --}}
        <div>
          <div class="text-xs uppercase tracking-wide text-slate-500">Reference</div>
          <div class="mt-1 flex items-center gap-2">
            <div id="refText" class="font-semibold text-slate-900 text-lg break-all">
              {{ $payment->reference }}
            </div>
            <button type="button" id="btnCopyRef" class="btn btn-muted" title="Salin Reference">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16 1a4 4 0 0 1 4 4v9a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4V5a4 4 0 0 1 4-4h7Zm2 13V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2ZM7 21a2 2 0 0 1-2-2V8a1 1 0 1 1 2 0v11h9a1 1 0 1 1 0 2H7Z"/></svg>
              Salin
            </button>
          </div>
        </div>

        {{-- Amount --}}
        <div class="grid grid-cols-2 gap-4">
          <div class="p-3 soft">
            <div class="text-xs uppercase tracking-wide text-slate-500">Amount</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900">
              Rp {{ number_format($payment->amount,0,',','.') }}
            </div>
          </div>

          {{-- Status --}}
          <div class="p-3 soft">
            <div class="text-xs uppercase tracking-wide text-slate-500">Status</div>
            @php
              $status = strtolower($payment->status ?? 'pending');
              $map = [
                'paid'     => 'pill-paid',
                'success'  => 'pill-paid',
                'settlement' => 'pill-paid',
                'pending'  => 'pill-pending',
                'failed'   => 'pill-failed',
                'deny'     => 'pill-failed',
                'cancel'   => 'pill-failed',
                'expired'  => 'pill-expired',
                'refund'   => 'pill-refunded',
                'refunded' => 'pill-refunded',
              ];
              $cls = $map[$status] ?? 'pill-pending';
            @endphp
            <div class="mt-1">
              <span class="pill {{ $cls }}">
                {{-- ikon kecil status --}}
                @if(in_array($status,['paid','success','settlement']))
                  ✓
                @elseif(in_array($status,['failed','deny','cancel']))
                  ✕
                @elseif($status === 'expired')
                  ⌛
                @elseif(in_array($status,['refund','refunded']))
                  ↺
                @else
                  ●
                @endif
                {{ ucfirst($payment->status) }}
              </span>
            </div>
          </div>
        </div>

        {{-- Waktu Bayar --}}
        <div class="p-3 soft">
          <div class="text-xs uppercase tracking-wide text-slate-500">Paid at</div>
          <div class="mt-1 font-semibold text-slate-900">
            {{ optional($payment->paid_at)->format('d M Y H:i') ?? '—' }}
          </div>
        </div>

        {{-- Item --}}
        <div class="p-3 soft">
          <div class="text-xs uppercase tracking-wide text-slate-500">Item</div>
          <div class="mt-1 text-slate-900">
            @if($payment->plan)
              Plan <strong>{{ $payment->plan->name }}</strong>
            @elseif($payment->course)
              Course <strong>{{ $payment->course->title }}</strong>
            @else
              —
            @endif
          </div>
        </div>

        {{-- Info tambahan opsional (hanya muncul jika ada) --}}
        <div class="grid md:grid-cols-2 gap-4">
          @isset($payment->method)
            <div class="p-3 soft">
              <div class="text-xs uppercase tracking-wide text-slate-500">Metode</div>
              <div class="mt-1 font-semibold text-slate-900">{{ $payment->method }}</div>
            </div>
          @endisset

          @isset($payment->gateway)
            <div class="p-3 soft">
              <div class="text-xs uppercase tracking-wide text-slate-500">Gateway</div>
              <div class="mt-1 font-semibold text-slate-900">{{ $payment->gateway }}</div>
            </div>
          @endisset

          @isset($payment->order_id)
            <div class="p-3 soft">
              <div class="text-xs uppercase tracking-wide text-slate-500">Order ID</div>
              <div class="mt-1 font-semibold text-slate-900 break-all">{{ $payment->order_id }}</div>
            </div>
          @endisset

          @isset($payment->va_number)
            <div class="p-3 soft">
              <div class="text-xs uppercase tracking-wide text-slate-500">VA Number</div>
              <div class="mt-1 font-semibold text-slate-900 break-all">{{ $payment->va_number }}</div>
            </div>
          @endisset
        </div>
      </div>

      {{-- Kolom Kanan (aksi) --}}
      <aside class="md:col-span-1">
        <div class="space-y-3">
          {{-- CTA berdasarkan status --}}
          @if(in_array($status,['pending']))
            @if(isset($payment->pay_url))
              <a href="{{ $payment->pay_url }}" class="btn btn-primary w-full text-center">Lanjutkan Pembayaran</a>
            @elseif(Route::has('app.payments.resume'))
              <a href="{{ route('app.payments.resume', $payment) }}" class="btn btn-primary w-full text-center">Lanjutkan Pembayaran</a>
            @endif
            <button type="button" class="btn btn-muted w-full" onclick="location.reload()">Cek Status</button>
          @endif

          @if(in_array($status,['paid','success','settlement','refunded']))
            @if(Route::has('app.payments.invoice'))
              <a href="{{ route('app.payments.invoice', $payment) }}" class="btn btn-primary w-full text-center">Unduh Invoice</a>
            @endif
          @endif

          {{-- Kembali ke daftar/riwayat --}}
          @if(Route::has('app.payments.index'))
            <a href="{{ route('app.payments.index') }}" class="btn btn-muted w-full text-center">Ke Riwayat Pembayaran</a>
          @else
            <a href="{{ url()->previous() }}" class="btn btn-muted w-full text-center">Kembali</a>
          @endif
        </div>

        {{-- Catatan --}}
        <div class="mt-4 p-3 soft text-sm text-slate-600">
          Simpan reference pembayaran Anda. Jika ada kendala, hubungi support dan sertakan reference di atas.
        </div>
      </aside>
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
        btn.textContent = 'Disalin ✓';
        setTimeout(() => (btn.textContent = 'Salin'), 1400);
      } catch (e) {
        console.error(e);
        alert('Gagal menyalin reference.');
      }
    });
  }
});
</script>
@endpush
