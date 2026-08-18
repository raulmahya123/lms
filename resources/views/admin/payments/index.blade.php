@extends('layouts.admin')
@section('title', 'Data Pembayaran — Admin')

@section('content')
@php
    $q = request('q') ?? '';
    $st = request('status');
    $pv = request('provider');
    $showFilters = request()->hasAny(['q', 'status', 'provider']);
    
    // Get unique providers for the filter dropdown
    $providersList = $providers ?? \App\Models\Payment::query()
        ->select('provider')
        ->whereNotNull('provider')
        ->distinct()
        ->pluck('provider')
        ->sort()
        ->values();
@endphp

<div x-data="{ q: @js($q), showFilters: @js($showFilters) }" class="space-y-6 pb-12">

    {{-- HEADER / ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Keuangan
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Pembayaran</h1>
            <p class="text-sm text-text-soft mt-1">Pantau seluruh transaksi, filter berdasarkan status dan penyedia (provider).</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
          class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Search --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" x-model="q" placeholder="Cari nama/email/invoice..."
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Pembayaran</label>
                <div class="relative">
                    <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="" @selected(!$st)>— Semua Status —</option>
                        <option value="pending" @selected($st==='pending')>Menunggu (Pending)</option>
                        <option value="paid" @selected($st==='paid')>Berhasil (Paid)</option>
                        <option value="failed" @selected($st==='failed')>Gagal (Failed)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Provider --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Penyedia Layanan (Provider)</label>
                <div class="relative">
                    <select name="provider" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="" @selected(!$pv)>— Semua Provider —</option>
                        @foreach($providersList as $pr)
                            <option value="{{ $pr }}" @selected($pv === $pr)>{{ strtoupper($pr) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-3 flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                @if ($showFilters)
                    <a href="{{ route('admin.payments.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                        Reset Filter
                    </a>
                @endif
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
        {{-- Header Strip --}}
        <div class="p-6 border-b border-gray-50 bg-softbg/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-bold text-text-main">Total {{ $items->total() }} Pembayaran</span>
                
                @if($q)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                        Cari: "{{ $q }}"
                    </span>
                @endif
                @if($st)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                        Status: {{ ucfirst($st) }}
                    </span>
                @endif
                @if($pv)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">
                        Provider: {{ strtoupper($pv) }}
                    </span>
                @endif
            </div>
            <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
                Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                        <th class="px-6 py-4 font-bold">Data Pengguna</th>
                        <th class="px-6 py-4 font-bold">Item & Jumlah</th>
                        <th class="px-6 py-4 font-bold text-center w-32">Status</th>
                        <th class="px-6 py-4 font-bold text-center w-36">Metode</th>
                        <th class="px-6 py-4 font-bold text-center w-40">Waktu Bayar</th>
                        <th class="px-6 py-4 font-bold text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $p)
                        @php
                            $paidAt = $p->paid_at ? \Illuminate\Support\Carbon::parse($p->paid_at)->timezone(config('app.timezone', 'UTC'))->format('d M Y, H:i') : '—';
                            $statusBadge = match($p->status){
                                'paid'    => ['bg-green-50', 'text-green-700', 'border-green-200', 'Berhasil', 'bg-green-500'],
                                'pending' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Menunggu', 'bg-amber-500'],
                                default   => ['bg-red-50', 'text-red-700', 'border-red-200', 'Gagal', 'bg-red-500'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-tosca-light flex items-center justify-center text-tosca-dark font-bold text-sm shrink-0 border border-tosca/20">
                                        {{ strtoupper(substr($p->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-text-main text-sm">{{ $p->user?->name ?? '—' }}</div>
                                        <div class="text-xs text-text-soft mt-0.5">{{ $p->user?->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1.5">
                                    <div class="font-bold text-tosca-dark bg-tosca-light/30 px-2.5 py-1 rounded-md border border-tosca/10 inline-block text-sm">
                                        Rp {{ number_format((float)$p->amount, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs font-medium text-gray-600 flex items-center gap-1.5 mt-1">
                                        @if($p->plan)   
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-400"></span> Paket: {{ $p->plan->name }}
                                        @elseif($p->course) 
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-400"></span> Course: {{ $p->course->title }}
                                        @else
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-400"></span> Item tidak diketahui
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $statusBadge[0] }} {{ $statusBadge[1] }} {{ $statusBadge[2] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge[4] }}"></span>
                                    {{ $statusBadge[3] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->provider)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ strtoupper($p->provider) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-bold">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="font-bold {{ $p->paid_at ? 'text-text-main' : 'text-gray-400' }} text-sm">{{ $paidAt }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if(Route::has('admin.payments.show'))
                                        <a href="{{ route('admin.payments.show', $p) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors" title="Lihat Detail & Update Status">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="py-16 text-center flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Pembayaran</h3>
                                    <p class="text-text-soft max-w-sm mb-6">Sistem belum mencatat adanya transaksi pembayaran, atau tidak ada yang sesuai dengan filter.</p>
                                    @if($showFilters)
                                        <a href="{{ route('admin.payments.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                                            Reset Filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@if (session('ok') || session('success'))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('ok') ?? session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: { popup: 'rounded-2xl' }
                });
            });
        </script>
    @endpush
@endif
@endsection
