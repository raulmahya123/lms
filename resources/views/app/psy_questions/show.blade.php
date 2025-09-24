@extends('app.layouts.base')

@section('title', 'Soal — '.$test->name)

@push('styles')
<style>
  :root{
    --blue-1:#2563eb; /* primary */
    --blue-2:#3b82f6; /* light */
    --blue-3:#1e40af; /* dark */
    --ink:#0f172a;    /* slate-900 */
    --muted:#64748b;  /* slate-500 */
    --panel:#ffffff;  /* white */
    --border:#e5e7eb; /* gray-200 */
    --bg:#f8fafc;     /* slate-50 */
  }

  body{background:var(--bg)}
  .card{background:var(--panel);border:1px solid var(--border);border-radius:18px}

  .btn{border-radius:12px;padding:.65rem 1rem;font-weight:700;letter-spacing:.01em;transition:.2s ease;display:inline-flex;align-items:center;gap:.5rem}
  .btn:disabled{opacity:.6;cursor:not-allowed}
  .btn-primary{background:linear-gradient(135deg,var(--blue-1),var(--blue-2));color:#fff;box-shadow:0 8px 24px rgba(37,99,235,.25)}
  .btn-primary:hover{filter:brightness(.98)}
  .btn-outline{border:1px solid var(--border);background:#fff;color:var(--ink)}

  .link{color:var(--blue-1)}
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.3rem .65rem;border-radius:999px;background:linear-gradient(135deg,#fff,#f3f6ff);border:1px solid #e0e7ff;color:#1f2937;font-size:.75rem;font-weight:600}

  .bar{height:10px;border-radius:999px;background:#eef2ff;overflow:hidden;box-shadow:inset 0 0 0 1px #e0e7ff}
  .bar>span{display:block;height:100%;background:linear-gradient(90deg,var(--blue-3),var(--blue-1),var(--blue-2))}

  .timebar{height:6px;border-radius:999px;background:#eef2ff;overflow:hidden}
  .timebar>span{display:block;height:100%;background:linear-gradient(90deg,var(--blue-1),var(--blue-2))}

  .choice{border:1px solid var(--border);border-radius:14px;padding:.95rem 1rem;transition:.15s ease;background:#fff}
  .choice:hover{background:#f8fafc;border-color:#dbeafe}
  .choice input{accent-color:var(--blue-1)}
  .choice--active{border-color:var(--blue-1);background:#eff6ff;box-shadow:0 0 0 3px #dbeafe inset}
  .kbd{border:1px solid var(--border);border-radius:8px;padding:.1rem .35rem;font-size:.75rem;background:#fff;min-width:1.25rem;text-align:center}

  .sticky-wrap{backdrop-filter:saturate(1.1) blur(8px);background:hsla(0,0%,100%,.85);border-bottom:1px solid rgba(15,23,42,.06)}
</style>
@endpush

@section('content')
@php
  $ids      = $test->questions()->orderBy('ordering')->orderBy('created_at')->pluck('id')->all();
  $pos      = array_search($question->id, $ids, true);
  $current  = $pos === false ? 1 : ($pos + 1);
  $total    = count($ids);
  $pct      = $total ? intval($current / $total * 100) : 0;

  $answerOptionId  = old('option_id', $selectedOptionId ?? ($currentAnswer->option_id ?? null));
  $answerValue     = old('value',     $typedValue       ?? ($currentAnswer->value     ?? null));

  $slugId          = $test->slug ?: $test->id;
@endphp

<div class="max-w-3xl mx-auto px-4 py-4 md:py-8"
     x-data="ui({
        secondsLeft: {{ (int)($secondsLeft ?? 0) }},
        timeLimitMin: {{ (int)($timeLimitMin ?? 0) }},
        nextUrl: @js($nextId ? route('app.psytests.questions.show', [$slugId, $nextId]) : null),
        prevUrl: @js($prevId ? route('app.psytests.questions.show', [$slugId, $prevId]) : null),
        finishUrl: @js(!$nextId ? route('app.psy.attempts.submit', $slugId) : null),
        pickedInit: @js($answerOptionId ?: null)
      })"
     x-init="init()">

  <!-- Sticky header (tanpa chip hint) -->
  <div class="sticky top-0 z-40 sticky-wrap">
    <div class="max-w-3xl mx-auto flex items-center justify-between gap-3 px-4 py-3">
      <div class="min-w-0">
        <a href="{{ route('app.psytests.show', $slugId) }}" class="text-sm link hover:underline">← {{ $test->name }}</a>
        <div class="mt-1 flex items-center flex-wrap gap-2 text-xs text-[var(--muted)]">
          <span class="chip">{{ $current }} / {{ $total }}</span>
          <div class="bar w-36"><span style="width: {{ $pct }}%"></span></div>
          @if(($timeLimitMin ?? 0) > 0)
            <span class="chip">⏳ <span x-text="fmt(left)"></span></span>
          @endif
        </div>
      </div>
      <!-- area kanan dikosongkan demi kebersihan UI -->
      <div></div>
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
      @foreach ($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
  @endif

  @if(($timeLimitMin ?? 0) > 0)
    <div class="text-xs text-[var(--muted)] mt-4">
      <div class="timebar"><span :style="`width:${percent()}%`"></span></div>
      <div class="mt-1">Sisa <span class="font-semibold text-[var(--ink)]" x-text="fmt(left)"></span></div>
    </div>
  @endif

  <div class="card p-5 mt-4 md:mt-6">
    <div class="flex items-start justify-between gap-3">
      <h2 class="text-lg md:text-xl font-semibold text-[var(--ink)]">{{ $question->prompt }}</h2>
      @if(!empty($question->trait_key))
        <span class="chip">{{ strtoupper($question->trait_key) }}</span>
      @endif
    </div>

    <form id="answerForm"
          method="POST"
          action="{{ route('app.psy.attempts.answer', [$slugId, $question->getKey()]) }}"
          class="mt-5 space-y-4"
          x-bind:class="(timeUp || submitting) ? 'opacity-60 pointer-events-none' : ''"
          @submit="
            submitting = true;
            $el.querySelectorAll('button').forEach(el => el.setAttribute('disabled','disabled'));
          ">
      @csrf

      @if($question->options->count())
        <div class="grid gap-2">
          @foreach($question->options as $i => $op)
            <label class="choice cursor-pointer"
                   :class="picked === @js($op->id) ? 'choice--active' : ''">
              <div class="flex items-center gap-3">
                <input type="radio"
                       name="option_id"
                       value="{{ $op->id }}"
                       class="h-4 w-4"
                       x-model="picked"
                       @checked($answerOptionId == $op->id)
                       required>
                <div class="flex items-center gap-2">
                  <span class="kbd">{{ $i+1 }}</span>
                  <span class="text-[var(--ink)]">{{ $op->label }}</span>
                </div>
              </div>
            </label>
          @endforeach
        </div>
        @error('option_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      @else
        <div class="flex items-center gap-3">
          <input type="number" inputmode="numeric" name="value"
                 class="border rounded-lg px-3 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-[var(--blue-2)] focus:border-[var(--blue-1)]"
                 value="{{ $answerValue }}" required placeholder="Nilai">
          @error('value') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      @endif

      <div class="flex items-center justify-between pt-2">
        <div class="flex items-center gap-2">
          @if($prevId)
            <a class="btn btn-outline" href="{{ route('app.psytests.questions.show', [$slugId, $prevId]) }}">← Sebelumnya</a>
          @endif
        </div>

        @if($nextId)
          <button type="submit" class="btn btn-primary" :disabled="timeUp || submitting">
            Simpan & Lanjut →
          </button>
        @else
          <button type="submit" class="btn btn-primary" :disabled="timeUp || submitting">
            Selesai & Hitung ✓
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

  <!-- Navigasi bawah DIHAPUS sesuai permintaan -->
</div>

<script>
function ui({secondsLeft, timeLimitMin, nextUrl, prevUrl, finishUrl, pickedInit}) {
  const full = Math.max(0, parseInt(timeLimitMin || 0, 10)) * 60;
  return {
    left: Math.max(0, parseInt(secondsLeft || 0, 10)),
    timeUp: false,
    submitting: false,
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

      window.addEventListener('keydown', (e) => {
        if (/^[1-9]$/.test(e.key)) {
          const idx = parseInt(e.key, 10) - 1;
          const radios = Array.from(document.querySelectorAll('input[type=radio][name=option_id]'));
          if (radios[idx]) { radios[idx].checked = true; this.picked = radios[idx].value; }
        }
        if (e.key === 'ArrowLeft' && prevUrl)  { e.preventDefault(); window.location.href = prevUrl; }
        if (e.key === 'ArrowRight' && nextUrl) { e.preventDefault(); window.location.href = nextUrl; }
        if (e.key === 'Enter' && !e.metaKey && !e.ctrlKey) {
          const form = document.getElementById('answerForm');
          if (form) { e.preventDefault(); form.requestSubmit(); }
        }
      });

      window.addEventListener('beforeunload', (e) => {
        if (timeLimitMin && !this.timeUp && !this.submitting) { e.preventDefault(); e.returnValue=''; }
      });
    }
  }
}
</script>
@endsection
