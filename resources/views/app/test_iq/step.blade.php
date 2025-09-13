@extends('app.layouts.base')
@section('title', $test->title.' — Soal '.$index.'/'.$total)

@section('content')
<div class="max-w-3xl mx-auto py-8"
     id="iq-step-root"
     data-started-at-ms="{{ $startedAtMs }}"
     data-duration-min="{{ (int)($test->duration_minutes ?? 0) }}"
     data-autosubmit="1">
  <div class="flex items-start justify-between mb-6 gap-4">
    <div>
      <h1 class="text-xl md:text-2xl font-bold">{{ $test->title }}</h1>
      <div class="text-gray-500 text-sm">Soal {{ $index }} dari {{ $total }}</div>
    </div>

    @if(($test->duration_minutes ?? 0) > 0)
    <div class="text-right">
      <div class="text-xs text-gray-500">Sisa Waktu</div>
      <div class="text-lg font-semibold tabular-nums">
        <span id="iq-min">--</span>:<span id="iq-sec">--</span>
      </div>
    </div>
    @endif
  </div>

  <div class="w-full h-2 bg-gray-200 rounded mb-4">
    <div class="h-2 bg-indigo-600 rounded" style="width: {{ (int)round(($index-1)/max(1,$total)*100) }}%"></div>
  </div>

  <div class="bg-white border rounded-2xl p-6 shadow-sm">
    <div class="text-lg md:text-xl font-semibold mb-5">
      {{ $q['q'] ?? '—' }}
    </div>

    <form id="iq-step-form" method="POST" action="{{ route('user.test-iq.answer', [$test, $index]) }}">
      @csrf

      <div class="grid gap-2 mb-6">
        @foreach(($q['options'] ?? []) as $opt)
          @php $checked = isset($prevAnswer) && $prevAnswer === $opt; @endphp
          <label class="flex items-center gap-3 px-4 py-3 border rounded-xl hover:bg-gray-50 cursor-pointer">
            <input type="radio" name="answer" value="{{ $opt }}" class="size-4 accent-indigo-600" @checked($checked)>
            <span class="text-gray-800">{{ $opt }}</span>
          </label>
        @endforeach
      </div>

      <div class="flex items-center justify-between">
        <button name="nav" value="prev" type="submit"
                class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-50"
                @disabled($index === 1)>
          ← Sebelumnya
        </button>

        @if($index < $total)
          <button name="nav" value="next" type="submit"
                  class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
            Selanjutnya →
          </button>
        @else
          <button name="nav" value="submit" type="submit"
                  class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
            Kirim Jawaban ✅
          </button>
        @endif
      </div>
    </form>
  </div>

  <div class="mt-4 text-xs text-gray-500">
    Tips: kamu bisa kembali ke soal sebelumnya sebelum kirim jawaban.
  </div>
</div>

{{-- Realtime countdown vanilla JS (independen dari Alpine) --}}
<script>
(function(){
  const root = document.getElementById('iq-step-root');
  if (!root) return;

  const durMin = parseInt(root.dataset.durationMin || '0', 10);
  if (!durMin) return; // kalau tidak ada durasi, tidak perlu timer

  const startedAtMs = parseInt(root.dataset.startedAtMs || Date.now(), 10);
  const autoSubmit  = root.dataset.autosubmit === '1';
  const minEl = document.getElementById('iq-min');
  const secEl = document.getElementById('iq-sec');
  const form  = document.getElementById('iq-step-form');

  function pad(n){ n = Math.max(0, n|0); return String(n).padStart(2,'0'); }

  let ticking = true;
  function tick(){
    if (!ticking) return;
    const end = startedAtMs + durMin * 60 * 1000;
    let left = Math.max(0, Math.floor((end - Date.now()) / 1000));

    if (minEl && secEl){
      minEl.textContent = pad(Math.floor(left / 60));
      secEl.textContent = pad(left % 60);
    }

    if (left <= 0){
      ticking = false;
      if (autoSubmit && form){
        // kirim nav=submit otomatis
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'nav';
        hidden.value = 'submit';
        form.appendChild(hidden);
        form.submit();
      }
      return;
    }
    requestAnimationFrame(tick); // smooth, realtime
  }

  // start segera (realtime), tanpa nunggu interval detik berikutnya
  requestAnimationFrame(tick);
})();
</script>
@endsection
