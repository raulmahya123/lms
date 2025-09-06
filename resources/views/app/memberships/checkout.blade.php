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
    <p class="mt-1 text-sm text-gray-600">Selesaikan pembayaran untuk mengaktifkan membership Anda.</p>
  </header>

  <div class="grid md:grid-cols-[1fr_18rem] gap-6 items-start">
    {{-- Rincian --}}
    <div class="p-5 rounded-lg border bg-white">
      <h2 class="text-base font-semibold text-gray-900">Ringkasan</h2>
      <dl class="mt-3 divide-y divide-gray-100 text-sm">
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Paket</dt>
          <dd class="font-medium text-gray-900">{{ $membership->plan->name ?? 'Plan' }}</dd>
        </div>
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Durasi</dt>
          <dd class="font-medium text-gray-900">{{ $membership->plan->duration_days ?? 30 }} hari</dd>
        </div>
        <div class="flex items-center justify-between py-2">
          <dt class="text-gray-600">Harga</dt>
          <dd class="font-medium text-gray-900">
            {{ isset($membership->plan->price) ? 'Rp '.number_format($membership->plan->price,0,',','.') : 'Gratis' }}
          </dd>
        </div>
      </dl>

      {{-- Instruksi / integrasi payment gateway --}}
      <div class="mt-5">
        <h3 class="text-sm font-semibold text-gray-900">Instruksi Pembayaran</h3>
        <div class="mt-2 text-sm text-gray-600 space-y-2">
          <p>Silakan lanjutkan pembayaran melalui metode yang tersedia. Setelah pembayaran berhasil, membership akan aktif otomatis.</p>
          <ul class="list-disc pl-5">
            <li>Metode: Transfer bank / e-wallet / kartu (sesuaikan gateway).</li>
            <li>Konfirmasi otomatis via callback gateway.</li>
          </ul>
        </div>
      </div>

      {{-- Tombol simulasi aktivasi (untuk dev / tanpa gateway) --}}
      <div class="mt-6 rounded border bg-gray-50 p-3">
        <div class="text-sm text-gray-700 mb-2 font-medium">Uji Aktivasi Manual (Dev)</div>
        <form method="POST" action="{{ route('app.memberships.activate', $membership) }}" class="flex items-center gap-2">
          @csrf
          <input type="text" name="reference" placeholder="Nomor referensi (opsional)"
                 class="w-full rounded border-gray-300 focus:border-gray-400 focus:ring-0" />
          <button class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
            Tandai Sudah Bayar
          </button>
        </form>
        <p class="mt-2 text-xs text-gray-500">Di produksi, ini digantikan oleh callback dari payment gateway.</p>
      </div>
    </div>

    {{-- Ringkasan harga di sidebar --}}
    <aside class="p-5 rounded-lg border bg-white">
      <h2 class="text-base font-semibold text-gray-900">Total</h2>
      <div class="mt-3 flex items-baseline gap-2">
        <div class="text-3xl font-bold text-gray-900">
          {{ isset($membership->plan->price) ? 'Rp '.number_format($membership->plan->price,0,',','.') : 'Gratis' }}
        </div>
        <div class="text-sm text-gray-500">/ {{ $membership->plan->duration_days ?? 30 }} hari</div>
      </div>

      {{-- CTA contoh ke gateway --}}
      <button type="button"
              class="mt-5 w-full px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700"
              onclick="alert('Integrasikan tombol ini dengan payment gateway (Midtrans, Xendit, dsb).')">
        Bayar Sekarang
      </button>

      <p class="mt-3 text-xs text-gray-500">
        Dengan melanjutkan, Anda menyetujui syarat & ketentuan yang berlaku.
      </p>
    </aside>
  </div>
</div>
@endsection
