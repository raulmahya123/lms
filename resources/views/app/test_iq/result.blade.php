{{-- resources/views/app/test_iq/result.blade.php --}}
@extends('layouts.app')
@section('title', 'Hasil '.$test->title)

@push('styles')
<style>
  @keyframes gradientShift { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
  .animated-gradient { background-size:200% 200%; animation: gradientShift 6s ease infinite; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
        </svg>
        Hasil Tes IQ
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">
        {{ $test->title }}
      </h1>
    </div>

    <div class="shrink-0 flex gap-3">
      @if(empty($nextAt))
        <a href="{{ route('user.test-iq.start', $test) }}"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border-2 border-tosca text-tosca font-bold hover:bg-tosca hover:text-white transition-colors shadow-sm">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Ulangi Tes
        </a>
      @else
        <button disabled
          class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed font-bold">
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
          </svg>
          Terkunci
        </button>
      @endif
    </div>
  </div>

  @if(session('status'))
    <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 font-medium flex items-center gap-3">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ session('status') }}
    </div>
  @endif

  @if($result)
    @php
      // ===== ambil & batasi soal sesuai meta.max_questions =====
      $allQuestions = array_values($test->questions ?? []);
      $maxQ = (int) data_get($test, 'meta.max_questions', 0);
      $questions = $maxQ > 0 ? array_slice($allQuestions, 0, $maxQ) : $allQuestions;

      // ===== dasar dari submission =====
      $total     = (int)($result['total'] ?? count($questions));
      if ($total !== count($questions)) { $total = count($questions); }
      $score     = (int)($result['raw_correct'] ?? 0);
      $pct       = (int) round($total > 0 ? ($score / max(1,$total)) * 100 : 0);

      // badge heuristik
      $badge =
        $pct >= 85 ? ['bg'=>'bg-green-100','text'=>'text-green-800','label'=>'Excellent ✨', 'border'=>'border-green-200'] :
        ($pct >= 70 ? ['bg'=>'bg-blue-100','text'=>'text-blue-800','label'=>'Good 👍', 'border'=>'border-blue-200'] :
        ($pct >= 50 ? ['bg'=>'bg-amber-100','text'=>'text-amber-800','label'=>'Fair 🙂', 'border'=>'border-amber-200'] :
                      ['bg'=>'bg-red-100','text'=>'text-red-800','label'=>'Need Practice 💪', 'border'=>'border-red-200']));

      $answersMap = is_array($result['answers'] ?? null) ? $result['answers'] : [];

      $qKey = function(array $q, int $step): string {
        return (string)($q['id'] ?? $q['uuid'] ?? $q['key'] ?? $step);
      };

      $rows = [];
      foreach ($questions as $i => $q) {
        $step = $i + 1;
        $key  = $qKey($q, $step);
        $opts = array_values($q['options'] ?? []);

        $rightIdx = array_key_exists('answer_index', $q) ? $q['answer_index'] : null;
        if ($rightIdx === null && isset($q['answer']) && is_string($q['answer'])) {
            $pos = array_search($q['answer'], $opts, true);
            $rightIdx = ($pos !== false) ? (int)$pos : null;
        }

        $userIdx = $answersMap[$key] ?? null;
        if (!is_null($userIdx) && !is_int($userIdx)) {
          if (is_numeric($userIdx)) {
            $userIdx = (int)$userIdx;
          } else {
            $pos = array_search((string)$userIdx, $opts, true);
            $userIdx = ($pos !== false) ? (int)$pos : null;
          }
        }

        $isRight   = (is_int($userIdx) && is_int($rightIdx) && $userIdx === $rightIdx);
        $userText  = (is_int($userIdx)  && array_key_exists($userIdx, $opts))  ? (string)$opts[$userIdx]  : '—';
        $rightText = (is_int($rightIdx) && array_key_exists($rightIdx, $opts)) ? (string)$opts[$rightIdx] : '—';

        $rows[] = [
          'step'     => $step,
          'q'        => $q['q'] ?? ($q['text'] ?? '—'),
          'answer'   => $userText,
          'correct'  => $rightText,
          'is_right' => $isRight,
        ];
      }

      // ===== IQ & band =====
      $iq      = $result['estimated_iq'] ?? null;
      $iqLabel = $result['band'] ?? null;

      if ($iq === null) {
        $base   = (float) (config('test_iq.iq.linear.base', 70));
        $perPct = (float) (config('test_iq.iq.linear.per_percent', 0.75));
        $minIQ  = (int) config('test_iq.iq.min', 55);
        $maxIQ  = (int) config('test_iq.iq.max', 160);
        $iq     = (int) max($minIQ, min($maxIQ, round($base + $perPct * $pct)));

        $bands = (array) config('test_iq.iq.bands', []);
        $iqLabel = 'Unspecified';
        foreach ($bands as $b) {
          if ($iq >= (int)($b['min'] ?? 0)) { $iqLabel = (string)($b['label'] ?? $iqLabel); break; }
        }
      }

      $dur = (int) max(0, (int)($result['duration_sec'] ?? 0));
    @endphp

    <div class="grid gap-6 md:grid-cols-3">
      {{-- Card Skor --}}
      <div class="md:col-span-2 relative overflow-hidden rounded-3xl bg-tosca-dark text-white p-8 shadow-[0_8px_30px_rgba(7,94,84,0.15)] flex flex-col justify-between">
        <!-- Decorative bg -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 pointer-events-none"></div>
        <div class="absolute bottom-0 right-32 -mb-10 w-40 h-40 bg-tosca-light rounded-full blur-2xl opacity-10 pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between mb-8">
          <div class="text-tosca-light font-bold uppercase tracking-wider text-sm">Skor Keseluruhan</div>
          <span class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider rounded-lg {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }} border">
            {{ $badge['label'] }}
          </span>
        </div>

        <div class="relative z-10 flex items-end gap-3 mb-10">
          <div class="text-7xl font-black tracking-tighter leading-none">{{ $score }}</div>
          <div class="text-2xl text-tosca-light font-bold mb-2">/ {{ $total }}</div>
        </div>

        <div class="relative z-10">
          <div class="flex justify-between items-end mb-2">
            <div class="text-sm font-semibold text-tosca-light">Persentase Ketuntasan</div>
            <div class="text-lg font-bold">{{ $pct }}%</div>
          </div>
          <div class="w-full h-3 rounded-full bg-black/20 overflow-hidden">
            <div class="h-full rounded-full bg-white transition-all duration-1000 ease-out" style="width: {{ $pct }}%"></div>
          </div>
        </div>
      </div>

      {{-- Ringkasan --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
        <h3 class="font-bold text-text-main mb-6">Ringkasan Hasil</h3>
        
        <div class="space-y-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 font-bold">IQ</div>
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Estimasi IQ</div>
              <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-text-main">{{ $iq }}</span>
                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider bg-purple-100 text-purple-700">{{ $iqLabel }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Waktu Pengerjaan</div>
              <div class="text-lg font-bold text-text-main">{{ $dur }} <span class="text-sm text-text-soft font-medium">detik</span></div>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Tanggal Tes</div>
              <div class="text-sm font-bold text-text-main">
                @php $ts = $result['submitted_at'] ?? null; @endphp
                {{ $ts ? \Carbon\Carbon::parse($ts)->timezone(config('app.timezone','Asia/Jakarta'))->format('d M Y, H:i') : '-' }}
              </div>
            </div>
          </div>
        </div>

        @if(!empty($nextAt))
          <div class="mt-6 p-4 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-sm font-medium">
            <div class="flex gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
              </svg>
              <div>
                Tes dikunci. Coba lagi pada:
                <div class="font-bold mt-1">{{ $nextAt->timezone(config('app.timezone','Asia/Jakarta'))->format('d M Y H:i') }}</div>
                <div class="text-xs opacity-80">({{ $nextAt->diffForHumans() }})</div>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Review Jawaban --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
      <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-softbg/30">
        <h3 class="font-bold text-text-main flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-tosca-light text-tosca flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          Review Jawaban
        </h3>
        <div class="text-xs font-bold text-text-soft flex items-center gap-4 uppercase tracking-wider">
          <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span> Benar</span>
          <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span> Salah</span>
        </div>
      </div>

      <div class="divide-y divide-gray-50">
        @forelse($rows as $row)
          <div class="p-6 hover:bg-gray-50/50 transition-colors {{ $row['is_right'] ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500' }}">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
              <div class="shrink-0 w-12 text-center">
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Soal</div>
                <div class="text-xl font-black {{ $row['is_right'] ? 'text-green-600' : 'text-red-600' }}">#{{ $row['step'] }}</div>
              </div>
              
              <div class="flex-1">
                <div class="font-bold text-text-main text-lg mb-4">{{ $row['q'] }}</div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div class="bg-gray-50 rounded-xl p-4 border {{ $row['is_right'] ? 'border-green-200' : 'border-red-200' }}">
                    <div class="text-[10px] font-bold text-text-soft uppercase tracking-wider mb-1">Jawaban Anda</div>
                    <div class="font-semibold text-text-main">{{ $row['answer'] ?? '—' }}</div>
                  </div>
                  
                  @if(!$row['is_right'])
                  <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                    <div class="text-[10px] font-bold text-green-700 uppercase tracking-wider mb-1">Kunci Jawaban</div>
                    <div class="font-semibold text-text-main">{{ $row['correct'] ?? '—' }}</div>
                  </div>
                  @else
                  <div class="bg-white rounded-xl p-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-bold text-green-600">Tepat Sekali!</span>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="p-8 text-center text-text-soft font-medium">Tidak ada data jawaban untuk direview.</div>
        @endforelse
      </div>
    </div>

  @else
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-12 text-center">
      <div class="w-20 h-20 mx-auto bg-softbg text-tosca rounded-3xl flex items-center justify-center mb-6">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <h2 class="text-2xl font-bold text-text-main mb-2">Belum Ada Hasil</h2>
      <p class="text-text-soft mb-8">Anda belum menyelesaikan tes IQ ini. Mulai tes sekarang untuk melihat hasilnya.</p>
      <a href="{{ route('user.test-iq.show', ['testIq' => $test->getRouteKey()]) }}"
         class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
        Mulai Tes IQ
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </a>
    </div>
  @endif
</div>
@endsection
