@extends('app.layouts.base')
@section('title', 'Checkout Membership')

@push('styles')
<style>
  .field {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: .55rem .8rem
  }
  .field:focus {
    outline: none;
    box-shadow: 0 0 0 4px #bfdbfe;
    border-color: #3b82f6
  }
  .btn {
    border-radius: 12px;
    padding: .6rem 1rem;
    font-weight: 600;
    transition: .15s ease
  }
  .btn-primary {
    background: #2563eb;
    color: #fff
  }
  .btn-primary:hover {
    background: #1d4ed8
  }
  .chip {
    display: inline-flex;
    gap: .4rem;
    align-items: center;
    font-size: .8rem;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    padding: .25rem .6rem;
    background: #f8fafc
  }
  .card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 6px rgba(0,0,0,.05);
  }
  #btnPay {
    background: linear-gradient(to right, #2563eb, #4f46e5);
    color: #fff;
    font-weight: 600;
    border-radius: 12px;
    padding: .7rem 1rem;
    transition: .2s ease;
  }
  #btnPay:hover {
    background: linear-gradient(to right, #1e40af, #4338ca);
  }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
      {{ session('ok') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
      {{ $errors->first() }}
    </div>
  @endif

  <header class="mb-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-extrabold text-gray-900">Checkout Membership</h1>
      <a href="{{ route('app.memberships.index') }}" class="text-sm text-blue-700 hover:underline">Kembali</a>
    </div>
    <p class="mt-1 text-sm text-gray-600">Masukkan kode kupon jika ada. Nominal akan dihitung otomatis.</p>
  </header>

  <div class="grid md:grid-cols-[1fr_18rem] gap-6 items-start">
    {{-- Ringkasan --}}
    <div class="card">
      <h2 class="text-base font-semibold text-gray-900">Ringkasan</h2>
      <dl class="mt-3 divide-y divide-gray-100 text-sm">
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Paket</dt>
          <dd class="font-medium text-gray-900">{{ $membership->plan->name ?? 'Plan' }}</dd>
        </div>
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Durasi</dt>
          <dd class="font-medium text-gray-900">
            @if(($membership->plan->period ?? 'monthly') === 'yearly') 12 bulan @else 30 hari @endif
          </dd>
        </div>
      </dl>

      {{-- Kupon --}}
      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Kupon</label>
        <div class="flex gap-2">
          <input id="couponCode" class="field flex-1" placeholder="contoh: HEMAT50">
          <button id="btnApplyCoupon" class="btn btn-primary">Terapkan</button>
        </div>
        <div id="couponInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
      </div>

      <div class="mt-6 rounded border bg-gray-50 p-3">
        <div class="text-sm text-gray-700 mb-2 font-medium">Catatan</div>
        <p class="text-xs text-gray-500">Jika popup ditutup sebelum pembayaran, Anda bisa buka lagi dari halaman ini.</p>
      </div>
    </div>

    {{-- Total --}}
    <aside class="card">
      <h2 class="text-base font-semibold text-gray-900">Total</h2>

      <div class="mt-3 space-y-1 text-sm text-gray-700">
        <div class="flex items-center justify-between">
          <span>Harga</span>
          <span id="priceRaw">Rp {{ number_format($membership->plan->price ?? 0,0,',','.') }}</span>
        </div>
        <div id="rowDiscount" class="flex items-center justify-between hidden">
          <span>Diskon</span>
          <span id="priceDiscount">- Rp 0</span>
        </div>
        <div class="flex items-center justify-between font-bold text-indigo-700 text-2xl mt-2 pt-2 border-t">
          <span>Bayar</span>
          <span id="priceFinal">Rp {{ number_format($membership->plan->price ?? 0,0,',','.') }}</span>
        </div>
      </div>

      <button id="btnPay" class="mt-5 w-full">Bayar Sekarang</button>

      <p class="mt-3 text-xs text-gray-500">Dengan melanjutkan, Anda menyetujui syarat & ketentuan yang berlaku.</p>
    </aside>
  </div>
</div>

@php
  $clientKey = config('services.midtrans.client_key');
  $isSandbox = !config('services.midtrans.is_production');
  $planId    = $membership->plan->id ?? null;
  $amount    = (int) ($membership->plan->price ?? 0);
@endphp
<script type="text/javascript"
  src="https://app{{ $isSandbox ? '.sandbox' : '' }}.midtrans.com/snap/snap.js"
  data-client-key="{{ $clientKey }}"></script>

<script>
(function() {
  const csrf          = '{{ csrf_token() }}';
  const startUrl      = "{{ route('app.memberships.snap', $membership, false) }}";
  const finishUrl     = "{{ route('app.memberships.finish') }}";
  const validateUrl   = "{{ route('app.coupons.validate') }}";

  const priceRawEl    = document.getElementById('priceRaw');
  const rowDiscountEl = document.getElementById('rowDiscount');
  const priceDiscEl   = document.getElementById('priceDiscount');
  const priceFinalEl  = document.getElementById('priceFinal');
  const couponInfoEl  = document.getElementById('couponInfo');

  const btnApply      = document.getElementById('btnApplyCoupon');
  const inputCode     = document.getElementById('couponCode');
  const btnPay        = document.getElementById('btnPay');

  const baseAmount    = {{ $amount }};
  let appliedCouponId = null;
  let finalAmount     = baseAmount;
  let discountAmount  = 0;

  function rupiah(n){ return 'Rp ' + (n||0).toLocaleString('id-ID'); }

  async function applyCoupon() {
    const code = (inputCode.value || '').trim();
    if (!code) {
      couponInfoEl.classList.remove('hidden');
      couponInfoEl.textContent = 'Masukkan kode kupon terlebih dahulu.';
      return;
    }

    try {
      const res = await fetch(validateUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
        body: JSON.stringify({
          code,
          amount: baseAmount,
          plan_id: "{{ $planId }}",
        })
      });
      const data = await res.json();

      if (!res.ok || !data.valid) {
        appliedCouponId = null;
        discountAmount  = 0;
        finalAmount     = baseAmount;

        rowDiscountEl.classList.add('hidden');
        priceDiscEl.textContent  = rupiah(0);
        priceFinalEl.textContent = rupiah(finalAmount);

        couponInfoEl.classList.remove('hidden');
        couponInfoEl.textContent = (data && data.reason) ? data.reason : 'Kupon tidak valid.';
        return;
      }

      appliedCouponId = data.coupon_id;
      discountAmount  = data.discount_amount;
      finalAmount     = data.final_amount;

      rowDiscountEl.classList.remove('hidden');
      priceDiscEl.textContent  = '- ' + rupiah(discountAmount);
      priceFinalEl.textContent = rupiah(finalAmount);

      couponInfoEl.classList.remove('hidden');
      couponInfoEl.textContent = `Kupon diterapkan: potongan ${data.discount_percent}%`;
    } catch (e) {
      console.error(e);
      couponInfoEl.classList.remove('hidden');
      couponInfoEl.textContent = 'Gagal memvalidasi kupon.';
    }
  }

  btnApply.addEventListener('click', applyCoupon);

  btnPay.addEventListener('click', async function(){
    const original = btnPay.textContent;
    btnPay.disabled = true;
    btnPay.textContent = 'Memproses…';

    try {
      const res = await fetch(startUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'application/json', 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
          coupon_id: appliedCouponId,
          coupon_code: inputCode.value || null
        })
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data?.message || 'Gagal membuat transaksi');

      if (data.free) {
        location.href = "{{ route('app.memberships.index') }}";
        return;
      }

      const orderId = data.order_id;

      if (!window.snap || !data.snap_token) {
        if (data.redirect_url) { location.href = data.redirect_url; return; }
        throw new Error('Token pembayaran tidak tersedia.');
      }

      window.snap.pay(data.snap_token, {
        onSuccess: () => location.href = finishUrl + '?order_id=' + encodeURIComponent(orderId),
        onPending: () => location.href = finishUrl + '?order_id=' + encodeURIComponent(orderId),
        onError:   (e) => { console.error(e); alert('Pembayaran gagal.'); },
        onClose:   ()  => alert('Popup ditutup sebelum bayar'),
      });
    } catch (e) {
      alert(e.message || 'Error memulai pembayaran');
      console.error(e);
    } finally {
      btnPay.disabled = false;
      btnPay.textContent = original;
    }
  });
})();
</script>
@endsection
