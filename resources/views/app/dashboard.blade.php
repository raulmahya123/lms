@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="space-y-8">
    
    {{-- GREETING & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-text-main flex items-center gap-2">
                Hey, {{ $user->name }} 👋
            </h1>
            <p class="text-text-soft mt-2">Welcome back! Here's a summary of your learning journey.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('app.courses.index') }}" class="px-5 py-2.5 rounded-xl bg-tosca text-white text-sm font-medium hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                Explore Courses
            </a>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50 flex items-center gap-5 hover:border-tosca/20 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <div class="text-sm font-medium text-text-soft">My Courses</div>
                <div class="text-3xl font-bold text-text-main mt-1">{{ (int)($stats['courses_count'] ?? 0) }}</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50 flex items-center gap-5 hover:border-tosca/20 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <div class="text-sm font-medium text-text-soft">Active Membership</div>
                <div class="text-xl font-bold text-text-main mt-1">
                    {{ optional(optional($stats['active_membership'] ?? null)->plan)->name ?? 'None' }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-gray-50 flex items-center gap-5 hover:border-tosca/20 transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-tosca-light text-tosca flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div>
                <div class="text-sm font-medium text-text-soft">Last Score</div>
                <div class="text-3xl font-bold text-text-main mt-1">
                    {{ optional($stats['last_attempt'] ?? null)->score !== null ? optional($stats['last_attempt'])->score : '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- CONTINUE LEARNING SECTION --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-text-main">Continue Learning</h2>
            <a href="{{ route('app.my.courses') }}" class="text-sm font-medium text-tosca hover:text-tosca-dark">View All Courses &rarr;</a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Course Progress Chart -->
                <div class="relative h-64 w-full">
                    <canvas id="chartProgress"></canvas>
                </div>
                <!-- Enrollments History Chart -->
                <div class="relative h-64 w-full">
                    <canvas id="chartEnroll"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ASSESSMENTS & GRADES --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-text-main">Quiz Score History</h2>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="chartQuiz"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-text-main">Lesson Completion (Monthly)</h2>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="chartCompleteMonthly"></canvas>
            </div>
        </div>
    </div>

</div>

@php
  $CH = $charts ?? [
    'progress'           => ['labels'=>[], 'percent'=>[], 'done'=>[], 'total'=>[]],
    'enroll'             => ['labels'=>[], 'counts'=>[]],
    'quiz'               => [],
    'completion_monthly' => ['labels'=>[], 'counts'=>[]]
  ];
@endphp

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const CHARTS = @json($CH);
    
    // Tosca Enterprise Colors
    const TOSCA = '#0F9D8A';
    const TOSCA_LIGHT = 'rgba(15, 157, 138, 0.15)';
    const GRAY = '#F3F4F6';
    const GRAY_DARK = '#9CA3AF';

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // Progress Chart (Bar)
    if(CHARTS.progress && CHARTS.progress.percent.length > 0) {
        new Chart(document.getElementById('chartProgress').getContext('2d'), {
            type: 'bar',
            data: {
                labels: CHARTS.progress.labels,
                datasets: [{
                    data: CHARTS.progress.percent,
                    backgroundColor: TOSCA,
                    borderRadius: 4,
                    barPercentage: 0.5
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, max: 100, grid: { borderDash: [4, 4], color: GRAY }, ticks: { callback: v => v + '%' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // Enrollments Chart
    if(CHARTS.enroll && CHARTS.enroll.counts.length > 0) {
        new Chart(document.getElementById('chartEnroll').getContext('2d'), {
            type: 'bar',
            data: {
                labels: CHARTS.enroll.labels,
                datasets: [{
                    data: CHARTS.enroll.counts,
                    backgroundColor: TOSCA_LIGHT,
                    borderColor: TOSCA,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: GRAY } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Quiz Chart (Line)
    if(Array.isArray(CHARTS.quiz) && CHARTS.quiz.length > 0) {
        new Chart(document.getElementById('chartQuiz').getContext('2d'), {
            type: 'line',
            data: {
                labels: CHARTS.quiz.map(p => p.t),
                datasets: [{
                    data: CHARTS.quiz.map(p => p.y),
                    borderColor: TOSCA,
                    backgroundColor: TOSCA_LIGHT,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { borderDash: [4, 4], color: GRAY } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Completion Monthly Chart (Line)
    if(CHARTS.completion_monthly && CHARTS.completion_monthly.counts.length > 0) {
        new Chart(document.getElementById('chartCompleteMonthly').getContext('2d'), {
            type: 'line',
            data: {
                labels: CHARTS.completion_monthly.labels,
                datasets: [{
                    data: CHARTS.completion_monthly.counts,
                    borderColor: TOSCA,
                    backgroundColor: TOSCA_LIGHT,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: GRAY } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endpush
@endsection
