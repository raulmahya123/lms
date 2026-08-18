@extends('layouts.app')
@section('title','Hasil Kuis')

@section('content')

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

<div class="max-w-5xl mx-auto space-y-8 pb-12">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Hasil Kuis
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">
        {{ $attempt->quiz->title ?? 'Kuis' }}
      </h1>
    </div>

    {{-- Tombol aksi --}}
    <div class="shrink-0 flex gap-3">
      @if($remain > 0)
        <form method="POST" action="{{ $startUrl }}">
          @csrf
          <button type="submit"
                  class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border-2 border-tosca text-tosca font-bold hover:bg-tosca hover:text-white transition-colors shadow-sm"
                  onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin -ml-1 mr-2 h-5 w-5 text-current inline-block\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Memproses...'; this.form.submit();">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Percobaan Kembali
          </button>
        </form>
      @else
        <button type="button"
                id="retryBtnDisabled"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed font-bold"
                disabled>
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
          </svg>
          Terkunci
        </button>

        <form method="POST" action="{{ $startUrl }}" id="retryFormActive" class="hidden">
          @csrf
          <button type="submit"
                  id="retryBtnActive"
                  class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border-2 border-tosca text-tosca font-bold hover:bg-tosca hover:text-white transition-colors shadow-sm"
                  onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin -ml-1 mr-2 h-5 w-5 text-current inline-block\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Memproses...'; this.form.submit();">
            Percobaan Kembali
          </button>
        </form>
      @endif
    </div>
  </div>

  @if(session('quiz_status'))
    <div class="p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 font-medium flex items-center gap-3 shadow-sm">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      {{ session('quiz_status') }}
    </div>
  @endif

  {{-- Banner info attempt --}}
  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      
      <div class="space-y-4 flex-1">
        <div class="flex items-center gap-4">
          <div class="text-sm font-bold text-text-soft uppercase tracking-wider">Attempt Musim Ini</div>
          <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
            <span class="font-extrabold text-tosca text-lg leading-none">{{ $attemptNo }}</span>
            <span class="text-text-soft font-bold">/ {{ $maxAttempts }}</span>
          </div>
        </div>

        {{-- Meter attempt --}}
        <div class="flex items-center gap-2">
          @for($i=1; $i<=$maxAttempts; $i++)
            <div class="h-3 flex-1 max-w-[40px] rounded-full transition-colors {{ $i <= $attemptNo ? 'bg-tosca shadow-sm shadow-tosca/20' : 'bg-gray-200' }}"></div>
          @endfor
        </div>

        {{-- Pesan kondisi --}}
        @if($remain > 0)
          <p class="text-sm font-medium text-amber-700 flex items-start gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Sisa percobaan musim ini: <strong class="mx-1">{{ $remain }}</strong>. Gunakan kesempatan sebaik mungkin.
          </p>
        @elseif($showCountdown)
          <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
            <p class="text-sm font-medium text-red-700 flex items-start gap-2" id="seasonLockText">
              <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
              <span>Maksimum {{ $maxAttempts }} percobaan tercapai. Season reset dalam <strong id="seasonRemain" data-raw="{{ $seasonRemain }}" class="tabular-nums"></strong>.</span>
            </p>
          </div>
        @else
          <p class="text-sm font-medium text-red-700 flex items-start gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
            Maksimum {{ $maxAttempts }} percobaan tercapai. Menunggu season berikutnya.
          </p>
        @endif
      </div>
      
    </div>
  </div>

  {{-- Ringkasan nilai --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col justify-center">
      <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Skor Attempt Ini</div>
      <div class="text-5xl font-black text-tosca-dark">{{ $attempt->score }}</div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col justify-center">
      <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">% Benar Attempt Ini</div>
      <div class="flex items-baseline gap-3">
        <div class="text-4xl font-extrabold text-text-main">{{ isset($percent) ? number_format($percent,0) : '-' }}%</div>
        @isset($correct)
          <div class="text-sm font-bold text-text-soft">({{ $correct }}/{{ $total }})</div>
        @endisset
      </div>
    </div>
    
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col justify-center bg-gradient-to-br from-white to-purple-50/50">
      <div class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">% Benar Terbaik (Semua)</div>
      <div class="flex items-baseline gap-3">
        <div class="text-4xl font-extrabold text-purple-700">{{ isset($best_percent) ? number_format($best_percent,0) : (isset($percent) ? number_format($percent,0) : '-') }}%</div>
        @isset($best_correct)
          <div class="text-sm font-bold text-purple-600/70">({{ $best_correct }}/{{ $best_total }})</div>
        @endisset
      </div>
    </div>
  </div>

  {{-- Status sertifikat + kunci unduh --}}
  @if($canDownload)
    <div class="bg-gradient-to-r from-green-50 to-emerald-100/50 rounded-3xl p-8 border border-green-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-green-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-green-500/30">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
          <h3 class="text-xl font-bold text-green-800">Selamat! Memenuhi Syarat Sertifikat 🎉</h3>
          <p class="text-green-700 mt-1">Minimal 80% benar telah tercapai. Anda berhak mendapatkan sertifikat.</p>
        </div>
      </div>
      <a href="{{ route('app.certificate.course', $course ?? 0) }}"
         class="shrink-0 inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-colors shadow-sm shadow-green-600/30 w-full md:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Unduh Sertifikat
      </a>
    </div>
  @else
    <div class="bg-red-50 rounded-3xl p-8 border border-red-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="flex items-start gap-5">
        <div class="w-14 h-14 rounded-full bg-red-100 text-red-500 flex items-center justify-center shrink-0">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
        </div>
        <div>
          <h3 class="text-lg font-bold text-red-800">Belum Memenuhi Syarat Sertifikat</h3>
          <p class="text-red-700 mt-1">Butuh minimal <strong class="text-red-800">80%</strong> jawaban benar pada attempt ini.</p>
          @isset($best_percent)
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-white/60 text-red-700 text-xs font-bold mt-2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Persentase terbaikmu: {{ number_format($best_percent,0) }}%
            </div>
          @endisset
        </div>
      </div>
      
      <button type="button"
              class="shrink-0 inline-flex items-center gap-2 px-6 py-4 bg-gray-200 text-gray-500 font-bold rounded-xl cursor-not-allowed w-full md:w-auto justify-center border border-gray-300"
              disabled>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
        Unduh Sertifikat
      </button>
    </div>
  @endif

  {{-- Riwayat attempt season ini --}}
  @isset($attemptsThisSeason)
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
      <div class="px-8 py-6 border-b border-gray-50 bg-softbg/30 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h2 class="text-lg font-bold text-text-main">Riwayat Attempt (Season Ini)</h2>
      </div>

      @if($attemptsThisSeason->isEmpty())
        <div class="p-8 text-center text-text-soft font-medium">Belum ada attempt pada season ini.</div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                <th class="px-6 py-4 font-bold">Attempt #</th>
                <th class="px-6 py-4 font-bold">Waktu Submit</th>
                <th class="px-6 py-4 font-bold">Skor</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              @foreach($attemptsThisSeason as $idx => $att)
                <tr class="hover:bg-softbg/30 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-text-main">
                    #{{ $idx+1 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-text-soft font-medium">
                    {{ optional($att->submitted_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-tosca-light text-tosca-dark font-extrabold text-sm">
                      {{ $att->score }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  @endisset

  {{-- Countdown season reset script --}}
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
            if (text) text.innerHTML = '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>Season baru dimulai. Anda dapat mencoba kembali.</span>';
            text.className = 'text-sm font-medium text-green-700 flex items-start gap-2';
            text.parentElement.className = 'p-3 bg-green-50 border border-green-100 rounded-xl';
            if (btnD) btnD.classList.add('hidden');
            if (formA) formA.classList.remove('hidden');
          }
        }, 1000);
      })();
    </script>
  @endif
</div>
@endsection
