@extends('layouts.admin')
@section('title','Edit Kuis — Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Perbarui Data
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Kuis</h1>
      <p class="text-sm text-text-soft mt-1">Ubah informasi kuis atau kelola daftar pertanyaan.</p>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
    </div>
  </div>

  @if (session('ok'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-2xl p-4 flex gap-3 shadow-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="font-bold">{{ session('ok') }}</p>
    </div>
  @endif

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

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Kolom Kiri: Form Kuis Utama & Tambah Pertanyaan --}}
    <div class="lg:col-span-1 space-y-8">
        
        {{-- Form Kuis Utama --}}
        <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-lg font-bold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Informasi Kuis
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Penempatan Pelajaran <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="lesson_id" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                            @foreach($lessons as $ls)
                            <option value="{{ $ls->id }}" @selected(old('lesson_id', $quiz->lesson_id)==$ls->id)>{{ $ls->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Judul Kuis <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $quiz->title) }}" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main" required>
                </div>
            </div>
            <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex justify-end">
                <button class="px-6 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2 w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Kuis
                </button>
            </div>
        </form>

        {{-- Form Tambah Pertanyaan --}}
        <form method="POST" action="{{ route('admin.questions.store') }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            @csrf
            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-lg font-bold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Pertanyaan
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Tipe Soal</label>
                    <div class="relative">
                        <select name="type" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="mcq">Pilihan Ganda (Multiple Choice)</option>
                            <option value="short">Isian Singkat (Short Answer)</option>
                            <option value="long">Esai (Long Answer)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Pertanyaan (Prompt) <span class="text-red-500">*</span></label>
                    <textarea name="prompt" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main resize-y leading-relaxed" placeholder="Tuliskan pertanyaan disini..." required></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Poin (Bobot Nilai)</label>
                    <input type="number" name="points" value="1" min="1" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main">
                </div>
            </div>
            <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                <button type="submit" class="w-full px-6 py-2.5 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambahkan
                </button>
            </div>
        </form>

    </div>

    {{-- Kolom Kanan: Daftar Pertanyaan --}}
    <div class="lg:col-span-2 space-y-6">
        
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-extrabold text-text-main">Daftar Pertanyaan ({{ $quiz->questions->count() }})</h2>
        </div>

        @forelse($quiz->questions as $index => $q)
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ expanded: false, editMode: false }">
                
                {{-- Question Header (View Mode) --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 transition-colors" :class="{ 'bg-gray-50': editMode }">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 shrink-0 rounded-2xl bg-tosca-light text-tosca flex items-center justify-center font-black text-lg">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-gray-200 text-gray-700">
                                    {{ $q->type === 'mcq' ? 'Pilihan Ganda' : ($q->type === 'short' ? 'Isian Singkat' : 'Esai') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                    {{ $q->points }} Poin
                                </span>
                            </div>
                            <div class="font-medium text-text-main leading-relaxed" x-show="!editMode">
                                {{ $q->prompt }}
                            </div>

                            {{-- Edit Question Form --}}
                            <div x-show="editMode" x-cloak class="mt-3 w-full" style="display: none;">
                                <form method="POST" action="{{ route('admin.questions.update', $q) }}" class="space-y-3">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="type" value="{{ $q->type }}">
                                    <textarea name="prompt" rows="2" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-medium text-sm">{{ $q->prompt }}</textarea>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 max-w-[150px]">
                                            <label class="block text-xs font-bold text-text-soft mb-1">Poin</label>
                                            <input type="number" name="points" value="{{ $q->points }}" min="1" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-1.5 focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all text-sm font-bold">
                                        </div>
                                        <div class="flex items-end gap-2 pt-5">
                                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors text-sm">Simpan</button>
                                            <button type="button" @click="editMode = false" class="px-4 py-1.5 rounded-lg bg-gray-200 text-gray-700 font-bold hover:bg-gray-300 transition-colors text-sm">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto ml-14 sm:ml-0" x-show="!editMode">
                        @if($q->type === 'mcq')
                            <button type="button" @click="expanded = !expanded" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Opsi Jawaban ({{ $q->options->count() }})
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        @endif
                        <button type="button" @click="editMode = true" class="p-1.5 rounded-lg text-gray-500 hover:text-tosca hover:bg-tosca-light transition-colors" title="Edit Pertanyaan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" class="inline" onsubmit="return confirm('Hapus pertanyaan ini? Seluruh opsi akan ikut terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus Pertanyaan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Options Section (Hanya MCQ) --}}
                @if($q->type === 'mcq')
                    <div x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="border-t border-gray-100 bg-gray-50/50 p-5 pl-14 sm:pl-19" style="display: none;">
                        
                        <div class="space-y-3 mb-4">
                            @forelse($q->options as $opIndex => $op)
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3 shadow-sm group" x-data="{ optEdit: false }">
                                    <div class="flex-1 flex items-center gap-3">
                                        <div class="w-6 h-6 rounded bg-gray-100 text-gray-500 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ chr(65 + $opIndex) }}
                                        </div>
                                        
                                        {{-- View Mode --}}
                                        <div x-show="!optEdit" class="flex-1 flex items-center gap-3">
                                            <span class="font-medium text-sm text-text-main {{ $op->is_correct ? 'text-green-700 font-bold' : '' }}">{{ $op->text }}</span>
                                            @if($op->is_correct)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wider">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    Benar
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Edit Mode --}}
                                        <div x-show="optEdit" x-cloak class="flex-1" style="display: none;">
                                            <form method="POST" action="{{ route('admin.options.update', $op) }}" class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input type="text" name="text" value="{{ $op->text }}" class="flex-1 bg-white border border-gray-300 rounded-lg px-3 py-1.5 focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none text-sm font-medium" required>
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-text-main px-2">
                                                    <input type="checkbox" name="is_correct" value="1" @checked($op->is_correct) class="rounded text-tosca focus:ring-tosca w-4 h-4 border-gray-300">
                                                    Jawaban Benar
                                                </label>
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-colors">Simpan</button>
                                                <button type="button" @click="optEdit = false" class="px-3 py-1.5 rounded-lg bg-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-300 transition-colors">Batal</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1 ml-4 opacity-0 group-hover:opacity-100 transition-opacity" x-show="!optEdit">
                                        <button type="button" @click="optEdit = true" class="p-1 rounded text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit Opsi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.options.destroy', $op) }}" class="inline" onsubmit="return confirm('Hapus opsi jawaban ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus Opsi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500 italic px-2">Belum ada opsi jawaban. Tambahkan di bawah.</div>
                            @endforelse
                        </div>

                        {{-- Tambah Opsi Baru --}}
                        <form method="POST" action="{{ route('admin.options.store') }}" class="flex items-center gap-3 bg-white p-2 pl-3 rounded-xl border border-gray-200">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                            <input type="text" name="text" class="flex-1 bg-transparent outline-none text-sm font-medium" placeholder="Ketik opsi jawaban baru..." required>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-gray-500 hover:text-text-main transition-colors border-l border-gray-200 pl-3">
                                <input type="checkbox" name="is_correct" value="1" class="rounded text-tosca focus:ring-tosca w-4 h-4 border-gray-300">
                                Benar?
                            </label>
                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-gray-900 text-white font-bold hover:bg-black transition-colors text-xs ml-2">Tambah</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-dashed border-gray-300 py-16 text-center flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4 text-gray-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-text-main mb-1">Belum Ada Pertanyaan</h3>
                <p class="text-sm text-text-soft">Gunakan form di sebelah kiri untuk menambahkan soal pertama.</p>
            </div>
        @endforelse

    </div>
  </div>
</div>
@endsection
