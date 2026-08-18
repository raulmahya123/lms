@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
<div class="space-y-8">
    
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-text-main flex items-center gap-2">
                My Courses
            </h1>
            <p class="text-text-soft mt-2">
                @if(isset($enrollments) && $enrollments->count())
                    You have {{ $enrollments->total() ?? $enrollments->count() }} active courses. Keep up the good work!
                @else
                    You haven't enrolled in any courses yet.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('app.courses.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-100 text-text-main text-sm font-medium hover:border-tosca/30 hover:text-tosca transition-colors shadow-sm">
                Explore Courses
            </a>
        </div>
    </div>

    @if(isset($enrollments) && $enrollments->count())
        {{-- COURSE GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($enrollments as $enr)
                @php
                    $course = $enr->course ?? null;
                    $pct    = (int)($enr->progress_percent ?? 0);
                    $done   = (int)($enr->done_lessons ?? 0);
                    $total  = (int)($enr->total_lessons ?? 0);
                    $status = $enr->status ?? 'active';
                    $cover  = ($course && $course->cover_url)
                                ? $course->cover_url
                                : 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=600&auto=format&fit=crop';
                    $pctClamped = max(0, min(100, $pct));
                @endphp

                <div class="group bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden hover:shadow-tosca/10 hover:border-tosca/20 transition-all flex flex-col h-full">
                    
                    {{-- COVER IMAGE --}}
                    <a href="{{ $course ? route('app.courses.show', $course) : '#' }}" class="relative h-48 w-full bg-softbg overflow-hidden block">
                        <img src="{{ $cover }}" alt="{{ $course->title ?? 'Untitled' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        {{-- BADGES --}}
                        <div class="absolute top-4 left-4 flex gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold shadow-sm backdrop-blur-md 
                                {{ $status === 'active' ? 'bg-tosca-light text-tosca-dark' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($status) }}
                            </span>
                            @if($total > 0 && $pctClamped === 100)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800 shadow-sm backdrop-blur-md">
                                    Completed
                                </span>
                            @endif
                        </div>
                    </a>

                    {{-- CARD BODY --}}
                    <div class="p-5 flex flex-col flex-1">
                        <a href="{{ $course ? route('app.courses.show', $course) : '#' }}" class="block mb-4">
                            <h3 class="text-lg font-bold text-text-main leading-tight group-hover:text-tosca transition-colors line-clamp-2">
                                {{ $course->title ?? 'Untitled Course' }}
                            </h3>
                        </a>

                        <div class="mt-auto">
                            {{-- PROGRESS BAR --}}
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-xs font-medium text-text-soft mb-1.5">
                                    <span>Progress</span>
                                    <span class="text-text-main">{{ $pctClamped }}%</span>
                                </div>
                                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-tosca rounded-full transition-all duration-500" style="width: {{ $pctClamped }}%"></div>
                                </div>
                                <div class="mt-1.5 text-xs text-text-soft">{{ $done }} of {{ $total }} lessons completed</div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <a href="{{ $course ? route('app.courses.show', $course) : '#' }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-tosca hover:text-tosca-dark transition-colors">
                                    Continue Learning
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                                @if(!empty($enr->activated_at))
                                    <span class="text-[11px] text-text-soft">
                                        {{ \Illuminate\Support\Carbon::parse($enr->activated_at)->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if(method_exists($enrollments,'links'))
            <div class="mt-8 flex justify-center">
                {{ $enrollments->withQueryString()->links('vendor.pagination.tailwind') }}
            </div>
        @endif

    @else
        {{-- EMPTY STATE --}}
        <div class="py-16 flex flex-col items-center justify-center bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-20 h-20 rounded-3xl bg-softbg text-tosca flex items-center justify-center mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-text-main">No courses yet</h3>
            <p class="text-sm text-text-soft mt-2 text-center max-w-sm">
                You haven't enrolled in any courses. Start exploring our catalog to find your next skill.
            </p>
            <a href="{{ route('app.courses.index') }}" class="mt-6 px-6 py-3 rounded-xl bg-tosca text-white font-medium hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                Explore Courses
            </a>
        </div>
    @endif

</div>
@endsection
