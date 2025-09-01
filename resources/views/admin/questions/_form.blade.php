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
        ->filter(fn($o) => $o['text'] !== '')
        ->values()
        ->all();
  } else {
      $options = collect(optional($question)->options ?? [])
        ->map(fn($o) => ['text' => $o->text ?? '', 'is_correct' => (bool)($o->is_correct ?? false)])
        ->filter(fn($o) => $o['text'] !== '')
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
    addOption(){ this.options.push({text:'',is_correct:false}) },
    removeOption(i){ this.options.splice(i,1) },
    ensureSingleCorrect(i){
      if(this.type==='multiple_choice'){
        this.options = this.options.map((o,idx)=>({ ...o, is_correct: idx===i }));
      }
    },
    setTrueFalse(){
      this.options = JSON.parse(@js(json_encode($tfDefault)));
    }
  }"
  x-init="if(type==='true_false'){ setTrueFalse() }"
  class="space-y-6"
>
  <div class="grid md:grid-cols-2 gap-4">
    {{-- QUIZ --}}
    <div>
      <label class="block text-sm font-medium mb-1">Quiz</label>
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
      <select name="type" x-model="type" @change="if(type==='true_false'){ setTrueFalse() }" class="w-full border rounded-xl px-3 py-2">
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

  {{-- QUESTION TEXT --}}
  <div>
    <label class="block text-sm font-medium mb-1">Pertanyaan</label>
    <textarea name="question" rows="3" class="w-full border rounded-xl px-3 py-2" required>{{ $textVal }}</textarea>
    @error('question') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- EXPLANATION --}}
  <div>
    <label class="block text-sm font-medium mb-1">Penjelasan (opsional)</label>
    <textarea name="explanation" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Mengapa jawaban ini benar...">{{ $expVal }}</textarea>
    @error('explanation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- OPTIONS --}}
  <div x-show="type!=='short_answer'">
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium">Options</label>
      <div class="flex items-center gap-2" x-show="type!=='true_false'">
        <button type="button" @click="addOption()" class="px-3 py-1.5 rounded-xl border">+ Option</button>
      </div>
    </div>

    <template x-if="type==='multiple_choice'">
      <p class="text-xs text-[#1D1C1A]/70 mt-1">Pilih tepat satu jawaban benar.</p>
    </template>
    <template x-if="type==='multiple_answer'">
      <p class="text-xs text-[#1D1C1A]/70 mt-1">Boleh lebih dari satu jawaban benar.</p>
    </template>

    <div class="mt-3 space-y-3">
      <template x-for="(opt, i) in options" :key="i">
        <div class="flex items-center gap-3">
          <input type="text" class="flex-1 border rounded-xl px-3 py-2" x-model="opt.text" :name="`options[${i}][text]`" placeholder="Teks opsi..." required>

          {{-- Correct toggle --}}
          <label class="inline-flex items-center gap-2 text-sm">
            <input
              type="checkbox"
              :name="`options[${i}][is_correct]`"
              value="1"
              x-model="opt.is_correct"
              @change="ensureSingleCorrect(i)"
            >
            Benar
          </label>

          {{-- Remove --}}
          <button type="button" class="px-2.5 py-1.5 rounded-xl border" @click="removeOption(i)" x-show="type!=='true_false'">Hapus</button>
        </div>
      </template>
    </div>
  </div>
</div>
