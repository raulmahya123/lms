{{-- resources/views/admin/test_iq/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Tes IQ — Admin')

@section('content')
@php
  $q      = request('q');
  $active = request('active'); // '1' | '0' | null
@endphp

<div x-data="{ q:@js($q??''), showFilters: {{ request()->hasAny(['q','active'])?'true':'false' }} }" class="space-y-6 pb-12">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            Kecerdasan Kognitif
        </div>
        <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Tes IQ</h1>
        <p class="text-sm text-text-soft mt-1">Kelola bank soal tes IQ, durasi, cooldown, dan tabel konversi norma.</p>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" @click="showFilters=!showFilters"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Data
        </button>
        <a href="{{ route('admin.test-iq.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Tes IQ Baru
        </a>
    </div>
  </div>

  {{-- FILTERS / SEARCH --}}
  <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
        class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Tes</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="q" x-model="q" placeholder="Cari berdasarkan judul atau deskripsi..."
                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Publikasi</label>
            <div class="relative">
                <select name="active" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                    <option value="">— Semua Status —</option>
                    <option value="1" @selected($active==='1')>Aktif (Dipublikasikan)</option>
                    <option value="0" @selected($active==='0')>Nonaktif (Draft)</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        <div class="flex items-end gap-3 pt-4 border-t border-gray-50 md:border-none md:pt-0">
            @if(request()->hasAny(['q','active']) && (($q!==null && $q!=='') || ($active!==null && $active!=='')))
                <a href="{{ route('admin.test-iq.index') }}" class="w-full md:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center shrink-0">
                    Reset
                </a>
            @endif
            <button class="w-full px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors text-center">
                Terapkan
            </button>
        </div>
    </div>
  </form>

  {{-- TABLE CARD --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
    <div class="p-6 border-b border-gray-50 bg-softbg/30 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-bold text-text-main">Total {{ $tests->total() }} Tes IQ</span>
            
            @if($q)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                    Pencarian: "{{ $q }}"
                </span>
            @endif
            
            @if($active !== null && $active!=='')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                    Status: {{ $active === '1' ? 'Aktif' : 'Nonaktif' }}
                </span>
            @endif
        </div>
        <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
            Halaman {{ $tests->currentPage() }} dari {{ $tests->lastPage() }}
        </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[1100px]">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
            <th class="px-6 py-4 font-bold">Informasi Tes IQ</th>
            <th class="px-6 py-4 font-bold text-center w-32">Status</th>
            <th class="px-6 py-4 font-bold text-center w-28">Total Soal</th>
            <th class="px-6 py-4 font-bold text-center w-32">Waktu & Jeda</th>
            <th class="px-6 py-4 font-bold text-center w-32">Data Norma</th>
            <th class="px-6 py-4 font-bold text-center w-32">Riwayat</th>
            <th class="px-6 py-4 font-bold text-right w-36">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($tests as $t)
            @php
              // Soal
              $qCount = is_array($t->questions ?? null) ? count($t->questions) : 0;

              // Durasi
              $min = (int)($t->duration_minutes ?? 0);
              if ($min) {
                if ($min >= 60) {
                  $h = intdiv($min, 60);
                  $m = $min % 60;
                  $durText = $h.'h'.($m ? ' '.$m.'m' : '');
                } else {
                  $durText = $min.'m';
                }
              } else {
                $durText = '—';
              }

              // Cooldown
              $cdVal  = $t->cooldown_value ?? null;
              $cdUnit = $t->cooldown_unit  ?? null;
              $cdText = ($cdVal !== null && $cdUnit) ? ($cdVal.' '.$cdUnit) : '—';

              // Submissions count
              $subsArr = is_array($t->submissions ?? null) ? $t->submissions : [];
              $subsCnt = is_array($subsArr) ? count($subsArr) : 0;

              // Norma exist?
              $hasNorm = !empty(data_get($t, 'meta.norm_table')) && is_array(data_get($t, 'meta.norm_table'));
            @endphp

            <tr class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="font-bold text-text-main text-sm mb-1">{{ $t->title }}</div>
                @if(!empty($t->description))
                  <div class="text-xs text-text-soft line-clamp-1 max-w-[300px]" title="{{ strip_tags($t->description) }}">
                    {{ strip_tags($t->description) }}
                  </div>
                @endif
              </td>

              <td class="px-6 py-4 text-center">
                <form action="{{ route('admin.test-iq.toggle', $t) }}" method="POST">
                  @csrf
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider {{ $t->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }} hover:opacity-80 transition-opacity" title="Klik untuk mengubah status">
                    <span class="w-1.5 h-1.5 rounded-full {{ $t->is_active ? 'bg-green-600' : 'bg-gray-400' }}"></span>
                    {{ $t->is_active ? 'Aktif' : 'Draft' }}
                  </button>
                </form>
              </td>

              <td class="px-6 py-4 text-center">
                <div class="inline-flex items-center justify-center min-w-[3rem] h-8 rounded-lg bg-gray-50 border border-gray-200 font-bold text-text-main text-sm px-2">
                    {{ $qCount }}
                </div>
              </td>

              <td class="px-6 py-4">
                <div class="flex flex-col gap-1.5 items-center">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-text-main bg-blue-50 px-2 py-1 rounded-md border border-blue-100 w-full justify-center">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $durText }}
                    </div>
                    <div class="inline-flex items-center gap-1.5 text-[10px] font-bold text-text-soft uppercase tracking-wider w-full justify-center" title="Cooldown / Jeda">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $cdText }}
                    </div>
                </div>
              </td>

              <td class="px-6 py-4 text-center">
                @if($hasNorm)
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-violet-50 text-violet-700 border border-violet-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tersedia
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-gray-50 text-gray-500 border border-gray-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Belum Ada
                  </span>
                @endif
              </td>

              <td class="px-6 py-4 text-center">
                <div class="inline-flex items-center justify-center min-w-[3rem] h-8 rounded-lg bg-emerald-50 border border-emerald-100 font-bold text-emerald-700 text-sm px-2">
                    {{ $subsCnt }}
                </div>
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('admin.test-iq.edit', $t) }}" class="p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:text-tosca transition-colors" title="Edit Pengaturan Tes">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </a>

                  <form action="{{ route('admin.test-iq.destroy', $t) }}" method="POST" class="inline js-delete-form" data-title="{{ $t->title }}">
                      @csrf @method('DELETE')
                      <button type="button" class="js-delete-btn p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Tes IQ">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                  <div class="py-16 text-center flex flex-col items-center justify-center">
                      <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                          <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                      </div>
                      <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Tes IQ</h3>
                      <p class="text-text-soft max-w-sm mb-6">Anda belum membuat atau mengonfigurasi satupun tes IQ.</p>
                      <a href="{{ route('admin.test-iq.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                          Buat Tes IQ Pertama
                      </a>
                  </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($tests->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
            {{ $tests->withQueryString()->links() }}
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
                    const title = form?.dataset.title || 'tes ini';

                    Swal.fire({
                        title: 'Hapus Tes IQ?',
                        html: `<b>${title}</b> beserta bank soal, norma, dan riwayat yang terkait akan dihapus permanen.`,
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

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-2xl' }
            });
        });
    </script>
@endif
@endpush
@endsection
