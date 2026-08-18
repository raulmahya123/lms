@extends('layouts.admin')
@section('title', 'Data Membership — Admin')

@section('content')
@php
    $q = request('q') ?? '';
    $st = request('status');
    $planId = request('plan_id');
    $showFilters = request()->hasAny(['q', 'status', 'plan_id']);
    $plansList = $plans ?? \App\Models\Plan::orderBy('name')->get(['id', 'name']);
@endphp

<div x-data="{ q: @js($q), showFilters: @js($showFilters) }" class="space-y-6 pb-12">

    {{-- HEADER / ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Anggota & Hak Akses
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Membership</h1>
            <p class="text-sm text-text-soft mt-1">Kelola data langganan pengguna, status aktif, dan masa berlaku akses.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
            <a href="{{ route('admin.memberships.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Manual
            </a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
          class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Aktif</label>
                <div class="relative">
                    <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="" @selected(!$st)>— Semua Status —</option>
                        <option value="pending" @selected($st === 'pending')>Menunggu (Pending)</option>
                        <option value="active" @selected($st === 'active')>Aktif (Active)</option>
                        <option value="inactive" @selected($st === 'inactive')>Tidak Aktif (Inactive)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Plan --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Paket Langganan</label>
                <div class="relative">
                    <select name="plan_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                        <option value="">— Semua Paket —</option>
                        @foreach ($plansList as $p)
                            <option value="{{ $p->id }}" @selected($planId == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Search user/email --}}
            <div>
                <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Peserta</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" x-model="q" placeholder="Cari nama atau email..."
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-3 flex items-center justify-end gap-3 pt-4 border-t border-gray-50">
                @if ($showFilters)
                    <a href="{{ route('admin.memberships.index') }}"
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
                <span class="text-sm font-bold text-text-main">Total {{ $items->total() }} Data</span>
                
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
                @if($planId)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">
                        Paket ID: {{ $planId }}
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
                        <th class="px-6 py-4 font-bold">Paket Langganan</th>
                        <th class="px-6 py-4 font-bold text-center w-32">Status</th>
                        <th class="px-6 py-4 font-bold text-center w-40">Masa Berlaku</th>
                        <th class="px-6 py-4 font-bold text-right w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $m)
                        @php
                            $from = optional($m->activated_at)?->format('d M Y, H:i');
                            $to = optional($m->expires_at)?->format('d M Y, H:i');
                            
                            $badge = match ($m->status) {
                                'active' => ['bg-green-50', 'text-green-700', 'border-green-200', 'Aktif', 'bg-green-500'],
                                'inactive' => ['bg-red-50', 'text-red-700', 'border-red-200', 'Nonaktif', 'bg-red-500'],
                                'pending' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Menunggu', 'bg-amber-500'],
                                default => ['bg-gray-50', 'text-gray-700', 'border-gray-200', ucfirst($m->status ?? 'Unknown'), 'bg-gray-500'],
                            };
                            
                            $isExpired = $m->expires_at && $m->expires_at->isPast();
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-tosca-light flex items-center justify-center text-tosca-dark font-bold text-sm shrink-0 border border-tosca/20">
                                        {{ strtoupper(substr($m->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-text-main text-sm">{{ $m->user?->name ?? '—' }}</div>
                                        <div class="text-xs text-text-soft mt-0.5">{{ $m->user?->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5 font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 text-sm">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $m->plan?->name ?? 'Tidak Ditemukan' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $badge[0] }} {{ $badge[1] }} {{ $badge[2] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[4] }}"></span>
                                    {{ $badge[3] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-text-soft uppercase tracking-wider text-[10px]">Mulai</span>
                                        <span class="font-bold text-text-main">{{ $from ?: '—' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-gray-100 pt-1">
                                        <span class="font-bold text-text-soft uppercase tracking-wider text-[10px]">Akhir</span>
                                        <span class="font-bold {{ $isExpired ? 'text-red-600' : 'text-text-main' }}">{{ $to ?: 'Selamanya' }}</span>
                                    </div>
                                    @if($isExpired)
                                        <div class="text-[10px] font-bold text-red-600 bg-red-50 text-center py-1 mt-1 rounded border border-red-100">KADALUARSA</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if (Route::has('admin.memberships.show'))
                                        <a href="{{ route('admin.memberships.show', $m) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('admin.memberships.edit', $m) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-tosca transition-colors" title="Edit Akses">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.memberships.destroy', $m) }}" class="inline js-delete-form" data-title="Akses milik {{ $m->user?->name }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="js-delete-btn p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Cabut Akses (Hapus)">
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
                                        <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Membership</h3>
                                    <p class="text-text-soft max-w-sm mb-6">Belum ada pengguna yang memiliki akses berlangganan, atau filter tidak cocok.</p>
                                    @if($showFilters)
                                        <a href="{{ route('admin.memberships.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                                            Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('admin.memberships.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah Akses Manual
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
                    const title = form?.dataset.title || 'data ini';

                    Swal.fire({
                        title: 'Cabut Membership?',
                        html: `<b>${title}</b> akan dicabut hak aksesnya dan datanya dihapus permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Cabut Akses',
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
