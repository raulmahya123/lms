@extends('layouts.admin')
@section('title', 'Buat Template Sertifikat — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Template Baru
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Buat Template Sertifikat</h1>
            <p class="text-sm text-text-soft mt-1">Konfigurasi desain dasar dan tata letak teks untuk sertifikat course.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.certificate-templates.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold">Gagal menyimpan template! Silakan periksa isian Anda:</p>
                <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.certificate-templates.store') }}" class="space-y-8">
        @csrf

        {{-- Meta & Desain --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Informasi Dasar
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Nama Template <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Template Kelulusan Dasar 2024"
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main text-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">URL Gambar Latar (Background URL)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <input type="url" name="background_url" value="{{ old('background_url') }}" placeholder="https://contoh.com/bg.png"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                    </div>
                    <p class="text-xs text-text-soft mt-2">Gambar latar belakang sertifikat (disarankan rasio A4 lanskap / 1.414:1).</p>
                </div>
            </div>
        </div>

        {{-- Konfigurasi Layout --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    Konfigurasi Data
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Pemetaan Data Teks (Fields JSON)</label>
                    <textarea name="fields_json" rows="5" placeholder='{ "name": "{name}", "course": "{course}", "date": "{date}" }'
                              class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-tosca outline-none transition-all font-mono text-sm text-gray-100 placeholder-gray-600 leading-relaxed custom-scrollbar">{{ old('fields_json') }}</textarea>
                    <p class="text-xs text-text-soft mt-2">Isikan struktur JSON untuk posisi teks dinamis, font, dan ukurannya (mis. x, y, font-size).</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Struktur Grafis Vektor (SVG JSON - Opsional)</label>
                    <textarea name="svg_json" rows="5" placeholder='{ "layers": [ ... ] }'
                              class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 focus:ring-2 focus:ring-tosca outline-none transition-all font-mono text-sm text-gray-100 placeholder-gray-600 leading-relaxed custom-scrollbar">{{ old('svg_json') }}</textarea>
                    <p class="text-xs text-text-soft mt-2">Struktur layer JSON apabila desain menggunakan generator SVG atau Fabric.js.</p>
                </div>
                
                <style>
                    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
                    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 8px; }
                    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 8px; }
                    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
                </style>
            </div>
        </div>

        {{-- Visibilitas --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Visibilitas
                </h2>
            </div>
            
            <div class="p-8">
                <div class="flex items-center gap-4">
                    <input type="hidden" name="is_active" value="0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', 1))>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-tosca/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca"></div>
                        <span class="ml-3 text-sm font-bold text-text-main">Aktif Digunakan (Publish)</span>
                    </label>
                </div>
                <p class="text-xs text-text-soft mt-2 ml-14">Jika dinonaktifkan, template ini tidak bisa dipilih pada konfigurasi course.</p>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
            <a href="{{ route('admin.certificate-templates.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Buat Template
            </button>
        </div>
    </form>
</div>
@endsection
