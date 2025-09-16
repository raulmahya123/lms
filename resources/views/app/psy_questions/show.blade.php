@extends('app.layouts.base')

@section('title', 'Soal — '.$test->name)

@push('styles')
<style>
  /* clean, minimal, gen-z-ish */
  .btn{border-radius:12px;padding:.6rem 1rem;font-weight:600}
  .btn-primary{background:#111827;color:#fff}
  .btn-outline{border:1px solid #e5e7eb;background:#fff}
  .chip{display:inline-flex;align-items:center;padding:.25rem .6rem;border:1px solid #e5e7eb;border-radius:999px;font-size:.75rem;background:#f9fafb}
  .bar{height:8px;border-radius:999px;background:#f1f5f9;overflow:hidden}
  .bar>span{display:block;height:100%;background:#111827}
  .timebar{height:6px;border-radius:999px;background:#f1f5f9;overflow:hidden}
  .timebar>span{display:block;height:100%;background:#111827}
  .choice{border:1px solid #e5e7eb;border-radius:14px;padding:.9rem 1rem;transition:.15s ease}
  .choice:hover{background:#f8fafc}
  .choice input{accent-color:#111827}
  .choice--active{border-color:#111827;background:#1118270f}
  .kbd{border:1px solid #e5e7eb;border-radius:8px;padding:.1rem .35rem;font-size:.75rem;background:#fff}
  .sheet{position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:flex-end;z-index:60}
  .sheet>div{background:#fff;width:100%;max-height:70vh;border-top-left-radius:16px;border-top-right-radius:16px}
  .qbtn{width:42px;height:42px;display:grid;place-items:center;border-radius:12px;border:1px solid #e5e7eb}
  .qbtn--done{background:#111827;color:#fff;border-color:#111827}
  .qbtn--current{box-shadow:0 0 0 2px #111827 inset}
</style>
@endpush

@section('content')
@php
  $ids      = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
  $pos      = array_search($question->id, $ids, true);
  $current  = $pos === false ? 1 : ($pos + 1);
  $total    = count($ids);
  $pct      = $total ? intval($current / $total * 100) : 0;

  $currentAnswer   = $currentAnswer ?? null;
  $answerOptionId  = old('option_id', $currentAnswer->option_id ?? null);
  $answerValue     = old('value',     $currentAnswer->value     ?? null);

  $answeredIds     = $answeredIds ?? [];
  $slugId          = $test->slug ?: $test->id;
@endphp

<div class="max-w-3xl mx-auto px-4 py-4 md:py-8"
     x-data="ui({
        secondsLeft: {{ (int)($secondsLeft ?? 0) }},
        timeLimitMin: {{ (int)($timeLimitMin ?? 0) }},
        nextUrl: @js($nextId ? route('app.psytests.questions.show', [$slugId, $nextId]) : null),
        prevUrl: @js($prevId ? route('app.psytests.questions.show', [$slugId, $prevId]) : null),
        finishUrl: @js(!$nextId ? route('app.psy.attempts.submit', $slugId) : null),
        pickedInit: {{ $answerOptionId ? (int)$answerOptionId : 'null' }}
      })"
     x-init="init()">

  <!-- top -->
  <div class="sticky top-0 z-40 bg-white/80 backdrop-blur py-3 border-b">
    <div class="max-w-3xl mx-auto flex items-center justify-between gap-3 px-4">
      <div class="min-w-0">
        <a href="{{ route('app.psytests.show', $slugId) }}" class="text-sm text-gray-700 hover:underline">← {{ $test->name }}</a>
        <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
          <span class="chip">{{ $current }} / {{ $total }}</span>
          <div class="bar w-32"><span style="width: {{ $pct }}%"></span></div>
          @if(($timeLimitMin ?? 0) > 0)
            <span class="chip">⏳ <span x-text="fmt(left)"></span></span>
          @endif
          <button type="button" class="btn btn-outline text-sm py-1 px-2" @click="openSheet=true">Daftar Soal</button>
        </div>
      </div>
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
  @endif

  @if(($timeLimitMin ?? 0) > 0)
    <div class="text-xs text-gray-600 mt-4">
      <div class="timebar"><span :style="`width:${percent()}%`"></span></div>
      <div class="mt-1">Sisa <span class="font-medium" x-text="fmt(left)"></span></div>
    </div>
  @endif

  <!-- card -->
  <div class="bg-white border rounded-2xl p-5 mt-4 md:mt-6">
    <div class="flex items-start justify-between gap-3">
      <h2 class="text-lg md:text-xl font-semibold">{{ $question->prompt }}</h2>
      @if(!empty($question->trait_key))
        <span class="chip">{{ strtoupper($question->trait_key) }}</span>
      @endif
    </div>

    <form id="answerForm"
          method="POST"
          action="{{ route('app.psy.attempts.answer', [$slugId, $question->getKey()]) }}"
          class="mt-5 space-y-4"
          x-bind:class="timeUp ? 'opacity-60 pointer-events-none' : ''"
          @submit="
            submitting=true;
            // Disable interaktif, jangan disable hidden inputs (_token)
            $el.querySelectorAll('button, a, select, input[type=radio], input[type=checkbox], input[type=number], input[type=text]').forEach(el => el.setAttribute('disabled','disabled'));
          ">
      @csrf

      @if($question->options->count())
        <div class="grid gap-2">
          @foreach($question->options as $i => $op)
            <label class="choice cursor-pointer"
                   :class="picked == {{ $op->id }} ? 'choice--active' : ''">
              <div class="flex items-center gap-3">
                <input type="radio"
                       name="option_id"
                       value="{{ $op->id }}"
                       class="h-4 w-4"
                       @checked($answerOptionId == $op->id)
                       @change="picked={{ $op->id }}"
                       required>
                <div class="flex items-center gap-2">
                  <span class="kbd">{{ $i+1 }}</span>
                  <span>{{ $op->label }}</span>
                </div>
              </div>
            </label>
          @endforeach
        </div>
        @error('option_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      @else
        <div>
          <input type="number" name="value" class="border rounded-lg px-3 py-2 w-40" value="{{ $answerValue }}" required
                 placeholder="Nilai">
          @error('value') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      @endif

      <div class="flex items-center justify-between pt-2">
        <div class="flex items-center gap-2">
          @if($prevId)
            <a class="btn btn-outline" href="{{ route('app.psytests.questions.show', [$slugId, $prevId]) }}">← Sebelumnya</a>
          @endif
          <button type="button" class="btn btn-outline" @click="openSheet=true">Daftar Soal</button>
        </div>

        @if($nextId)
          {{-- Soal belum terakhir --}}
          <button type="submit" class="btn btn-primary" :disabled="timeUp || submitting">
            Simpan & Lanjut
          </button>
        @else
          {{-- Soal terakhir: langsung Selesai & Hitung --}}
          <button type="submit" class="btn btn-primary" :disabled="timeUp || submitting">
            Selesai & Hitung
          </button>
        @endif
      </div>
    </form>

    <template x-if="timeUp">
      <div class="mt-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        Waktu habis. Mengakhiri tes…
      </div>
    </template>
  </div>

  <div class="flex justify-between text-sm mt-4">
    @if($prevId)
      <a class="text-gray-700 hover:underline" href="{{ route('app.psytests.questions.show', [$slugId, $prevId]) }}">← Sebelumnya</a>
    @else <span></span>
    @endif
    @if($nextId)
      <a class="text-gray-700 hover:underline" href="{{ route('app.psytests.questions.show', [$slugId, $nextId]) }}">Berikutnya →</a>
    @endif
  </div>
</div>

<!-- Drawer -->
<div x-show="openSheet" x-transition.opacity class="sheet" style="display:none" @click.self="openSheet=false">
  <div class="p-5">
    <div class="flex items-center justify-between mb-3">
      <div class="font-semibold">Daftar Soal</div>
      <button class="btn btn-outline py-1 px-3" @click="openSheet=false">Tutup</button>
    </div>
    <div class="grid grid-cols-8 sm:grid-cols-10 gap-2 p-2">
      @foreach($ids as $i => $qid)
        @php
          $done = in_array($qid, $answeredIds, true);
          $isCurrent = $qid === $question->id;
        @endphp
        <a href="{{ route('app.psytests.questions.show', [$slugId, $qid]) }}"
           class="qbtn {{ $done ? 'qbtn--done' : '' }} {{ $isCurrent ? 'qbtn--current' : '' }}"
           @click="openSheet=false" title="Soal {{ $i+1 }}">{{ $i+1 }}</a>
      @endforeach
    </div>
    <div class="h-2"></div>
  </div>
</div>

<script>
function ui({secondsLeft, timeLimitMin, nextUrl, prevUrl, finishUrl, pickedInit}) {
  const full = Math.max(0, parseInt(timeLimitMin || 0, 10)) * 60;
  return {
    left: Math.max(0, parseInt(secondsLeft || 0, 10)),
    timeUp: false,
    submitting: false,
    openSheet: false,
    picked: pickedInit ?? null,
    tickId: null,

    fmt(s){ if(!timeLimitMin) return '—'; const m=Math.floor(s/60),sec=s%60; return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`; },
    percent(){ if(!full) return 0; return Math.max(0, Math.min(100, Math.round((this.left/full)*100))); },

    init(){
      if(timeLimitMin){
        if(!this.left) this.left = full;
        this.tickId = setInterval(() => {
          if(this.left > 0) this.left--;
          if(this.left <= 0){
            clearInterval(this.tickId);
            this.timeUp = true;
            const form = document.getElementById('answerForm');
            if(form){
              const auto = document.createElement('input');
              auto.type='hidden'; auto.name='_autosubmit'; auto.value='1';
              form.appendChild(auto); form.submit();
            } else if(finishUrl){ window.location.href = finishUrl; }
          }
        }, 1000);
      }

      // hotkeys (ringkas)
      window.addEventListener('keydown', (e) => {
        if (/^[1-9]$/.test(e.key)) {
          const idx = parseInt(e.key, 10) - 1;
          const radios = Array.from(document.querySelectorAll('input[type=radio][name=option_id]'));
          if (radios[idx]) { radios[idx].checked = true; this.picked = parseInt(radios[idx].value, 10); }
        }
        if (e.key === 'ArrowLeft' && prevUrl) { e.preventDefault(); window.location.href = prevUrl; }
        if (e.key === 'ArrowRight' && nextUrl) { e.preventDefault(); window.location.href = nextUrl; }
        if (e.key === 'Enter' && !e.metaKey && !e.ctrlKey) {
          const form = document.getElementById('answerForm');
          if (form) { e.preventDefault(); form.requestSubmit(); }
        }
        if (e.key === '?') { e.preventDefault(); this.openSheet = !this.openSheet; }
      });

      window.addEventListener('beforeunload', (e) => {
        if (timeLimitMin && !this.timeUp && !this.submitting) { e.preventDefault(); e.returnValue=''; }
      });
    }
  }
}
</script>
@endsection
