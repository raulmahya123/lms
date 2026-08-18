@extends('layouts.app')

@section('title', $test->name)

@section('content')
@php
  $slugId = $test->slug ?: $test->id;
  $hasQuestions = ($test->questions_count ?? 0) > 0;
@endphp

<div class="max-w-5xl mx-auto space-y-8">

  {{-- Header + CTA --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8 md:p-10 relative overflow-hidden">
    {{-- Decorative elements --}}
    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-tosca-light rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 -mb-10 w-40 h-40 bg-purple-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-8">
      <div class="min-w-0 flex-1">
        <a href="{{ route('app.psy.tests.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-tosca hover:text-tosca-dark transition-colors mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Semua Tes
        </a>
        
        <h1 class="text-3xl md:text-4xl font-extrabold text-text-main leading-tight mb-4">
          {{ $test->name }}
        </h1>

        <div class="flex flex-wrap items-center gap-2 mb-6">
          <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-softbg text-tosca-dark uppercase tracking-wider">{{ strtoupper($test->type) }}</span>
          <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 uppercase tracking-wider">{{ ucfirst($test->track) }}</span>
          <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 uppercase tracking-wider">{{ $test->questions_count }} Soal</span>
          @if(!empty($test->time_limit_min))
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 uppercase tracking-wider">⏳ {{ (int)$test->time_limit_min }} menit</span>
          @endif
          @if(!$hasQuestions)
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 uppercase tracking-wider">Belum ada soal</span>
          @endif
        </div>

        @if(!empty(optional($test)->description))
          <p class="text-text-soft leading-relaxed max-w-3xl">{{ $test->description }}</p>
        @endif
      </div>

      <div class="shrink-0 md:w-64">
        <div class="bg-white/60 backdrop-blur-md border border-gray-100 rounded-2xl p-5 shadow-sm">
          <div class="text-center mb-4">
            <div class="w-16 h-16 mx-auto bg-softbg text-tosca rounded-full flex items-center justify-center mb-3 shadow-inner">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-bold text-text-main">Siap Mengikuti Tes?</h3>
            <p class="text-xs text-text-soft mt-1">Pastikan Anda berada di tempat yang tenang.</p>
          </div>
          
          @if($hasQuestions)
            <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}">
              @csrf
              <button class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                Mulai Tes
              </button>
            </form>
          @else
            <button class="w-full py-3 px-4 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed">
              Tidak bisa mulai
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Info ringkas --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex items-center gap-5 hover:border-tosca/30 hover:shadow-tosca/10 transition-all">
      <div class="w-14 h-14 rounded-2xl bg-softbg text-tosca flex items-center justify-center text-2xl shrink-0">🧩</div>
      <div>
        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Jumlah Soal</div>
        <div class="text-2xl font-extrabold text-text-main">{{ $test->questions_count }}</div>
      </div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex items-center gap-5 hover:border-purple-200 hover:shadow-purple-500/10 transition-all">
      <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0">🗂️</div>
      <div>
        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Tipe</div>
        <div class="text-xl font-extrabold text-text-main">{{ strtoupper($test->type) }}</div>
      </div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex items-center gap-5 hover:border-amber-200 hover:shadow-amber-500/10 transition-all">
      <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0">🕒</div>
      <div>
        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Batas Waktu</div>
        <div class="text-xl font-extrabold text-text-main">
          {{ !empty($test->time_limit_min) ? (int)$test->time_limit_min.' menit' : 'Tanpa batas' }}
        </div>
      </div>
    </div>
  </div>

  {{-- Daftar Soal (preview) --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-50 bg-softbg/30 flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-tosca-light text-tosca flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
      </div>
      <h2 class="text-lg font-bold text-text-main">Preview Daftar Soal</h2>
    </div>

    <div class="divide-y divide-gray-50">
      @forelse($test->questions as $q)
        <div class="px-8 py-6 hover:bg-gray-50/50 transition-colors">
          <div class="font-bold text-text-main mb-3 flex items-start gap-3">
            <span class="text-tosca">{{ $loop->iteration }}.</span>
            <span>{{ $q->prompt }}</span>
          </div>
          
          @if($q->options->count())
            <div class="ml-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
              @foreach($q->options as $op)
                <div class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 bg-white">
                  <div class="w-5 h-5 rounded-full border-2 border-gray-200 shrink-0 mt-0.5"></div>
                  <span class="text-sm text-text-soft">{{ $op->label }}</span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @empty
        <div class="px-8 py-16 text-center">
          <div class="w-16 h-16 mx-auto bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <h3 class="font-bold text-text-main">Belum Ada Soal</h3>
          <p class="text-text-soft mt-1">Tes ini masih kosong dan sedang dalam tahap persiapan.</p>
        </div>
      @endforelse
    </div>
  </div>

</div>
@endsection
