{{-- resources/views/app/psy_attempts/result.blade.php --}}
@extends('app.layouts.base')

@section('title', 'Hasil — '.$test->name)

@push('styles')
<style>
  .party-emoji { animation: bounce 1.2s infinite; display:inline-block; }
  @keyframes bounce {
    0%,100%{ transform: translateY(0) rotate(0); }
    30%{ transform: translateY(-4px) rotate(-3deg); }
    60%{ transform: translateY(1px) rotate(3deg); }
  }
  .wiggle { animation: wiggle .6s ease-in-out infinite; }
  @keyframes wiggle {
    0%,100%{ transform: rotate(0deg) }
    25%{ transform: rotate(2deg) }
    75%{ transform: rotate(-2deg) }
  }
  .confetti {
    position: absolute; inset: 0; pointer-events: none; overflow: hidden;
  }
  .confetti span {
    position: absolute; top:-10px; font-size: 18px; animation: fall linear forwards;
  }
  @keyframes fall { to { transform: translateY(110vh) rotate(360deg); } }
</style>
@endpush

@section('content')
@php
  $scores = $scores ?? [];
  $total  = (int)($total ?? 0);
  $profileKey = $profile ?? null;
  $recoText   = $reco ?? 'Profil belum terdefinisi, tapi semangat selalu! 😄';

  // Pilih emoji lucu berdasarkan total
  $emoji = '🤓';
  if ($total >= 80)      $emoji = '🧠✨';
  elseif ($total >= 60)  $emoji = '🚀😎';
  elseif ($total >= 40)  $emoji = '🧭🙂';
  elseif ($total >= 20)  $emoji = '🌱🤗';
  else                   $emoji = '🐢💤';

  // Hitung persen buat "Kocak Meter" berdasarkan total kalau mau fun (clamp 0..100)
  $funPercent = max(0, min(100, $total));
@endphp

<div class="relative">
  {{-- confetti container --}}
  <div id="confetti" class="confetti"></div>
</div>

<div class="max-w-4xl mx-auto px-4 py-10 space-y-8">
  {{-- Header --}}
  <div class="text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
      <span class="h-2 w-2 rounded-full bg-blue-600"></span> Hasil Tes Tersaji Hangat
    </div>
    <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900">
      {{ $emoji }} Hasil Tes: <span class="party-emoji">🎉</span>
    </h1>
    <p class="mt-2 text-slate-600">
      {{ $test->name }} — selamat, kamu berhasil melewati lika-liku pertanyaan.
    </p>
  </div>

  {{-- Kartu Hasil Utama --}}
  <div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-slate-500">Total Skor</div>
      <div class="mt-1 text-4xl font-extrabold text-slate-900 flex items-center gap-2">
        {{ $total }}
        <span class="text-2xl wiggle">✨</span>
      </div>
      <div class="mt-4">
        <div class="text-sm text-slate-600 mb-1 flex justify-between">
          <span>Kocak Meter</span>
          <span>{{ $funPercent }}%</span>
        </div>
        <div class="h-2.5 bg-slate-200 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-fuchsia-500"
               style="width: {{ $funPercent }}%"></div>
        </div>
        <p class="mt-2 text-xs text-slate-500">
          *Kocak Meter™ adalah pengukuran tidak ilmiah yang sepenuhnya dibuat-buat untuk menambah keceriaan. 😜
        </p>
      </div>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="text-sm text-slate-500">Profil</div>
      <div class="mt-1 text-xl font-bold text-slate-900 flex items-center gap-2">
        {{ $profileKey ?? '—' }}
        <span>🥳</span>
      </div>

      <div class="mt-4 text-sm text-slate-700">
        {!! nl2br(e($recoText)) !!}
      </div>

      <div class="mt-5 flex flex-wrap items-center gap-2">
        <a href="{{ route('app.psytests.show', $test->slug ?: $test->id) }}"
           class="px-4 py-2 rounded-xl border text-slate-700 hover:bg-slate-50">
          Lihat Tes
        </a>
        <a href="{{ route('app.psytests.index') }}"
           class="px-4 py-2 rounded-xl border text-slate-700 hover:bg-slate-50">
          Coba Tes Lain
        </a>
        {{-- tombol share/salin --}}
        <button id="copyBtn" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
          Salin Ringkasan
        </button>
        <button id="shareBtn" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
          Bagikan Hasil
        </button>
      </div>
    </div>
  </div>

  {{-- Breakdown Trait --}}
  <div class="bg-white border rounded-2xl p-5">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900">Rata-rata per Trait</h2>
      <span class="text-xs text-slate-500">Semakin tinggi, semakin “kamu banget”.</span>
    </div>

    @php
      $traits = collect($scores)->except('_total');
    @endphp

    @if($traits->isEmpty())
      <div class="mt-4 p-4 rounded-xl bg-slate-50 text-slate-600">
        Tidak ada breakdown trait. Tapi kamu tetap spesial di hati sistem. 💖
      </div>
    @else
      <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($traits as $k => $v)
          @php
            $val = is_numeric($v) ? round($v,2) : $v;
            // normalisasi 0..100 kalau skala -2..+2 / 1..5; heuristik ringan
            $norm = 50;
            if (is_numeric($v)) {
              // coba tebak skala
              if ($v <= 5 && $v >= 1) { // 1..5
                $norm = intval((($v-1) / 4) * 100);
              } elseif ($v <= 2 && $v >= -2) { // -2..+2
                $norm = intval((($v+2) / 4) * 100);
              } else {
                // fallback ke rasio terhadap total absolute max 100
                $norm = max(0, min(100, intval(($v / max(abs($v), 1)) * 100)));
              }
            }
          @endphp
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <div class="text-sm font-medium text-slate-700">{{ strtoupper($k) }}</div>
              <div class="text-sm text-slate-500">Skor: <span class="font-semibold">{{ $val }}</span></div>
            </div>
            <div class="mt-3 h-2.5 bg-slate-200 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ $norm }}%"></div>
            </div>
            <p class="mt-2 text-xs text-slate-500">
              Interpretasi santai: <em>{{ $norm >= 66 ? 'Wah, ini kamu banget!' : ($norm >= 33 ? 'Cukup terasa vibes-nya.' : 'Masih malu-malu kucing.') }}</em>
            </p>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Aksi Lanjutan --}}
  <div class="grid md:grid-cols-2 gap-4">
    <div class="rounded-2xl border p-5 bg-gradient-to-br from-amber-50 to-rose-50">
      <h3 class="text-lg font-semibold text-slate-900">Tips Lanjutan 🎯</h3>
      <ul class="mt-2 text-sm text-slate-700 list-disc pl-5 space-y-1">
        <li>Ambil tes lain untuk perspektif yang berbeda.</li>
        <li>Jangan overthinking — ini buat fun + insight ringan.</li>
        <li>Coba ulang minggu depan buat lihat konsistensi hasil.</li>
      </ul>
    </div>
    <div class="rounded-2xl border p-5 bg-gradient-to-br from-sky-50 to-indigo-50">
      <h3 class="text-lg font-semibold text-slate-900">Mau Pamer Dikit? 😏</h3>
      <p class="mt-2 text-sm text-slate-700">
        Klik <strong>Bagikan Hasil</strong> biar temanmu tahu seberapa mantap “kocak meter”-mu.
      </p>
      <p class="mt-1 text-xs text-slate-500">*Kami tidak akan mem-posting apa pun tanpa persetujuanmu.</p>
    </div>
  </div>

  <div class="text-center">
    <a href="{{ route('app.psytests.index') }}"
       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700">
      Lihat Semua Tes
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
           stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
           d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
    </a>
  </div>
</div>

@push('scripts')
<script>
  // Confetti emoji ringan (tanpa library)
  (function makeConfetti(){
    const el = document.getElementById('confetti');
    if (!el) return;
    const pieces = 40;
    const icons = ['🎉','✨','🎈','🥳','💥','🌟'];
    for(let i=0;i<pieces;i++){
      const s = document.createElement('span');
      s.textContent = icons[Math.floor(Math.random()*icons.length)];
      s.style.left = Math.random()*100+'%';
      s.style.animationDuration = (2 + Math.random()*2)+'s';
      s.style.animationDelay = (Math.random()*0.8)+'s';
      el.appendChild(s);
      setTimeout(()=> s.remove(), 4000);
    }
  })();

  // Copy summary
  document.getElementById('copyBtn')?.addEventListener('click', async () => {
    const data = {
      test: @json($test->name),
      total: @json($total),
      profile: @json($profileKey),
      reco: @json($recoText),
      traits: @json(collect($scores ?? [])->except('_total')),
      url: window.location.href
    };
    const lines = [];
    lines.push(`Hasil Tes: ${data.test}`);
    lines.push(`Total Skor: ${data.total}`);
    lines.push(`Profil: ${data.profile ?? '-'}`);
    if (Object.keys(data.traits || {}).length) {
      lines.push('Trait:');
      Object.entries(data.traits).forEach(([k,v]) => lines.push(`- ${k.toUpperCase()}: ${v}`));
    }
    lines.push(`Rekomendasi: ${data.reco}`);
    lines.push(data.url);
    try {
      await navigator.clipboard.writeText(lines.join('\n'));
      alert('Ringkasan disalin. Tempelkan di chat/notes-mu!');
    } catch(e) {
      alert('Gagal menyalin. Coba manual ya 😅');
    }
  });

  // Share API
  document.getElementById('shareBtn')?.addEventListener('click', async () => {
    const title = 'Hasil Tes Psikologi — BERKEMAH';
    const text  = `Aku baru selesai tes: {{ $test->name }} — Skor: {{ $total }} (Profil: {{ $profileKey ?? "-" }})`;
    const url   = window.location.href;
    if (navigator.share) {
      try {
        await navigator.share({ title, text, url });
      } catch(e) { /* user cancel */ }
    } else {
      alert('Fitur share tidak tersedia. Pakai tombol "Salin Ringkasan" ya! 🙏');
    }
  });
</script>
@endpush
@endsection
