@extends('app.layouts.base')
@section('title', 'Paket Membership')

@section('content')
<div class="max-w-6xl mx-auto">
  <header class="mb-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Paket Membership</h1>
      <a href="{{ route('app.memberships.index') }}"
         class="text-sm text-blue-700 hover:underline">Kembali ke Membership</a>
    </div>
    <p class="mt-1 text-sm text-gray-600">Pilih paket yang sesuai. Anda dapat melanjutkan ke checkout setelah memilih.</p>
  </header>

  @if($plans->count())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($plans as $plan)
        <div class="p-5 rounded-lg border bg-white flex flex-col">
          <div class="text-lg font-semibold text-gray-900">{{ $plan->name }}</div>
          <div class="mt-2">
            <div class="text-2xl font-bold text-gray-900">
              {{ isset($plan->price) ? 'Rp '.number_format($plan->price,0,',','.') : 'Gratis' }}
            </div>
            <div class="text-sm text-gray-500">
              Durasi {{ $plan->duration_days ?? 30 }} hari
            </div>
          </div>
          <ul class="mt-4 text-sm text-gray-600 space-y-1">
            {{-- contoh benefit, sesuaikan jika punya kolom/relasi features --}}
            <li>✔ Akses materi premium</li>
            <li>✔ Update berkala</li>
            <li>✔ Dukungan komunitas</li>
          </ul>
          <form method="POST" action="{{ route('app.memberships.subscribe', $plan) }}" class="mt-5">
            @csrf
            <button class="w-full px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
              Pilih Paket
            </button>
          </form>
        </div>
      @endforeach
    </div>
  @else
    <div class="rounded border bg-white p-6 text-center text-sm text-gray-500">
      Belum ada paket yang tersedia.
    </div>
  @endif
</div>
@endsection
