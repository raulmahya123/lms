@extends('layouts.admin')
@section('title', 'Edit Paket — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Paket
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Paket Belajar</h1>
            <p class="text-sm text-text-soft mt-1">Perbarui nama, harga, periode, fitur, dan akses course.</p>
        </div>
        
        <div class="shrink-0 flex gap-3">
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-8" id="planForm">
        @csrf @method('PUT')

        {{-- Meta Information --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Dasar
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-text-main mb-2">Nama Paket <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">Rp</span>
                            </div>
                            <input type="number" name="price" value="{{ old('price', $plan->price) }}" min="0" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-12 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Periode <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="period" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                                <option value="monthly" @selected(old('period', $plan->period)==='monthly')>Bulanan (Monthly)</option>
                                <option value="yearly"  @selected(old('period', $plan->period)==='yearly')>Tahunan (Yearly)</option>
                                <option value="lifetime" @selected(old('period', $plan->period)==='lifetime')>Selamanya (Lifetime)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Features & Courses --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            @php
                $existingFeatures = (array)($plan->features ?? []);
                if(empty($existingFeatures)) $existingFeatures = [''];
            @endphp

            {{-- Features Builder --}}
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden" x-data="featureBuilder({ initialFeatures: {{ json_encode($existingFeatures) }} })">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Fitur (Selling Point)
                    </h2>
                    <button type="button" @click="addFeature()" class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-600 hover:text-white transition-colors">
                        + Tambah
                    </button>
                </div>
                
                <div class="p-8 space-y-3">
                    <template x-for="(feat, idx) in features" :key="idx">
                        <div class="relative flex items-center">
                            <div class="absolute left-4 w-1.5 h-1.5 rounded-full bg-blue-400 pointer-events-none"></div>
                            <input type="text" x-model="features[idx]" name="features[]" placeholder="Contoh: Akses ke semua materi dasar"
                                   class="w-full pl-8 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
                            
                            <button type="button" class="absolute right-3 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" 
                                    @click="removeFeature(idx)" x-show="features.length > 1" title="Hapus Fitur">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>

                    <p class="text-xs text-text-soft mt-3">Fitur ini akan ditampilkan pada halaman penawaran paket (pricing page) sebagai daftar benefit (bullet points).</p>
                </div>
            </div>

            {{-- Courses Selection --}}
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Akses Course (Hak Akses)
                    </h2>
                </div>
                
                <div class="p-8">
                    <label class="block text-sm font-bold text-text-main mb-2">Pilih Course yang Termasuk</label>
                    @php
                        $selected = $plan->planCourses()->pluck('course_id')->all();
                    @endphp
                    <select name="course_ids[]" multiple size="8" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main text-sm">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" @selected(in_array($c->id, old('course_ids', $selected))) class="py-1.5 px-2 rounded-lg hover:bg-gray-100">{{ $c->title }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3 flex items-start gap-2 bg-blue-50 border border-blue-100 text-blue-800 p-3 rounded-xl text-xs font-medium">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tips: Tahan tombol <kbd class="bg-white border border-gray-200 px-1 rounded shadow-sm text-gray-700">CTRL</kbd> (Windows) atau <kbd class="bg-white border border-gray-200 px-1 rounded shadow-sm text-gray-700">⌘</kbd> (Mac) untuk memilih lebih dari satu course secara bersamaan.
                    </div>
                </div>
            </div>

        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.plans.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('featureBuilder', (config) => ({
            features: config.initialFeatures,
            addFeature() {
                this.features.push('');
            },
            removeFeature(idx) {
                this.features.splice(idx, 1);
            }
        }));
    });
</script>
@endpush
@endsection
