@extends('layouts.admin')
@section('title','Edit Profil Psikologi — Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Perbarui Data
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Profil</h1>
      <p class="text-sm text-text-soft mt-1">Ubah rentang skor dan interpretasi hasil untuk tes psikologi.</p>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('admin.psy-profiles.index', ['test_id'=>old('test_id', $profile->test_id ?? null)]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
      <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      <div>
        <p class="font-bold">Gagal menyimpan perubahan! Silakan periksa isian Anda:</p>
        <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.psy-profiles.update', $profile) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf @method('PUT')

    <div class="p-8 space-y-8">
      
      {{-- Test & Key --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Tes Terkait <span class="text-red-500">*</span></label>
            <div class="relative">
              <select name="test_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                <option value="">— Pilih Tes —</option>
                @foreach($tests as $t)
                  <option value="{{ $t->id }}" @selected(old('test_id', $profile->test_id ?? '')==$t->id)>
                    {{ Str::limit($t->name, 35) }} ({{ ucfirst($t->track) }})
                  </option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('test_id') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Kunci (Key) <span class="text-red-500">*</span></label>
            <input type="text" name="key" value="{{ old('key', $profile->key ?? '') }}" 
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400 uppercase" 
                   placeholder="Misal: BACKEND_LOW" required>
            @error('key') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Name --}}
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Nama Profil <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $profile->name ?? '') }}" 
               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400" 
               placeholder="Contoh: Backend Fit — Low" required>
        @error('name') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Range (Min/Max Total) --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50/50 border border-gray-100 rounded-2xl">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Skor Minimal <span class="text-red-500">*</span></label>
            <input type="number" name="min_total" value="{{ old('min_total', $profile->min_total ?? 0) }}" min="0"
                   class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" required>
            @error('min_total') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Skor Maksimal <span class="text-red-500">*</span></label>
            <input type="number" name="max_total" value="{{ old('max_total', $profile->max_total ?? 0) }}" min="0"
                   class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" required>
            @error('max_total') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Description --}}
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Deskripsi / Interpretasi (Opsional)</label>
        <textarea name="description" rows="5" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400 resize-y leading-relaxed" placeholder="Tulis narasi atau rekomendasi untuk peserta yang mendapatkan skor ini...">{{ old('description', $profile->description ?? '') }}</textarea>
        @error('description') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
      <a href="{{ route('admin.psy-profiles.index', ['test_id'=>old('test_id', $profile->test_id ?? null)]) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
        Batal
      </a>
      <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection
