@extends('layouts.app')
@section('title', 'Checkout Membership')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
  
  {{-- Header --}}
  <div class="mb-8 flex items-center justify-between">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        Pembayaran
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Selesaikan Pembayaran</h1>
    </div>
    <a href="{{ route('app.memberships.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-text-soft hover:text-tosca transition-colors">
      Batal
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </a>
  </div>

  {{-- Flash --}}
  @if(session('ok'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 font-medium flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      {{ session('ok') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-medium flex items-center gap-3">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      {{ $errors->first() }}
    </div>
  @endif

  <div class="grid lg:grid-cols-[1fr_380px] gap-8 items-start">
    {{-- Bagian Kiri: Ringkasan & Kupon --}}
    <div class="space-y-6">
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8">
        <h2 class="text-xl font-bold text-text-main mb-6 flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-softbg text-tosca flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
          </div>
          Detail Pesanan
        </h2>
        
        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 mb-8">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-tosca to-green-400 text-white flex items-center justify-center shrink-0 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">{{ $membership->plan->name ?? 'Paket Premium' }}</h3>
            <p class="text-text-soft font-medium">Akses tak terbatas {{ ($membership->plan->period ?? 'monthly') === 'yearly' ? 'selama 12 bulan' : 'selama 30 hari' }}</p>
          </div>
        </div>

        <h2 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
          Gunakan Kupon Promo
        </h2>
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <input type="text" id="couponCode" class="w-full pl-11 pr-4 py-3 bg-white border-2 border-gray-100 rounded-xl focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400 uppercase" placeholder="KODEKUPON">
          </div>
          <button type="button" id="btnApplyCoupon" class="px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition-colors whitespace-nowrap">
            Terapkan
          </button>
        </div>
        <div id="couponInfo" class="mt-3 text-sm font-bold text-tosca hidden flex items-center gap-1.5"></div>
      </div>
      
      <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3 text-sm font-medium text-blue-800">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p>Proses pembayaran dijamin aman dan terenkripsi. Jika halaman pembayaran tertutup secara tidak sengaja, Anda dapat melanjutkannya kembali dari halaman ini.</p>
      </div>
    </div>

    {{-- Bagian Kanan: Total & Pay --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8 sticky top-6">
      <h2 class="text-xl font-bold text-text-main mb-6">Ringkasan Pembayaran</h2>

      <div class="space-y-4 mb-6">
        <div class="flex items-center justify-between text-text-soft font-medium">
          <span>Harga Paket</span>
          <span id="priceRaw" class="text-text-main font-bold">Rp {{ number_format($membership->plan->price ?? 0,0,',','.') }}</span>
        </div>
        
        <div id="rowDiscount" class="flex items-center justify-between font-bold text-tosca hidden">
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Potongan Promo
          </span>
          <span id="priceDiscount">- Rp 0</span>
        </div>
        
        <div class="pt-4 border-t border-dashed border-gray-200">
          <div class="flex items-center justify-between mb-1">
            <span class="font-bold text-text-main">Total Tagihan</span>
            <span id="priceFinal" class="text-3xl font-extrabold text-gray-900">Rp {{ number_format($membership->plan->price ?? 0,0,',','.') }}</span>
          </div>
          <p class="text-xs text-text-soft text-right">Sudah termasuk PPN (Jika ada)</p>
        </div>
      </div>

      <button id="btnPay" class="w-full py-4 bg-tosca text-white font-bold text-lg rounded-xl hover:bg-tosca-dark transition-all shadow-sm shadow-tosca/30 flex items-center justify-center gap-2 group">
        <span>Bayar Sekarang</span>
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>

      <div class="mt-6 flex items-center justify-center gap-4 text-gray-400">
        <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <span class="text-[10px] font-bold uppercase tracking-widest">Secure Payment by Midtrans</span>
      </div>
    </div>
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
      couponInfoEl.classList.remove('hidden', 'text-tosca', 'text-red-500');
      couponInfoEl.classList.add('text-amber-500');
      couponInfoEl.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Masukkan kode kupon terlebih dahulu.';
      return;
    }

    try {
      btnApply.disabled = true;
      btnApply.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
      
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

        couponInfoEl.classList.remove('hidden', 'text-tosca', 'text-amber-500');
        couponInfoEl.classList.add('text-red-500');
        couponInfoEl.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${(data && data.reason) ? data.reason : 'Kupon tidak valid.'}`;
        return;
      }

      appliedCouponId = data.coupon_id;
      discountAmount  = data.discount_amount;
      finalAmount     = data.final_amount;

      rowDiscountEl.classList.remove('hidden');
      priceDiscEl.textContent  = '- ' + rupiah(discountAmount);
      priceFinalEl.textContent = rupiah(finalAmount);

      couponInfoEl.classList.remove('hidden', 'text-red-500', 'text-amber-500');
      couponInfoEl.classList.add('text-tosca');
      couponInfoEl.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Berhasil diterapkan! Potongan ${data.discount_percent}%`;
    } catch (e) {
      console.error(e);
      couponInfoEl.classList.remove('hidden', 'text-tosca', 'text-amber-500');
      couponInfoEl.classList.add('text-red-500');
      couponInfoEl.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Terjadi kesalahan sistem.';
    } finally {
      btnApply.disabled = false;
      btnApply.textContent = 'Terapkan';
    }
  }

  btnApply.addEventListener('click', applyCoupon);

  btnPay.addEventListener('click', async function(){
    const original = btnPay.innerHTML;
    btnPay.disabled = true;
    btnPay.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyiapkan Pembayaran...</span>';

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
        onClose:   ()  => alert('Popup ditutup sebelum bayar. Anda dapat melanjutkannya nanti.'),
      });
    } catch (e) {
      alert(e.message || 'Error memulai pembayaran');
      console.error(e);
    } finally {
      btnPay.disabled = false;
      btnPay.innerHTML = original;
    }
  });
})();
</script>
@endsection
