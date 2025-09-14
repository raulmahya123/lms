@extends('app.layouts.base')
@section('title','Dashboard Psikologi')

@push('styles')
<style>
  :root{
    --ink:#0b1220; --muted:#64748b; --line:rgba(2,6,23,.08);
    --sky:#38bdf8; --indigo:#6366f1; --blue:#3b82f6; --emerald:#10b981; --amber:#f59e0b; --rose:#fb7185;
  }
  /* ===== Aesthetic base ===== */
  .glass{backdrop-filter: blur(10px); background:linear-gradient(180deg,rgba(255,255,255,.65),rgba(255,255,255,.45)); border:1px solid rgba(255,255,255,.6)}
  .card{border:1px solid var(--line); border-radius:18px; background:#fff; transition:.25s ease; box-shadow:0 8px 30px rgba(2,6,23,.07)}
  .card:hover{transform:translateY(-3px); box-shadow:0 20px 50px rgba(2,6,23,.15)}
  .chip{font-size:.72rem;padding:.28rem .6rem;border-radius:999px}
  .tag{border:1px dashed rgba(2,6,23,.12); padding:.24rem .6rem; border-radius:9px; font-size:.72rem}
  .btn{display:inline-flex; align-items:center; gap:.45rem; padding:.6rem .9rem; border-radius:12px; font-weight:700; border:1px solid var(--line); background:#fff}
  .btn-sky{background:linear-gradient(90deg,#60a5fa,#22d3ee); color:#fff; border:none}
  .btn-sky:hover{filter:brightness(.98)}
  .btn-ghost{background:transparent; border:1px dashed var(--line)}
  .badge-blue{background:#eff6ff; color:#1d4ed8}
  .badge-emerald{background:#ecfdf5; color:#047857}
  .badge-amber{background:#fffbeb; color:#b45309}
  .avatar{width:42px;height:42px;border-radius:999px;background:linear-gradient(120deg,#60a5fa,#818cf8);color:#fff;display:grid;place-items:center;font-weight:800}
  .hero{border-radius:22px; overflow:hidden; position:relative; border:1px solid rgba(99,102,241,.15);}
  .hero::before{content:''; position:absolute; inset:-80px -120px auto auto; width:520px; height:520px; background:radial-gradient(closest-side, rgba(99,102,241,.18), transparent 70%); filter:blur(6px);}
  .hero::after{content:''; position:absolute; inset:auto auto -120px -60px; width:680px; height:680px; background:radial-gradient(closest-side, rgba(56,189,248,.18), transparent 70%); filter:blur(8px);}
  .hero-bg{background:linear-gradient(135deg, #eef2ff, #f0f9ff);}  
  /* ===== Privacy guard ===== */
  .blurred{filter:blur(6px); transition:filter .25s ease}
  .blurred.revealed{filter:none}
  .lock-icon{display:inline-flex;align-items:center;gap:.35rem;font-size:.8rem;padding:.3rem .55rem;border-radius:9px;background:#0b122006;border:1px solid var(--line)}
  .no-select{user-select:none}
  /* ===== Shimmer ===== */
  .shimmer{position:relative; overflow:hidden}
  .shimmer::after{content:""; position:absolute; inset:0; background:linear-gradient(110deg, transparent 0%, rgba(255,255,255,.6) 40%, transparent 80%); transform:translateX(-100%); animation:shine 1.6s infinite}
  @keyframes shine{to{transform:translateX(120%)}}
  /* Charts dark mode friendly (if parent adds .dark) */
  .dark .card{background:#0b1220;border-color:#0b122020}
  .dark .hero-bg{background:linear-gradient(135deg,#0b1220,#0f172a)}
  .dark .btn{background:#0b1220;color:#e2e8f0}
  .dark .btn-sky{color:#0b1220}
</style>
<!-- Prevent indexing (privacy) -->
<meta name="robots" content="noindex,nofollow" />
<meta name="referrer" content="same-origin" />
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-8"
     x-data="dashboard()" x-init="init()" @keyup.window.ctrl.slash.prevent="togglePrivacy()">

  {{-- HEADER / HERO --}}
  <div class="hero hero-bg p-6 sm:p-8 relative">
    <div class="flex items-center justify-between gap-4 flex-wrap relative z-10">
      <div class="flex items-center gap-4">
        <div class="avatar no-select">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</div>
        <div>
          <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">
            Hai, <span x-text="privacyOn ? 'Teman' : @js(auth()->user()->name ?? 'Pengguna')"></span>
          </h1>
          <p class="text-sm text-slate-600">Tes psikologi, progres, dan rekomendasi profil — all in one place.</p>
        </div>
      </div>
      <div class="flex items-center gap-2 text-slate-600">
        <span class="tag">Gen‑Z mode ✨</span>
        <span class="tag">Blue vibes 💙</span>
        <button class="btn btn-ghost" @click="togglePrivacy()" :aria-pressed="privacyOn">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 1C7 1 3 5 3 10v2a9 9 0 0 0 18 0v-2c0-5-4-9-9-9Z"/><path d="M8 12v-2a4 4 0 0 1 8 0v2"/><rect x="9" y="12" width="6" height="7" rx="1"/></svg>
          <span x-text="privacyOn ? 'Private ON' : 'Private OFF'"></span>
        </button>
      </div>
    </div>
  </div>

  {{-- REKOMENDASI --}}
  @if(!empty($recommendation))
    <div class="rounded-2xl p-5 glass">
      <div class="flex items-start gap-3">
        <div class="chip badge-blue">Profil Terbaru</div>
        <div>
          <div class="font-semibold text-slate-900">{{ $recommendation['title'] }}</div>
          <p class="text-slate-700">{{ $recommendation['desc'] }}</p>
        </div>
      </div>
    </div>
  @endif

  {{-- RINGKASAN CEPAT --}}
  @php
    $totalAttempts = collect($stats ?? [])->sum(fn($s) => (int)($s->attempts ?? 0));
    $distinctTestsTried = collect($stats ?? [])->filter(fn($s) => (int)($s->attempts ?? 0) > 0)->count();
    $latestAttempt = optional($attempts)->first();
    $lastScore = $latestAttempt?->total_score ?? 0;
  @endphp

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card p-5">
      <div class="text-xs text-slate-500">Total Percobaan</div>
      <div class="mt-1 text-3xl font-extrabold no-select">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $totalAttempts }}</span>
      </div>
      <div class="mt-2 lock-icon" x-show="privacyOn"><span>Ctrl+/ untuk reveal cepat</span></div>
    </div>
    <div class="card p-5">
      <div class="text-xs text-slate-500">Tes Berbeda</div>
      <div class="mt-1 text-3xl font-extrabold no-select">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $distinctTestsTried }}</span>
      </div>
    </div>
    <div class="card p-5">
      <div class="text-xs text-slate-500">Skor Terakhir</div>
      <div class="mt-1 text-3xl font-extrabold no-select">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $lastScore }}</span>
      </div>
    </div>
  </div>

  {{-- ==== INSIGHT GRAFIK ==== --}}
  @php
    /* 1) Line: skor over time */
    $scoreSeries = collect($attempts->items() ?? [])
      ->sortBy(fn($a) => $a->submitted_at ?? $a->created_at)
      ->values();

    $chartScoreLabels = $scoreSeries->map(fn($a) => ($a->submitted_at?->format('d M') ?? $a->created_at->format('d M')));
    $chartScoreValues = $scoreSeries->map(fn($a) => (int)$a->total_score);

    /* 2) Bar: attempts per test */
    $chartAttemptsLabels = collect($tests ?? [])->map(fn($t) => $t->title);
    $chartAttemptsCounts = collect($tests ?? [])->map(function($t) use ($stats){
      $s = $stats[$t->id] ?? null; return (int) ($s->attempts ?? 0);
    });

    /* 3) Doughnut: distribusi skor */
    $bins = ['0–25'=>0,'26–50'=>0,'51–75'=>0,'76–100'=>0];
    foreach(($attempts->items() ?? []) as $it){
      $sc=(int)$it->total_score;
      if($sc<=25) $bins['0–25']++;
      elseif($sc<=50) $bins['26–50']++;
      elseif($sc<=75) $bins['51–75']++;
      else $bins['76–100']++;
    }
    $chartDistLabels = array_keys($bins);
    $chartDistCounts = array_values($bins);

    $hasAnyChartData = ($chartScoreValues->sum() > 0) || ($chartAttemptsCounts->sum() > 0) || (collect($chartDistCounts)->sum() > 0);
  @endphp

  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-lg">Insight Grafik</h2>
      <div class="flex items-center gap-2">
        <button class="btn btn-sky" data-dl="all" :disabled="privacyOn" :class="privacyOn ? 'opacity-60 cursor-not-allowed' : ''">Download Semua</button>
        <button class="btn" @click="toggleTheme()" title="Tema Gelap/Terang">🌓</button>
      </div>
    </div>

    @if(!$hasAnyChartData)
      <div class="card p-6 text-center text-slate-500">
        <div class="mx-auto h-2 w-40 rounded-full bg-slate-200 shimmer mb-3"></div>
        Belum ada data untuk ditampilkan. Coba kerjakan 1–2 tes dulu ya 😉
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Skor dari Waktu ke Waktu</h3>
            <button class="btn" data-dl="chartScores" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:320px"><canvas id="chartScores"></canvas></div>
        </div>

        <div class="card p-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Attempts per Test</h3>
            <button class="btn" data-dl="chartAttempts" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:320px"><canvas id="chartAttempts"></canvas></div>
        </div>

        <div class="card p-4 md:col-span-2">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Distribusi Skor</h3>
            <button class="btn" data-dl="chartDist" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:320px"><canvas id="chartDist"></canvas></div>
        </div>
      </div>
    @endif
  </div>

  {{-- TES TERSEDIA --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-lg">Tes Tersedia</h2>
      @if(Route::has('psy-tests.index'))
        @can('manage', App\Models\PsyTest::class)
          <a href="{{ route('psy-tests.index') }}" class="text-sky-600 hover:underline">Kelola (Admin) →</a>
        @endcan
      @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      @forelse($tests as $t)
        @php $s = $stats[$t->id] ?? null; @endphp
        <div class="card p-5 relative">
          <div class="flex items-center justify-between mb-1">
            <span class="chip badge-blue">Soal: {{ $t->questions_count }}</span>
            @if(!empty($t->is_premium))
              <span class="chip" style="background:#f5f3ff;color:#6d28d9">Premium</span>
            @endif
          </div>
          <h3 class="font-semibold">{{ $t->title }}</h3>
          <p class="text-sm text-slate-600 line-clamp-2">{{ $t->description }}</p>

          <div class="mt-3 grid grid-cols-3 text-center text-xs text-slate-500">
            <div>
              <div class="font-semibold text-slate-900 data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ (int)($s->attempts ?? 0) }}</div>
              attempt
            </div>
            <div>
              <div class="font-semibold text-slate-900 data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $s ? number_format((float)$s->avg_score,1) : '0.0' }}</div>
              rata2
            </div>
            <div>
              <div class="font-semibold text-slate-900 data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ (int)($s->best ?? 0) }}</div>
              terbaik
            </div>
          </div>

          <div class="mt-4">
            @if(!empty($t->locked))
              @if(Route::has('memberships.index'))
                <a href="{{ route('memberships.index') }}" class="btn w-full justify-center">Buka Akses (Paket)</a>
              @else
                <span class="inline-flex items-center px-3 py-2 rounded-xl bg-slate-200 text-slate-600">Terkunci</span>
              @endif
              <span class="absolute top-3 right-3 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded">Locked</span>
            @else
              @if(!empty($routeNames['take_show']))
                <a href="{{ route($routeNames['take_show'], $t) }}" class="btn btn-sky w-full justify-center">Mulai / Ulangi Tes</a>
              @else
                <a href="{{ url('/psy-tests/'.$t->id) }}" class="btn btn-sky w-full justify-center">Mulai / Ulangi Tes</a>
              @endif
            @endif
          </div>
        </div>
      @empty
        <p class="text-slate-500">Belum ada tes.</p>
      @endforelse
    </div>
  </div>

  {{-- RIWAYAT --}}
  <div>
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-lg">Riwayat Terbaru</h2>
      <span class="text-xs text-slate-500">Data sensitif disembunyikan saat Private ON</span>
    </div>

    @if($attempts->isEmpty())
      <div class="card p-6 text-center text-slate-500">
        Belum ada riwayat tes. Gas coba satu tes dulu! 🚀
      </div>
    @else
      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="text-left px-4 py-2 font-medium text-slate-600">Tanggal</th>
              <th class="text-left px-4 py-2 font-medium text-slate-600">Tes</th>
              <th class="text-left px-4 py-2 font-medium text-slate-600">Skor</th>
              <th class="text-left px-4 py-2 font-medium text-slate-600">Hasil</th>
            </tr>
          </thead>
          <tbody>
            @foreach($attempts as $a)
              @php
                // PROFIL: ambil berdasar user_id
                $profile = null;
                if (class_exists(\App\Services\PsyAccess::class)) {
                  $profile = \App\Services\PsyAccess::findProfile($a->test_id, (int)$a->total_score, auth()->id());
                }
                if (!$profile && $a->result_key) { $profile = (object)['name' => $a->result_key]; }
              @endphp
              <tr class="border-t hover:bg-slate-50/50">
                <td class="px-4 py-2 whitespace-nowrap">
                  <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $a->submitted_at?->format('d M Y, H:i') ?? $a->created_at->format('d M Y, H:i') }}</span>
                </td>
                <td class="px-4 py-2">{{ $a->test->title }}</td>
                <td class="px-4 py-2 font-semibold">
                  <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ (int) $a->total_score }}</span>
                </td>
                <td class="px-4 py-2">
                  @if($profile)
                    <span class="chip badge-emerald data-guard" :class="privacyOn ? 'blurred' : 'revealed'">
                      {{ is_object($profile) ? ($profile->name ?? '-') : (string)$profile }}
                    </span>
                  @else
                    <span class="chip badge-amber">Belum terklasifikasi</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $attempts->links() }}</div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const SCORE_LABELS    = @json($chartScoreLabels ?? []);
  const SCORE_VALUES    = @json($chartScoreValues ?? []);
  const ATTEMPT_LABELS  = @json($chartAttemptsLabels ?? []);
  const ATTEMPT_COUNTS  = @json($chartAttemptsCounts ?? []);
  const DIST_LABELS     = @json($chartDistLabels ?? []);
  const DIST_COUNTS     = @json($chartDistCounts ?? []);

  const dl = id => { const c=document.getElementById(id); if(!c) return;
    const a=document.createElement('a'); a.download=id+'.png'; a.href=c.toDataURL('image/png'); a.click(); };
  document.querySelectorAll('[data-dl]').forEach(b=>b.addEventListener('click',()=>{
    const k=b.getAttribute('data-dl'); if(k==='all'){['chartScores','chartAttempts','chartDist'].forEach(dl)} else dl(k);
  }));

  const grad = (ctx) => { const g = ctx.createLinearGradient(0,0,0,300);
    g.addColorStop(0,'rgba(99,102,241,.35)'); g.addColorStop(1,'rgba(34,211,238,.08)'); return g; };

  // Line: scores
  if (document.getElementById('chartScores') && SCORE_VALUES.length) {
    const ctx = document.getElementById('chartScores').getContext('2d');
    new Chart(ctx, {
      type:'line',
      data:{labels:SCORE_LABELS, datasets:[{label:'Skor', data:SCORE_VALUES, borderColor:'#6366f1', backgroundColor:grad(ctx), fill:true, tension:.35, pointRadius:3}]},
      options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true, suggestedMax:100}}, plugins:{legend:{display:false}, tooltip:{callbacks:{label:c=>`Skor: ${c.parsed.y}`}}}}
    });
  }

  // Bar: attempts per test
  if (document.getElementById('chartAttempts') && ATTEMPT_COUNTS.length) {
    const ctx = document.getElementById('chartAttempts').getContext('2d');
    Chart.defaults.elements.bar.borderRadius = 8;
    new Chart(ctx, {
      type:'bar',
      data:{labels:ATTEMPT_LABELS, datasets:[{label:'Attempts', data:ATTEMPT_COUNTS, backgroundColor:grad(ctx)}]},
      options:{responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true, ticks:{precision:0}}}, plugins:{legend:{display:false}}}
    });
  }

  // Doughnut: distribusi skor
  if (document.getElementById('chartDist') && DIST_COUNTS.some(v=>v>0)) {
    new Chart(document.getElementById('chartDist'), {
      type:'doughnut',
      data:{labels:DIST_LABELS, datasets:[{data:DIST_COUNTS, backgroundColor:['#93c5fd','#60a5fa','#818cf8','#38bdf8'], borderWidth:0}]},
      options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}}
    });
  }

  // Alpine helpers: privacy + theme
  function applyPrivacy(on){
    document.querySelectorAll('.data-guard').forEach(el=>{
      el.classList.toggle('blurred', !!on);
      el.classList.toggle('revealed', !on);
    });
  }
  window.dashboard = () => ({
    privacyOn: localStorage.getItem('psy_privacy') === '1',
    init(){ applyPrivacy(this.privacyOn); this.applyTheme(); },
    togglePrivacy(){ this.privacyOn = !this.privacyOn; localStorage.setItem('psy_privacy', this.privacyOn ? '1' : '0'); applyPrivacy(this.privacyOn); },
    toggleTheme(){ const isDark = document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light'); },
    applyTheme(){ const saved = localStorage.getItem('theme'); if(saved==='dark'){ document.documentElement.classList.add('dark'); } }
  });
</script>
@endpush
