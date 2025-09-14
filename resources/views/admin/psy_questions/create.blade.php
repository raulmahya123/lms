{{-- resources/views/admin/psy_questions/create.blade.php --}}
@extends('layouts.admin')
@section('title','New Question — '.($currentTest->name ?? 'All Tests'))

@section('content')
<div x-data="questionForm()" class="max-w-3xl mx-auto space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">
      Add Question • {{ $currentTest->name ?? 'Pilih Test' }}
    </h1>

    <a href="{{ route('admin.psy-questions.index', array_filter(['psy_test_id' => $selected])) }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back</a>
  </div>

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.psy-questions.store') }}"
        class="bg-white border rounded-2xl p-6 space-y-5">
    @csrf

    {{-- Test --}}
    <div>
      <label class="block text-sm font-medium mb-1">Test <span class="text-red-500">*</span></label>
      <select name="psy_test_id" class="w-full border rounded-xl p-3" required>
        <option value="">— pilih —</option>
        @foreach($tests as $t)
          <option value="{{ $t->id }}" @selected((int)$selected === $t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
      @error('psy_test_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Prompt --}}
    <div>
      <label class="block text-sm font-medium mb-1">Prompt <span class="text-red-500">*</span></label>
      <textarea name="prompt" rows="4" class="w-full border rounded-xl p-3" required>{{ old('prompt') }}</textarea>
      @error('prompt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Trait Key --}}
    <div>
      <label class="block text-sm font-medium mb-1">Trait Key (opsional)</label>
      <input type="text" name="trait_key" value="{{ old('trait_key') }}"
             class="w-full border rounded-xl p-2"
             placeholder="misal: logic, openness, conscientiousness">
      @error('trait_key') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Type --}}
    <div>
      <label class="block text-sm font-medium mb-1">Question Type <span class="text-red-500">*</span></label>
      <select name="qtype" x-model="qtype" class="w-full border rounded-xl p-2" required>
        <option value="likert" @selected(old('qtype')==='likert')>Likert</option>
        <option value="mcq" @selected(old('qtype')==='mcq')>MCQ</option>
      </select>
      @error('qtype') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Ordering --}}
    <div>
      <label class="block text-sm font-medium mb-1">Ordering</label>
      <input type="number" name="ordering" value="{{ old('ordering',0) }}" min="0"
             class="w-full border rounded-xl p-2">
      @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Options: Likert --}}
    <template x-if="qtype==='likert'">
      <div>
        <label class="block text-sm font-medium mb-2">Likert Options</label>
        <p class="text-xs text-gray-500 mb-3">
          Default skala: Sangat Tidak Setuju (-2), Tidak Setuju (-1), Netral (0), Setuju (1), Sangat Setuju (2)
        </p>
        <div class="grid grid-cols-1 gap-2">
          <template x-for="(opt,i) in likertDefaults" :key="i">
            <div class="flex gap-2">
              <input type="text" :name="`options[${i}][label]`" x-model="opt.label"
                     class="w-full border rounded-xl p-2">
              <input type="number" :name="`options[${i}][value]`" x-model="opt.value"
                     class="w-28 border rounded-xl p-2">
            </div>
          </template>
        </div>
      </div>
    </template>

    {{-- Options: MCQ --}}
    <template x-if="qtype==='mcq'">
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-sm font-medium">MCQ Options</label>
          <button type="button" @click="addOption()"
                  class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">+ Add Option</button>
        </div>
        <div class="grid grid-cols-1 gap-2">
          <template x-for="(opt,i) in options" :key="i">
            <div class="flex gap-2 items-center">
              <input type="text" class="w-full border rounded-xl p-2"
                     :name="`options[${i}][label]`" x-model="opt.label" placeholder="Option label">
              <input type="number" class="w-28 border rounded-xl p-2"
                     :name="`options[${i}][value]`" x-model.number="opt.value" placeholder="Value">
              <button type="button" @click="removeOption(i)"
                      class="px-2 py-1 rounded border border-red-200 text-red-700 hover:bg-red-50">
                Remove
              </button>
            </div>
          </template>
        </div>
      </div>
    </template>

    {{-- Submit --}}
    <div class="pt-2">
      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
        Create Question
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
    // default dari old('options') atau 1 baris kosong
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
