@extends('layouts.admin')
@section('title', 'Detail Template Sertifikat — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                Template #{{ \Illuminate\Support\Str::of($template->id)->substr(0, 8) }}
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">{{ $template->name }}</h1>
            <p class="text-sm text-text-soft mt-1">Detail konfigurasi desain, layout JSON, dan status template.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.certificate-templates.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            @if(Route::has('admin.certificate-templates.edit'))
            <a href="{{ route('admin.certificate-templates.edit', $template) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Template
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: Info & Preview --}}
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Desain & Pratinjau (Preview)
                    </h2>
                </div>
                <div class="p-8">
                    @php
                        $bg = $template->background_url;
                        if ($bg && !\Illuminate\Support\Str::startsWith($bg, ['http://', 'https://', '/storage/', 'storage/'])) {
                            $bg = \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($bg, '/'));
                        } elseif ($bg && \Illuminate\Support\Str::startsWith($bg, 'storage/')) {
                            $bg = "/$bg";
                        }
                    @endphp

                    @if($bg)
                        <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm shadow-gray-100 bg-gray-50 relative aspect-[1.414/1] flex items-center justify-center">
                            <img src="{{ $bg }}" alt="Background preview" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="mt-4 flex justify-between items-center text-sm">
                            <span class="text-gray-500 truncate mr-4 max-w-sm">{{ $template->background_url }}</span>
                            <a href="{{ $bg }}" target="_blank" class="inline-flex items-center gap-1.5 font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0">
                                Buka Ukuran Penuh
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    @else
                        <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 aspect-[1.414/1] flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-bold">Tidak ada background yang diunggah</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Fields JSON --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                            Data Fields (JSON)
                        </h2>
                    </div>
                    <div class="p-6">
                        <pre class="text-xs font-mono bg-gray-900 text-gray-100 rounded-xl p-4 overflow-auto max-h-64 border border-gray-800 shadow-inner custom-scrollbar">{{ json_encode(is_array($template->fields_json) ? $template->fields_json : (json_decode($template->fields_json ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>

                {{-- SVG JSON --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            Konfigurasi SVG (JSON)
                        </h2>
                    </div>
                    <div class="p-6">
                        <pre class="text-xs font-mono bg-gray-900 text-gray-100 rounded-xl p-4 overflow-auto max-h-64 border border-gray-800 shadow-inner custom-scrollbar">{{ json_encode(is_array($template->svg_json) ? $template->svg_json : (json_decode($template->svg_json ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>
            
            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
            </style>
        </div>

        {{-- Right Column: Status & Timeline --}}
        <div class="md:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status Template
                    </h2>
                </div>
                <div class="p-6">
                    @if ($template->is_active)
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200 w-full justify-center">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Aktif Digunakan
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-3 text-center">Template ini dapat dipilih saat menautkan sertifikat ke course.</p>
                    @else
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wider border bg-gray-50 text-gray-700 border-gray-200 w-full justify-center">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                Nonaktif (Draft)
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-3 text-center">Template ini tidak akan muncul di opsi pemilihan sertifikat course.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Log Aktivitas
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Dibuat Pada</div>
                        <div class="font-bold text-text-main">{{ optional($template->created_at)->format('d M Y, H:i') ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Terakhir Diperbarui</div>
                        <div class="font-bold text-text-main">{{ optional($template->updated_at)->format('d M Y, H:i') ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
