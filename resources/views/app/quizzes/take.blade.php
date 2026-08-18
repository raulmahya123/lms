@extends('layouts.app')

@section('title','Kuis: '.$quiz->title)

@section('content')
@php
  /** @var array<string,string> $answers */
  $answers = $answers ?? [];
@endphp

<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-tosca-light rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    
    <div class="relative z-10">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-softbg text-tosca-dark text-xs font-bold uppercase tracking-wider mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Sesi Kuis Aktif
      </div>
      <h1 class="text-3xl font-extrabold text-text-main leading-tight">{{ $quiz->title }}</h1>
      <p class="text-text-soft mt-2">Jawab semua pertanyaan dengan cermat. Pastikan Anda memeriksa kembali jawaban sebelum menekan tombol kirim.</p>
    </div>
  </div>

  {{-- Quiz Form --}}
  <form id="quizForm" method="POST" action="{{ route('app.quiz.submit', $quiz) }}" class="space-y-6">
    @csrf
    <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

    @foreach($quiz->questions as $q)
      <input type="hidden" name="question_ids[]" value="{{ $q->id }}">
    @endforeach

    <div class="space-y-6">
      @foreach($quiz->questions as $q)
        @php
          $fieldName = "answers.{$q->id}";
          $oldValue  = old("answers.{$q->id}", $answers[$q->id] ?? null);
          $hasError  = $errors->has($fieldName);
        @endphp

        <div class="bg-white rounded-3xl border {{ $hasError ? 'border-red-200 shadow-[0_4px_20px_rgba(239,68,68,0.1)]' : 'border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]' }} p-6 sm:p-8 hover:border-tosca/30 transition-colors">
          <div class="flex items-start justify-between gap-4 mb-6">
            <h3 class="font-bold text-lg text-text-main leading-relaxed flex items-start gap-3">
              <span class="text-tosca">{{ $loop->iteration }}.</span>
              {{ $q->prompt }}
            </h3>
            <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-500 font-bold text-xs whitespace-nowrap">
              {{ $q->points }} Pts
            </span>
          </div>

          <div class="pl-0 sm:pl-7 space-y-3">
            @if($q->type === 'mcq')
              @forelse($q->options as $opt)
                @php
                  $id = "q{$q->id}_opt{$opt->id}";
                  $isChecked = ((string)$oldValue === (string)$opt->id);
                @endphp
                <label for="{{ $id }}" class="group flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 {{ $isChecked ? 'border-tosca bg-tosca-light/30' : 'border-gray-100 hover:border-tosca/50' }}">
                  <input
                    id="{{ $id }}"
                    type="radio"
                    name="answers[{{ $q->id }}]"
                    value="{{ $opt->id }}"
                    class="peer sr-only"
                    required
                    @checked($isChecked)
                  >
                  
                  <div class="relative shrink-0 flex items-center justify-center w-6 h-6 rounded-full border-2 transition-colors {{ $isChecked ? 'border-tosca bg-tosca' : 'border-gray-300 group-hover:border-tosca/50' }} mt-0.5">
                    @if($isChecked)
                      <div class="w-2 h-2 rounded-full bg-white"></div>
                    @endif
                  </div>
                  
                  <span class="font-medium pt-0.5 {{ $isChecked ? 'text-tosca-dark font-bold' : 'text-text-main' }}">
                    {{ $opt->text }}
                  </span>
                </label>
              @empty
                <div class="p-4 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold flex items-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  Tidak ada opsi untuk soal ini.
                </div>
              @endforelse
            @else
              <textarea
                name="answers[{{ $q->id }}]"
                class="w-full rounded-2xl border-2 border-gray-100 p-4 focus:outline-none focus:border-tosca focus:ring-4 focus:ring-tosca/10 transition-all resize-y min-h-[120px] font-medium text-text-main placeholder-gray-400"
                placeholder="Tuliskan jawaban Anda di sini..."
                required
              >{{ old($fieldName, $oldValue) }}</textarea>
            @endif

            @error("answers.{$q->id}")
              <p class="text-sm font-bold text-red-500 mt-2 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $message }}
              </p>
            @enderror
          </div>
        </div>
      @endforeach
    </div>

    {{-- Submission Area --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 sm:p-8 sticky bottom-6 z-20 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-softbg text-tosca flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <span class="text-sm font-semibold text-text-soft">Pastikan semua soal sudah terjawab dengan benar.</span>
      </div>
      
      <button
        type="submit"
        class="w-full sm:w-auto px-8 py-3.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-colors shadow-sm shadow-green-600/30 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed group"
      >
        <span>Kirim Jawaban</span>
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>
    </div>
  </form>
</div>

{{-- Script for dynamic styling and double-submit prevention --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Prevent double submit
    const form = document.getElementById('quizForm');
    if(form) {
      form.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) { 
          btn.disabled = true; 
          btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...`;
        }
      });
    }

    // Dynamic styling for radio buttons
    const radioInputs = document.querySelectorAll('input[type="radio"]');
    radioInputs.forEach(input => {
      input.addEventListener('change', function() {
        const groupName = this.getAttribute('name');
        const allInGroup = document.querySelectorAll(`input[name="${groupName}"]`);
        
        allInGroup.forEach(radio => {
          const label = radio.closest('label');
          const circle = label.querySelector('div.w-6.h-6');
          const text = label.querySelector('span.font-medium');
          
          if(radio.checked) {
            label.classList.add('border-tosca', 'bg-tosca-light/30');
            label.classList.remove('border-gray-100');
            
            circle.classList.add('border-tosca', 'bg-tosca');
            circle.classList.remove('border-gray-300');
            circle.innerHTML = '<div class="w-2 h-2 rounded-full bg-white"></div>';
            
            text.classList.add('text-tosca-dark', 'font-bold');
            text.classList.remove('text-text-main');
          } else {
            label.classList.remove('border-tosca', 'bg-tosca-light/30');
            label.classList.add('border-gray-100');
            
            circle.classList.remove('border-tosca', 'bg-tosca');
            circle.classList.add('border-gray-300');
            circle.innerHTML = '';
            
            text.classList.remove('text-tosca-dark', 'font-bold');
            text.classList.add('text-text-main');
          }
        });
      });
    });
  });
</script>
@endsection
