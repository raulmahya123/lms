<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $test->title }} — Soal {{ $index }}/{{ $total }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    .timer-danger { color:#dc2626 }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 to-blue-100 text-slate-800">
  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b">
    <div class="mx-auto max-w-3xl md:max-w-5xl px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
      <div class="min-w-0">
        <p class="text-[11px] tracking-wide uppercase text-slate-500">Ujian IQ</p>
        <h1 class="text-base sm:text-lg font-semibold truncate" title="{{ $test->title }}">{{ $test->title }}</h1>
      </div>

      @if(($test->duration_minutes ?? 0) > 0)
      <div class="shrink-0 text-right">
        <p class="text-[11px] uppercase tracking-wide text-slate-500">Sisa Waktu</p>
        <div id="timer-box" class="text-xl sm:text-2xl font-extrabold tabular-nums" aria-live="polite">
          <span id="iq-min">--</span><span class="mx-0.5">:</span><span id="iq-sec">--</span>
        </div>
      </div>
      @endif
    </div>
    @if(($test->duration_minutes ?? 0) > 0)
    <div class="h-1 w-full bg-slate-200">
      <div id="time-progress" class="h-1 bg-blue-600 transition-[width]" style="width:100%"></div>
    </div>
    @endif
  </header>

  <!-- Main -->
  <main id="iq-step-root"
        class="mx-auto max-w-3xl md:max-w-5xl px-4 sm:px-6 pt-6 sm:pt-8 pb-28 sm:pb-10"
        data-started-at-ms="{{ $startedAtMs }}"
        data-duration-min="{{ (int)($test->duration_minutes ?? 0) }}"
        data-autosubmit="1">

    <!-- Progress -->
    <div class="flex items-end justify-between gap-3 mb-3 sm:mb-5">
      <div class="text-sm text-slate-600">Soal <span class="font-semibold">{{ $index }}</span> dari <span class="font-semibold">{{ $total }}</span></div>
      <div class="hidden sm:block text-xs text-slate-500">Progress</div>
    </div>
    <div class="w-full h-2 bg-slate-100 rounded overflow-hidden mb-4">
      <div class="h-2 bg-blue-600" style="width: {{ (int)round(($index-1)/max(1,$total-1)*100) }}%"></div>
    </div>

    <!-- Kartu soal -->
    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 sm:p-7">
      @php
        // Normalisasi struktur soal (compat q/text)
        $questionText = $q['text'] ?? ($q['q'] ?? '—');
        // Pastikan opsi berupa list string
        $options = is_array($q['options'] ?? null) ? array_values($q['options']) : [];
        // prevAnswer dari controller bisa INT (index) atau STRING (legacy)
        $prev = $prevAnswer ?? null;
      @endphp

      <h2 class="text-lg sm:text-xl font-semibold leading-snug mb-5">
        {{ $questionText }}
      </h2>

      <form id="iq-step-form" method="POST" action="{{ route('user.test-iq.answer', [$test, $index]) }}" class="space-y-6">
        @csrf

        <!-- Opsi jawaban -->
        <div class="grid gap-2">
          @forelse($options as $i => $opt)
            @php
              $optText = (string)$opt;
              // tandai checked jika prev == index ATAU prev string = teks opsi (kompat lama)
              $isChecked = (is_int($prev) && $prev === $i) || (!is_int($prev) && is_string($prev) && $prev === $optText);
              $letter    = chr(65 + $i);
              $id        = 'opt_'.$index.'_'.$i; // unik per step
            @endphp
            <label for="{{ $id }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer transition">
              <!-- value = INDEX (sinkron dengan controller yang menyimpan index) -->
              <input id="{{ $id }}" type="radio" name="answer" value="{{ $i }}" class="peer sr-only" @checked($isChecked)>
              <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm peer-checked:bg-blue-600 peer-checked:text-white">{{ $letter }}</span>
              <span class="text-slate-800 leading-relaxed">{{ $optText }}</span>
              <span class="ml-auto hidden sm:inline text-xs text-slate-400 peer-checked:text-blue-600">pilih</span>
            </label>
          @empty
            <div class="rounded-xl border bg-amber-50 text-amber-800 px-3 py-2 text-sm">
              Opsi jawaban belum diset untuk soal ini.
            </div>
          @endforelse
        </div>

        <!-- Navigasi desktop -->
        <div class="hidden sm:flex justify-between mt-6">
          <button name="nav" value="prev" type="submit"
                  class="px-4 py-2.5 rounded-xl border text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  @disabled($index === 1)>
            ← Sebelumnya
          </button>

          @if($index < $total)
            <button name="nav" value="next" type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow">
              Selanjutnya →
            </button>
          @else
            <button name="nav" value="submit" type="submit"
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow">
              Kirim Jawaban ✅
            </button>
          @endif
        </div>

        <!-- Sticky footer nav (mobile) -->
        <div class="sm:hidden fixed bottom-0 inset-x-0 z-40">
          <div class="mx-4 mb-[calc(env(safe-area-inset-bottom)+1rem)] rounded-2xl border border-slate-200 bg-white/95 backdrop-blur shadow-lg">
            <div class="p-3 grid grid-cols-2 gap-2">
              <button name="nav" value="prev" type="submit"
                      class="px-4 py-2.5 rounded-xl border text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                      @disabled($index === 1)>
                ← Sebelumnya
              </button>

              @if($index < $total)
                <button name="nav" value="next" type="submit"
                        class="px-4 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
                  Selanjutnya →
                </button>
              @else
                <button name="nav" value="submit" type="submit"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
                  Kirim ✅
                </button>
              @endif
            </div>
          </div>
        </div>
      </form>
    </section>

    <p class="mt-4 text-xs text-slate-500">Tips: kamu dapat meninjau dan mengubah jawaban sebelum mengirim.</p>
  </main>

  <!-- Countdown -->
  <script>
  (function(){
    const root = document.getElementById('iq-step-root');
    if (!root) return;

    const durMin = parseInt(root.dataset.durationMin || '0', 10);
    if (!durMin) return;

    const startedAtMs = parseInt(root.dataset.startedAtMs || Date.now(), 10);
    const autoSubmit  = root.dataset.autosubmit === '1';
    const minEl = document.getElementById('iq-min');
    const secEl = document.getElementById('iq-sec');
    const form  = document.getElementById('iq-step-form');
    const box   = document.getElementById('timer-box');
    const bar   = document.getElementById('time-progress');

    const pad = (n) => String(Math.max(0, n|0)).padStart(2,'0');
    const end = startedAtMs + durMin * 60 * 1000;

    function render(left){
      if (minEl && secEl){
        minEl.textContent = pad(Math.floor(left / 60));
        secEl.textContent = pad(left % 60);
      }
      if (bar){
        const pct = Math.max(0, Math.min(100, (left/(durMin*60)) * 100));
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

    // keyboard navigasi
    document.addEventListener('keydown', (e) => {
      const radios = Array.from(document.querySelectorAll('input[name="answer"]'));
      if (!radios.length) return;
      const idx = radios.findIndex(r => r.checked);
      if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
        e.preventDefault();
        const next = radios[(idx + 1 + radios.length) % radios.length];
        next.checked = true; next.dispatchEvent(new Event('change'));
      }
      if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        e.preventDefault();
        const prev = radios[(idx - 1 + radios.length) % radios.length];
        prev.checked = true; prev.dispatchEvent(new Event('change'));
      }
      if (e.key === 'Enter') {
        const nextBtn = document.querySelector('button[name="nav"][value="next"]');
        const submitBtn = document.querySelector('button[name="nav"][value="submit"]');
        (nextBtn || submitBtn)?.click();
      }
    });

    requestAnimationFrame(tick);
  })();
  </script>
</body>
</html>
