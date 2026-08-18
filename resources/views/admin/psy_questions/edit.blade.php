{{-- resources/views/admin/psy_questions/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit Soal — Admin')

@section('content')
@php
  /** @var \App\Models\PsyQuestion $question */
@endphp

<div class="max-w-4xl mx-auto space-y-8 pb-12"
      x-data="{
        options: @js($question->options->map(fn($o)=>[
          'id'=>$o->id,
          'label'=>$o->label,
          'value'=>$o->value,
        ])),
        addOption() { this.options.push({id:null,label:'',value:null}) },
        removeOption(i) { this.options.splice(i,1) }
      }">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Perbarui Data
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Pertanyaan</h1>
      <p class="text-sm text-text-soft mt-1">Ubah detail soal dan konfigurasi opsi jawaban.</p>
    </div>
    
    <div class="shrink-0 flex gap-3">
        @php
            $backUrl = isset($question->test_id) 
                ? route('admin.psy-tests.questions.show', ['psy_test'=>$question->test_id,'psy_question'=>$question->id])
                : route('admin.psy-questions.show', $question->id);
        @endphp
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.psy-questions.update',['psy_question'=>$question->id]) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf @method('PUT')

    <div class="p-8 space-y-8">
      
      {{-- Prompt --}}
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Teks Pertanyaan <span class="text-red-500">*</span></label>
        <textarea name="prompt" rows="3" required
                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400 resize-y leading-relaxed" 
                  placeholder="Ketikkan isi soal di sini...">{{ old('prompt', $question->prompt) }}</textarea>
        @error('prompt') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Trait / Type / Ordering --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-5 bg-gray-50/50 border border-gray-100 rounded-2xl">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Trait / Kunci Dimensi</label>
            <input type="text" name="trait_key" value="{{ old('trait_key', $question->trait_key) }}"
                   class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400"
                   placeholder="misal: logic, openness">
            @error('trait_key') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Tipe Soal</label>
            <div class="relative">
                <select name="qtype" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                  <option value="likert" @selected($question->qtype==='likert')>Likert Scale</option>
                  <option value="mcq" @selected($question->qtype==='mcq')>Pilihan Ganda (MCQ)</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            @error('qtype') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Urutan Tampil (Ordering)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                </div>
                <input type="number" name="ordering" value="{{ old('ordering', $question->ordering) }}"
                       class="w-full bg-white border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main">
            </div>
            @error('ordering') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Options --}}
      <div class="pt-6 border-t border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
          <div>
              <h3 class="text-lg font-bold text-text-main">Opsi Jawaban</h3>
              <p class="text-sm text-text-soft">Kelola pilihan jawaban beserta bobot nilainya.</p>
          </div>
          <button type="button" @click="addOption"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-tosca-light text-tosca-dark font-bold hover:bg-tosca hover:text-white transition-colors border border-tosca/20 shadow-sm shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Opsi
          </button>
        </div>

        <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 bg-gray-100/50 text-xs font-bold text-text-soft uppercase tracking-wider">
                <div class="col-span-1 text-center">#</div>
                <div class="col-span-7">Teks Pilihan</div>
                <div class="col-span-2 text-center">Nilai</div>
                <div class="col-span-2 text-center">Hapus</div>
            </div>
            <div class="p-4 space-y-3">
              <template x-for="(opt,i) in options" :key="i">
                <div class="grid grid-cols-12 gap-4 items-center bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
                  <input type="hidden" :name="`options[${i}][id]`" x-model="opt.id">
                  
                  <div class="col-span-1 text-center font-bold text-gray-400" x-text="i+1"></div>
                  
                  <div class="col-span-7">
                    <input type="text" :name="`options[${i}][label]`" x-model="opt.label"
                           placeholder="Label jawaban..."
                           class="w-full border-0 bg-transparent focus:ring-0 outline-none font-medium text-text-main placeholder-gray-400">
                  </div>
                  
                  <div class="col-span-2 border-l border-gray-100 pl-4">
                    <input type="number" :name="`options[${i}][value]`" x-model.number="opt.value"
                           placeholder="0"
                           class="w-full border-0 bg-transparent focus:ring-0 outline-none font-bold text-center text-text-main">
                  </div>
                  
                  <div class="col-span-2 flex justify-center border-l border-gray-100 pl-2">
                    <button type="button" @click="removeOption(i)"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus Opsi">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </div>
                </div>
              </template>
              
              <div x-show="options.length === 0" class="text-center py-6 text-gray-400 font-medium">
                  Belum ada opsi jawaban.
              </div>
            </div>
        </div>
      </div>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
      <a href="{{ $backUrl }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
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
