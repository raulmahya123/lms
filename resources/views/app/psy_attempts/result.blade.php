{{-- resources/views/app/psy_attempts/result.blade.php --}}
@extends('app.layouts.base')

@section('title', 'Hasil — '.$test->name)

@push('styles')
<style>
  :root{
    --ink:#0E0E0E;

    /* Blue palette */
    --blue-1:#2563EB; /* utama */
    --blue-2:#3B82F6; /* terang */
    --blue-3:#1E40AF; /* gelap */
  }

  .party-emoji{animation:bounce 1.2s infinite;display:inline-block}
  @keyframes bounce{0%,100%{transform:translateY(0)}30%{transform:translateY(-4px)}60%{transform:translateY(1px)}}

  .confetti{position:fixed;inset:0;pointer-events:none;overflow:hidden}
  .confetti span{position:absolute;top:-10px;font-size:16px;animation:fall linear forwards}
  @keyframes fall{to{transform:translateY(110vh) rotate(360deg)}}
</style>
@endpush

@section('content')
@php
  $scores      = $scores ?? [];
  $total       = (int)($total ?? 0);
  $profileKey  = $profileKey ?? ($profile ?? null);
  $profileName = $profileName ?? null;
  $recoText    = $recoText ?? ($reco ?? 'Profil belum terdefinisi. Tetap semangat untuk terus berkembang!');

  $emoji = '🤓';
  if ($total >= 80)      $emoji = '🧠✨';
  elseif ($total >= 60)  $emoji = '🚀😎';
  elseif ($total >= 40)  $emoji = '🧭🙂';
  elseif ($total >= 20)  $emoji = '🌱🤗';
  else                   $emoji = '🐢💤';

  // Gunakan persentil jika tersedia; jika tidak, gunakan total yang dibatasi 0–100
  $funPercent = is_numeric($percentile ?? null) ? (int)$percentile : max(0, min(100, $total));

  // Hilangkan _total dari rincian
  $traits = collect($scores)->except('_total');
@endphp

{{-- Confetti (ringan) --}}
<div id="confetti" class="confetti"></div>

<div class="max-w-5xl mx-auto px-4 py-10 space-y-8">
  {{-- Hero --}}
  <header class="text-center">
    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium
                  bg-[color:var(--blue-1)]/10 text-[color:var(--blue-1)] ring-1 ring-[color:var(--blue-1)]/20">
      <span class="h-1.5 w-1.5 rounded-full bg-[color:var(--blue-1)]"></span>
      Hasil Tes Siap
    </span>
    <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold tracking-tight text-[color:var(--ink)]">
      {{ $emoji }} Hasil: <span class="party-emoji">🎉</span>
    </h1>
    <p class="mt-2 text-sm sm:text-base text-[color:var(--ink)]/70">
      {{ $test->name }} — selamat, Anda telah menuntaskan tes ini.
    </p>
  </header>

  {{-- Ringkasan Utama --}}
  <section class="grid md:grid-cols-2 gap-4">
    {{-- Kartu Skor --}}
    <div class="rounded-2xl border border-black/10 bg-white p-5">
      <div class="text-xs uppercase tracking-wide text-[color:var(--ink)]/50">Total Skor</div>
      <div class="mt-1 flex items-end gap-2">
        <div class="text-4xl font-extrabold text-[color:var(--ink)]">{{ $total }}</div>
        <span class="text-lg">✨</span>
      </div>

      <div class="mt-5">
        <div class="flex items-center justify-between text-sm text-[color:var(--ink)]/70 mb-1">
          <span>Indeks Pencapaian</span>
          <span class="font-medium">{{ $funPercent }}%</span>
        </div>
        <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
          <div class="h-full"
               style="width: {{ $funPercent }}%;
                      background: linear-gradient(90deg, var(--blue-1), var(--blue-2));">
          </div>
        </div>

        <div class="mt-3 space-y-1 text-xs text-[color:var(--ink)]/60">
          @isset($percentile)
            <p>≈ Estimasi persentil: <span class="font-semibold text-[color:var(--ink)]">{{ $percentile }}%</span></p>
          @endisset
          @isset($durationSec)
            <p>Durasi: <span class="font-semibold text-[color:var(--ink)]">{{ gmdate('i\m s\d', (int)$durationSec) }}</span></p>
          @endisset
        </div>
      </div>
    </div>

    {{-- Kartu Profil --}}
    <div class="rounded-2xl border border-black/10 bg-white p-5">
      <div class="text-xs uppercase tracking-wide text-[color:var(--ink)]/50">Profil</div>
      <div class="mt-1 flex items-center gap-2 text-xl font-bold text-[color:var(--ink)]">
        {{ $profileName ?? $profileKey ?? '—' }} <span>🥳</span>
      </div>

      <p class="mt-3 text-sm leading-relaxed text-[color:var(--ink)]/80">
        {!! nl2br(e($recoText)) !!}
      </p>

      <div class="mt-5 flex flex-wrap items-center gap-2">
        <a href="{{ route('app.psy.tests.show', $test->slug ?: $test->id) }}"
           class="px-4 py-2 rounded-xl border border-black/10 text-[color:var(--ink)] bg-white hover:bg-gray-50 transition">
          Tinjau Tes
        </a>

        <button id="copyBtn"
                class="px-4 py-2 rounded-xl text-white hover:opacity-95 transition"
                style="background:linear-gradient(90deg,var(--blue-1),var(--blue-2));">
          Salin Ringkasan
        </button>
        <button id="shareBtn"
                class="px-4 py-2 rounded-xl bg-black text-white hover:bg-black/90 transition">
          Bagikan
        </button>
      </div>
    </div>
  </section>

  {{-- Breakdown Aspek --}}
  <section class="rounded-2xl border border-black/10 bg-white p-5">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-[color:var(--ink)]">Rata-rata per Aspek</h2>
      <span class="text-xs text-[color:var(--ink)]/50">Semakin tinggi, kian merepresentasikan karakteristik Anda.</span>
    </div>

    @if($traits->isEmpty())
      <div class="mt-4 p-4 rounded-xl bg-slate-50 text-[color:var(--ink)]/70">
        Belum tersedia perincian aspek.
      </div>
    @else
      <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($traits as $k => $v)
          @php
            $val  = is_numeric($v) ? round($v,2) : $v;
            $norm = 50;
            if (is_numeric($v)) {
              if ($v <= 5 && $v >= 1) { $norm = intval((($v-1) / 4) * 100); }
              elseif ($v <= 2 && $v >= -2) { $norm = intval((($v+2) / 4) * 100); }
              else { $norm = max(0, min(100, intval(($v / max(abs($v), 1)) * 100))); }
            }
          @endphp
          <div class="rounded-xl border border-black/10 p-4">
            <div class="flex items-center justify-between">
              <div class="text-sm font-medium text-[color:var(--ink)]/80">{{ strtoupper($k) }}</div>
              <div class="text-sm text-[color:var(--ink)]/50">Skor:
                <span class="font-semibold text-[color:var(--ink)]">{{ $val }}</span>
              </div>
            </div>
            <div class="mt-3 h-2.5 bg-slate-200 rounded-full overflow-hidden">
              <div class="h-full" style="width: {{ $norm }}%;
                   background:linear-gradient(90deg,var(--blue-2),var(--blue-1));"></div>
            </div>
            <p class="mt-2 text-xs text-[color:var(--ink)]/60">
              Interpretasi: <em>{{ $norm >= 66 ? 'Tinggi' : ($norm >= 33 ? 'Sedang' : 'Rendah') }}</em>
            </p>
          </div>
        @endforeach
      </div>
    @endif
  </section>

  {{-- Aksi Lanjutan --}}
  <section class="grid md:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-black/10 p-5"
         style="background:linear-gradient(135deg,#f7fbff,#eef4ff)">
      <h3 class="text-lg font-semibold text-[color:var(--ink)]">Langkah Berikutnya 🎯</h3>
      <ul class="mt-2 text-sm text-[color:var(--ink)]/80 space-y-1 list-disc pl-5">
        <li>Lakukan tes lain untuk memperkaya sudut pandang.</li>
        <li>Gunakan hasil sebagai wawasan pengembangan diri, bukan penilaian yang mutlak.</li>
        <li>Ulangi tes setelah beberapa waktu untuk menilai konsistensi jawaban.</li>
      </ul>
    </div>
    <div class="rounded-2xl border border-black/10 p-5"
         style="background:linear-gradient(135deg,#f5f9ff,#f2f6ff)">
      <h3 class="text-lg font-semibold text-[color:var(--ink)]">Bagikan Hasil</h3>
      <p class="mt-2 text-sm text-[color:var(--ink)]/80">
        Tekan <b>Bagikan</b> untuk membagikan hasil kepada rekan atau kolega.
      </p>
      <p class="mt-1 text-xs text-[color:var(--ink)]/50">*Data Anda aman. Tidak ada informasi yang dipublikasikan tanpa persetujuan eksplisit.</p>
    </div>
  </section>
</div>

@push('scripts')
<script>
  // Confetti ringan (auto-off setelah 4s)
  (function makeConfetti(){
    const el = document.getElementById('confetti'); if(!el) return;
    const pieces = 28; const icons = ['🎉','✨','🎈','🥳','🌟'];
    for (let i=0;i<pieces;i++){
      const s = document.createElement('span');
      s.textContent = icons[Math.floor(Math.random()*icons.length)];
      s.style.left = Math.random()*100+'%';
      s.style.animationDuration = (2+Math.random()*2)+'s';
      s.style.animationDelay = (Math.random()*0.6)+'s';
      el.appendChild(s);
      setTimeout(()=> s.remove(), 3800);
    }
    setTimeout(()=> el.innerHTML='', 4000);
  })();

  // Copy summary
  document.getElementById('copyBtn')?.addEventListener('click', async () => {
    const data = {
      test: @json($test->name),
      total: @json($total),
      profileKey: @json($profileKey),
      profileName: @json($profileName),
      reco: @json($recoText),
      traits: @json(collect($scores ?? [])->except('_total')),
      url: window.location.href
    };
    const lines = [];
    lines.push(`Hasil Tes: ${data.test}`);
    lines.push(`Total Skor: ${data.total}`);
    lines.push(`Profil: ${data.profileName ?? data.profileKey ?? '-'}`);
    if (Object.keys(data.traits || {}).length){
      lines.push('Aspek:');
      Object.entries(data.traits).forEach(([k,v]) => lines.push(`- ${String(k).toUpperCase()}: ${v}`));
    }
    lines.push(`Rekomendasi: ${data.reco}`);
    lines.push(data.url);
    try{
      await navigator.clipboard.writeText(lines.join('\n'));
      alert('Ringkasan disalin. Silakan tempel pada catatan Anda.');
    }catch(e){ alert('Gagal menyalin. Silakan coba kembali.'); }
  });

  // Share API
  document.getElementById('shareBtn')?.addEventListener('click', async () => {
    const title = 'Hasil Tes Psikologi — BERKEMAH';
    const text  = `Saya baru menyelesaikan tes: {{ $test->name }} — Skor: {{ $total }} (Profil: {{ $profileName ?? $profileKey ?? "-" }})`;
    const url   = window.location.href;
    if (navigator.share) { try { await navigator.share({ title, text, url }); } catch(e){} }
    else { alert('Fitur bagikan tidak tersedia. Gunakan tombol "Salin Ringkasan".'); }
  });
</script>
@endpush
@endsection
