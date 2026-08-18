@extends('layouts.admin')
@section('title', 'Buat Tes IQ Baru — Admin')

@php
  $initialQuestions = old('questions_json');
  $initialQuestions = is_string($initialQuestions)
      ? json_decode($initialQuestions, true)
      : $initialQuestions;

  if (!is_array($initialQuestions) || empty($initialQuestions)) {
      $initialQuestions = [
          ['id'=>1, 'text'=>'', 'options'=>['','','',''], 'answer_index'=>null],
      ];
  }

  $initialNormJson = old('norm_table_json', '');
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tes Baru
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Buat Tes IQ</h1>
            <p class="text-sm text-text-soft mt-1">Lengkapi informasi dasar, aturan, norma skor, dan bank soal.</p>
        </div>
        
        <div class="shrink-0 flex gap-3">
            <a href="{{ route('admin.test-iq.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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

    <form method="POST" action="{{ route('admin.test-iq.store') }}" class="space-y-8" id="iqTestForm">
        @csrf

        {{-- Meta Information --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Dasar
                </h2>
                
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca"></div>
                    <span class="ml-3 text-sm font-bold text-text-main">Aktif / Publik</span>
                </label>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-text-main mb-2">Judul Tes <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" 
                               placeholder="Contoh: Tes Kecerdasan Logika A" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-text-main mb-2">Deskripsi Tes</label>
                        <textarea name="description" rows="3" 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400 resize-y" 
                                  placeholder="Jelaskan tujuan tes, instruksi singkat, dll...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Durasi Pengerjaan</label>
                        <div class="relative">
                            <input type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes', 0) }}" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-4 pr-16 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-500 font-medium">
                                menit
                            </div>
                        </div>
                        <p class="text-xs text-text-soft mt-2">Isi 0 jika tidak ada batasan waktu.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Jeda Pengulangan (Cooldown)</label>
                        <div class="flex gap-2">
                            <input type="number" name="cooldown_value" min="0" value="{{ old('cooldown_value', 1) }}" 
                                   class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main text-center">
                            <select name="cooldown_unit" class="w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                                <option value="day"   {{ old('cooldown_unit')==='day'?'selected':'' }}>Hari</option>
                                <option value="week"  {{ old('cooldown_unit')==='week'?'selected':'' }}>Minggu</option>
                                <option value="month" {{ old('cooldown_unit','month')==='month'?'selected':'' }}>Bulan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Norm Table --}}
        <div x-data="normTableEditor({ initial: @js($initialNormJson) })" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Tabel Norma (Opsional)
                    </h2>
                    <p class="text-sm text-text-soft mt-1">Konversi nilai mentah (raw correct) ke skor IQ.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="insertExample()" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 text-sm transition-colors">
                        Isi Contoh
                    </button>
                    <button type="button" @click="sortByMinRaw()" class="px-4 py-2 rounded-xl bg-purple-50 text-purple-700 font-bold border border-purple-100 hover:bg-purple-100 text-sm transition-colors">
                        Urutkan
                    </button>
                </div>
            </div>

            <div class="p-8">
                <textarea x-model="json" name="norm_table_json" rows="6" 
                          class="w-full bg-gray-900 text-gray-100 border-0 rounded-xl px-5 py-4 font-mono text-sm leading-relaxed focus:ring-4 focus:ring-purple-500/20 outline-none transition-all"
                          placeholder="Masukkan JSON mapping skor di sini..."></textarea>
                
                <div class="flex items-center justify-between mt-3 text-sm font-medium">
                    <div class="flex items-center gap-2" :class="valid ? 'text-green-600' : 'text-red-600'">
                        <svg x-show="valid" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <svg x-show="!valid" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span x-text="valid ? 'Format JSON Valid' : 'Format JSON Tidak Valid!'"></span>
                    </div>
                    <div class="text-gray-400">
                        <span x-text="(json||'').length"></span> Karakter
                    </div>
                </div>
            </div>
        </div>

        {{-- Question Builder --}}
        <div x-data="questionBuilder({ initial: @js($initialQuestions) })" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        Bank Soal
                    </h2>
                    <p class="text-sm text-text-soft mt-1">
                        Total: <strong x-text="questions.length" class="text-tosca"></strong> soal dibuat.
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="addFive()" class="px-4 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 text-sm transition-colors">
                        +5 Soal
                    </button>
                    <button type="button" @click="addQuestion()" class="px-4 py-2.5 rounded-xl bg-tosca-light text-tosca-dark border border-tosca/20 font-bold hover:bg-tosca hover:text-white text-sm transition-colors">
                        Tambah Soal
                    </button>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                <template x-for="(q, qi) in questions" :key="q.id">
                    <div class="p-8">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-700 font-extrabold flex items-center justify-center text-sm border border-gray-200" x-text="qi+1"></div>
                            </div>

                            <div class="flex-1 space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Teks Pertanyaan</label>
                                    <textarea x-model="q.text" rows="2" 
                                              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" 
                                              placeholder="Tulis pertanyaannya..."></textarea>
                                </div>

                                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="text-sm font-bold text-text-soft uppercase tracking-wider">Opsi Jawaban</label>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="addOption(qi)" class="text-xs font-bold text-tosca bg-tosca/10 px-3 py-1.5 rounded-lg hover:bg-tosca hover:text-white transition-colors">
                                                + Opsi
                                            </button>
                                            <button type="button" @click="resetOptions(qi)" class="text-xs font-bold text-gray-500 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                                Reset
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-4">
                                        <template x-for="(opt, oi) in q.options" :key="oi">
                                            <div class="relative flex items-center">
                                                <input type="radio" 
                                                       :name="`q-${q.id}-answer`" 
                                                       :value="oi" 
                                                       :checked="q.answer_index === oi" 
                                                       @change="q.answer_index = oi"
                                                       class="peer absolute left-4 w-5 h-5 text-tosca bg-gray-100 border-gray-300 focus:ring-tosca cursor-pointer z-10">
                                                
                                                <input type="text" 
                                                       x-model="q.options[oi]" 
                                                       :placeholder="`Jawaban #${oi+1}`"
                                                       class="w-full pl-12 pr-10 py-3 bg-white border border-gray-200 rounded-xl focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-medium text-text-main peer-checked:border-tosca peer-checked:bg-tosca/5">
                                                
                                                <button type="button" 
                                                        class="absolute right-3 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors z-10" 
                                                        @click="removeOption(qi, oi)" 
                                                        x-show="q.options.length > 2"
                                                        title="Hapus opsi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="mt-4 flex items-center gap-2 text-xs font-medium text-gray-500">
                                        <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pilih radio button untuk menandai jawaban yang benar.
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 mt-2">
                                <button type="button" class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors border border-transparent hover:border-red-100" @click="removeQuestion(qi)" title="Hapus Soal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="questions.length === 0">
                    <div class="p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mb-4 text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        </div>
                        <p class="text-text-main font-bold mb-1">Belum Ada Soal</p>
                        <p class="text-sm text-text-soft">Silakan klik tombol "Tambah Soal" di atas.</p>
                    </div>
                </template>
            </div>

            <textarea name="questions_json" x-model="json" class="hidden"></textarea>

            <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="text-sm font-bold flex items-center gap-2" :class="valid ? 'text-green-600' : 'text-red-600'">
                    <svg x-show="valid" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <svg x-show="!valid" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span x-text="valid ? 'Semua pertanyaan telah lengkap.' : 'Ada pertanyaan yang belum lengkap/belum ada jawaban benar!'"></span>
                </div>
                <div class="text-xs font-mono font-medium text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                    <span x-text="json.length"></span> Bytes
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.test-iq.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Tes IQ
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
  document.addEventListener('alpine:init', () => {
    // ---------- Norm Table Editor ----------
    Alpine.data('normTableEditor', ({ initial = '' } = {}) => ({
      json: initial || '',
      get valid() {
        if (!this.json || !this.json.trim()) return true;
        try {
          const arr = JSON.parse(this.json);
          if (!Array.isArray(arr)) return false;
          return arr.every(r =>
            r && Number.isInteger(+r.min_raw) && Number.isInteger(+r.iq)
          );
        } catch (e) {
          return false;
        }
      },
      insertExample() {
        const demo = [
          {"min_raw":0,  "iq":70},
          {"min_raw":5,  "iq":85},
          {"min_raw":10, "iq":95},
          {"min_raw":15, "iq":105},
          {"min_raw":20, "iq":115},
          {"min_raw":25, "iq":125},
          {"min_raw":30, "iq":135}
        ];
        this.json = JSON.stringify(demo, null, 2);
      },
      sortByMinRaw() {
        try {
          const arr = JSON.parse(this.json || '[]');
          if (!Array.isArray(arr)) return;
          arr.sort((a,b) => (+a.min_raw) - (+b.min_raw));
          this.json = JSON.stringify(arr, null, 2);
        } catch(e) {}
      }
    }));

    // ---------- Question Builder ----------
    Alpine.data('questionBuilder', ({ initial = [] } = {}) => ({
      questions: (Array.isArray(initial) && initial.length) ? normalize(initial) : [
        { id: 1, text: '', options: ['', '', '', ''], answer_index: null },
      ],
      nextId:  (Array.isArray(initial) && initial.length) ? Math.max(...initial.map(q => +q.id || 0)) + 1 : 2,
      get valid() {
        return this.questions.every(q =>
          String(q.text).trim() !== '' &&
          Array.isArray(q.options) && q.options.length >= 2 &&
          q.options.every(o => String(o).trim() !== '') &&
          Number.isInteger(q.answer_index) &&
          q.answer_index >= 0 && q.answer_index < q.options.length
        );
      },
      get json() {
        const payload = this.questions.map(q => ({
          id: q.id,
          text: String(q.text).trim(),
          options: q.options.map(o => String(o)),
          answer_index: q.answer_index
        }));
        return JSON.stringify(payload);
      },
      addQuestion() {
        this.questions.push({ id: this.nextId++, text: '', options: ['', '', '', ''], answer_index: null });
      },
      addFive() {
        for (let i = 0; i < 5; i++) this.addQuestion();
      },
      removeQuestion(idx) { this.questions.splice(idx, 1); },
      addOption(qi) { this.questions[qi].options.push(''); },
      resetOptions(qi) { this.questions[qi].options = ['', '', '', '']; this.questions[qi].answer_index = null; },
      removeOption(qi, oi) {
        const q = this.questions[qi];
        q.options.splice(oi, 1);
        if (q.answer_index === oi) q.answer_index = null;
        if (q.answer_index > oi) q.answer_index--;
      }
    }));

    function normalize(arr) {
      return arr.map((q, i) => ({
        id: q.id ?? (i+1),
        text: String(q.text ?? ''),
        options: Array.isArray(q.options) ? q.options.map(o => String(o)) : ['', '', '', ''],
        answer_index: Number.isInteger(q.answer_index) ? q.answer_index : null
      }));
    }
  });

  document.getElementById('iqTestForm').addEventListener('submit', function(e) {
      const qBuilder = document.querySelector('[x-data^="questionBuilder"]').__x.$data;
      if (!qBuilder.valid) {
          e.preventDefault();
          alert('Mohon lengkapi semua soal dan pastikan setiap soal memiliki satu jawaban benar yang dipilih.');
      }
  });
</script>
@endpush
@endsection
