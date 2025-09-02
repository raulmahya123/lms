@php
  $quizVal   = old('quiz_id', optional($question)->quiz_id);
  $textVal   = old('question', optional($question)->question);
  $typeVal   = old('type', optional($question)->type ?? 'multiple_choice');
  $pointsVal = old('points', optional($question)->points ?? 1);
  $statusVal = old('status', optional($question)->status ?? 'active');
  $expVal    = old('explanation', optional($question)->explanation);

  // Options (untuk MCQ/MA). Format: [['text'=>..., 'is_correct'=>bool], ...]
  $oldOptions = old('options');
  if (!is_null($oldOptions)) {
      $options = collect($oldOptions)
        ->map(fn($opt) => [
          'text' => $opt['text'] ?? '',
          'is_correct' => isset($opt['is_correct']) && (string)$opt['is_correct']==='1'
        ])
        ->filter(fn($o) => trim($o['text']) !== '')
        ->values()
        ->all();
  } else {
      $options = collect(optional($question)->options ?? [])
        ->map(fn($o) => ['text' => $o->text ?? '', 'is_correct' => (bool)($o->is_correct ?? false)])
        ->filter(fn($o) => trim($o['text']) !== '')
        ->values()
        ->all();
  }
  if (empty($options)) $options = [
    ['text' => '', 'is_correct' => false],
    ['text' => '', 'is_correct' => false],
  ];

  // Default true/false template
  $tfDefault = [
    ['text' => 'True',  'is_correct' => true],
    ['text' => 'False', 'is_correct' => false],
  ];
@endphp

<div
  x-data="{
    type: @js($typeVal),
    options: @js($options),
    addOption(){
      // batasi maksimal 6 biar rapi
      if(this.options.length >= 6) return;
      this.options.push({text:'',is_correct:false})
    },
    removeOption(i){
      if(this.type === 'true_false') return;
      // jaga minimal 2 opsi utk MCQ/MA
      if(this.options.length <= 2) return;
      this.options.splice(i,1)
    },
    ensureSingleCorrect(i){
      if(this.type==='multiple_choice'){
        this.options = this.options.map((o,idx)=>({ ...o, is_correct: idx===i }));
      }
    },
    setTrueFalse(){
      this.options = JSON.parse(@js(json_encode($tfDefault)));
    },
    normalizeOnTypeChange(){
      if(this.type==='true_false'){ this.setTrueFalse(); return; }
      // jika dari MA/TF ke MCQ: pastikan hanya satu benar
      if(this.type==='multiple_choice'){
        const firstTrue = this.options.findIndex(o=>o.is_correct===true);
        this.options = this.options.map((o,idx)=>({ ...o, is_correct: idx===firstTrue && firstTrue!==-1 }));
      }
      // jika dari MCQ/TF ke MA: biarkan tanda benar yang ada
      // pastikan minimal 2 opsi
      if(this.options.length < 2){
        while(this.options.length<2){ this.options.push({text:'', is_correct:false}); }
      }
    }
  }"
  x-init="if(type==='true_false'){ setTrueFalse() }"
  x-effect="normalizeOnTypeChange()"
  class="space-y-6"
>
  {{-- Kartu: Meta --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <h3 class="font-semibold mb-4">Detail Pertanyaan</h3>
    <div class="grid md:grid-cols-2 gap-4">
      {{-- QUIZ --}}
      <div>
        <label class="block text-sm font-medium mb-1">Quiz <span class="text-red-600">*</span></label>
        <select name="quiz_id" class="w-full border rounded-xl px-3 py-2" required>
          <option value="" disabled {{ $quizVal ? '' : 'selected' }}>— pilih quiz —</option>
          @foreach ($quizzes as $qz)
            <option value="{{ $qz->id }}" {{ (string)$quizVal === (string)$qz->id ? 'selected' : '' }}>
              {{ $qz->title ?? $qz->name ?? ('Quiz #'.$qz->id) }}
            </option>
          @endforeach
        </select>
        @error('quiz_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- POINTS --}}
      <div>
        <label class="block text-sm font-medium mb-1">Poin</label>
        <input type="number" name="points" min="0" value="{{ $pointsVal }}" class="w-full border rounded-xl px-3 py-2">
        @error('points') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- TYPE --}}
      <div>
        <label class="block text-sm font-medium mb-1">Tipe</label>
        <select name="type" x-model="type" @change="normalizeOnTypeChange()" class="w-full border rounded-xl px-3 py-2">
          <option value="multiple_choice">Multiple Choice (1 jawaban benar)</option>
          <option value="multiple_answer">Multiple Answer (bisa >1 benar)</option>
          <option value="true_false">True / False</option>
          <option value="short_answer">Short Answer</option>
        </select>
        @error('type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- STATUS --}}
      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border rounded-xl px-3 py-2">
          @foreach (['active'=>'Active','inactive'=>'Inactive'] as $k => $v)
            <option value="{{ $k }}" {{ $statusVal === $k ? 'selected' : '' }}>{{ $v }}</option>
          @endforeach
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- Kartu: Teks & Penjelasan --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Pertanyaan <span class="text-red-600">*</span></label>
      <textarea name="question" rows="3" class="w-full border rounded-xl px-3 py-2" required>{{ $textVal }}</textarea>
      @error('question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Penjelasan (opsional)</label>
      <textarea name="explanation" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Mengapa jawaban ini benar...">{{ $expVal }}</textarea>
      @error('explanation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
  </div>

  {{-- Kartu: Options --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5" x-show="type!=='short_answer'">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-semibold">Options</h3>
        <template x-if="type==='multiple_choice'">
          <p class="text-xs text-[#1D1C1A]/70 mt-1">Pilih tepat satu jawaban benar.</p>
        </template>
        <template x-if="type==='multiple_answer'">
          <p class="text-xs text-[#1D1C1A]/70 mt-1">Boleh lebih dari satu jawaban benar.</p>
        </template>
        <template x-if="type==='true_false'">
          <p class="text-xs text-[#1D1C1A]/70 mt-1">Opsi dikunci ke True/False.</p>
        </template>
      </div>

      <div class="flex items-center gap-2" x-show="type!=='true_false'">
        <button type="button" @click="addOption()"
                class="px-3 py-1.5 rounded-xl border hover:bg-gray-50">
          + Option
        </button>
      </div>
    </div>

    <div class="mt-4 space-y-3">
      <template x-for="(opt, i) in options" :key="i">
        <div class="flex items-start gap-3">
          <div class="flex-1">
            <input type="text"
                   class="w-full border rounded-xl px-3 py-2"
                   x-model.trim="opt.text"
                   :name="`options[${i}][text]`"
                   placeholder="Teks opsi..."
                   required>
            {{-- error per-option (opsional: tampilkan error server) --}}
            @foreach ($errors->get('options.*.text') as $msg)
              <p class="text-xs text-red-600 mt-1">{{ $msg[0] ?? '' }}</p>
            @endforeach
          </div>

          {{-- Correct toggle --}}
          <label class="inline-flex items-center gap-2 text-sm mt-2">
            <input
              type="checkbox"
              :name="`options[${i}][is_correct]`"
              value="1"
              x-model="opt.is_correct"
              @change="ensureSingleCorrect(i)"
              :disabled="type==='true_false' && (i===0 ? false : false)" {{-- tetap aktif agar TF default bisa diubah bila perlu --}}
            >
            Benar
          </label>

          {{-- Remove --}}
          <button type="button"
                  class="px-2.5 py-1.5 rounded-xl border hover:bg-gray-50 mt-1"
                  @click="removeOption(i)"
                  x-show="type!=='true_false'">
            Hapus
          </button>
        </div>
      </template>
    </div>
  </div>
</div>
