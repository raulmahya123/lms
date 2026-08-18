<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>{{ $test->title }} — Soal {{ $index }}/{{ $total }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  {{-- Tailwind CDN (Sesuai Foundation) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            tosca: {
              DEFAULT: '#0F9D8A',
              dark: '#087A6C',
              light: '#DFF5F1',
            },
            softbg: '#F4FBF9',
            text: {
              main: '#1e293b',
              soft: '#64748b'
            }
          },
          fontFamily: {
            sans: ['Inter', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    .timer-danger { color: #ef4444; }
    /* Hide scrollbar for clean look */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  </style>
</head>
<body class="min-h-screen bg-softbg text-text-main flex flex-col">
  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
      <div class="min-w-0 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-tosca-light text-tosca flex items-center justify-center shrink-0 hidden sm:flex">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
        </div>
        <div>
          <p class="text-[10px] font-bold tracking-wider uppercase text-text-soft">Tes Berlangsung</p>
          <h1 class="text-sm sm:text-base font-extrabold truncate text-text-main" title="{{ $test->title }}">{{ $test->title }}</h1>
        </div>
      </div>

      @if(($test->duration_minutes ?? 0) > 0)
      <div class="shrink-0 flex items-center gap-3 bg-gray-50 border border-gray-100 px-4 py-2 rounded-xl">
        <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="text-right">
          <div id="timer-box" class="text-lg sm:text-xl font-extrabold tabular-nums tracking-tight text-tosca-dark leading-none" aria-live="polite">
            <span id="iq-min">--</span><span class="mx-0.5">:</span><span id="iq-sec">--</span>
          </div>
        </div>
      </div>
      @endif
    </div>
    @if(($test->duration_minutes ?? 0) > 0)
    <div class="h-1.5 w-full bg-gray-100">
      <div id="time-progress" class="h-full bg-tosca transition-[width] duration-1000 ease-linear rounded-r-full" style="width:100%"></div>
    </div>
    @endif
  </header>

  <!-- Main -->
  <main id="iq-step-root"
        class="flex-1 w-full mx-auto max-w-3xl px-4 sm:px-6 py-8 sm:py-12 pb-32 sm:pb-12"
        data-started-at-ms="{{ $startedAtMs }}"
        data-duration-min="{{ (int)($test->duration_minutes ?? 0) }}"
        data-autosubmit="1">

    <!-- Progress -->
    <div class="mb-8">
      <div class="flex items-end justify-between gap-3 mb-3">
        <div class="text-sm text-text-soft font-medium">Pertanyaan <span class="text-text-main font-bold text-lg">{{ $index }}</span> dari <span class="font-bold text-text-main">{{ $total }}</span></div>
        <div class="text-xs font-bold text-tosca uppercase tracking-wider">{{ (int)round(($index-1)/max(1,$total-1)*100) }}% Selesai</div>
      </div>
      <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
        <div class="h-full bg-tosca rounded-full transition-all duration-500 ease-out" style="width: {{ (int)round(($index-1)/max(1,$total-1)*100) }}%"></div>
      </div>
    </div>

    <!-- Kartu soal -->
    <section class="bg-white border border-gray-50 rounded-3xl shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden">
      @php
        $questionText = $q['text'] ?? ($q['q'] ?? '—');
        $options = is_array($q['options'] ?? null) ? array_values($q['options']) : [];
        $prev = $prevAnswer ?? null;
      @endphp

      <div class="p-6 sm:p-10 border-b border-gray-50 bg-white">
        <h2 class="text-xl sm:text-2xl font-bold text-text-main leading-relaxed">
          {{ $questionText }}
        </h2>
      </div>

      <form id="iq-step-form" method="POST" action="{{ route('user.test-iq.answer', [$test, $index]) }}">
        @csrf
        
        <div class="p-6 sm:p-10 bg-gray-50/50">
          <!-- Opsi jawaban -->
          <div class="grid gap-3">
            @forelse($options as $i => $opt)
              @php
                $optText = (string)$opt;
                $isChecked = (is_int($prev) && $prev === $i) || (!is_int($prev) && is_string($prev) && $prev === $optText);
                $letter    = chr(65 + $i);
                $id        = 'opt_'.$index.'_'.$i; 
              @endphp
              <label for="{{ $id }}" class="group flex items-start gap-4 p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 {{ $isChecked ? 'border-tosca bg-tosca-light/30' : 'border-gray-200 bg-white hover:border-tosca/50 hover:shadow-sm' }}">
                <input id="{{ $id }}" type="radio" name="answer" value="{{ $i }}" class="peer sr-only" @checked($isChecked)>
                
                <div class="relative shrink-0 flex items-center justify-center w-8 h-8 rounded-full border-2 transition-colors {{ $isChecked ? 'border-tosca bg-tosca text-white' : 'border-gray-300 text-gray-500 group-hover:border-tosca/50' }}">
                  <span class="text-sm font-bold">{{ $letter }}</span>
                </div>
                
                <span class="text-base font-medium pt-1 {{ $isChecked ? 'text-tosca-dark' : 'text-text-main' }}">
                  {{ $optText }}
                </span>
              </label>
            @empty
              <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 text-amber-800 p-5 text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Opsi jawaban belum diset untuk soal ini.
              </div>
            @endforelse
          </div>

          <!-- Navigasi desktop -->
          <div class="hidden sm:flex justify-between items-center mt-10 pt-6 border-t border-gray-200/60">
            <button name="nav" value="prev" type="submit"
                    class="px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold hover:bg-gray-100 hover:text-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    @disabled($index === 1)>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              Sebelumnya
            </button>

            @if($index < $total)
              <button name="nav" value="next" type="submit"
                      class="px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center gap-2">
                Selanjutnya
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            @else
              <button name="nav" value="submit" type="submit"
                      class="px-8 py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 transition-colors shadow-sm shadow-green-600/30 flex items-center gap-2">
                Kirim Jawaban
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              </button>
            @endif
          </div>
        </div>

        <!-- Sticky footer nav (mobile) -->
        <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] pb-safe">
          <div class="p-4 grid grid-cols-2 gap-3 max-w-md mx-auto">
            <button name="nav" value="prev" type="submit"
                    class="px-4 py-3.5 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled($index === 1)>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              Kembali
            </button>

            @if($index < $total)
              <button name="nav" value="next" type="submit"
                      class="px-4 py-3.5 rounded-xl bg-tosca text-white font-bold shadow-sm shadow-tosca/20 hover:bg-tosca-dark flex items-center justify-center gap-2">
                Lanjut
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            @else
              <button name="nav" value="submit" type="submit"
                      class="px-4 py-3.5 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 flex items-center justify-center gap-2">
                Kirim
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              </button>
            @endif
          </div>
        </div>
      </form>
    </section>

    <div class="mt-8 text-center hidden sm:block">
      <p class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-100 text-xs font-semibold text-text-soft shadow-sm">
        <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Kamu dapat meninjau dan mengubah jawaban sebelum mengirim. Gunakan tombol panah di keyboard untuk navigasi.
      </p>
    </div>
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
        if (pct < 20) {
            bar.classList.remove('bg-tosca');
            bar.classList.add('bg-red-500');
        }
      }
      if (box){
        if (left <= 60) {
            box.classList.add('text-red-500', 'animate-pulse');
            box.classList.remove('text-tosca-dark');
        }
        else {
            box.classList.remove('text-red-500', 'animate-pulse');
            box.classList.add('text-tosca-dark');
        }
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

    // Dynamic styling when a radio button is selected
    const radioInputs = document.querySelectorAll('input[name="answer"]');
    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Reset all styles
            document.querySelectorAll('label').forEach(label => {
                label.classList.remove('border-tosca', 'bg-tosca-light/30');
                label.classList.add('border-gray-200', 'bg-white');
                const circle = label.querySelector('div');
                circle.classList.remove('border-tosca', 'bg-tosca', 'text-white');
                circle.classList.add('border-gray-300', 'text-gray-500');
                const text = label.querySelector('span.text-base');
                text.classList.remove('text-tosca-dark');
                text.classList.add('text-text-main');
            });

            // Apply selected styles
            if (this.checked) {
                const label = this.closest('label');
                label.classList.add('border-tosca', 'bg-tosca-light/30');
                label.classList.remove('border-gray-200', 'bg-white');
                const circle = label.querySelector('div');
                circle.classList.add('border-tosca', 'bg-tosca', 'text-white');
                circle.classList.remove('border-gray-300', 'text-gray-500');
                const text = label.querySelector('span.text-base');
                text.classList.add('text-tosca-dark');
                text.classList.remove('text-text-main');
            }
        });
    });

    // keyboard navigasi
    document.addEventListener('keydown', (e) => {
      const radios = Array.from(document.querySelectorAll('input[name="answer"]'));
      if (!radios.length) return;
      
      const checkedInput = radios.find(r => r.checked);
      let idx = checkedInput ? radios.indexOf(checkedInput) : -1;
      
      if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
        e.preventDefault();
        const nextIdx = idx === -1 ? 0 : (idx + 1) % radios.length;
        radios[nextIdx].checked = true; 
        radios[nextIdx].dispatchEvent(new Event('change'));
      }
      if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        e.preventDefault();
        const prevIdx = idx === -1 ? radios.length - 1 : (idx - 1 + radios.length) % radios.length;
        radios[prevIdx].checked = true; 
        radios[prevIdx].dispatchEvent(new Event('change'));
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
