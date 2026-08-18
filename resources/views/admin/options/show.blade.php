@extends('layouts.admin')
@section('title', 'Detail Opsi Jawaban — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Opsi #{{ $option->id }}
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Opsi Jawaban</h1>
            <p class="text-sm text-text-soft mt-1">Lihat teks opsi dan hubungannya dengan pertanyaan terkait.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.options.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            @if(Route::has('admin.options.edit'))
            <a href="{{ route('admin.options.edit', $option) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Opsi
            </a>
            @endif
        </div>
    </div>

    @if (session('ok') || session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex gap-3 text-sm text-green-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">{{ session('ok') ?? session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Konten Opsi
            </h2>
            
            @if($option->is_correct)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200 shadow-sm shadow-green-100/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Jawaban Benar
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border bg-gray-50 text-gray-500 border-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Jawaban Salah
                </span>
            @endif
        </div>
        
        <div class="p-8 space-y-8">
            {{-- Pertanyaan Terkait --}}
            <div>
                <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Konteks Pertanyaan (Question)
                </div>
                <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5">
                    <div class="font-medium text-blue-900 text-lg leading-relaxed">
                        {!! nl2br(e($option->question->prompt ?? 'Pertanyaan tidak ditemukan atau telah dihapus.')) !!}
                    </div>
                    @if($option->question)
                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('admin.questions.show', $option->question) }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                Lihat Detail Pertanyaan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <hr class="border-gray-50">

            {{-- Teks Opsi --}}
            <div>
                <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    Teks Opsi (Option Text)
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                    <div class="font-medium text-text-main text-xl leading-relaxed whitespace-pre-line">
                        {{ $option->text }}
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                <div class="bg-softbg rounded-2xl p-4 flex items-center justify-between border border-gray-50">
                    <span class="text-sm font-bold text-text-soft uppercase tracking-wider">Dibuat Pada</span>
                    <span class="text-sm font-bold text-gray-700">{{ optional($option->created_at)->format('d M Y, H:i') ?: '—' }}</span>
                </div>
                <div class="bg-softbg rounded-2xl p-4 flex items-center justify-between border border-gray-50">
                    <span class="text-sm font-bold text-text-soft uppercase tracking-wider">Terakhir Diperbarui</span>
                    <span class="text-sm font-bold text-gray-700">{{ optional($option->updated_at)->format('d M Y, H:i') ?: '—' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <form action="{{ route('admin.options.destroy', $option) }}" method="POST" class="inline js-delete-form" data-title="opsi jawaban ini">
            @csrf @method('DELETE')
            <button type="button" class="js-delete-btn inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-red-200 text-red-600 font-bold rounded-xl hover:bg-red-50 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus Opsi
            </button>
        </form>
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
                    const title = form?.dataset.title || 'opsi ini';

                    Swal.fire({
                        title: 'Hapus Opsi Jawaban?',
                        html: `<b>${title}</b> akan dihapus permanen.`,
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
