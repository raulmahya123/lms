@extends('layouts.admin')

@section('title', 'Buat Kursus Baru — Admin')

@section('content')
<div x-data class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Baru
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Tambah Kursus</h1>
            <p class="text-sm text-text-soft mt-1">Lengkapi informasi dasar kursus di bawah ini.</p>
        </div>
        <div class="shrink-0 flex gap-3">
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold">Gagal menyimpan data! Silakan periksa isian Anda:</p>
                <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        @csrf
        <input type="hidden" name="is_published" id="publishField" value="{{ old('is_published') ? 1 : 0 }}">

        <div class="p-8 space-y-8">
            
            {{-- Informasi Dasar --}}
            <div>
                <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">1</span>
                    Informasi Dasar
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Judul Kursus <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400" 
                               placeholder="Contoh: Belajar Laravel dari Dasar" required autofocus>
                        @error('title') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Deskripsi Kursus</label>
                        <textarea name="description" rows="4" 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y leading-relaxed" 
                                  placeholder="Tuliskan deksripsi singkat mengenai materi apa saja yang akan dipelajari...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Media & Visual --}}
            <div>
                <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">2</span>
                    Media Visual
                </h3>
                
                <div x-data="{
                        preview: null,
                        isDropping: false,
                        handleFiles(files){
                            if (!files?.length) return;
                            const file = files[0];
                            $refs.input.files = files;
                            this.preview = URL.createObjectURL(file);
                        }
                    }">
                    
                    <label class="block text-sm font-bold text-text-main mb-2">Cover Kursus (Thumbnail)</label>
                    
                    <div @dragover.prevent="isDropping = true"
                         @dragleave.prevent="isDropping = false"
                         @drop.prevent="isDropping=false; handleFiles($event.dataTransfer.files)"
                         class="relative rounded-2xl border-2 border-dashed p-8 transition-colors text-center"
                         :class="isDropping ? 'border-tosca bg-tosca-light/30' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'">
                        
                        <input type="file" name="cover" accept="image/*" x-ref="input" @change="handleFiles($event.target.files)" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />
                        
                        <div class="pointer-events-none flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4-4-4 4M12 8v9"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v.01"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-text-main text-sm">Klik atau seret gambar ke area ini</p>
                                <p class="text-xs text-text-soft font-medium mt-1">Format didukung: JPG, PNG, WEBP (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <template x-if="preview">
                        <div class="mt-4 p-4 border border-gray-200 rounded-2xl bg-white shadow-sm inline-block">
                            <p class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pratinjau Cover</p>
                            <img :src="preview" alt="Preview" class="h-48 w-80 object-cover rounded-xl border border-gray-100">
                        </div>
                    </template>
                    @error('cover') <p class="text-sm font-medium text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Pengaturan Harga & Publikasi --}}
            <div>
                <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">3</span>
                    Harga & Aksesibilitas
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Harga --}}
                    <div x-data="{ isFree: {{ old('is_free', 1) ? 'true' : 'false' }} }" class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-text-main text-sm">Tipe Harga</h4>
                                <p class="text-xs text-text-soft mt-0.5">Tentukan apakah kursus ini berbayar.</p>
                            </div>
                            <input type="hidden" name="is_free" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_free" value="1" class="sr-only peer" @change="isFree = !isFree" :checked="isFree">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca"></div>
                                <span class="ml-3 text-sm font-bold text-text-main">Gratis</span>
                            </label>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200" x-show="!isFree" x-transition>
                            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Harga (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center font-bold text-gray-500">Rp</span>
                                <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                                       class="w-full bg-white border border-gray-200 rounded-xl pl-12 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main">
                            </div>
                            @error('price') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Publikasi --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-text-main text-sm">Status Publikasi</h4>
                                <p class="text-xs text-text-soft mt-0.5">Visibilitas kursus ke pengguna akhir.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" @change="document.getElementById('publishField').value = $event.target.checked ? 1 : 0" {{ old('is_published') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-text-soft leading-relaxed">
                                Jika diaktifkan, kursus akan langsung terlihat di halaman eksplorasi dan pengguna bisa mulai mendaftar. Jika dimatikan (Draft), hanya Admin yang bisa melihatnya.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('admin.courses.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" @click="document.getElementById('publishField').value = 0" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors text-center">
                Simpan sebagai Draft
            </button>
            <button type="submit" @click="document.getElementById('publishField').value = 1" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Publish Sekarang
            </button>
        </div>
    </form>
</div>
@endsection
