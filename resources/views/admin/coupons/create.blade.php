@extends('layouts.admin')
@section('title', 'Buat Kupon Baru — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Kupon Baru
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Buat Kode Promo</h1>
            <p class="text-sm text-text-soft mt-1">Konfigurasi kode kupon, nilai diskon, limit penggunaan, dan masa berlaku.</p>
        </div>
        
        <div class="shrink-0 flex gap-3">
            <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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

    <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-8" id="couponForm" x-data="{ discountType: 'percent' }">
        @csrf

        {{-- Meta Information --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Kupon
                </h2>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Kode Kupon (Code) <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-mono font-bold text-lg tracking-widest text-text-main placeholder-gray-400 uppercase" 
                           placeholder="PROMO2024" required>
                    <p class="text-xs text-text-soft mt-2">Kode yang akan dimasukkan oleh pengguna saat checkout. Hindari penggunaan spasi.</p>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Nilai Diskon (Persentase) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="discount_percent" value="{{ old('discount_percent', 10) }}" min="1" max="100" 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 pr-12 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main text-lg" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold text-lg">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Batas Nominal Diskon Maks (Maksimal)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount') }}" min="0" 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-12 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" placeholder="Misal: 50000">
                            </div>
                            <p class="text-xs text-text-soft mt-2">Kosongkan jika tidak ada batas nominal potongan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Limit & Periode --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Batas Penggunaan & Masa Berlaku
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Batas Jumlah Penggunaan (Usage Limit)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" placeholder="Biarkan kosong untuk tanpa batas (unlimited)">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Mulai Berlaku (Valid From)</label>
                        <div class="relative">
                            <input type="datetime-local" name="valid_from" value="{{ old('valid_from') }}" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Berakhir Pada (Valid Until)</label>
                        <div class="relative">
                            <input type="datetime-local" name="valid_until" value="{{ old('valid_until') }}" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.coupons.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Kupon
            </button>
        </div>
    </form>
</div>
@endsection
