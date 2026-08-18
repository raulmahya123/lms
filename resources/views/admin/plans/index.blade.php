@extends('layouts.admin')
@section('title', 'Paket Berlangganan — Admin')

@section('content')
@php 
    $q = request('q') ?? '';
    $period = request('period');
    $min = request('min');
    $max = request('max');
    $showFilters = request()->hasAny(['q', 'period', 'min', 'max']);
@endphp

<div x-data="{ q: @js($q), showFilters: @js($showFilters) }" class="space-y-6 pb-12">

    {{-- HEADER / ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Monetisasi
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Paket (Plans)</h1>
            <p class="text-sm text-text-soft mt-1">Kelola harga, periode, cakupan materi, & pantau jumlah pelanggan per paket.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
            <a href="{{ route('admin.plans.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Paket Baru
            </a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
          class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pencarian Nama</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" x-model="q" placeholder="Cari nama paket..."
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                </div>
            </div>

            {{-- Period --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Periode</label>
                <div class="relative">
                    <select name="period" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="" @selected(!$period)>— Semua Periode —</option>
                        <option value="monthly" @selected($period === 'monthly')>Bulanan (Monthly)</option>
                        <option value="yearly" @selected($period === 'yearly')>Tahunan (Yearly)</option>
                        <option value="lifetime" @selected($period === 'lifetime')>Selamanya (Lifetime)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Price range --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Rentang Harga (Rp)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="min" value="{{ $min }}" placeholder="Min"
                           class="w-1/2 px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                    <span class="text-gray-400 font-bold">-</span>
                    <input type="number" name="max" value="{{ $max }}" placeholder="Max"
                           class="w-1/2 px-3 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-4 flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                @if ($showFilters)
                    <a href="{{ route('admin.plans.index') }}"
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
                <span class="text-sm font-bold text-text-main">Total {{ $plans->total() }} Paket</span>
                
                @if($q)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                        Cari: "{{ $q }}"
                    </span>
                @endif
                @if($period)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                        Periode: {{ ucfirst($period) }}
                    </span>
                @endif
                @if($min || $max)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-green-50 text-green-700 border border-green-100">
                        Harga: {{ $min ?: '0' }} - {{ $max ?: '∞' }}
                    </span>
                @endif
            </div>
            <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
                Halaman {{ $plans->currentPage() }} dari {{ $plans->lastPage() }}
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                        <th class="px-6 py-4 font-bold">Informasi Paket</th>
                        <th class="px-6 py-4 font-bold w-40">Harga (Rp)</th>
                        <th class="px-6 py-4 font-bold text-center w-36">Periode</th>
                        <th class="px-6 py-4 font-bold text-center w-32">Akses Course</th>
                        <th class="px-6 py-4 font-bold text-center w-32">Total Member</th>
                        <th class="px-6 py-4 font-bold text-right w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($plans as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-text-main text-sm">{{ $p->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-tosca-dark bg-tosca-light/30 px-3 py-1.5 rounded-lg border border-tosca/10 inline-block">
                                    {{ number_format((float) $p->price, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $periodColors = [
                                        'monthly' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'yearly' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'lifetime' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    ];
                                    $pColor = $periodColors[$p->period] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $pColor }}">
                                    {{ $p->period === 'monthly' ? 'Bulanan' : ($p->period === 'yearly' ? 'Tahunan' : 'Selamanya') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-center min-w-[3rem] h-8 rounded-lg bg-gray-50 border border-gray-200 font-bold text-text-main text-sm px-2">
                                    {{ $p->plan_courses_count ?? $p->planCourses()->count() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center justify-center min-w-[3rem] h-8 rounded-lg bg-emerald-50 border border-emerald-100 font-bold text-emerald-700 text-sm px-2">
                                    {{ $p->memberships_count ?? $p->memberships()->count() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if (Route::has('admin.plans.show'))
                                        <a href="{{ route('admin.plans.show', $p) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('admin.plans.edit', $p) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-tosca transition-colors" title="Edit Paket">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.plans.destroy', $p) }}" class="inline js-delete-form" data-title="{{ $p->name }} ({{ ucfirst($p->period) }})">
                                        @csrf @method('DELETE')
                                        <button type="button" class="js-delete-btn p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Paket">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="py-16 text-center flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Paket</h3>
                                    <p class="text-text-soft max-w-sm mb-6">Anda belum memiliki paket berlangganan apapun, atau tidak ada yang cocok dengan filter.</p>
                                    @if($showFilters)
                                        <a href="{{ route('admin.plans.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                                            Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.plans.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Paket Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($plans->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
                {{ $plans->withQueryString()->links() }}
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
                    const title = form?.dataset.title || 'paket ini';

                    Swal.fire({
                        title: 'Hapus Paket Berlangganan?',
                        html: `Paket <b>${title}</b> akan dihapus secara permanen. Pastikan tidak ada member aktif yang masih terkait dengan paket ini.`,
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
