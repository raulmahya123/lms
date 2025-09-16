@extends('app.layouts.base')
@section('title', 'Checkout Membership')

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
      <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Checkout Membership</h1>
      <a href="{{ route('app.memberships.index') }}" class="text-sm text-blue-700 hover:underline">Kembali</a>
    </div>
    <p class="mt-1 text-sm text-gray-600">Selesaikan pembayaran. Setelah berhasil, status akan berubah otomatis via webhook.</p>
  </header>

  <div class="grid md:grid-cols-[1fr_18rem] gap-6 items-start">
    {{-- Ringkasan --}}
    <div class="p-5 rounded-lg border bg-white">
      <h2 class="text-base font-semibold text-gray-900">Ringkasan</h2>
      <dl class="mt-3 divide-y divide-gray-100 text-sm">
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Paket</dt>
          <dd class="font-medium text-gray-900">{{ $membership->plan->name ?? 'Plan' }}</dd>
        </div>
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Durasi</dt>
          <dd class="font-medium text-gray-900">
            @if(($membership->plan->period ?? 'monthly') === 'yearly')
              12 bulan
            @else
              30 hari
            @endif
          </dd>
        </div>
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Harga</dt>
          <dd class="font-medium text-gray-900">
            {{ isset($membership->plan->price) ? 'Rp '.number_format($membership->plan->price,0,',','.') : 'Gratis' }}
          </dd>
        </div>
      </dl>

      <div class="mt-5">
        <h3 class="text-sm font-semibold text-gray-900">Instruksi Pembayaran</h3>
        <div class="mt-2 text-sm text-gray-600 space-y-2">
          <p>Gunakan tombol di samping untuk membuka Snap Midtrans. Setelah selesai, Anda akan diarahkan kembali dan status akan otomatis diperbarui oleh sistem.</p>
          <ul class="list-disc pl-5">
            <li>Metode: Transfer bank / e-wallet / kartu.</li>
            <li>Konfirmasi otomatis via webhook (tidak perlu upload bukti).</li>
          </ul>
        </div>
      </div>

      <div class="mt-6 rounded border bg-gray-50 p-3">
        <div class="text-sm text-gray-700 mb-2 font-medium">Catatan</div>
        <p class="text-xs text-gray-500">Jika popup ditutup sebelum pembayaran, Anda bisa buka lagi dari halaman ini.</p>
      </div>
    </div>

    {{-- Sidebar --}}
    <aside class="p-5 rounded-lg border bg-white">
      <h2 class="text-base font-semibold text-gray-900">Total</h2>
      <div class="mt-3 flex items-baseline gap-2">
        <div class="text-3xl font-bold text-gray-900">
          {{ isset($membership->plan->price) ? 'Rp '.number_format($membership->plan->price,0,',','.') : 'Gratis' }}
        </div>
        <div class="text-sm text-gray-500">
          / {{ ($membership->plan->period ?? 'monthly') === 'yearly' ? '12 bulan' : '30 hari' }}
        </div>
      </div>

      <button id="btnPay"
          class="mt-5 w-full px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">
          Bayar Sekarang
      </button>

      <p class="mt-3 text-xs text-gray-500">
        Dengan melanjutkan, Anda menyetujui syarat & ketentuan yang berlaku.
      </p>
    </aside>
  </div>
</div>

@php
  $clientKey = config('services.midtrans.client_key');
  $isSandbox = !config('services.midtrans.is_production');
@endphp
<script type="text/javascript"
  src="https://app{{ $isSandbox ? '.sandbox' : '' }}.midtrans.com/snap/snap.js"
  data-client-key="{{ $clientKey }}"></script>

<script>
(function() {
  const btn  = document.getElementById('btnPay');
  const csrf = '{{ csrf_token() }}';

  // ⬇️ pakai relative path agar tidak tergantung APP_URL
  const startSnapUrl = "{{ route('app.memberships.snap', $membership, /* absolute */ false) }}";

  async function startSnap() {
    const res  = await fetch(startSnapUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      credentials: 'same-origin' // kirim cookie session auth
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data?.message || 'Gagal membuat transaksi');
    return data;
  }

  btn.addEventListener('click', async function () {
    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Memproses…';

    try {
      const data = await startSnap();
      if (data.free) return location.href = "{{ route('app.memberships.index') }}";

      const snapToken   = data.snap_token;
      const redirectUrl = data.redirect_url;

      if (!window.snap || !snapToken) {
        if (redirectUrl) return location.href = redirectUrl;
        throw new Error('Token pembayaran tidak tersedia.');
      }

      window.snap.pay(snapToken, {
        onSuccess: () => location.href = "{{ route('app.memberships.index') }}",
        onPending: () => location.href = "{{ route('app.memberships.index') }}",
        onError:   (e) => { console.error(e); alert('Pembayaran gagal.'); },
        onClose:   ()  => alert('Popup ditutup sebelum bayar')
      });
    } catch (e) {
      alert(e.message || 'Error memulai pembayaran');
      console.error(e);
    } finally {
      btn.disabled  = false;
      btn.textContent = original;
    }
  });
})();
</script>

@endsection
