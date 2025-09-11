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
      <div class="mt-3 flex items-baseline gap-2">
        <div class="text-3xl font-bold">Rp {{ number_format($course->price,0,',','.') }}</div>
      </div>
      <button id="btnPay" class="mt-5 w-full px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">
        Bayar Sekarang
      </button>
      <p class="mt-3 text-xs text-gray-500">Dengan melanjutkan, Anda menyetujui syarat & ketentuan.</p>
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
document.getElementById('btnPay').addEventListener('click', async function(){
  try {
    const res = await fetch("{{ route('app.courses.snap', $course) }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json' }
    });
    const data = await res.json();
    if (!res.ok) { alert(data.message || 'Gagal membuat transaksi'); return; }

    if (data.free) { window.location = "{{ route('app.my.courses') }}"; return; }

    window.snap.pay(data.snap_token, {
      onSuccess: () => window.location = "{{ route('app.my.courses') }}",
      onPending: () => window.location = "{{ route('app.my.courses') }}",
      onError:   (e) => { console.error(e); alert('Pembayaran gagal'); },
      onClose:   () => alert('Popup ditutup sebelum bayar'),
    });
  } catch (e) {
    alert(e.message || 'Error memulai pembayaran');
    console.error(e);
  }
});
</script>
@endsection
