{{-- resources/views/admin/questions/_form.blade.php --}}
@php
  $quizVal   = old('quiz_id', optional($question)->quiz_id);
  $typeVal   = old('type', optional($question)->type ?? 'mcq');    // sinkron dgn controller
  $pointsVal = old('points', optional($question)->points ?? 1);
  $promptVal = old('prompt', optional($question)->prompt);
@endphp

<div class="space-y-6">
  {{-- Kartu: Meta --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <h3 class="font-semibold mb-4">Detail Pertanyaan</h3>

    <div class="grid md:grid-cols-2 gap-4">
      {{-- QUIZ --}}
      <div>
        <label for="quiz_id" class="block text-sm font-medium mb-1">
          Quiz <span class="text-red-600">*</span>
        </label>
        <select id="quiz_id" name="quiz_id" class="w-full border rounded-xl px-3 py-2" required>
          <option value="" disabled {{ $quizVal ? '' : 'selected' }}>— pilih quiz —</option>
          @foreach ($quizzes as $qz)
            <option value="{{ $qz->id }}" {{ (string)$quizVal === (string)$qz->id ? 'selected' : '' }}>
              {{ $qz->title ?? $qz->name ?? ('Quiz #'.$qz->id) }}
            </option>
          @endforeach
        </select>
        @error('quiz_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- TYPE (mcq|short|long) --}}
      <div>
        <label for="type" class="block text-sm font-medium mb-1">
          Tipe <span class="text-red-600">*</span>
        </label>
        <select id="type" name="type" class="w-full border rounded-xl px-3 py-2" required>
          <option value="mcq"  {{ $typeVal === 'mcq'  ? 'selected' : '' }}>Multiple Choice (1 jawaban benar)</option>
          <option value="short"{{ $typeVal === 'short'? 'selected' : '' }}>Jawaban Singkat</option>
          <option value="long" {{ $typeVal === 'long' ? 'selected' : '' }}>Esai / Jawaban Panjang</option>
        </select>
        @error('type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- POINTS (min 1) --}}
      <div>
        <label for="points" class="block text-sm font-medium mb-1">Poin</label>
        <input id="points" type="number" name="points" min="1" step="1"
               value="{{ $pointsVal }}"
               class="w-full border rounded-xl px-3 py-2">
        @error('points') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>
  </div>

  {{-- Kartu: Prompt --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5">
    <label for="prompt" class="block text-sm font-medium mb-1">
      Pertanyaan / Prompt <span class="text-red-600">*</span>
    </label>
    <textarea id="prompt" name="prompt" rows="5" required
              class="w-full border rounded-xl px-3 py-2"
    >{{ $promptVal }}</textarea>
    @error('prompt') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>
</div>
