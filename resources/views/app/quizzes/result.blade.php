@extends('app.layouts.base')
@section('title','Hasil Kuis')

@section('content')
<h1 class="text-xl font-semibold">Hasil Kuis</h1>

{{-- Flash status khusus quiz --}}
@if(session('quiz_status'))
  <div class="p-3 mt-3 border rounded text-emerald-800 bg-emerald-50">
    {{ session('quiz_status') }}
  </div>
@endif

@php
  // ---- kunci angka dari controller (per-season) ----
  $maxAttempts    = (int)($maxAttempts ?? 2);              // MAX_ATTEMPTS_PER_SEASON
  $usedAttempts   = (int)($submittedCount ?? 0);           // attempt yang sdh disubmit di season ini
  $attemptNo      = min($usedAttempts, $maxAttempts);      // hanya untuk tampilan
  $remain         = max(0, (int)($remainAttempts ?? ($maxAttempts - $usedAttempts)));
  $seasonRemain   = (int)($seasonRemain ?? 0);             // detik sampai season berakhir
  $showCountdown  = ($remain === 0 && $seasonRemain > 0);  // tampilkan hitung mundur saat lock aktif
  $canDownload    = isset($percent) ? ($percent >= 80) : false; // gate unduh sertifikat (attempt ini)

  $startUrl       = route('app.quiz.start', $attempt->quiz->lesson);
@endphp

{{-- Banner info attempt & aksi --}}
<div class="p-4 mt-3 border rounded-lg bg-gray-50">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div class="space-y-2">
      <div class="text-sm text-gray-600">
        Attempt musim ini: <span class="font-semibold">{{ $attemptNo }}</span>
        / <span class="font-semibold">{{ $maxAttempts }}</span>.
      </div>

      {{-- Meter attempt (visual) --}}
      <div class="flex items-center gap-1.5">
        @for($i=1; $i<=$maxAttempts; $i++)
          <span class="inline-block h-2.5 w-2.5 rounded-full
              {{ $i <= $attemptNo ? 'bg-indigo-600' : 'bg-gray-300' }}">
          </span>
        @endfor
      </div>

      {{-- Pesan kondisi --}}
      @if($remain > 0)
        <div class="text-sm text-amber-700">
          Sisa percobaan musim ini: <span class="font-semibold">{{ $remain }}</span>.
          Hindari klik berulang—sistem membatasi total percobaan per season.
        </div>
      @elseif($showCountdown)
        <div class="text-sm text-rose-700" id="seasonLockText">
          Maksimum {{ $maxAttempts }} percobaan tercapai.
          Season reset dalam <span id="seasonRemain" data-raw="{{ $seasonRemain }}"></span>.
        </div>
      @else
        <div class="text-sm text-rose-700">
          Maksimum {{ $maxAttempts }} percobaan tercapai. Menunggu season berikutnya.
        </div>
      @endif
    </div>

    {{-- Tombol aksi: start quiz via POST + anti double-click + auto-enable setelah reset --}}
    <div class="flex items-center gap-2">
      @if($remain > 0)
        <form method="POST" action="{{ $startUrl }}">
          @csrf
          <button type="submit"
                  class="inline-flex items-center px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                  onclick="this.disabled=true; this.textContent='Memulai...'; this.form.submit();">
            Percobaan kembali
          </button>
        </form>
      @else
        {{-- Terkunci: tombol disabled + form hidden yang aktif otomatis saat season reset --}}
        <button type="button"
                id="retryBtnDisabled"
                class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-300 rounded cursor-not-allowed"
                disabled>
          Percobaan kembali
        </button>

        <form method="POST" action="{{ $startUrl }}" id="retryFormActive" class="hidden">
          @csrf
          <button type="submit"
                  id="retryBtnActive"
                  class="inline-flex items-center px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                  onclick="this.disabled=true; this.textContent='Memulai...'; this.form.submit();">
            Percobaan kembali
          </button>
        </form>
      @endif
    </div>
  </div>
</div>

{{-- Ringkasan nilai --}}
<div class="grid gap-3 mt-3 sm:grid-cols-3">
  <div class="p-4 bg-white border rounded-lg">
    <div class="text-xs text-gray-500">Skor Attempt Ini</div>
    <div class="mt-1 text-xl font-semibold">{{ $attempt->score }}</div>
  </div>
  <div class="p-4 bg-white border rounded-lg">
    <div class="text-xs text-gray-500">% Benar Attempt Ini</div>
    <div class="mt-1 text-xl font-semibold">
      {{ isset($percent) ? number_format($percent,2) : '-' }}%
      @isset($correct)
        <span class="text-sm text-gray-500">({{ $correct }}/{{ $total }})</span>
      @endisset
    </div>
  </div>
  <div class="p-4 bg-white border rounded-lg">
    <div class="text-xs text-gray-500">% Benar Terbaik (Semua Attempt)</div>
    <div class="mt-1 text-xl font-semibold">
      {{ isset($best_percent) ? number_format($best_percent,2) : (isset($percent) ? number_format($percent,2) : '-') }}%
      @isset($best_correct)
        <span class="text-sm text-gray-500">({{ $best_correct }}/{{ $best_total }})</span>
      @endisset
    </div>
  </div>
</div>

{{-- Status sertifikat + kunci unduh --}}
@if($canDownload)
  <div class="p-4 mt-4 border rounded bg-emerald-50 text-emerald-800">
    <div class="font-semibold">Selamat! Kamu memenuhi syarat sertifikat 🎉</div>
    <p class="mt-1 text-sm">Minimal 80% benar telah tercapai.</p>
    <a href="{{ route('app.certificate.course', $course) }}"
       class="inline-block px-4 py-2 mt-3 text-white rounded bg-emerald-600 hover:bg-emerald-700">
      Unduh Sertifikat
    </a>
  </div>
@else
  <div class="p-4 mt-4 border rounded bg-rose-50 text-rose-800">
    <div class="font-semibold">Belum memenuhi syarat sertifikat</div>
    <p class="mt-1 text-sm">
      Butuh minimal <span class="font-semibold">80%</span> jawaban benar pada attempt ini.
    </p>
    @isset($best_percent)
      <p class="mt-1 text-xs text-rose-700">Persentase terbaikmu saat ini: {{ number_format($best_percent,2) }}%.</p>
    @endisset

    <button type="button"
            class="inline-flex items-center gap-2 px-4 py-2 mt-3 text-gray-600 bg-gray-300 rounded cursor-not-allowed"
            title="Terkunci: capai minimal 80% pada attempt ini untuk mengunduh sertifikat"
            aria-disabled="true"
            disabled>
      🔒 Unduh Sertifikat (terkunci)
    </button>
  </div>
@endif

{{-- (Opsional) Riwayat attempt season ini --}}
@isset($attemptsThisSeason)
  <div class="mt-6">
    <h2 class="text-sm font-semibold text-gray-700">Riwayat attempt (season ini)</h2>
    @if($attemptsThisSeason->isEmpty())
      <div class="mt-2 text-sm text-gray-500">Belum ada attempt pada season ini.</div>
    @else
      <div class="mt-2 overflow-hidden border rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="text-gray-600 bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left">#</th>
              <th class="px-3 py-2 text-left">Waktu Submit</th>
              <th class="px-3 py-2 text-left">Skor (poin)</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($attemptsThisSeason as $idx => $att)
              <tr>
                <td class="px-3 py-2">{{ $idx+1 }}</td>
                <td class="px-3 py-2">
                  {{ optional($att->submitted_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                </td>
                <td class="px-3 py-2">{{ $att->score }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endisset

{{-- Countdown season reset --}}
@if($showCountdown)
  <script>
    (function(){
      const el    = document.getElementById('seasonRemain');
      const text  = document.getElementById('seasonLockText');
      const btnD  = document.getElementById('retryBtnDisabled');
      const formA = document.getElementById('retryFormActive');
      if (!el) return;

      let raw = parseInt(el.dataset.raw || '0', 10);

      function fmt(sec){
        const h = Math.floor(sec/3600);
        const m = Math.floor((sec%3600)/60);
        const s = sec%60;
        const hh = h>0 ? (h+'j ') : '';
        const mm = (m<10?'0':'') + m;
        const ss = (s<10?'0':'') + s;
        return hh + mm + ':' + ss;
      }

      el.textContent = fmt(raw);

      const t = setInterval(() => {
        raw = Math.max(0, raw - 1);
        el.textContent = fmt(raw);
        if (raw <= 0) {
          clearInterval(t);
          if (text) text.textContent = 'Season baru dimulai. Kamu bisa mencoba kembali.';
          if (btnD) btnD.classList.add('hidden');
          if (formA) formA.classList.remove('hidden');
        }
      }, 1000);
    })();
  </script>
@endif
@endsection
