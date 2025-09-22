@extends('app.layouts.base')
@section('title','Checkout Course')

@section('content')
<div class="max-w-3xl mx-auto">
  <header class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl md:text-3xl font-semibold">Checkout: {{ $course->title }}</h1>
    <a href="{{ route('app.courses.index') }}" class="text-sm text-blue-700 hover:underline">Kembali</a>
  </header>

  <div class="grid md:grid-cols-[1fr_18rem] gap-6">
    <div class="p-5 rounded-lg border bg-white">
      <h2 class="text-base font-semibold">Ringkasan</h2>
      <dl class="mt-3 text-sm divide-y divide-gray-100">
        <div class="flex justify-between py-2">
          <dt class="text-gray-600">Judul</dt><dd class="font-medium">{{ $course->title }}</dd>
        </div>
        <div class="flex justify-between py-2">
          <dt class="text-gray-600">Harga</dt>
          <dd class="font-medium">Rp {{ number_format($course->price,0,',','.') }}</dd>
        </div>
      </dl>
    </div>

    <aside class="p-5 rounded-lg border bg-white">
  <h2 class="text-base font-semibold">Total</h2>

  {{-- Kupon --}}
  <div class="mt-3">
    <label class="text-sm font-medium text-gray-700">Kode Kupon</label>
    <div class="mt-1 flex gap-2">
      <input id="couponCode" class="border rounded px-3 py-2 flex-1" placeholder="MASUKKAN KODE">
      <button id="btnApplyCoupon" type="button" class="px-3 py-2 rounded bg-gray-900 text-white">Terapkan</button>
    </div>
    <p id="couponMsg" class="mt-2 text-xs"></p>
  </div>

  {{-- Ringkasan harga dinamis --}}
  <div class="mt-4 text-sm space-y-1">
    <div class="flex justify-between">
      <span>Harga</span>
      <span id="priceRaw">Rp {{ number_format($course->price,0,',','.') }}</span>
    </div>
    <div id="rowDiscount" class="flex justify-between hidden">
      <span>Diskon</span>
      <span id="discountText">- Rp 0</span>
    </div>
    <hr class="my-2">
    <div class="flex justify-between font-semibold">
      <span>Total</span>
      <span id="totalText">Rp {{ number_format($course->price,0,',','.') }}</span>
    </div>
  </div>

  <button id="btnPay" class="mt-5 w-full px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">
    Bayar Sekarang
  </button>
  <p class="mt-3 text-xs text-gray-500">Dengan melanjutkan, Anda menyetujui syarat & ketentuan.</p>
</aside>

@php
  $clientKey = config('services.midtrans.client_key');
  $isSandbox = !config('services.midtrans.is_production');
@endphp
<script type="text/javascript"
  src="https://app{{ $isSandbox ? '.sandbox' : '' }}.midtrans.com/snap/snap.js"
  data-client-key="{{ $clientKey }}">
</script>

<script>
const priceOriginal = @json((int) ($course->price ?? 0));
let applied = { coupon_id: null, discount: 0, final: priceOriginal, code: null };

const elMsg   = document.getElementById('couponMsg');
const elRowD  = document.getElementById('rowDiscount');
const elDisc  = document.getElementById('discountText');
const elTotal = document.getElementById('totalText');

function fmt(x){ return new Intl.NumberFormat('id-ID').format(x); }

document.getElementById('btnApplyCoupon').addEventListener('click', async function(){
  const code = document.getElementById('couponCode').value.trim();
  if (!code) {
    elMsg.textContent = 'Masukkan kode terlebih dulu.';
    elMsg.className   = 'mt-2 text-xs text-red-600';
    return;
  }

  try {
    const res = await fetch(@json(route('app.coupons.validate')), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': @json(csrf_token()),
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        code: code,
        amount: @json((int) ($course->price ?? 0)),
        course_id: @json($course->id)
      })
    });

    if (!res.ok) {
      const text = await res.text();
      let msg = 'Kupon tidak valid';
      try {
        const j = JSON.parse(text);
        if (j?.reason) msg = j.reason;
        else if (j?.errors) {
          const firstKey = Object.keys(j.errors)[0];
          msg = j.errors[firstKey]?.[0] ?? msg;
        }
      } catch (_) { msg = text.slice(0,160); }

      applied = { coupon_id: null, discount: 0, final: priceOriginal, code: null };
      elRowD.classList.add('hidden'); elDisc.textContent = '- Rp 0';
      elTotal.textContent = 'Rp ' + fmt(priceOriginal);
      elMsg.textContent = msg;
      elMsg.className = 'mt-2 text-xs text-red-600';
      return;
    }

    const data = await res.json();
    if (!data.valid) {
      applied = { coupon_id: null, discount: 0, final: priceOriginal, code: null };
      elRowD.classList.add('hidden');
      elDisc.textContent  = '- Rp 0';
      elTotal.textContent = 'Rp ' + fmt(priceOriginal);
      elMsg.textContent   = (data?.reason || 'Kupon tidak valid');
      elMsg.className     = 'mt-2 text-xs text-red-600';
      return;
    }

    applied = {
      coupon_id: data.coupon_id,
      discount: Math.round(data.discount_amount),
      final:    Math.max(0, Math.round(data.final_amount)),
      code
    };
    elRowD.classList.remove('hidden');
    elDisc.textContent  = '- Rp ' + fmt(applied.discount);
    elTotal.textContent = 'Rp ' + fmt(applied.final);
    elMsg.textContent   = 'Kupon diterapkan (' + (data.discount_percent ?? '-') + '%).';
    elMsg.className     = 'mt-2 text-xs text-emerald-700';
  } catch (e) {
    applied = { coupon_id: null, discount: 0, final: priceOriginal, code: null };
    elRowD.classList.add('hidden');
    elDisc.textContent  = '- Rp 0';
    elTotal.textContent = 'Rp ' + fmt(priceOriginal);
    elMsg.textContent   = e.message || 'Gagal validasi kupon';
    elMsg.className     = 'mt-2 text-xs text-red-600';
  }
});

document.getElementById('btnPay').addEventListener('click', async function(){
  try {
    const res  = await fetch(@json(route('app.courses.snap', $course)), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': @json(csrf_token()),
        'Accept':'application/json',
        'Content-Type':'application/json'
      },
      body: JSON.stringify({
        coupon_id: applied.coupon_id,
        coupon_code: applied.code
      })
    });

    const data = await res.json();
    if (!res.ok) { alert(data.message || 'Gagal membuat transaksi'); return; }

    if (data.free) {
      window.location = @json(route('app.my.courses'));
      return;
    }

    const orderId = data.order_id;
    window.snap.pay(data.snap_token, {
      onSuccess: () => window.location = @json(route('app.payments.finish')) + '?order_id=' + encodeURIComponent(orderId),
      onPending: () => window.location = @json(route('app.payments.finish')) + '?order_id=' + encodeURIComponent(orderId),
      onError:   (e) => { console.error(e); alert('Pembayaran gagal'); },
      onClose:   () => alert('Popup ditutup sebelum bayar')
    });
  } catch (e) {
    alert(e.message || 'Error memulai pembayaran');
    console.error(e);
  }
});
</script>
@endsection
