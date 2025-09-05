@extends('layouts.admin')

@section('title','Admin Dashboard — BERKEMAH')

@section('content')
<div class="space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7 opacity-80" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 3h18v4H3V3Zm0 7h18v4H3v-4Zm0 7h18v4H3v-4Z"/>
        </svg>
        Dashboard
      </h1>
      <p class="text-sm opacity-70">Ringkasan metrik & aktivitas terbaru.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.payments.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">Payments</a>
      <a href="{{ route('admin.courses.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">Courses</a>
      <a href="{{ route('admin.memberships.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">Memberships</a>
    </div>
  </div>

  {{-- STATS GRID --}}
  <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
    @php
      $statCards = [
        ['label'=>'Users','value'=>$stats['users'] ?? 0,'icon'=>'M5 4h14v16H5z'],
        ['label'=>'Courses','value'=>$stats['courses'] ?? 0,'icon'=>'M4 6h16v4H4zM4 12h10v6H4z'],
        ['label'=>'Modules','value'=>$stats['modules'] ?? 0,'icon'=>'M4 4h7v7H4zM13 4h7v7h-7zM4 13h16v7H4z'],
        ['label'=>'Lessons','value'=>$stats['lessons'] ?? 0,'icon'=>'M4 5h16v14H4z'],
        ['label'=>'Quizzes','value'=>$stats['quizzes'] ?? 0,'icon'=>'M3 4h18v4H3zM3 10h18v10H3z'],
        ['label'=>'Plans','value'=>$stats['plans'] ?? 0,'icon'=>'M12 2 3 7v10l9 5 9-5V7z'],
        ['label'=>'Active Members','value'=>$stats['memberships_active'] ?? 0,'icon'=>'M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0z'],
        ['label'=>'Active Enrolls','value'=>$stats['enrollments_active'] ?? 0,'icon'=>'M4 6h16v12H4z'],
      ];
    @endphp
    @foreach($statCards as $c)
      <div class="rounded-2xl border bg-white p-4">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs opacity-60">{{ $c['label'] }}</div>
            <div class="text-2xl font-extrabold mt-1">{{ number_format($c['value']) }}</div>
          </div>
          <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="currentColor">
            <path d="{{ $c['icon'] }}"/>
          </svg>
        </div>
      </div>
    @endforeach
  </div>

  {{-- REVENUE & PENDING --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
    <div class="rounded-2xl border bg-white p-4">
      <div class="text-xs opacity-60">Revenue (this month)</div>
      <div class="mt-1 text-3xl font-extrabold">Rp {{ number_format((int)($stats['revenue_month'] ?? 0),0,',','.') }}</div>
      <a href="{{ route('admin.payments.index') }}" class="mt-3 inline-flex text-sm text-blue-600 hover:underline">View payments</a>
    </div>
    <div class="rounded-2xl border bg-white p-4">
      <div class="text-xs opacity-60">Payments Pending</div>
      <div class="mt-1 text-3xl font-extrabold">{{ number_format($stats['payments_pending'] ?? 0) }}</div>
      <a href="{{ route('admin.payments.index',['status'=>'pending']) }}" class="mt-3 inline-flex text-sm text-blue-600 hover:underline">Review pending</a>
    </div>
    <div class="rounded-2xl border bg-white p-4">
      <div class="text-xs opacity-60">Quick Actions</div>
      <div class="mt-2 flex flex-wrap gap-2">
        <a href="{{ route('admin.courses.create') }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-sm">+ New Course</a>
        <a href="{{ route('admin.plans.create') }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-sm">+ New Plan</a>
        <a href="{{ route('admin.quizzes.create') }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-sm">+ New Quiz</a>
        <a href="{{ route('admin.coupons.create') }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 text-sm">+ New Coupon</a>
      </div>
    </div>
  </div>

  {{-- TWO COLUMNS: Recent Payments & Enrollments --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- RECENT PAYMENTS --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
        <div class="text-sm font-semibold">Recent Payments</div>
        <a href="{{ route('admin.payments.index') }}" class="text-xs text-blue-600 hover:underline">All payments</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="p-3 text-left">User</th>
              <th class="p-3 text-left">Item</th>
              <th class="p-3 text-left">Amount</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Paid At</th>
            </tr>
          </thead>
          <tbody class="[&>tr:hover]:bg-gray-50">
            @forelse($recentPayments as $p)
              @php
                $paidAt = $p->paid_at ? \Illuminate\Support\Carbon::parse($p->paid_at)->timezone(config('app.timezone','UTC'))->format('Y-m-d H:i') : '—';
                $item = $p->plan?->name ? ('Plan: '.$p->plan->name) : ($p->course?->title ? ('Course: '.$p->course->title) : '—');
              @endphp
              <tr class="border-t">
                <td class="p-3">
                  <div class="font-medium">{{ $p->user?->name ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $p->user?->email ?? '—' }}</div>
                </td>
                <td class="p-3">{{ $item }}</td>
                <td class="p-3">Rp {{ number_format((float)$p->amount,0,',','.') }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full text-xs
                    @if($p->status==='paid') bg-green-100 text-green-800
                    @elseif($p->status==='pending') bg-amber-100 text-amber-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($p->status) }}
                  </span>
                </td>
                <td class="p-3">{{ $paidAt }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="p-6 text-center text-gray-500">No records.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- RECENT ENROLLMENTS --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
        <div class="text-sm font-semibold">Recent Enrollments</div>
        <a href="{{ route('admin.enrollments.index') }}" class="text-xs text-blue-600 hover:underline">All enrollments</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="p-3 text-left">User</th>
              <th class="p-3 text-left">Course</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Activated</th>
            </tr>
          </thead>
          <tbody class="[&>tr:hover]:bg-gray-50">
            @forelse($recentEnrolls as $e)
              @php
                $activated = $e->activated_at
                  ? \Illuminate\Support\Carbon::parse($e->activated_at)->timezone(config('app.timezone','UTC'))->format('Y-m-d H:i')
                  : '—';
              @endphp
              <tr class="border-t">
                <td class="p-3">
                  <div class="font-medium">{{ $e->user?->name ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $e->user?->email ?? '—' }}</div>
                </td>
                <td class="p-3">{{ $e->course?->title ?? '—' }}</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full text-xs
                    @if($e->status==='active') bg-green-100 text-green-800
                    @elseif($e->status==='pending') bg-amber-100 text-amber-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($e->status ?? 'inactive') }}
                  </span>
                </td>
                <td class="p-3">{{ $activated }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="p-6 text-center text-gray-500">No records.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ======== CHARTS (CUSTOMIZABLE) ======== --}}
  <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
    {{-- Revenue Chart --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
        <div class="text-sm font-semibold">Revenue (12 months)</div>
        <div class="flex items-center gap-2">
          <select id="revRange" class="text-xs border rounded-lg px-2 py-1">
            <option value="12" selected>12m</option>
            <option value="6">6m</option>
          </select>
          <select id="revType" class="text-xs border rounded-lg px-2 py-1">
            <option value="line" selected>Line</option>
            <option value="bar">Bar</option>
          </select>
        </div>
      </div>
      <div class="p-4">
        <canvas id="chartRevenue" height="120"></canvas>
      </div>
    </div>

    {{-- Payments Status (Donut) --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold">Payments · Status</div>
      <div class="p-4">
        <canvas id="chartPayStatus" height="120"></canvas>
      </div>
    </div>

    {{-- Enrollments (14 days) --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold">Enrollments (14 days)</div>
      <div class="p-4">
        <canvas id="chartEnrollments" height="120"></canvas>
      </div>
    </div>

    {{-- Providers (Top) / Members by Plan --}}
    <div class="rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold">Payments · Providers (Top)</div>
      <div class="p-4">
        <canvas id="chartProviders" height="120"></canvas>
      </div>
    </div>

    <div class="xl:col-span-2 rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold">Active Members per Plan</div>
      <div class="p-4">
        <canvas id="chartMembersPlan" height="120"></canvas>
      </div>
    </div>
  </div>

</div>

{{-- Chart.js CDN + init --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data dari controller
  const REV   = @json($revenueMonthly ?? ['labels'=>[],'data'=>[]]);
  const PSTAT = @json($paymentsStatus ?? ['labels'=>[],'data'=>[]]);
  const PPROV = @json($paymentsProviders ?? ['labels'=>[],'data'=>[]]);
  const ENRL  = @json($enrollmentsDaily ?? ['labels'=>[],'data'=>[]]);
  const MPLAN = @json($membershipsByPlan ?? ['labels'=>[],'data'=>[]]);

  // Palet warna (tailwind-ish)
  const C = {
    blue:  'rgba(37, 99, 235, 1)',
    blueA: 'rgba(37, 99, 235, .15)',
    green: 'rgba(22, 163, 74, 1)',
    greenA:'rgba(22, 163, 74, .15)',
    amber: 'rgba(245, 158, 11, 1)',
    amberA:'rgba(245, 158, 11, .15)',
    red:   'rgba(239, 68, 68, 1)',
    redA:  'rgba(239, 68, 68, .15)',
    slate: 'rgba(100, 116, 139, 1)',
    slateA:'rgba(100, 116, 139, .15)',
    indigo:'rgba(79, 70, 229, 1)',
    indigoA:'rgba(79, 70, 229, .15)',
  };

  Chart.defaults.font.family = 'ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial';
  Chart.defaults.plugins.legend.position = 'bottom';
  Chart.defaults.plugins.tooltip.mode = 'index';
  Chart.defaults.plugins.tooltip.intersect = false;
  Chart.defaults.responsive = true;
  Chart.defaults.maintainAspectRatio = false;

  // Revenue (customizable)
  let revenueChart;
  const elRevenue = document.getElementById('chartRevenue').getContext('2d');

  function buildRevenueChart(type='line', months=12) {
    const labels = REV.labels.slice(-months);
    const data   = REV.data.slice(-months);

    const cfg = {
      type,
      data: {
        labels,
        datasets: [{
          label: 'Revenue',
          data,
          borderColor: C.blue,
          backgroundColor: type==='line' ? C.blueA : C.blue,
          fill: type==='line',
          tension: .3,
          borderWidth: 2
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
          x: { ticks: { autoSkip: true, maxTicksLimit: months } }
        }
      }
    };

    if (revenueChart) { revenueChart.destroy(); }
    revenueChart = new Chart(elRevenue, cfg);
  }

  // Payments Status (Donut)
  const payStatusChart = new Chart(
    document.getElementById('chartPayStatus').getContext('2d'),
    {
      type: 'doughnut',
      data: {
        labels: PSTAT.labels,
        datasets: [{
          data: PSTAT.data,
          backgroundColor: [C.green, C.amber, C.red],
          borderWidth: 0
        }]
      },
      options: { cutout: '65%' }
    }
  );

  // Enrollments (14 days)
  const enrollChart = new Chart(
    document.getElementById('chartEnrollments').getContext('2d'),
    {
      type: 'bar',
      data: {
        labels: ENRL.labels,
        datasets: [{
          label: 'Enrollments',
          data: ENRL.data,
          backgroundColor: C.indigo,
          borderWidth: 0
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true },
          x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 14 } }
        }
      }
    }
  );

  // Providers (Top) - horizontal bar
  const providersChart = new Chart(
    document.getElementById('chartProviders').getContext('2d'),
    {
      type: 'bar',
      data: {
        labels: PPROV.labels,
        datasets: [{
          label: 'Count',
          data: PPROV.data,
          backgroundColor: C.slate,
          borderWidth: 0
        }]
      },
      options: {
        indexAxis: 'y',
        scales: { x: { beginAtZero: true } }
      }
    }
  );

  // Members per Plan
  const membersPlanChart = new Chart(
    document.getElementById('chartMembersPlan').getContext('2d'),
    {
      type: 'bar',
      data: {
        labels: MPLAN.labels,
        datasets: [{
          label: 'Active Members',
          data: MPLAN.data,
          backgroundColor: C.green,
          borderWidth: 0
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    }
  );

  // Controls for Revenue
  const revType  = document.getElementById('revType');
  const revRange = document.getElementById('revRange');

  revType?.addEventListener('change', () => buildRevenueChart(revType.value, parseInt(revRange.value,10)));
  revRange?.addEventListener('change', () => buildRevenueChart(revType.value, parseInt(revRange.value,10)));

  // Initial build
  buildRevenueChart('line', 12);
</script>
@endsection
