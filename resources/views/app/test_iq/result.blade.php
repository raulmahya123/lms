{{-- resources/views/app/test_iq/result.blade.php --}}
@extends('app.layouts.base')
@section('title', 'Hasil '.$test->title)

@push('styles')
<style>
  /* animasi gradient & progress */
  @keyframes gradientShift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
  .animated-gradient { background-size:200% 200%; animation: gradientShift 6s ease infinite; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium
                  bg-gradient-to-r from-indigo-500/10 to-fuchsia-500/10 text-indigo-600 border border-indigo-200/40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
        </svg>
        IQ Test • Result
      </div>
      <h1 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight">
        Hasil: <span class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 bg-clip-text text-transparent">{{ $test->title }}</span>
      </h1>
    </div>

    {{-- Ulangi Test (lock = disabled + gembok) --}}
    @if(empty($nextAt))
      <a href="{{ route('user.test-iq.start', $test) }}"
         class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-indigo-200 text-indigo-700
                hover:bg-indigo-50 hover:border-indigo-300 transition-all duration-200 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v8m0 0l-3-3m3 3l3-3M4 12a8 8 0 1116 0 8 8 0 01-16 0z"/>
        </svg>
        Ulangi Test
      </a>
    @else
      <button disabled
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border bg-gray-50 text-gray-400 cursor-not-allowed
               border-gray-200">
        {{-- icon gembok --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
        </svg>
        Terkunci
      </button>
    @endif
  </div>

  {{-- Flash --}}
  @if(session('status'))
    <div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('status') }}</div>
  @endif

  @if($result)
    @php
      // Data dasar
      $questions = array_values($test->questions ?? []);
      $total     = count($questions);
      $score     = (int) ($result['score'] ?? 0);
      $pct       = $total > 0 ? round($score / $total * 100) : 0;

      $badge =
        $pct >= 85 ? ['bg'=>'bg-emerald-100','text'=>'text-emerald-800','label'=>'Excellent ✨'] :
        ($pct >= 70 ? ['bg'=>'bg-blue-100','text'=>'text-blue-800','label'=>'Good 👍'] :
        ($pct >= 50 ? ['bg'=>'bg-amber-100','text'=>'text-amber-800','label'=>'Fair 🙂'] :
                      ['bg'=>'bg-rose-100','text'=>'text-rose-800','label'=>'Need Practice 💪']));

      $answers = is_array($result['answers'] ?? null) ? $result['answers'] : [];
      $qKey = function(array $q, int $step): string { return (string)($q['id'] ?? $q['uuid'] ?? $q['key'] ?? $step); };

      $rows = [];
      foreach ($questions as $i => $q) {
        $step     = $i + 1;
        $key      = $qKey($q, $step);
        $correct  = $q['answer'] ?? null;
        $userAns  = $answers[$key] ?? null;
        $isRight  = ($correct !== null && $userAns === $correct);
        $rows[] = [
          'step'     => $step,
          'q'        => $q['q'] ?? ($q['text'] ?? null) ?? '—',
          'answer'   => $userAns,
          'correct'  => $correct,
          'is_right' => $isRight,
        ];
      }
    @endphp

    <div class="grid gap-5 md:grid-cols-3 mb-6">
      {{-- Card Skor --}}
      <div class="md:col-span-2 relative overflow-hidden rounded-2xl border
                  bg-white/70 backdrop-blur-xl shadow-sm">
        <div class="absolute inset-0 opacity-40 pointer-events-none
                    animated-gradient bg-[linear-gradient(120deg,#c7d2fe_0%,#f5d0fe_50%,#c7d2fe_100%)]"></div>
        <div class="relative p-6">
          <div class="flex items-center justify-between mb-3">
            <div class="text-gray-600">Skor</div>
            <span class="px-2 py-1 text-xs rounded {{ $badge['bg'] }} {{ $badge['text'] }} border border-white/60">
              {{ $badge['label'] }}
            </span>
          </div>
          <div class="flex items-end gap-3">
            <div class="text-5xl font-extrabold tracking-tight">{{ $score }}</div>
            <div class="text-gray-600">/ {{ $total }}</div>
          </div>

          {{-- Progress bar --}}
          <div class="mt-5">
            <div class="text-sm text-gray-600 mb-1">Persentase</div>
            <div class="w-full h-2.5 rounded-full bg-gray-200 overflow-hidden">
              <div class="h-2.5 rounded-full animated-gradient
                          bg-[linear-gradient(90deg,#6366f1,#a855f7,#22d3ee)]
                          shadow-inner" style="width: {{ $pct }}%"></div>
            </div>
            <div class="text-right text-xs text-gray-500 mt-1">{{ $pct }}%</div>
          </div>
        </div>
      </div>

      {{-- Card Ringkasan --}}
      <div class="rounded-2xl border bg-white/70 backdrop-blur-xl shadow-sm p-6">
        <div class="text-gray-600 mb-2">Ringkasan</div>
        <div class="space-y-2 text-sm">
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-slate-100">
              ⏱
            </span>
            <div><span class="text-gray-500">Waktu Kerja:</span> <span class="font-medium">{{ $result['duration_sec'] ?? '-' }} detik</span></div>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center h-6 w-6 rounded-lg bg-slate-100">
              📅
            </span>
            <div>
              <span class="text-gray-500">Tanggal:</span>
              <span class="font-medium">
                @php $ts = $result['submitted_at'] ?? null; @endphp
                {{ $ts ? \Carbon\Carbon::parse($ts)->timezone(config('app.timezone','Asia/Jakarta'))->format('d M Y H:i') : '-' }}
              </span>
            </div>
          </div>

          @if(!empty($nextAt))
            <div class="pt-2 text-xs">
              <div class="flex items-start gap-2 px-2 py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                <div>
                  Tes masih terkunci. Coba lagi pada
                  <strong>{{ $nextAt->timezone(config('app.timezone','Asia/Jakarta'))->format('d M Y H:i') }}</strong>
                  ({{ $nextAt->diffForHumans() }}).
                </div>
              </div>
            </div>
          @endif
        </div>

        {{-- CTA Coba Lagi --}}
        @if(empty($nextAt))
          <a href="{{ route('user.test-iq.start', $test) }}"
             class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl
                    bg-gradient-to-r from-indigo-600 to-fuchsia-600 text-white hover:opacity-95
                    transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v8m0 0l-3-3m3 3l3-3M4 12a8 8 0 1116 0 8 8 0 01-16 0z"/>
            </svg>
            Coba Lagi
          </a>
        @else
          <button disabled
            class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl
                   bg-gray-200 text-gray-500 cursor-not-allowed border border-gray-300">
            {{-- gembok --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            Coba Lagi (Terkunci)
          </button>
        @endif
      </div>
    </div>

    {{-- Review Jawaban --}}
    <div class="rounded-2xl border bg-white/70 backdrop-blur-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="font-semibold">Review Jawaban</div>
        <div class="text-sm text-gray-500 flex items-center gap-2">
          <span class="inline-flex h-3 w-3 rounded-full bg-emerald-400"></span> Benar
          <span class="inline-flex h-3 w-3 rounded-full bg-rose-400 ml-3"></span> Salah
        </div>
      </div>

      <div class="space-y-3">
        @forelse($rows as $row)
          <div class="group rounded-xl border p-4 transition-all duration-200
                      {{ $row['is_right'] ? 'border-emerald-300/70 bg-emerald-50/60' : 'border-rose-300/70 bg-rose-50/60' }}
                      hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between mb-2">
              <div class="text-sm text-gray-600">Soal {{ $row['step'] }}</div>
              <div class="text-xs px-2 py-0.5 rounded-full
                          {{ $row['is_right'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $row['is_right'] ? 'Correct' : 'Incorrect' }}
              </div>
            </div>
            <div class="font-medium mb-2">{{ $row['q'] }}</div>
            <div class="text-sm grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div><span class="text-gray-500">Jawabanmu:</span> <span class="font-medium">{{ $row['answer'] ?? '—' }}</span></div>
              <div><span class="text-gray-500">Kunci:</span> <span class="font-medium">{{ $row['correct'] ?? '—' }}</span></div>
            </div>
          </div>
        @empty
          <div class="rounded-xl border p-4 bg-gray-50 text-gray-600">Tidak ada data jawaban.</div>
        @endforelse
      </div>
    </div>

  @else
    {{-- kosong --}}
    <div class="p-6 rounded-2xl border bg-white/70 backdrop-blur-xl shadow-sm">
      <p class="text-gray-600 mb-4">Belum ada hasil test untuk kamu.</p>
     <a href="{{ route('user.test-iq.show', ['testIq' => $test->getRouteKey()]) }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
          bg-gradient-to-r from-indigo-600 to-fuchsia-600 
          text-white shadow-md hover:opacity-95 transition">
    Mulai Tes
</a>

    </div>
  @endif
</div>
@endsection
