{{-- resources/views/app/dashboard.blade.php --}}
@extends('app.layouts.base')
@section('title','Dashboard')

@push('styles')
<style>
  :root{
    --card-bg: rgba(255,255,255,.75);
    --card-brd: rgba(2,6,23,.08);
    --ink: #0f172a;
    --muted: #64748b;
  }
  @media (prefers-color-scheme: dark){
    :root{
      --card-bg: rgba(15,23,42,.6);
      --card-brd: rgba(148,163,184,.12);
      --ink: #e2e8f0;
      --muted: #94a3b8;
    }
  }

  .glass {background: var(--card-bg); border:1px solid var(--card-brd); backdrop-filter: blur(12px); border-radius: 18px}
  .hover-lift{transition:transform .22s cubic-bezier(.2,.8,.2,1), box-shadow .22s;}
  .hover-lift:hover{transform:translateY(-3px); box-shadow:0 18px 60px rgba(2,6,23,.14)}
  .chip{display:inline-flex;align-items:center;gap:.4rem;font-size:.72rem;padding:.3rem .55rem;border-radius:999px;background:#eef2ff;border:1px solid rgba(2,6,23,.05)}
  .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.55rem .9rem;border-radius:12px;font-weight:600;background:linear-gradient(135deg,#6366f1,#22d3ee);color:#fff;border:0}
  .btn.secondary{background:transparent;color:var(--ink);border:1px solid var(--card-brd)}
  .stat-num{font-size:2rem;font-weight:800;letter-spacing:-.02em}
  .subtle{color:var(--muted)}
  .progress-wrap{height:10px;background:rgba(99,102,241,.12);border-radius:999px;overflow:hidden}
  .progress-bar{height:100%;background:linear-gradient(90deg,#6366f1,#22d3ee)}
  .shine{position:relative;overflow:hidden}
  .shine:after{content:"";position:absolute;inset:-150% -50% auto;transform:rotate(12deg);height:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);animation:shine 6s linear infinite}
  @keyframes shine{0%{left:-120%}100%{left:140%}}
  .blob{position:absolute;filter:blur(60px);opacity:.5;z-index:-1}
  .blob.b1{background:#a78bfa;width:320px;height:320px;left:-80px;top:-60px;border-radius:50%}
  .blob.b2{background:#22d3ee;width:280px;height:280px;right:-60px;top:120px;border-radius:50%}
</style>
@endpush

@section('content')
<div class="relative">
  <div class="blob b1"></div>
  <div class="blob b2"></div>
</div>

<h1 class="text-2xl md:text-3xl font-extrabold mb-1">Hey, {{ $user->name }} ✨</h1>
<p class="subtle mb-6">Ringkasan pembelajaran & vibes harian kamu.</p>

{{-- === Stat Cards === --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="glass p-5 hover-lift shine">
    <div class="subtle text-sm">Courses Saya</div>
    <div class="stat-num">{{ (int)($stats['courses_count'] ?? 0) }}</div>
  </div>
  <div class="glass p-5 hover-lift">
    <div class="subtle text-sm">Membership Aktif</div>
    <div class="text-xl font-semibold">
      {{ optional(optional($stats['active_membership'] ?? null)->plan)->name ?? '—' }}
    </div>
  </div>
  <div class="glass p-5 hover-lift">
    <div class="subtle text-sm">Attempt Terakhir</div>
    <div class="text-xl font-semibold">
      {{ optional($stats['last_attempt'] ?? null)->score !== null ? optional($stats['last_attempt'])->score : '—' }}
    </div>
  </div>
</div>

{{-- === Grafik Utama === --}}
@php
  // Default struktur charts agar view tidak error jika controller belum mengirim semua kunci (tanpa trailing comma)
  $CH = $charts ?? [
    'progress'           => ['labels'=>[], 'percent'=>[], 'done'=>[], 'total'=>[]],
    'enroll'             => ['labels'=>[], 'counts'=>[]],
    'distribution'       => ['labels'=>[], 'counts'=>[]],
    'quiz'               => [],
    'completion_monthly' => ['labels'=>[], 'counts'=>[]],
    'attempts_monthly'   => ['labels'=>[], 'counts'=>[]],

    // tambahan agar semua section ada chart
    'my_courses'     => ['labels'=>[], 'percent'=>[]],
    'recommended'    => ['labels'=>[], 'counts'=>[]],
    'coupons'        => ['labels'=>[], 'counts'=>[]],
    'psy_tests'      => ['labels'=>[], 'questions'=>[]],
    'iq_tests'       => ['labels'=>[], 'duration'=>[]],
    'threads_latest' => ['labels'=>[], 'replies'=>[]],
    'threads_mine'   => ['labels'=>[], 'replies'=>[]]
  ];
@endphp

<div class="flex items-center justify-between mt-8 mb-2">
  <h2 class="text-lg md:text-xl font-bold">Insight Grafik 📈</h2>
  <div class="flex items-center gap-2">
    <button class="btn secondary" data-dl="all">Download Semua</button>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  {{-- Progress per Course --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Progress per Course</h3>
      <button class="btn secondary" data-dl="chartProgress">PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartProgress"></canvas></div>
  </div>

  {{-- Distribusi Progress --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Distribusi Progress</h3>
      <span class="chip">🔥 fokus area</span>
    </div>
    <div style="height:320px"><canvas id="chartDist"></canvas></div>
  </div>

  {{-- Enrollments --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Enrollments</h3>
      <button class="btn secondary" data-dl="chartEnroll">PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartEnroll"></canvas></div>
  </div>

  {{-- Riwayat Skor Quiz --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Riwayat Skor Quiz</h3>
      <button class="btn secondary" data-dl="chartQuiz">PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartQuiz"></canvas></div>
  </div>

  {{-- Lesson Completion / Bulan --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Lesson Completion / Bulan</h3>
      <button class="btn secondary" data-dl="chartCompleteMonthly">PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartCompleteMonthly"></canvas></div>
  </div>

  {{-- Quiz Attempts / Bulan --}}
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Quiz Attempts / Bulan</h3>
      <button class="btn secondary" data-dl="chartAttemptsMonthly">PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartAttemptsMonthly"></canvas></div>
  </div>
</div>

{{-- === My Courses (Grafik) === --}}
<div class="mt-10 glass p-4 hover-lift">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-lg md:text-xl font-bold">Courses Saya 🎒 (Grafik)</h2>
    <a href="{{ route('app.courses.index') }}" class="subtle hover:underline">Lihat semua →</a>
  </div>
  <div style="height:360px"><canvas id="chartMyCourses"></canvas></div>
</div>

{{-- === Active Coupons (Grafik) === --}}
<div class="mt-10 glass p-4 hover-lift">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-lg md:text-xl font-bold">Kupon Aktif 🎁 (Grafik)</h2>
  </div>
  <div style="height:360px"><canvas id="chartCoupons"></canvas></div>
</div>

{{-- === Psy & IQ Tests (Grafik) === --}}
<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Tes Psikologi 🧠 — Jumlah Pertanyaan</h2>
    </div>
    <div style="height:320px"><canvas id="chartPsyTests"></canvas></div>
  </div>
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Tes IQ ⚡️ — Durasi (menit)</h2>
    </div>
    <div style="height:320px"><canvas id="chartIqTests"></canvas></div>
  </div>
</div>

{{-- === Threads (Grafik) === --}}
<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Thread Terbaru 💬 — Jumlah Balasan</h2>
    </div>
    <div style="height:320px"><canvas id="chartThreadsLatest"></canvas></div>
  </div>
  <div class="glass p-4 hover-lift">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Thread Saya ✍️ — Jumlah Balasan</h2>
    </div>
    <div style="height:320px"><canvas id="chartThreadsMine"></canvas></div>
  </div>
</div>
@endsection

@push('scripts')
{{-- CDN Chart.js (tanpa SRI kosong). Untuk security maksimal, pertimbangkan self-host via Vite. --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js" referrerpolicy="no-referrer"></script>

<script>
const CHARTS = @json($CH, JSON_UNESCAPED_UNICODE);

// utils
function has(arr){ return Array.isArray(arr) && arr.length>0; }
function dl(id){
  const cvs = document.getElementById(id);
  if(!cvs) return;
  const link=document.createElement('a');
  link.download = id+'.png';
  link.href = cvs.toDataURL('image/png');
  link.click();
}
document.querySelectorAll('[data-dl]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const key=btn.getAttribute('data-dl');
    const all = ['chartProgress','chartDist','chartEnroll','chartQuiz','chartCompleteMonthly','chartAttemptsMonthly','chartMyCourses','chartRecommended','chartCoupons','chartPsyTests','chartIqTests','chartThreadsLatest','chartThreadsMine'];
    if(key==='all'){ all.forEach(id=>dl(id)); }
    else dl(key);
  });
});

const ctxGrad = (ctx) => {
  const g = ctx.createLinearGradient(0,0,0,300);
  g.addColorStop(0,'rgba(99,102,241,.35)');
  g.addColorStop(1,'rgba(34,211,238,.05)');
  return g;
};

// global chart style
Chart.defaults.elements.bar.borderRadius = 8;
Chart.defaults.plugins.legend.labels.usePointStyle = true;

/* === Progress per Course === */
if(document.getElementById('chartProgress') && CHARTS.progress && has(CHARTS.progress.percent)){
  const c = document.getElementById('chartProgress').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{ labels: CHARTS.progress.labels, datasets:[{label:'Progress (%)', data: CHARTS.progress.percent, backgroundColor: ctxGrad(c)}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100,ticks:{stepSize:20}}}, plugins:{tooltip:{callbacks:{label:ctx=>(ctx.parsed.y||0)+'%'}}}}
  });
}

/* === Distribusi === */
if(document.getElementById('chartDist') && CHARTS.distribution && has(CHARTS.distribution.counts)){
  new Chart(document.getElementById('chartDist'),{
    type:'doughnut',
    data:{ labels:CHARTS.distribution.labels, datasets:[{data:CHARTS.distribution.counts}]},
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}}
  });
}

/* === Enrollments === */
if(document.getElementById('chartEnroll') && CHARTS.enroll && has(CHARTS.enroll.counts)){
  const c=document.getElementById('chartEnroll').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{ labels:CHARTS.enroll.labels, datasets:[{label:'Enrollments', data:CHARTS.enroll.counts, backgroundColor: ctxGrad(c)}]},
    options:{responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}

/* === Quiz === */
if(document.getElementById('chartQuiz') && Array.isArray(CHARTS.quiz) && CHARTS.quiz.length){
  const c=document.getElementById('chartQuiz').getContext('2d');
  new Chart(c,{
    type:'line',
    data:{ labels: CHARTS.quiz.map(function(p){return p.t;}), datasets:[{label:'Score %', data:CHARTS.quiz.map(function(p){return p.y;}), backgroundColor: ctxGrad(c), borderColor:'#6366f1', fill:true, tension:.3, pointRadius:3}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100}}}
  });
}

/* === Completion / month === */
if(document.getElementById('chartCompleteMonthly') && CHARTS.completion_monthly && has(CHARTS.completion_monthly.labels)){
  const c=document.getElementById('chartCompleteMonthly').getContext('2d');
  new Chart(c,{
    type:'line',
    data:{labels:CHARTS.completion_monthly.labels, datasets:[{label:'Lessons selesai',data:CHARTS.completion_monthly.counts, backgroundColor:ctxGrad(c), borderColor:'#22d3ee', fill:true, tension:.3, pointRadius:3}]},
    options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true}}}
  });
}

/* === Attempts / month === */
if(document.getElementById('chartAttemptsMonthly') && CHARTS.attempts_monthly && has(CHARTS.attempts_monthly.labels)){
  const c=document.getElementById('chartAttemptsMonthly').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.attempts_monthly.labels, datasets:[{label:'Quiz attempts',data:CHARTS.attempts_monthly.counts, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true}}}
  });
}

/* === My Courses (progress per course %) === */
if(document.getElementById('chartMyCourses') && CHARTS.my_courses && has(CHARTS.my_courses.percent)){
  const c=document.getElementById('chartMyCourses').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.my_courses.labels, datasets:[{label:'Progress (%)', data:CHARTS.my_courses.percent, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true,max:100}}}
  });
}

/* === Recommended Courses (jumlah siswa) === */
if(document.getElementById('chartRecommended') && CHARTS.recommended && has(CHARTS.recommended.counts)){
  const c=document.getElementById('chartRecommended').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.recommended.labels, datasets:[{label:'Siswa', data:CHARTS.recommended.counts, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}

/* === Coupons timeline === */
if(document.getElementById('chartCoupons') && CHARTS.coupons && has(CHARTS.coupons.counts)){
  const c=document.getElementById('chartCoupons').getContext('2d');
  new Chart(c,{
    type:'line',
    data:{labels:CHARTS.coupons.labels, datasets:[{label:'Kupon aktif', data:CHARTS.coupons.counts, backgroundColor:ctxGrad(c), borderColor:'#10b981', fill:true, tension:.3}]},
    options:{responsive:true,maintainAspectRatio:false, scales:{y:{beginAtZero:true}}}
  });
}

/* === Psy Tests: jumlah pertanyaan === */
if(document.getElementById('chartPsyTests') && CHARTS.psy_tests && has(CHARTS.psy_tests.questions)){
  const c=document.getElementById('chartPsyTests').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.psy_tests.labels, datasets:[{label:'Pertanyaan', data:CHARTS.psy_tests.questions, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}

/* === IQ Tests: durasi menit === */
if(document.getElementById('chartIqTests') && CHARTS.iq_tests && has(CHARTS.iq_tests.duration)){
  const c=document.getElementById('chartIqTests').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.iq_tests.labels, datasets:[{label:'Durasi (menit)', data:CHARTS.iq_tests.duration, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}

/* === Threads: latest & mine (jumlah balasan) === */
if(document.getElementById('chartThreadsLatest') && CHARTS.threads_latest && has(CHARTS.threads_latest.replies)){
  const c=document.getElementById('chartThreadsLatest').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.threads_latest.labels, datasets:[{label:'Balasan', data:CHARTS.threads_latest.replies, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}
if(document.getElementById('chartThreadsMine') && CHARTS.threads_mine && has(CHARTS.threads_mine.replies)){
  const c=document.getElementById('chartThreadsMine').getContext('2d');
  new Chart(c,{
    type:'bar',
    data:{labels:CHARTS.threads_mine.labels, datasets:[{label:'Balasan', data:CHARTS.threads_mine.replies, backgroundColor:ctxGrad(c)}]},
    options:{responsive:true,maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}
</script>
@endpush
