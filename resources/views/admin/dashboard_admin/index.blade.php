@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-main flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-tosca-light flex items-center justify-center text-tosca-dark">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                Overview
            </h1>
            <p class="text-sm text-text-soft mt-1">Platform metrics and recent activities at a glance.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded-xl border border-gray-100 bg-white text-sm font-medium hover:border-tosca/30 hover:text-tosca transition-colors shadow-sm">Payments</a>
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded-xl border border-gray-100 bg-white text-sm font-medium hover:border-tosca/30 hover:text-tosca transition-colors shadow-sm">Courses</a>
            <a href="{{ route('admin.memberships.index') }}" class="px-4 py-2 rounded-xl bg-tosca text-white text-sm font-medium hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">Memberships</a>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4">
        @php
        $statCards = [
            ['label'=>'Users', 'value'=>$stats['users'] ?? 0, 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'Courses', 'value'=>$stats['courses'] ?? 0, 'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label'=>'Modules', 'value'=>$stats['modules'] ?? 0, 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
            ['label'=>'Lessons', 'value'=>$stats['lessons'] ?? 0, 'icon'=>'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Quizzes', 'value'=>$stats['quizzes'] ?? 0, 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label'=>'Plans', 'value'=>$stats['plans'] ?? 0, 'icon'=>'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
            ['label'=>'Members', 'value'=>$stats['memberships_active'] ?? 0, 'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label'=>'Enrolls', 'value'=>$stats['enrollments_active'] ?? 0, 'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        ];
        @endphp
        @foreach($statCards as $c)
        <div class="bg-white rounded-2xl p-4 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50 flex flex-col justify-between hover:border-tosca/20 hover:shadow-tosca/5 transition-all">
            <div class="flex items-start justify-between">
                <div class="text-xs font-medium text-text-soft uppercase tracking-wider">{{ $c['label'] }}</div>
                <div class="w-7 h-7 rounded-md bg-tosca-light flex items-center justify-center text-tosca">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"></path></svg>
                </div>
            </div>
            <div class="mt-2 text-2xl font-bold text-text-main">{{ number_format($c['value']) }}</div>
        </div>
        @endforeach
    </div>

    {{-- REVENUE & QUICK ACTIONS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue Card -->
        <div class="bg-gradient-to-br from-tosca to-tosca-dark rounded-3xl p-6 text-white shadow-lg shadow-tosca/20 lg:col-span-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="text-tosca-light font-medium text-sm">Revenue (This Month)</div>
            <div class="mt-2 text-4xl font-extrabold tracking-tight">Rp {{ number_format((int)($stats['revenue_month'] ?? 0),0,',','.') }}</div>
            <div class="mt-6">
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 text-sm font-medium bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                    View Payments <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50 flex flex-col justify-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-sm font-medium text-text-soft">Pending Payments</div>
            </div>
            <div class="mt-4 text-3xl font-bold text-text-main">{{ number_format($stats['payments_pending'] ?? 0) }}</div>
            <div class="mt-4">
                <a href="{{ route('admin.payments.index',['status'=>'pending']) }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">Review pending &rarr;</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50">
            <div class="text-sm font-semibold text-text-main mb-4">Quick Actions</div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.courses.create') }}" class="flex flex-col items-center justify-center gap-2 py-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-text-main">New Course</span>
                </a>
                <a href="{{ route('admin.plans.create') }}" class="flex flex-col items-center justify-center gap-2 py-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-text-main">New Plan</span>
                </a>
                <a href="{{ route('admin.quizzes.create') }}" class="flex flex-col items-center justify-center gap-2 py-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-text-main">New Quiz</span>
                </a>
                <a href="{{ route('admin.coupons.create') }}" class="flex flex-col items-center justify-center gap-2 py-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-text-main">New Coupon</span>
                </a>
            </div>
        </div>
    </div>

    {{-- LISTS: Payments & Enrollments --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Payments -->
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-main">Recent Payments</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-tosca hover:text-tosca-dark">View All</a>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-text-soft bg-gray-50 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 font-medium">User & Item</th>
                            <th class="px-6 py-3 font-medium">Amount</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentPayments as $p)
                            @php
                                $item = $p->plan?->name ? $p->plan->name : ($p->course?->title ?? 'Unknown Item');
                            @endphp
                            <tr class="hover:bg-softbg/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-text-main">{{ $p->user?->name ?? 'Deleted User' }}</div>
                                    <div class="text-xs text-text-soft mt-0.5">{{ $item }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-text-main whitespace-nowrap">
                                    Rp {{ number_format((float)$p->amount,0,',','.') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($p->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-100">Paid</span>
                                    @elseif($p->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-100">{{ ucfirst($p->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-text-soft text-sm">No recent payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-text-main">Recent Enrollments</h3>
                <a href="{{ route('admin.enrollments.index') }}" class="text-sm font-medium text-tosca hover:text-tosca-dark">View All</a>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-text-soft bg-gray-50 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 font-medium">User</th>
                            <th class="px-6 py-3 font-medium">Course</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentEnrolls as $e)
                            <tr class="hover:bg-softbg/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-text-main">{{ $e->user?->name ?? 'Deleted User' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-text-main truncate max-w-[150px]" title="{{ $e->course?->title }}">{{ $e->course?->title ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($e->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-tosca-light text-tosca-dark border border-tosca/20">Active</span>
                                    @elseif($e->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-text-soft text-sm">No recent enrollments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- CHARTS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-text-main">Revenue Chart</h3>
                <div class="flex gap-2">
                    <select id="revRange" class="text-xs border-gray-200 rounded-lg px-2 py-1 text-text-main focus:ring-tosca focus:border-tosca">
                        <option value="12" selected>12 Months</option>
                        <option value="6">6 Months</option>
                    </select>
                </div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <h3 class="text-lg font-bold text-text-main mb-4">Enrollments (Last 14 Days)</h3>
            <div class="relative h-64 w-full">
                <canvas id="chartEnrollments"></canvas>
            </div>
        </div>

    </div>

</div>

{{-- Chart.js CDN + init --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const REV   = @json($revenueMonthly ?? ['labels'=>[],'data'=>[]]);
    const ENRL  = @json($enrollmentsDaily ?? ['labels'=>[],'data'=>[]]);

    // Tosca Enterprise Colors
    const TOSCA = '#0F9D8A';
    const TOSCA_LIGHT = 'rgba(15, 157, 138, 0.15)';
    const GRAY = '#F3F4F6';
    const GRAY_DARK = '#9CA3AF';

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // Revenue Line Chart
    let revenueChart;
    function buildRevenueChart(months=12) {
        const labels = REV.labels.slice(-months);
        const data   = REV.data.slice(-months);
        const ctx = document.getElementById('chartRevenue').getContext('2d');

        if (revenueChart) revenueChart.destroy();
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data,
                    borderColor: TOSCA,
                    backgroundColor: TOSCA_LIGHT,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: TOSCA,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [4, 4], color: GRAY },
                        ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v/1000) + 'K', color: GRAY_DARK }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: GRAY_DARK, maxTicksLimit: months }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: '#1F2937',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });
    }

    // Enrollments Bar Chart
    const enrollCtx = document.getElementById('chartEnrollments').getContext('2d');
    new Chart(enrollCtx, {
        type: 'bar',
        data: {
            labels: ENRL.labels,
            datasets: [{
                data: ENRL.data,
                backgroundColor: TOSCA,
                borderRadius: 4,
                borderWidth: 0,
                barPercentage: 0.6
            }]
        },
        options: {
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: GRAY },
                    ticks: { color: GRAY_DARK }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: GRAY_DARK, maxTicksLimit: 14 }
                }
            },
            plugins: {
                tooltip: {
                    backgroundColor: '#1F2937',
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });

    document.getElementById('revRange')?.addEventListener('change', (e) => {
        buildRevenueChart(parseInt(e.target.value, 10));
    });

    buildRevenueChart(12);
</script>
@endsection
