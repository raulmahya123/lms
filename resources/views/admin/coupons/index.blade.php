@extends('layouts.admin')
@section('title', 'Data Kupon — Admin')

@section('content')
@php
    $q = request('q') ?? '';
    $status = request('status');
    $showFilters = request()->hasAny(['q', 'status']);
@endphp

<div x-data="{ q: @js($q), showFilters: @js($showFilters) }" class="space-y-6 pb-12">

    {{-- HEADER / ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                Promosi & Diskon
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Kupon</h1>
            <p class="text-sm text-text-soft mt-1">Kelola kode promosi, masa berlaku, potongan harga, dan pantau penggunaannya.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
            <a href="{{ route('admin.coupons.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Kupon Baru
            </a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
          class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Search --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Kode Kupon</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" x-model="q" placeholder="Masukkan kode promo..."
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400 uppercase">
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Kupon</label>
                <div class="relative">
                    <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="" @selected(!$status)>— Semua Status —</option>
                        <option value="active" @selected($status === 'active')>Aktif (Active)</option>
                        <option value="expired" @selected($status === 'expired')>Kadaluarsa (Expired)</option>
                        <option value="scheduled" @selected($status === 'scheduled')>Terjadwal (Scheduled)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                @if ($showFilters)
                    <a href="{{ route('admin.coupons.index') }}"
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
                <span class="text-sm font-bold text-text-main">Total {{ $coupons->total() }} Kupon</span>
                
                @if($q)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                        Kode: "{{ $q }}"
                    </span>
                @endif
                @if($status)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                        Status: {{ ucfirst($status) }}
                    </span>
                @endif
            </div>
            <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
                Halaman {{ $coupons->currentPage() }} dari {{ $coupons->lastPage() }}
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                        <th class="px-6 py-4 font-bold">Kode Kupon</th>
                        <th class="px-6 py-4 font-bold w-40">Nilai Diskon</th>
                        <th class="px-6 py-4 font-bold text-center w-48">Masa Berlaku</th>
                        <th class="px-6 py-4 font-bold text-center w-32">Penggunaan</th>
                        <th class="px-6 py-4 font-bold text-right w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($coupons as $c)
                        @php
                            $from = $c->valid_from ? \Illuminate\Support\Carbon::parse($c->valid_from) : null;
                            $to = $c->valid_until ? \Illuminate\Support\Carbon::parse($c->valid_until) : null;
                            $now = now();
                            
                            $isActive = (!$from || $from->lte($now)) && (!$to || $to->gte($now));
                            $isExpired = $to && $to->lt($now);
                            
                            $statusBadge = $isActive
                                ? ['bg-green-50', 'text-green-700', 'border-green-200', 'Aktif', 'bg-green-500']
                                : ($isExpired
                                    ? ['bg-red-50', 'text-red-700', 'border-red-200', 'Kadaluarsa', 'bg-red-500']
                                    : ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Terjadwal', 'bg-amber-500']);
                                    
                            $used = $c->redemptions_count ?? ($c->relationLoaded('redemptions') ? $c->redemptions->count() : $c->redemptions()->count());
                            $limit = $c->usage_limit ?? '∞';
                            
                            // Check if limit reached
                            if ($isActive && $c->usage_limit && $used >= $c->usage_limit) {
                                $statusBadge = ['bg-gray-100', 'text-gray-700', 'border-gray-200', 'Habis (Limit)', 'bg-gray-500'];
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="inline-flex items-center w-fit px-3 py-1.5 bg-gray-900 text-white rounded-lg font-mono font-bold tracking-widest text-sm shadow-sm">
                                        {{ $c->code }}
                                    </div>
                                    @if ($c->max_discount_amount)
                                        <div class="text-[10px] font-bold text-text-soft bg-gray-100 w-fit px-2 py-0.5 rounded uppercase tracking-wide">
                                            Maks Diskon: Rp {{ number_format($c->max_discount_amount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if (!is_null($c->discount_percent))
                                    <div class="font-bold text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg inline-block text-sm">
                                        Diskon {{ rtrim(rtrim(number_format($c->discount_percent, 2), '0'), '.') }}%
                                    </div>
                                @elseif(!is_null($c->discount_amount))
                                    <div class="font-bold text-tosca-dark bg-tosca-light/30 border border-tosca/10 px-3 py-1.5 rounded-lg inline-block text-sm">
                                        Rp {{ number_format($c->discount_amount, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $statusBadge[0] }} {{ $statusBadge[1] }} {{ $statusBadge[2] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge[4] }}"></span>
                                        {{ $statusBadge[3] }}
                                    </span>
                                    <div class="text-[11px] text-gray-500 font-medium text-center leading-tight">
                                        {{ $from?->format('d M Y') ?: 'Sekarang' }}<br>
                                        <span class="text-gray-400 font-bold">s/d</span><br>
                                        {{ $to?->format('d M Y') ?: 'Selamanya' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl">
                                    <span class="font-extrabold text-text-main text-sm">{{ $used }}</span>
                                    <span class="text-gray-400 font-bold text-xs">/</span>
                                    <span class="font-bold text-text-soft text-sm">{{ $limit }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if (Route::has('admin.coupons.show'))
                                        <a href="{{ route('admin.coupons.show', $c) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors" title="Lihat Riwayat & Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('admin.coupons.edit', $c) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-tosca transition-colors" title="Edit Kupon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}" class="inline js-delete-form" data-title="Kupon {{ $c->code }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="js-delete-btn p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Kupon">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="py-16 text-center flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Kupon</h3>
                                    <p class="text-text-soft max-w-sm mb-6">Sistem belum memiliki data kode promo atau kupon yang sesuai dengan filter.</p>
                                    @if($showFilters)
                                        <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                                            Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.coupons.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Kupon Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
                {{ $coupons->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        function bindDeleteButtons() {
            document.querySelectorAll('.js-delete-btn').forEach(btn => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';

                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form.js-delete-form');
                    const title = form?.dataset.title || 'kupon ini';

                    Swal.fire({
                        title: 'Hapus Kupon?',
                        html: `Data <b>${title}</b> akan dihapus secara permanen. Pengguna tidak akan bisa menggunakan kupon ini lagi.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl font-bold px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white border-0',
                            cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border-0'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) {
                            if (!form.dataset.submitting) {
                                form.dataset.submitting = '1';
                                const b = form.querySelector('.js-delete-btn');
                                if (b) {
                                    b.disabled = true;
                                    b.innerHTML = '<span class="animate-spin mr-2">⏳</span>';
                                }
                                form.submit();
                            }
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', bindDeleteButtons);
        document.addEventListener('turbo:load', bindDeleteButtons);
        document.addEventListener('livewire:navigated', bindDeleteButtons);
    })();
</script>

@if (session('ok') || session('success'))
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
@endif
@endpush
@endsection
