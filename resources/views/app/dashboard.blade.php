@extends('app.layouts.base')
@section('title','Dashboard')

@push('styles')
<style>
  .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
  .hover-lift:hover{transform:translateY(-2px); box-shadow:0 14px 40px rgba(2,6,23,.12)}
  .soft-card{background:#fff; border:1px solid rgba(2,6,23,.08); border-radius:16px}
  .progress-wrap{height:10px; background:#eef2ff; border-radius:999px; overflow:hidden}
  .progress-bar{height:100%; background:linear-gradient(90deg,#6366f1,#22d3ee)}
  .card-head{display:flex; align-items:center; justify-content:space-between; gap:.75rem}
  .btn{display:inline-flex; align-items:center; gap:.4rem; padding:.4rem .7rem; border-radius:10px; font-size:.78rem; border:1px solid rgba(2,6,23,.08); background:#f8fafc}
  .btn:hover{background:#eef2ff}
</style>
@endpush

@section('content')
<h1 class="text-xl font-semibold mb-1">Halo, {{ $user->name }}</h1>
<p class="text-sm text-gray-500 mb-6">Ringkasan pembelajaran dan aktivitasmu 🎯</p>

{{-- === Stat box === --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="p-4 soft-card">
    <div class="text-sm text-gray-500">Courses Saya</div>
    <div class="text-3xl font-bold">{{ $stats['courses_count'] ?? 0 }}</div>
  </div>
  <div class="p-4 soft-card">
    <div class="text-sm text-gray-500">Membership Aktif</div>
    <div class="text-lg">
      {{ optional(optional($stats['active_membership'] ?? null)->plan)->name ?? '—' }}
    </div>
  </div>
  <div class="p-4 soft-card">
    <div class="text-sm text-gray-500">Attempt Terakhir</div>
    <div class="text-lg">
      {{ optional($stats['last_attempt'] ?? null)->score !== null ? optional($stats['last_attempt'])->score : '—' }}
    </div>
  </div>
</div>

{{-- === Grafik === --}}
@php
  $CH = $charts ?? [
    'progress'=>['labels'=>[],'percent'=>[],'done'=>[],'total'=>[]],
    'enroll'=>['labels'=>[],'counts'=>[]],
    'distribution'=>['labels'=>[],'counts'=>[]],
    'quiz'=>[],
    'completion_monthly'=>['labels'=>[],'counts'=>[]],
    'attempts_monthly'=>['labels'=>[],'counts'=>[]],
  ];
@endphp

<div class="flex items-center justify-between mt-8 mb-2">
  <h2 class="text-lg font-semibold">Insight Grafik</h2>
  {{-- contoh filter (dummy, bisa kamu sambungkan nanti) --}}
  <div class="flex items-center gap-2">
    <select class="btn">
      <option>Semua Course</option>
      <option>Course Terbaru</option>
      <option>Course Populer</option>
    </select>
    <select class="btn">
      <option>6 Bulan</option>
      <option>12 Bulan</option>
    </select>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Progress per Course</h3>
      <button class="btn" data-dl="chartProgress">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartProgress"></canvas></div>
  </div>

  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Distribusi Progress</h3>
      <button class="btn" data-dl="chartDist">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartDist"></canvas></div>
  </div>

  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Enrollments (Courses Saya)</h3>
      <button class="btn" data-dl="chartEnroll">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartEnroll"></canvas></div>
  </div>

  @if(!empty($CH['quiz']))
  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Riwayat Skor Quiz</h3>
      <button class="btn" data-dl="chartQuiz">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartQuiz"></canvas></div>
  </div>
  @endif

  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Lesson Completion / Bulan</h3>
      <button class="btn" data-dl="chartCompleteMonthly">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartCompleteMonthly"></canvas></div>
  </div>

  <div class="p-4 soft-card hover-lift">
    <div class="card-head mb-3">
      <h3 class="font-semibold">Quiz Attempts / Bulan</h3>
      <button class="btn" data-dl="chartAttemptsMonthly">Download PNG</button>
    </div>
    <div style="height:320px"><canvas id="chartAttemptsMonthly"></canvas></div>
  </div>
</div>

{{-- === My Courses (dengan progress bar) === --}}
<div class="mt-10">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">Courses Saya</h2>
    <a href="{{ route('app.courses.index') }}" class="text-indigo-600 text-sm hover:underline">Lihat semua</a>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse(($myCourses ?? collect()) as $c)
      <div class="p-4 soft-card hover-lift">
        <div class="font-medium">{{ \Illuminate\Support\Str::limit($c->title, 48) }}</div>
        <div class="text-xs text-gray-500 mt-1">
          {{ $c->modules_count ?? 0 }} modul • {{ $c->lessons_count ?? 0 }} lessons
        </div>
        <div class="mt-3 progress-wrap">
          <div class="progress-bar" style="width: {{ (int)($c->progress_percent ?? 0) }}%"></div>
        </div>
        <div class="mt-1 text-xs text-gray-600">
          {{ (int)($c->progress_done ?? 0) }} / {{ (int)($c->progress_total ?? 0) }} selesai ({{ (int)($c->progress_percent ?? 0) }}%)
        </div>
      </div>
    @empty
      <div class="col-span-full p-6 soft-card text-center text-gray-500">
        Kamu belum mengambil course apa pun.
      </div>
    @endforelse
  </div>
</div>

{{-- === Recommended Courses === --}}
<div class="mt-10">
  <h2 class="text-lg font-semibold mb-3">Rekomendasi Buat Kamu</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse(($recommendedCourses ?? collect()) as $c)
      <div class="p-4 soft-card hover-lift">
        <div class="font-medium">{{ \Illuminate\Support\Str::limit($c->title, 48) }}</div>
        <div class="text-xs text-gray-500 mt-1">
          {{ $c->modules_count ?? 0 }} modul • {{ $c->lessons_count ?? 0 }} lessons
        </div>
        <div class="mt-3 text-sm text-indigo-600">
          {{ number_format($c->enrollments_count ?? 0) }} siswa
        </div>
      </div>
    @empty
      <div class="col-span-full p-6 soft-card text-center text-gray-500">
        Rekomendasi belum tersedia.
      </div>
    @endforelse
  </div>
</div>

{{-- === Active Coupons === --}}
<div class="mt-10">
  <h2 class="text-lg font-semibold mb-3">Kupon Aktif</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse(($activeCoupons ?? collect()) as $cp)
      <div class="p-4 soft-card hover-lift">
        <div class="text-sm text-gray-500">Kode</div>
        <div class="text-lg font-semibold">{{ $cp->code }}</div>
        <div class="mt-2 text-sm">
          Diskon: {{ $cp->discount_label ?? ($cp->percent ? $cp->percent.'%' : '—') }}
        </div>
        <div class="text-xs text-gray-500">
          Berlaku s/d: {{ optional($cp->valid_until)->format('d M Y') ?? 'Tanpa batas' }}
        </div>
      </div>
    @empty
      <div class="col-span-full p-6 soft-card text-center text-gray-500">
        Tidak ada kupon aktif hari ini.
      </div>
    @endforelse
  </div>
</div>

{{-- === Psy Tests & IQ Tests === --}}
<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>
    <h2 class="text-lg font-semibold mb-3">Tes Psikologi</h2>
    <div class="grid grid-cols-1 gap-4">
      @forelse(($psyTests ?? collect()) as $t)
        <div class="p-4 soft-card hover-lift">
          <div class="font-medium">{{ \Illuminate\Support\Str::limit($t->title, 48) }}</div>
          <div class="text-xs text-gray-500">{{ $t->questions_count ?? 0 }} pertanyaan</div>
        </div>
      @empty
        <div class="p-6 soft-card text-center text-gray-500">Belum ada tes.</div>
      @endforelse
    </div>
  </div>
  <div>
    <h2 class="text-lg font-semibold mb-3">Tes IQ</h2>
    <div class="grid grid-cols-1 gap-4">
      @forelse(($iqTests ?? collect()) as $t)
        <div class="p-4 soft-card hover-lift">
          <div class="font-medium">{{ \Illuminate\Support\Str::limit($t->title, 48) }}</div>
          <div class="text-xs text-gray-500">{{ $t->duration_minutes ?? 0 }} menit</div>
        </div>
      @empty
        <div class="p-6 soft-card text-center text-gray-500">Belum ada tes IQ.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- === Threads === --}}
<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>
    <h2 class="text-lg font-semibold mb-3">Thread Terbaru</h2>
    <div class="space-y-3">
      @forelse(($latestThreads ?? collect()) as $th)
        <div class="p-4 soft-card hover-lift">
          <div class="font-medium">{{ \Illuminate\Support\Str::limit($th->title ?? '(Tanpa judul)', 64) }}</div>
          <div class="text-xs text-gray-500 mt-1">
            oleh {{ optional($th->user)->name ?? 'Anon' }} • {{ $th->replies_count ?? 0 }} balasan
          </div>
        </div>
      @empty
        <div class="p-6 soft-card text-center text-gray-500">Belum ada thread.</div>
      @endforelse
    </div>
  </div>
  <div>
    <h2 class="text-lg font-semibold mb-3">Thread Saya</h2>
    <div class="space-y-3">
      @forelse(($myThreads ?? collect()) as $th)
        <div class="p-4 soft-card hover-lift">
          <div class="font-medium">{{ \Illuminate\Support\Str::limit($th->title ?? '(Tanpa judul)', 64) }}</div>
          <div class="text-xs text-gray-500 mt-1">
            {{ optional($th->course)->title ?? '—' }} • {{ optional($th->lesson)->title ?? '—' }} • {{ $th->replies_count ?? 0 }} balasan
          </div>
        </div>
      @empty
        <div class="p-6 soft-card text-center text-gray-500">Kamu belum membuat thread.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const CHARTS = {!! json_encode($CH, JSON_UNESCAPED_UNICODE) !!};

// Helper warna
function pastel(n){ const a=[]; for(let i=0;i<n;i++){a.push(`hsl(${Math.floor(360*Math.random())} 70% 65%)`);} return a; }
function hasData(arr){ return Array.isArray(arr) && arr.length > 0; }

// Download PNG
document.querySelectorAll('[data-dl]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const id = btn.getAttribute('data-dl');
    const canvas = document.getElementById(id);
    if(!canvas) return;
    const link = document.createElement('a');
    link.download = id + '.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
  });
});

// Progress
if(document.getElementById('chartProgress') && hasData(CHARTS.progress.percent)){
  new Chart(document.getElementById('chartProgress'), {
    type: 'bar',
    data: { labels: CHARTS.progress.labels, datasets:[{label:'Progress (%)', data: CHARTS.progress.percent, backgroundColor: pastel(CHARTS.progress.percent.length)}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100, ticks:{stepSize:20}}}}
  });
}

// Distribusi
if(document.getElementById('chartDist') && hasData(CHARTS.distribution.counts)){
  new Chart(document.getElementById('chartDist'), {
    type: 'doughnut',
    data: { labels: CHARTS.distribution.labels, datasets:[{data: CHARTS.distribution.counts, backgroundColor: pastel(CHARTS.distribution.counts.length)}]},
    options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}}
  });
}

// Enrollments
if(document.getElementById('chartEnroll') && hasData(CHARTS.enroll.counts)){
  new Chart(document.getElementById('chartEnroll'), {
    type: 'bar',
    data: { labels: CHARTS.enroll.labels, datasets:[{label:'Enrollments', data: CHARTS.enroll.counts, backgroundColor: pastel(CHARTS.enroll.counts.length)}]},
    options:{responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
  });
}

// Quiz
if(document.getElementById('chartQuiz') && Array.isArray(CHARTS.quiz) && CHARTS.quiz.length){
  new Chart(document.getElementById('chartQuiz'), {
    type: 'line',
    data: { labels: CHARTS.quiz.map(p=>p.t), datasets:[{label:'Score %', data:CHARTS.quiz.map(p=>p.y), fill:false, tension:0.25, pointRadius:3}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true,max:100}}}
  });
}

// Completion / month
if(document.getElementById('chartCompleteMonthly') && CHARTS.completion_monthly && hasData(CHARTS.completion_monthly.labels)){
  new Chart(document.getElementById('chartCompleteMonthly'), {
    type: 'line',
    data: { labels: CHARTS.completion_monthly.labels, datasets:[{label:'Lessons selesai', data: CHARTS.completion_monthly.counts, fill:false, tension:0.25, pointRadius:3}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true}}}
  });
}

// Attempts / month
if(document.getElementById('chartAttemptsMonthly') && CHARTS.attempts_monthly && hasData(CHARTS.attempts_monthly.labels)){
  new Chart(document.getElementById('chartAttemptsMonthly'), {
    type: 'bar',
    data: { labels: CHARTS.attempts_monthly.labels, datasets:[{label:'Quiz attempts', data: CHARTS.attempts_monthly.counts}]},
    options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true}}}
  });
}
</script>
@endpush
