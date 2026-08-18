@extends('layouts.app')
@section('title', 'Dashboard Psikologi')

@push('styles')
<!-- Prevent indexing (privacy) -->
<meta name="robots" content="noindex,nofollow" />
<meta name="referrer" content="same-origin" />
<style>
  .blurred { filter: blur(6px); transition: filter .25s ease; user-select: none; }
  .revealed { filter: none; }
</style>
@endpush

@section('content')
@php
  $totalAttempts = collect($stats ?? [])->sum(fn($s) => (int)($s->attempts ?? 0));
  $distinctTestsTried = collect($stats ?? [])->filter(fn($s) => (int)($s->attempts ?? 0) > 0)->count();
  $latestAttempt = optional($attempts)->first();
  $lastScore = $latestAttempt?->total_score ?? 0;

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

<div class="max-w-7xl mx-auto space-y-8" x-data="dashboard()" x-init="init()" @keyup.window.ctrl.slash.prevent="togglePrivacy()">

  {{-- HERO SECTION --}}
  <div class="relative bg-tosca-dark rounded-3xl p-8 md:p-12 overflow-hidden shadow-[0_8px_30px_rgba(7,94,84,0.15)] text-white">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-32 -mb-10 w-40 h-40 bg-tosca-light opacity-10 rounded-full blur-2xl pointer-events-none"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-white text-tosca-dark flex items-center justify-center text-2xl font-black shadow-lg">
          {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
            Hi, <span x-text="privacyOn ? 'Friend' : @js(auth()->user()->name ?? 'User')"></span>
          </h1>
          <p class="text-tosca-light/80 mt-1">Your psychology tests, progress, and profiles — all in one place.</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button @click="togglePrivacy()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition-colors backdrop-blur-md text-sm font-semibold">
          <svg x-show="!privacyOn" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
          <svg x-show="privacyOn" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
          <span x-text="privacyOn ? 'Privacy Mode: ON' : 'Privacy Mode: OFF'"></span>
        </button>
      </div>
    </div>
  </div>

  {{-- RECOMMENDATION --}}
  @if(!empty($recommendation))
    <div class="bg-gradient-to-r from-tosca-light/30 to-softbg rounded-3xl p-6 border border-tosca/10 shadow-sm flex items-start gap-4">
      <div class="w-12 h-12 rounded-full bg-tosca text-white flex items-center justify-center shrink-0 shadow-md">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
      </div>
      <div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-tosca-light text-tosca-dark uppercase tracking-wider mb-2">Latest Profile</div>
        <h3 class="font-bold text-lg text-text-main">{{ $recommendation['title'] }}</h3>
        <p class="text-text-soft mt-1">{{ $recommendation['desc'] }}</p>
      </div>
    </div>
  @endif

  {{-- STATS GRID --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
      <div class="text-sm font-semibold text-text-soft uppercase tracking-wider">Total Attempts</div>
      <div class="mt-2 text-4xl font-extrabold text-text-main">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $totalAttempts }}</span>
      </div>
      <div class="mt-2 text-xs text-text-soft bg-gray-50 px-2 py-1 rounded inline-block" x-show="privacyOn">Press Ctrl+/ to reveal</div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
      <div class="text-sm font-semibold text-text-soft uppercase tracking-wider">Distinct Tests</div>
      <div class="mt-2 text-4xl font-extrabold text-text-main">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $distinctTestsTried }}</span>
      </div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
      <div class="text-sm font-semibold text-text-soft uppercase tracking-wider">Last Score</div>
      <div class="mt-2 text-4xl font-extrabold text-tosca">
        <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $lastScore }}</span>
      </div>
    </div>
  </div>

  {{-- CHARTS INSIGHT --}}
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-text-main">Analytics Insights</h2>
      <button class="px-4 py-2 bg-white border border-gray-100 rounded-xl text-sm font-semibold text-text-main hover:text-tosca hover:border-tosca/30 transition-colors shadow-sm" data-dl="all" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">
        Download Reports
      </button>
    </div>

    @if(!$hasAnyChartData)
      <div class="bg-white rounded-3xl p-10 border border-gray-50 shadow-sm text-center">
        <div class="w-16 h-16 mx-auto bg-softbg rounded-full flex items-center justify-center text-tosca mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-text-main">No Data Available</h3>
        <p class="text-text-soft mt-1">Take 1 or 2 tests to start seeing your analytics here 😉</p>
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Chart 1 --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-text-main">Scores Over Time</h3>
            <button data-dl="chartScores" class="text-xs font-bold text-text-soft hover:text-tosca uppercase tracking-wider bg-gray-50 px-2 py-1 rounded" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:300px"><canvas id="chartScores"></canvas></div>
        </div>

        {{-- Chart 2 --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-text-main">Attempts per Test</h3>
            <button data-dl="chartAttempts" class="text-xs font-bold text-text-soft hover:text-tosca uppercase tracking-wider bg-gray-50 px-2 py-1 rounded" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:300px"><canvas id="chartAttempts"></canvas></div>
        </div>

        {{-- Chart 3 --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] md:col-span-2">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-text-main">Score Distribution</h3>
            <button data-dl="chartDist" class="text-xs font-bold text-text-soft hover:text-tosca uppercase tracking-wider bg-gray-50 px-2 py-1 rounded" :disabled="privacyOn" :class="privacyOn ? 'opacity-50 cursor-not-allowed' : ''">PNG</button>
          </div>
          <div style="height:300px" class="flex justify-center"><canvas id="chartDist"></canvas></div>
        </div>
      </div>
    @endif
  </div>

  {{-- AVAILABLE TESTS --}}
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-text-main">Available Tests</h2>
      @if(Route::has('psy-tests.index'))
        @can('manage', App\Models\PsyTest::class)
          <a href="{{ route('psy-tests.index') }}" class="text-sm font-semibold text-tosca hover:text-tosca-dark transition-colors">Manage (Admin) →</a>
        @endcan
      @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($tests as $t)
        @php $s = $stats[$t->id] ?? null; @endphp
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 flex flex-col hover:border-tosca/30 hover:shadow-tosca/10 transition-all group">
          <div class="flex items-center justify-between mb-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-softbg text-tosca-dark uppercase tracking-wider">
              {{ $t->questions_count }} Questions
            </span>
            @if(!empty($t->is_premium))
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-700 uppercase tracking-wider">Premium</span>
            @endif
          </div>
          
          <h3 class="font-bold text-lg text-text-main group-hover:text-tosca transition-colors leading-tight mb-2">{{ $t->title }}</h3>
          <p class="text-sm text-text-soft line-clamp-2 mb-4 flex-1">{{ $t->description }}</p>

          <div class="grid grid-cols-3 gap-2 text-center bg-gray-50 rounded-2xl p-3 mb-5">
            <div>
              <div class="text-lg font-bold text-text-main data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ (int)($s->attempts ?? 0) }}</div>
              <div class="text-[10px] uppercase font-bold text-text-soft tracking-wider mt-0.5">Attempts</div>
            </div>
            <div class="border-x border-gray-200">
              <div class="text-lg font-bold text-text-main data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $s ? number_format((float)$s->avg_score,1) : '0.0' }}</div>
              <div class="text-[10px] uppercase font-bold text-text-soft tracking-wider mt-0.5">Average</div>
            </div>
            <div>
              <div class="text-lg font-bold text-text-main data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ (int)($s->best ?? 0) }}</div>
              <div class="text-[10px] uppercase font-bold text-text-soft tracking-wider mt-0.5">Best</div>
            </div>
          </div>

          <div>
            @if(!empty($t->locked))
              @if(Route::has('memberships.index'))
                <a href="{{ route('memberships.index') }}" class="block w-full text-center py-2.5 bg-gray-100 text-gray-500 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                  Unlock Access
                </a>
              @else
                <span class="block w-full text-center py-2.5 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed">Locked</span>
              @endif
            @else
              @if(!empty($routeNames['take_show']))
                <a href="{{ route($routeNames['take_show'], $t) }}" class="block w-full text-center py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                  Start Test
                </a>
              @else
                <a href="{{ url('/psy-tests/'.$t->id) }}" class="block w-full text-center py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                  Start Test
                </a>
              @endif
            @endif
          </div>
        </div>
      @empty
        <div class="col-span-full p-10 bg-white rounded-3xl border border-gray-50 text-center">
          <p class="text-text-soft">No tests available at the moment.</p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- HISTORY --}}
  <div class="space-y-4 pb-12">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-text-main">Recent History</h2>
      <span class="text-xs font-semibold text-text-soft uppercase tracking-wider bg-gray-100 px-3 py-1.5 rounded-lg" x-show="privacyOn">Private Mode Active</span>
    </div>

    @if($attempts->isEmpty())
      <div class="bg-white rounded-3xl border border-gray-50 p-10 text-center shadow-sm">
        <p class="text-text-soft">You haven't taken any tests yet. Let's get started! 🚀</p>
      </div>
    @else
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-softbg/50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                <th class="px-6 py-4 font-bold">Date</th>
                <th class="px-6 py-4 font-bold">Test Name</th>
                <th class="px-6 py-4 font-bold">Score</th>
                <th class="px-6 py-4 font-bold">Result Profile</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              @foreach($attempts as $a)
                <tr class="hover:bg-softbg/30 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-text-soft">
                    <span class="data-guard" :class="privacyOn ? 'blurred' : 'revealed'">{{ $a->submitted_at?->format('d M Y, H:i') ?? $a->created_at->format('d M Y, H:i') }}</span>
                  </td>
                  <td class="px-6 py-4 text-sm font-semibold text-text-main">{{ $a->test->title }}</td>
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-tosca-light text-tosca-dark font-bold text-sm data-guard" :class="privacyOn ? 'blurred' : 'revealed'">
                      {{ (int) $a->total_score }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    @if(!empty($a->profile_name) && $a->profile_name !== '-')
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold text-xs uppercase tracking-wider bg-green-100 text-green-700 data-guard" :class="privacyOn ? 'blurred' : 'revealed'">
                        {{ $a->profile_name }}
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold text-xs uppercase tracking-wider bg-gray-100 text-gray-500">
                        Unclassified
                      </span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="mt-4">{{ $attempts->links() }}</div>
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

  // Theme Colors for Tosca Enterprise
  const toscaMain = '#0F9D8A';
  const toscaLight = '#DFF5F1';
  const textSoft = '#64748b';

  const dl = id => { const c=document.getElementById(id); if(!c) return;
    const a=document.createElement('a'); a.download=id+'.png'; a.href=c.toDataURL('image/png'); a.click(); };
  document.querySelectorAll('[data-dl]').forEach(b=>b.addEventListener('click',()=>{
    const k=b.getAttribute('data-dl'); if(k==='all'){['chartScores','chartAttempts','chartDist'].forEach(dl)} else dl(k);
  }));

  const grad = (ctx) => { 
    const g = ctx.createLinearGradient(0,0,0,300);
    g.addColorStop(0, 'rgba(15, 157, 138, 0.2)'); // tosca with opacity
    g.addColorStop(1, 'rgba(15, 157, 138, 0)');
    return g; 
  };

  Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
  Chart.defaults.color = textSoft;

  // Line: scores
  if (document.getElementById('chartScores') && SCORE_VALUES.length) {
    const ctx = document.getElementById('chartScores').getContext('2d');
    new Chart(ctx, {
      type:'line',
      data:{
        labels:SCORE_LABELS, 
        datasets:[{
          label:'Score', 
          data:SCORE_VALUES, 
          borderColor:toscaMain, 
          backgroundColor:grad(ctx), 
          fill:true, 
          tension:0.4, 
          borderWidth: 2,
          pointBackgroundColor: toscaMain,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options:{
        responsive:true, 
        maintainAspectRatio:false, 
        scales:{
          y:{beginAtZero:true, suggestedMax:100, border:{display:false}, grid:{color:'#f1f5f9'}},
          x:{border:{display:false}, grid:{display:false}}
        }, 
        plugins:{
          legend:{display:false}, 
          tooltip:{
            backgroundColor: '#0F9D8A',
            padding: 10,
            cornerRadius: 8,
            callbacks:{label:c=>` Score: ${c.parsed.y}`}
          }
        }
      }
    });
  }

  // Bar: attempts per test
  if (document.getElementById('chartAttempts') && ATTEMPT_COUNTS.length) {
    const ctx = document.getElementById('chartAttempts').getContext('2d');
    new Chart(ctx, {
      type:'bar',
      data:{
        labels:ATTEMPT_LABELS, 
        datasets:[{
          label:'Attempts', 
          data:ATTEMPT_COUNTS, 
          backgroundColor:toscaMain,
          borderRadius: 6,
          barPercentage: 0.6
        }]
      },
      options:{
        responsive:true, 
        maintainAspectRatio:false, 
        indexAxis:'y', 
        scales:{
          x:{beginAtZero:true, ticks:{precision:0}, border:{display:false}, grid:{color:'#f1f5f9'}},
          y:{border:{display:false}, grid:{display:false}}
        }, 
        plugins:{
          legend:{display:false},
          tooltip:{
            backgroundColor: '#0F9D8A',
            padding: 10,
            cornerRadius: 8,
          }
        }
      }
    });
  }

  // Doughnut: distribusi skor
  if (document.getElementById('chartDist') && DIST_COUNTS.some(v=>v>0)) {
    new Chart(document.getElementById('chartDist'), {
      type:'doughnut',
      data:{
        labels:DIST_LABELS, 
        datasets:[{
          data:DIST_COUNTS, 
          backgroundColor:['#DFF5F1', '#88D8CC', '#0F9D8A', '#075E54'], 
          borderWidth:2,
          borderColor: '#ffffff'
        }]
      },
      options:{
        responsive:true, 
        maintainAspectRatio:false, 
        cutout: '70%',
        plugins:{
          legend:{position:'bottom', labels:{usePointStyle:true, padding:20}},
          tooltip:{
            backgroundColor: '#0F9D8A',
            padding: 10,
            cornerRadius: 8,
          }
        }
      }
    });
  }

  // Alpine helpers: privacy
  function applyPrivacy(on){
    document.querySelectorAll('.data-guard').forEach(el=>{
      el.classList.toggle('blurred', !!on);
      el.classList.toggle('revealed', !on);
    });
  }
  window.dashboard = () => ({
    privacyOn: localStorage.getItem('psy_privacy') === '1',
    init(){ applyPrivacy(this.privacyOn); },
    togglePrivacy(){ 
      this.privacyOn = !this.privacyOn; 
      localStorage.setItem('psy_privacy', this.privacyOn ? '1' : '0'); 
      applyPrivacy(this.privacyOn); 
    }
  });
</script>
@endpush
