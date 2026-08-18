@extends('layouts.admin')
@section('title','Tambah Tes Psikologi — Admin')

@section('content')
@php($tracks = ['backend','frontend','fullstack','qa','devops','pm','custom'])
@php($types  = ['likert','mcq','iq','disc','big5','custom'])

<div class="max-w-3xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Baru
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Tambah Tes Psikologi</h1>
      <p class="text-sm text-text-soft mt-1">Buat instrumen tes psikologi baru untuk profiling pengguna.</p>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('admin.psy-tests.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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

  <form method="POST" action="{{ route('admin.psy-tests.store') }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf

    <div class="p-8 space-y-8">
      
      {{-- Name & Slug --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Nama Tes <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400" 
                   placeholder="Contoh: Tes Kepribadian DISC" required>
            @error('name') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Slug (Opsional)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" 
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" 
                   placeholder="(Otomatis dari nama jika kosong)">
            @error('slug') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Track & Type --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Kategori Track <span class="text-red-500">*</span></label>
            <div class="relative">
              <select name="track" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                <option value="">— Pilih Track —</option>
                @foreach($tracks as $t)
                  <option value="{{ $t }}" @selected(old('track')===$t)>{{ ucfirst($t) }}</option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('track') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Tipe Tes <span class="text-red-500">*</span></label>
            <div class="relative">
              <select name="type" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                <option value="">— Pilih Tipe —</option>
                @foreach($types as $t)
                  <option value="{{ $t }}" @selected(old('type')===$t)>{{ strtoupper($t) }}</option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('type') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Time Limit --}}
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Batas Waktu (Menit)</label>
        <div class="relative w-full md:w-1/2">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <input type="number" name="time_limit_min" value="{{ old('time_limit_min') }}" min="1" max="600"
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" 
                   placeholder="Kosongkan jika tanpa batas waktu">
        </div>
        @error('time_limit_min') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Active Toggle --}}
      <div class="pt-4 border-t border-gray-100">
        <label class="flex items-center gap-3 cursor-pointer group">
          <input type="hidden" name="is_active" value="0">
          <div class="relative">
            <input type="checkbox" name="is_active" value="1" class="peer sr-only" @checked(old('is_active', 1))>
            <div class="block w-14 h-8 bg-gray-200 rounded-full peer-checked:bg-tosca transition-colors"></div>
            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform peer-checked:translate-x-6 shadow-sm"></div>
          </div>
          <div>
            <div class="font-bold text-text-main group-hover:text-tosca transition-colors">Tes Aktif</div>
            <div class="text-xs text-text-soft">Pengguna dapat melihat dan mengerjakan tes ini.</div>
          </div>
        </label>
        @error('is_active') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
      <a href="{{ route('admin.psy-tests.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
        Batal
      </a>
      <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Simpan Tes
      </button>
    </div>
  </form>
</div>
@endsection
