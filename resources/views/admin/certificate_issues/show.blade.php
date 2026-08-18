@extends('layouts.admin')
@section('title', 'Detail Sertifikat Terbit — Admin')

@section('content')
@php($issue = $issue ?? $certificate_issue ?? null)
@if(!$issue)
    <div class="max-w-4xl mx-auto pb-12">
        <div class="bg-red-50 border border-red-200 rounded-3xl p-8 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h2 class="text-xl font-extrabold text-red-800 mb-2">Data Tidak Ditemukan</h2>
            <p class="text-red-600 mb-6">Data sertifikat yang Anda cari tidak tersedia atau belum dikirim ke halaman ini.</p>
            <a href="{{ route('admin.certificate-issues.index') }}" class="px-6 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-colors shadow-sm">
                Kembali ke Daftar
            </a>
        </div>
    </div>
@else
<div class="max-w-5xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                Data Penerbitan #{{ \Illuminate\Support\Str::of($issue->id)->substr(0, 8) }}
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Sertifikat Terbit</h1>
            <p class="text-sm text-text-soft mt-1">Lihat informasi spesifik penerbitan sertifikat untuk pengguna.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.certificate-issues.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            
            <form method="POST" action="{{ route('admin.certificate-issues.destroy', $issue) }}" class="inline js-delete-form" data-title="{{ $issue->recipient_name ?? 'sertifikat ini' }}">
                @csrf @method('DELETE')
                <button type="button" class="js-delete-btn inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-red-200 text-red-600 font-bold rounded-xl hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </form>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: Utama --}}
        <div class="md:col-span-2 space-y-8">
            {{-- Info Spesifik --}}
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Informasi Kepemilikan
                    </h2>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">No. Serial Sertifikat</div>
                            <div class="flex items-center gap-3">
                                <div class="font-mono text-lg font-bold text-gray-900 bg-gray-50 border border-gray-200 px-4 py-2 rounded-xl" title="{{ $issue->serial ?? $issue->cert_no }}">
                                    {{ $issue->serial ?? $issue->cert_no ?? '—' }}
                                </div>
                                @if($issue->serial || $issue->cert_no)
                                    <button type="button" class="p-2 rounded-lg bg-gray-100 text-gray-500 hover:text-tosca hover:bg-tosca/10 transition-colors" onclick="navigator.clipboard.writeText('{{ $issue->serial ?? $issue->cert_no }}')" title="Salin Serial">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Pengguna (Penerima)</div>
                            @if($issue->user)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-tosca-light flex items-center justify-center text-tosca-dark font-bold text-sm shrink-0 border border-tosca/20">
                                        {{ strtoupper(substr($issue->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-text-main">{{ $issue->user->name }}</div>
                                        <div class="text-xs text-text-soft">{{ $issue->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-100 bg-gray-50 text-xs font-bold text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Data Pengguna Tidak Tersedia
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konteks Kelulusan --}}
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Konteks Kelulusan
                    </h2>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Jenis Assessment</div>
                            @php $isCourse = ($issue->assessment_type ?? 'course') === 'course'; @endphp
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-bold uppercase tracking-wider {{ $isCourse ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-purple-50 text-purple-700 border border-purple-100' }}">
                                @if ($isCourse)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                @endif
                                {{ $isCourse ? 'Course / Materi' : 'Psychological Test' }}
                            </span>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Nama Program / Konteks</div>
                            <div class="font-bold text-text-main text-lg">{{ $issue->course->title ?? $issue->context ?? '—' }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Skor Akhir</div>
                            @php
                                $score = $issue->score ?? null;
                                $scoreText = is_null($score) ? '—' : rtrim(rtrim(number_format($score, 2), '0'), '.');
                            @endphp
                            <div class="inline-flex items-center justify-center min-w-[4rem] px-4 py-2 rounded-xl bg-gray-100 text-gray-900 font-extrabold tabular-nums border border-gray-200 text-xl">
                                {{ $scoreText }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Template Terkait</div>
                            @if($issue->template)
                                <a href="{{ route('admin.certificate-templates.show', $issue->template) }}" class="inline-flex items-center gap-2 text-tosca hover:text-tosca-dark font-bold group">
                                    {{ $issue->template->name }}
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @else
                                <span class="text-gray-400 font-medium">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Meta JSON --}}
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        Meta Data Snapshot (JSON)
                    </h2>
                </div>
                <div class="p-8">
                    <pre class="text-xs font-mono bg-gray-900 text-gray-100 rounded-xl p-6 overflow-auto max-h-80 border border-gray-800 shadow-inner custom-scrollbar">{{ json_encode(is_array($issue->meta) ? $issue->meta : (json_decode($issue->meta ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
            
            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
            </style>
        </div>

        {{-- Right Column: Timeline --}}
        <div class="md:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Waktu & Validitas
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Diterbitkan Pada
                        </div>
                        <div class="font-bold text-text-main text-lg">{{ optional($issue->issued_at)->format('d M Y') ?: '—' }}</div>
                        @if($issue->issued_at)
                            <div class="text-sm text-gray-500 mt-0.5">{{ $issue->issued_at->format('H:i') }} WIB</div>
                        @endif
                    </div>
                    
                    <hr class="border-gray-100">

                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Berlaku Hingga
                        </div>
                        <div class="font-bold text-text-main text-lg">{{ optional($issue->expired_at)->format('d M Y') ?: 'Selamanya' }}</div>
                        @if($issue->expired_at)
                            <div class="text-sm text-gray-500 mt-0.5">{{ $issue->expired_at->format('H:i') }} WIB</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Log Sistem
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Dibuat (Created At)</div>
                        <div class="font-bold text-gray-700">{{ optional($issue->created_at)->format('d M Y, H:i') ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Diperbarui (Updated At)</div>
                        <div class="font-bold text-gray-700">{{ optional($issue->updated_at)->format('d M Y, H:i') ?: '—' }}</div>
                    </div>
                </div>
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
                    const title = form?.dataset.title || 'sertifikat ini';

                    Swal.fire({
                        title: 'Hapus Sertifikat Terbit?',
                        html: `Data sertifikat untuk <b>${title}</b> akan dihapus permanen.`,
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
@endif
@endsection
