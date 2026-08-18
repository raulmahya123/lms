{{-- resources/views/admin/psy_questions/create.blade.php --}}
@extends('layouts.admin')
@section('title','Tambah Soal — '.($currentTest->name ?? 'Semua Tes'))

@section('content')
<div x-data="questionForm()" class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Soal Baru
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Tambah Pertanyaan</h1>
      <p class="text-sm text-text-soft mt-1">Buat soal dan konfigurasi opsi jawaban untuk tes psikologi.</p>
    </div>

    <div class="shrink-0 flex gap-3">
      <a href="{{ route('admin.psy-questions.index', array_filter(['psy_test_id' => $selected])) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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

  <form method="POST" action="{{ route('admin.psy-questions.store') }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf

    <div class="p-8 space-y-8">
      
      {{-- Test Selection & Trait Key --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50/50 border border-gray-100 rounded-2xl">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Pilih Tes Psikologi <span class="text-red-500">*</span></label>
            <div class="relative">
              <select name="psy_test_id" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                <option value="">— Pilih Tes —</option>
                @foreach($tests as $t)
                  <option value="{{ $t->id }}" @selected((int)$selected === $t->id)>{{ Str::limit($t->name, 45) }}</option>
                @endforeach
              </select>
              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            @error('psy_test_id') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Trait / Kunci Dimensi (Opsional)</label>
            <input type="text" name="trait_key" value="{{ old('trait_key') }}"
                   class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400"
                   placeholder="misal: logic, openness, dll">
            @error('trait_key') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Prompt --}}
      <div>
        <label class="block text-sm font-bold text-text-main mb-2">Teks Pertanyaan <span class="text-red-500">*</span></label>
        <textarea name="prompt" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400 resize-y leading-relaxed" placeholder="Ketikkan isi soal di sini..." required>{{ old('prompt') }}</textarea>
        @error('prompt') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Type & Ordering --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Tipe Soal <span class="text-red-500">*</span></label>
            <div class="flex gap-3">
                <label class="flex-1 relative">
                    <input type="radio" name="qtype" value="likert" x-model="qtype" class="peer sr-only">
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-tosca/10 peer-checked:border-tosca peer-checked:ring-1 peer-checked:ring-tosca transition-all cursor-pointer text-center group">
                        <span class="block text-sm font-bold text-gray-500 peer-checked:text-tosca-dark group-hover:text-gray-900 transition-colors">Likert Scale</span>
                    </div>
                </label>
                <label class="flex-1 relative">
                    <input type="radio" name="qtype" value="mcq" x-model="qtype" class="peer sr-only">
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl peer-checked:bg-tosca/10 peer-checked:border-tosca peer-checked:ring-1 peer-checked:ring-tosca transition-all cursor-pointer text-center group">
                        <span class="block text-sm font-bold text-gray-500 peer-checked:text-tosca-dark group-hover:text-gray-900 transition-colors">Pilihan Ganda (MCQ)</span>
                    </div>
                </label>
            </div>
            @error('qtype') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Urutan Tampil (Ordering)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                </div>
                <input type="number" name="ordering" value="{{ old('ordering', 0) }}" min="0"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main">
            </div>
            @error('ordering') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
      </div>

      {{-- Options: Likert --}}
      <template x-if="qtype==='likert'">
        <div class="pt-6 border-t border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-text-main">Opsi Skala Likert</h3>
                <p class="text-sm text-text-soft">Default skala 5 poin. Anda dapat mengubah label atau nilai poin.</p>
            </div>
          </div>
          
          <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-12 gap-4 px-4 py-3 border-b border-gray-200 bg-gray-100/50 text-xs font-bold text-text-soft uppercase tracking-wider">
                <div class="col-span-8">Label Pilihan</div>
                <div class="col-span-4 text-center">Nilai / Poin</div>
            </div>
            <div class="p-4 space-y-3">
              <template x-for="(opt,i) in likertDefaults" :key="i">
                <div class="grid grid-cols-12 gap-4 items-center">
                  <div class="col-span-8">
                      <input type="text" :name="`options[${i}][label]`" x-model="opt.label"
                             class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                  </div>
                  <div class="col-span-4">
                      <input type="number" :name="`options[${i}][value]`" x-model="opt.value"
                             class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-center text-text-main">
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </template>

      {{-- Options: MCQ --}}
      <template x-if="qtype==='mcq'">
        <div class="pt-6 border-t border-gray-100">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-bold text-text-main">Opsi Pilihan Ganda</h3>
                <p class="text-sm text-text-soft">Tambahkan pilihan jawaban beserta bobot nilainya.</p>
            </div>
            <button type="button" @click="addOption()"
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
                  <div class="col-span-1 text-center font-bold text-gray-400" x-text="i+1"></div>
                  <div class="col-span-7">
                      <input type="text" :name="`options[${i}][label]`" x-model="opt.label" placeholder="Masukkan pilihan jawaban..."
                             class="w-full border-0 bg-transparent focus:ring-0 outline-none font-medium text-text-main placeholder-gray-400">
                  </div>
                  <div class="col-span-2 border-l border-gray-100 pl-4">
                      <input type="number" :name="`options[${i}][value]`" x-model.number="opt.value" placeholder="0"
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
            </div>
          </div>
        </div>
      </template>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
      <a href="{{ route('admin.psy-questions.index', array_filter(['psy_test_id' => $selected])) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
        Batal
      </a>
      <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Simpan Pertanyaan
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
function questionForm(){
  return {
    qtype: @json(old('qtype','likert')),
    likertDefaults: [
      {label: 'Sangat Tidak Setuju', value: -2},
      {label: 'Tidak Setuju',        value: -1},
      {label: 'Netral',              value:  0},
      {label: 'Setuju',              value:  1},
      {label: 'Sangat Setuju',       value:  2},
    ],
    options: @json(old('options', [['label' => '', 'value' => null]])),
    addOption(){ this.options.push({label:'',value:null}); },
    removeOption(i){
      this.options.splice(i,1);
      if(this.options.length===0){ this.options.push({label:'',value:null}); }
    },
  }
}
</script>
@endpush
