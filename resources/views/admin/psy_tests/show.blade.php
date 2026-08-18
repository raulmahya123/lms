{{-- resources/views/admin/psy_tests/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Tes Psikologi — Admin')

@section('content')
@php
  /** @var \App\Models\PsyTest $psy_test */
@endphp

<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        Detail Informasi
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Tes Psikologi</h1>
      <p class="text-sm text-text-soft mt-1">Lihat informasi tes, konfigurasi, dan jumlah soal yang terdaftar.</p>
    </div>
    <div class="shrink-0 flex items-center gap-3">
      <a href="{{ route('admin.psy-tests.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
      <a href="{{ route('admin.psy-tests.edit', $psy_test) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Edit Tes
      </a>
    </div>
  </div>

  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    
    {{-- Main Info Section --}}
    <div class="p-8 sm:p-10 border-b border-gray-50 bg-gradient-to-br from-white to-gray-50/50">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="space-y-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-text-main mb-2">{{ $psy_test->name }}</h2>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 font-mono text-sm border border-gray-200">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        /{{ $psy_test->slug ?? '—' }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                        Track: {{ ucfirst($psy_test->track) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-violet-50 text-violet-700 border border-violet-100">
                        Tipe: {{ strtoupper($psy_test->type) }}
                    </span>
                    @if($psy_test->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                            <span class="w-2 h-2 rounded-full bg-green-600"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span> Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="flex flex-row md:flex-col gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm w-32 shrink-0 text-center">
                    <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Durasi</div>
                    <div class="text-2xl font-extrabold text-tosca">
                        {{ $psy_test->time_limit_min ?? '∞' }}
                    </div>
                    <div class="text-xs font-medium text-text-soft mt-1">
                        {{ $psy_test->time_limit_min ? 'Menit' : 'Tanpa Batas' }}
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm w-32 shrink-0 text-center">
                    <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Jumlah Soal</div>
                    <div class="text-2xl font-extrabold text-blue-600">
                        {{ $psy_test->questions()->count() }}
                    </div>
                    <div class="text-xs font-medium text-text-soft mt-1">Pertanyaan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="p-6 sm:p-8 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-6">
      
      <div class="flex items-center gap-4 text-sm font-medium text-text-soft">
        <svg class="w-12 h-12 text-tosca-light shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
        <p>Anda dapat mengelola daftar pertanyaan (soal & opsi) di menu khusus Manajemen Soal Tes.</p>
      </div>

      <div class="shrink-0 flex items-center gap-3 w-full sm:w-auto">
        <form method="POST" action="{{ route('admin.psy-tests.destroy', $psy_test) }}" class="js-delete-form w-full sm:w-auto" data-title="{{ $psy_test->name }}">
            @csrf @method('DELETE')
            <button type="button" class="js-delete-btn w-full sm:w-auto px-6 py-3 rounded-xl border border-red-200 bg-white text-red-600 font-bold hover:bg-red-50 hover:border-red-300 transition-colors">
                Hapus Tes
            </button>
        </form>
        
        <a href="{{ route('admin.psy-tests.questions.index', $psy_test) }}"
           class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors shadow-sm flex items-center justify-center gap-2">
           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5h10.125M8.25 9h10.125M8.25 13.5h10.125M8.25 18h10.125M3.375 4.5h.008v.008h-.008V4.5zM3.375 9h.008v.008h-.008V9zM3.375 13.5h.008v.008h-.008v-.008zM3.375 18h.008v.008h-.008V18z"></path></svg>
           Kelola Soal
        </a>
      </div>
    </div>
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
                        title: 'Hapus Tes Psikologi?',
                        html: `<b>${title}</b> beserta seluruh soal di dalamnya akan dihapus permanen.`,
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
@endpush
@endsection
