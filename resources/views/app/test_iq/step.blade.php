<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $test->title }} — Soal {{ $index }}/{{ $total }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Decorative blobs (subtle) */
    .bg-ornament::before,
    .bg-ornament::after {
      content: ""; position: absolute; inset: auto; width: 38rem; height: 38rem;
      filter: blur(80px); pointer-events: none; z-index: 0; opacity: .38;
    }
    .bg-ornament::before { top: -8rem; left: -10rem; background: radial-gradient(closest-side, #a5b4fc 0%, transparent 70%); }
    .bg-ornament::after { bottom: -10rem; right: -8rem; background: radial-gradient(closest-side, #c4b5fd 10%, transparent 70%); }
    .timer-danger { color: #dc2626; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 to-violet-100 text-slate-800">
  <div class="relative bg-ornament">
    <!-- Top Bar -->
    <header class="sticky top-0 z-20 backdrop-blur supports-[backdrop-filter]:bg-white/60 bg-white/90 border-b">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
        <div class="min-w-0">
          <div class="text-xs text-slate-500">Ujian IQ</div>
          <h1 class="text-base sm:text-lg font-semibold truncate">{{ $test->title }}</h1>
        </div>
        @if(($test->duration_minutes ?? 0) > 0)
        <div class="text-right">
          <div class="text-[11px] uppercase tracking-wide text-slate-500">Sisa Waktu</div>
          <div class="text-xl sm:text-2xl font-bold tabular-nums" id="timer-box" aria-live="polite">
            <span id="iq-min">--</span><span class="mx-0.5">:</span><span id="iq-sec">--</span>
          </div>
        </div>
        @endif
      </div>
      @if(($test->duration_minutes ?? 0) > 0)
      <!-- time progress -->
      <div class="h-1 w-full bg-slate-200">
        <div id="time-progress" class="h-1 bg-indigo-600" style="width: 100%"></div>
      </div>
      @endif
    </header>

    <!-- Main -->
    <main id="iq-step-root"
          class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10 relative"
          data-started-at-ms="{{ $startedAtMs }}"
          data-duration-min="{{ (int)($test->duration_minutes ?? 0) }}"
          data-autosubmit="1">

      <!-- step progress -->
      <div class="mb-4 sm:mb-6">
        <div class="flex items-end justify-between gap-2 mb-2">
          <div class="text-sm text-slate-500">Soal {{ $index }} dari {{ $total }}</div>
          <div class="text-sm text-slate-500">Progress</div>
        </div>
        <div class="w-full h-2 bg-slate-200 rounded overflow-hidden">
          <div class="h-2 bg-indigo-600" style="width: {{ (int)round(($index-1)/max(1,$total)*100) }}%"></div>
        </div>
      </div>

      <!-- Card -->
      <section class="relative z-10 bg-white/90 backdrop-blur border border-slate-200 shadow-sm rounded-2xl p-5 sm:p-7">
        <div class="text-lg sm:text-xl font-semibold mb-5">{{ $q['q'] ?? '—' }}</div>

        <form id="iq-step-form" method="POST" action="{{ route('user.test-iq.answer', [$test, $index]) }}" class="contents">
          @csrf

          <div class="grid gap-2 mb-6">
            @foreach(($q['options'] ?? []) as $opt)
              @php $checked = isset($prevAnswer) && $prevAnswer === $opt; @endphp
              <label class="flex items-center gap-3 px-4 py-3 border rounded-xl hover:bg-slate-50 cursor-pointer">
                <input type="radio" name="answer" value="{{ $opt }}" class="size-4 accent-indigo-600" @checked($checked)>
                <span class="text-slate-800">{{ $opt }}</span>
              </label>
            @endforeach
          </div>

          <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <button name="nav" value="prev" type="submit"
                    class="px-4 py-2 rounded-xl border text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled($index === 1)>
              ← Sebelumnya
            </button>

            @if($index < $total)
              <button name="nav" value="next" type="submit"
                      class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow">
                Selanjutnya →
              </button>
            @else
              <button name="nav" value="submit" type="submit"
                      class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow">
                Kirim Jawaban ✅
              </button>
            @endif
          </div>
        </form>
      </section>

      <p class="mt-4 text-xs text-slate-500">Tips: kamu bisa kembali ke soal sebelumnya sebelum kirim jawaban.</p>
    </main>
  </div>

  <!-- Realtime countdown vanilla JS (no Alpine required) -->
  <script>
  (function(){
    const root = document.getElementById('iq-step-root');
    if (!root) return;

    const durMin = parseInt(root.dataset.durationMin || '0', 10);
    if (!durMin) return; // no timer when duration is 0

    const startedAtMs = parseInt(root.dataset.startedAtMs || Date.now(), 10);
    const autoSubmit  = root.dataset.autosubmit === '1';
    const minEl = document.getElementById('iq-min');
    const secEl = document.getElementById('iq-sec');
    const form  = document.getElementById('iq-step-form');
    const box   = document.getElementById('timer-box');
    const bar   = document.getElementById('time-progress');

    function pad(n){ n = Math.max(0, n|0); return String(n).padStart(2,'0'); }

    const end = startedAtMs + durMin * 60 * 1000;

    function render(left){
      if (minEl && secEl){
        minEl.textContent = pad(Math.floor(left / 60));
        secEl.textContent = pad(left % 60);
      }
      if (bar){
        const pct = Math.max(0, Math.min(100, left/(durMin*60) * 100));
        bar.style.width = pct + '%';
      }
      if (box){
        if (left <= 60) box.classList.add('timer-danger','animate-pulse');
        else box.classList.remove('timer-danger','animate-pulse');
      }
    }

    let raf;
    function tick(){
      const now  = Date.now();
      const left = Math.max(0, Math.floor((end - now) / 1000));
      render(left);
      if (left <= 0){
        if (autoSubmit && form){
          const hidden = document.createElement('input');
          hidden.type = 'hidden'; hidden.name = 'nav'; hidden.value = 'submit';
          form.appendChild(hidden);
          form.submit();
        }
        cancelAnimationFrame(raf);
        return;
      }
      raf = requestAnimationFrame(tick);
    }

    // Kick off immediately
    raf = requestAnimationFrame(tick);
  })();
  </script>
</body>
</html>
