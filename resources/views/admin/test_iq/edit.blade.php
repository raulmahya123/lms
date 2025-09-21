@extends('layouts.admin')
@section('title','Edit Test IQ — BERKEMAH')

@php
  // ---- Ambil initial questions yang aman untuk @js() ----
  // 1) Prioritaskan old('questions_json') dari submit sebelumnya (string JSON)
  $initialQuestions = old('questions_json');

  // Jika old() ada dan berupa string JSON → decode ke array
  if (is_string($initialQuestions) && $initialQuestions !== '') {
      try {
          $decoded = json_decode($initialQuestions, true, 512, JSON_THROW_ON_ERROR);
          $initialQuestions = is_array($decoded) ? $decoded : null;
      } catch (\Throwable $e) {
          $initialQuestions = null;
      }
  } else {
      $initialQuestions = null;
  }

  // 2) Jika tidak ada old(), pakai dari model (sudah dicast array di model)
  if (!is_array($initialQuestions)) {
      $fromModel = $testIq->questions ?? null; // <- kolom array cast
      $initialQuestions = is_array($fromModel) ? $fromModel : null;
  }

  // 3) Fallback minimal satu pertanyaan kosong
  if (!is_array($initialQuestions) || empty($initialQuestions)) {
      $initialQuestions = [
          ['id'=>1, 'text'=>'', 'options'=>['','','',''], 'answer_index'=>null],
      ];
  }
@endphp

@section('content')
<h1 class="text-2xl font-extrabold tracking-wide mb-6">Edit Test IQ</h1>

@if(session('success'))
  <div class="mb-5 p-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800">
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
    <ul class="list-disc ml-5 space-y-1">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.test-iq.update', $testIq) }}" class="max-w-4xl space-y-6">
  @csrf @method('PUT')

  {{-- Meta --}}
  <div class="grid md:grid-cols-2 gap-4">
    <label class="grid gap-1">
      <span class="font-medium">Judul <span class="text-red-500">*</span></span>
      <input type="text" name="title" value="{{ old('title', $testIq->title) }}" class="border rounded-xl px-3 py-2" required>
    </label>

    <label class="grid gap-1">
      <span class="font-medium">Durasi (menit)</span>
      <input type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes', $testIq->duration_minutes) }}" class="border rounded-xl px-3 py-2">
    </label>

    <label class="md:col-span-2 grid gap-1">
      <span class="font-medium">Deskripsi</span>
      <textarea name="description" rows="3" class="border rounded-xl px-3 py-2">{{ old('description', $testIq->description) }}</textarea>
    </label>

    <label class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testIq->is_active) ? 'checked' : '' }}>
      <span>Aktif</span>
    </label>
  </div>

  {{-- Question Builder --}}
  <div
    x-data="questionBuilder({ initial: @js($initialQuestions) })"
    class="rounded-2xl border bg-white"
  >
    <div class="px-5 py-4 border-b flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-lg">Pertanyaan</h2>
        <p class="text-sm opacity-70" x-text="`${questions.length} pertanyaan`"></p>
      </div>
      <div class="flex gap-2">
        <button type="button" @click="addQuestion()" class="px-3 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">+ Tambah Pertanyaan</button>
        <button type="button" @click="addFive()" class="px-3 py-2 rounded-xl border">+5 Cepat</button>
      </div>
    </div>

    <div class="divide-y">
      <template x-for="(q, qi) in questions" :key="q.id">
        <div class="p-5 space-y-3">
          <div class="flex items-start gap-3">
            <div class="w-10 shrink-0">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 font-semibold" x-text="qi+1"></span>
            </div>
            <div class="grow space-y-3">

              <div>
                <label class="text-sm font-medium">Teks Pertanyaan</label>
                <textarea x-model="q.text" rows="2" class="mt-1 w-full border rounded-xl px-3 py-2" placeholder="Tulis pertanyaannya..."></textarea>
              </div>

              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="text-sm font-medium">Opsi Jawaban</label>
                  <button type="button" @click="addOption(qi)" class="text-sm px-2 py-1 rounded border">+ Opsi</button>
                </div>

                <div class="grid md:grid-cols-2 gap-2">
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <label class="flex items-center gap-2 border rounded-xl px-3 py-2">
                      <input type="radio"
                             class="mt-0.5"
                             :name="`q-${q.id}-answer`"
                             :value="oi"
                             :checked="q.answer_index === oi"
                             @change="q.answer_index = oi">
                      <input type="text" class="grow outline-none"
                             x-model="q.options[oi]"
                             :placeholder="`Jawaban #${oi+1}`">
                      <button type="button" class="text-red-600" @click="removeOption(qi, oi)" x-show="q.options.length > 2">✕</button>
                    </label>
                  </template>
                </div>
                <p class="text-xs opacity-70 mt-1">Centang radio di kiri untuk menandai jawaban yang benar.</p>
              </div>

            </div>

            <div class="shrink-0">
              <button type="button" class="text-red-600 px-2 py-1" @click="removeQuestion(qi)">Hapus</button>
            </div>
          </div>
        </div>
      </template>

      <template x-if="questions.length === 0">
        <div class="p-5 text-sm opacity-70">Belum ada pertanyaan. Klik “Tambah Pertanyaan”.</div>
      </template>
    </div>

    {{-- Hidden JSON sink untuk dikirim ke controller --}}
    <textarea name="questions_json" x-model="json" class="hidden"></textarea>

    <div class="px-5 py-4 border-t flex items-center justify-between">
      <div class="text-sm" :class="valid ? 'text-emerald-700' : 'text-red-700'">
        <span x-text="valid ? 'Struktur valid.' : 'Ada pertanyaan yang belum lengkap.'"></span>
      </div>
      <div class="text-xs opacity-70">
        Serialized: <span x-text="json.length"></span> chars
      </div>
    </div>
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Update</button>
    <a href="{{ route('admin.test-iq.index') }}" class="px-4 py-2 rounded-xl border hover:bg-gray-50">Kembali</a>
  </div>
</form>

{{-- Alpine helpers --}}
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('questionBuilder', ({ initial = [] } = {}) => ({
      questions: (Array.isArray(initial) && initial.length) ? normalize(initial) : [
        { id: 1, text: '', options: ['', '', '', ''], answer_index: null },
      ],
      nextId:  (Array.isArray(initial) && initial.length)
                ? Math.max(...initial.map(q => +q.id || 0)) + 1
                : 2,
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
        // kirim minimal field yang dibutuhkan controller
        const payload = this.questions.map(q => ({
          id: q.id,
          text: String(q.text).trim(),
          options: q.options.map(o => String(o)),
          answer_index: (Number.isInteger(q.answer_index) ? q.answer_index : null),
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
      removeOption(qi, oi) {
        const q = this.questions[qi];
        q.options.splice(oi, 1);
        if (q.answer_index === oi) q.answer_index = null;
        if (q.answer_index > oi) q.answer_index--;
      }
    }));

    function normalize(arr) {
      return arr.map((q, i) => {
        const options = Array.isArray(q.options) ? q.options.map(o => String(o)) : ['', '', '', ''];
        // dukung data lama yang mungkin punya "answer" (string)
        let ai = Number.isInteger(q.answer_index) ? q.answer_index : null;
        if (ai === null && typeof q.answer === 'string') {
          const idx = options.indexOf(q.answer);
          ai = idx >= 0 ? idx : null;
        }
        return {
          id: q.id ?? (i+1),
          text: String(q.text ?? ''),
          options,
          answer_index: ai,
        };
      });
    }
  });
</script>
@endsection
