@extends('layouts.app')
@section('title', 'Pilih Paket Membership')

@section('content')
<div class="max-w-6xl mx-auto pb-12">
  <div class="text-center max-w-3xl mx-auto mb-12">
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-tosca-light text-tosca-dark text-sm font-bold uppercase tracking-wider mb-4 shadow-sm border border-tosca-light/50">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V5a1 1 0 1 1 2 0v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6Z"></path></svg>
      Membership
    </div>
    <h1 class="text-4xl md:text-5xl font-extrabold text-text-main mb-4 leading-tight">
      Pilih Paket Sesuai Kebutuhanmu
    </h1>
    <p class="text-lg text-text-soft">
      Dapatkan akses tak terbatas ke semua kelas, uji psikologi premium, dan benefit eksklusif lainnya. Batalkan kapan saja.
    </p>
  </div>

  @if($plans->count())
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 items-center max-w-5xl mx-auto">
      @foreach($plans as $plan)
        @php
          $isPremium = (stripos($plan->name, 'premium') !== false) || (stripos($plan->name, 'pro') !== false);
          $price = isset($plan->price) ? $plan->price : 0;
          $durationText = ($plan->period ?? 'monthly') === 'yearly' ? '/ 12 bulan' : '/ 30 hari';
        @endphp

        <div class="relative bg-white rounded-3xl border {{ $isPremium ? 'border-tosca shadow-[0_8px_30px_rgba(7,94,84,0.15)] scale-105 z-10' : 'border-gray-100 shadow-sm' }} overflow-hidden group hover:-translate-y-2 transition-all duration-300 flex flex-col h-full">
          @if($isPremium)
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-tosca to-green-400"></div>
            <div class="absolute top-0 right-0 bg-gradient-to-r from-tosca to-green-400 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-sm">Paling Populer</div>
          @endif
          
          <div class="p-8 flex-1">
            <h3 class="text-xl font-bold text-text-main mb-2">{{ $plan->name }}</h3>
            <p class="text-sm text-text-soft mb-6 h-10">{{ $plan->description ?? 'Akses penuh ke semua materi pembelajaran.' }}</p>
            
            <div class="mb-6">
              <span class="text-4xl font-extrabold {{ $isPremium ? 'text-tosca-dark' : 'text-text-main' }}">
                {{ $price > 0 ? 'Rp '.number_format($price, 0, ',', '.') : 'Gratis' }}
              </span>
              @if($price > 0)
                <span class="text-sm font-semibold text-text-soft">{{ $durationText }}</span>
              @endif
            </div>

            <ul class="space-y-4 text-sm font-medium text-text-main">
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Akses ke semua materi kelas</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Update materi secara berkala</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Dukungan forum komunitas Q&A</span>
              </li>
              @if($isPremium)
                <li class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="font-bold text-tosca-dark">Akses Tes Psikologi & IQ Premium</span>
                </li>
                <li class="flex items-start gap-3">
                  <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="font-bold text-tosca-dark">Sertifikat Digital (Resmi)</span>
                </li>
              @else
                <li class="flex items-start gap-3 opacity-50">
                  <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  <span class="line-through text-gray-500">Tes Psikologi & IQ Premium</span>
                </li>
                <li class="flex items-start gap-3 opacity-50">
                  <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  <span class="line-through text-gray-500">Sertifikat Digital</span>
                </li>
              @endif
            </ul>
          </div>
          
          <div class="p-8 pt-0 mt-auto">
            <form method="POST" action="{{ route('app.memberships.subscribe', $plan) }}">
              @csrf
              <button class="w-full py-3.5 rounded-xl font-bold transition-all shadow-sm {{ $isPremium ? 'bg-tosca text-white hover:bg-tosca-dark shadow-tosca/30' : 'bg-white border-2 border-tosca text-tosca hover:bg-tosca hover:text-white' }}">
                Pilih Paket
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="max-w-2xl mx-auto bg-white rounded-3xl border border-gray-50 p-12 text-center shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
      <div class="w-16 h-16 rounded-full bg-softbg text-tosca flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Paket</h3>
      <p class="text-text-soft">Maaf, saat ini belum ada paket membership yang tersedia.</p>
    </div>
  @endif

  <div class="text-center mt-12">
    <a href="{{ route('app.memberships.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-text-soft hover:text-tosca transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      Kembali ke Membership Saya
    </a>
  </div>
</div>
@endsection
