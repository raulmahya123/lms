{{-- resources/views/admin/psy_questions/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit Question — BERKEMAH')

@section('content')
@php
  /** @var \App\Models\PsyQuestion $question */
@endphp

<div class="max-w-4xl mx-auto space-y-6"
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
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-blue-900">Edit Question</h1>
      <p class="text-sm text-blue-700/70">Ubah detail pertanyaan & opsi jawaban.</p>
    </div>
    <a href="{{ route('admin.psy-questions.show',$question) }}"
       class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
      ← Back
    </a>
  </div>

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.psy-questions.update',['psy_question'=>$question->id]) }}" class="space-y-6">
    @csrf @method('PUT')

    {{-- Prompt --}}
    <div>
      <label class="block text-sm font-medium mb-1">Prompt</label>
      <textarea name="prompt" rows="3" required
        class="w-full border rounded-xl px-3 py-2">{{ old('prompt',$question->prompt) }}</textarea>
    </div>

    {{-- Trait / Type / Ordering --}}
    <div class="grid md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Trait Key</label>
        <input type="text" name="trait_key" value="{{ old('trait_key',$question->trait_key) }}"
          class="w-full border rounded-xl px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="qtype" class="w-full border rounded-xl px-3 py-2" required>
          <option value="likert" @selected($question->qtype==='likert')>Likert</option>
          <option value="mcq" @selected($question->qtype==='mcq')>Multiple Choice</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Ordering</label>
        <input type="number" name="ordering" value="{{ old('ordering',$question->ordering) }}"
          class="w-full border rounded-xl px-3 py-2">
      </div>
    </div>

    {{-- Options --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-blue-900">Options</h2>
        <button type="button" @click="addOption"
          class="text-sm px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
          + Add Option
        </button>
      </div>

      <template x-for="(opt,i) in options" :key="i">
        <div class="flex gap-3 items-center border rounded-xl p-3 bg-white">
          <input type="hidden" :name="`options[${i}][id]`" x-model="opt.id">
          <div class="flex-1">
            <input type="text" :name="`options[${i}][label]`" x-model="opt.label"
              placeholder="Label"
              class="w-full border rounded-xl px-3 py-2">
          </div>
          <div class="w-28">
            <input type="number" :name="`options[${i}][value]`" x-model="opt.value"
              placeholder="Value"
              class="w-full border rounded-xl px-3 py-2">
          </div>
          <button type="button" @click="removeOption(i)"
            class="px-3 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50">
            ✕
          </button>
        </div>
      </template>
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-3">
      <button class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
        Save Changes
      </button>
      <a href="{{ route('admin.psy-questions.show',$question) }}"
         class="px-5 py-2 rounded-xl border hover:bg-gray-50">Cancel</a>
    </div>
  </form>
</div>
@endsection
