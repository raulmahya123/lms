@extends('layouts.app')
@section('title','Buat Diskusi Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Mulai Topik Baru
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Buat Diskusi</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('app.qa-threads.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Batal
      </a>
    </div>
  </div>

  <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3 text-sm font-medium text-blue-800 shadow-sm">
    <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <p>Gunakan judul yang jelas dan deskripsi yang detail agar anggota komunitas lainnya dapat memahami pertanyaan atau topik diskusi Anda dengan mudah.</p>
  </div>

  <form method="POST" action="{{ route('app.qa-threads.store') }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf

    <div class="p-8 space-y-6">
      
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Judul Diskusi <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400" placeholder="Contoh: Bagaimana cara mengimplementasikan Authentication di Laravel?" required autofocus>
        @error('title') <p class="text-sm font-medium text-red-600 mt-1 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Deskripsi / Pertanyaan Lengkap <span class="text-red-500">*</span></label>
        <textarea name="body" rows="8" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y leading-relaxed" placeholder="Jelaskan pertanyaan Anda secara detail. Anda juga dapat menyertakan langkah-langkah yang sudah Anda coba atau pesan error yang muncul..." required>{{ old('body') }}</textarea>
        @error('body') <p class="text-sm font-medium text-red-600 mt-1 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</p> @enderror
      </div>

      <div class="pt-6 border-t border-gray-50">
        <h3 class="font-bold text-text-main mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
          Kaitkan dengan Materi (Opsional)
        </h3>
        <div class="grid sm:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pilih Kursus</label>
            <div class="relative">
              <select name="course_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                <option value="">— Tidak Dikaitkan —</option>
                @foreach($courses as $c)
                  <option value="{{ $c->id }}" @selected(old('course_id')==$c->id)>{{ $c->title }}</option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('course_id') <p class="text-sm font-medium text-red-600 mt-1 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pilih Pelajaran</label>
            <div class="relative">
              <select name="lesson_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                <option value="">— Tidak Dikaitkan —</option>
                @foreach($lessons as $l)
                  <option value="{{ $l->id }}" @selected(old('lesson_id')==$l->id)>{{ $l->title }}</option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('lesson_id') <p class="text-sm font-medium text-red-600 mt-1 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> {{ $message }}</p> @enderror
          </div>
        </div>
      </div>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex items-center justify-end gap-3">
      <a href="{{ route('app.qa-threads.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
        Batal
      </a>
      <button type="submit" class="px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
        Kirim Diskusi
      </button>
    </div>
  </form>
</div>
@endsection
