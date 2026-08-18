@extends('layouts.app')

@section('title', $course->title)

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\{Enrollment, Membership};

    $uid = Auth::id();
    $enr = Enrollment::where('user_id', $uid)->where('course_id', $course->id)->first();
    
    $hasMembership = isset($hasMembership)
        ? (bool) $hasMembership
        : Membership::where('user_id', $uid)->where('status', 'active')
            ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->exists();

    $effectiveAccess = false;
    $lockedByMembership = false;

    if ($enr && $enr->status === 'active') {
        if (in_array($enr->access_via, ['purchase','free'], true)) {
            $effectiveAccess = true;
        } elseif ($enr->access_via === 'membership') {
            $notExpired = is_null($enr->access_expires_at) || now()->lt($enr->access_expires_at);
            $effectiveAccess = ($hasMembership && $notExpired);
            $lockedByMembership = !$effectiveAccess;
        } else {
            $effectiveAccess = true;
        }
    }

    $cover = $course->cover_url ?: 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=1200&auto=format&fit=crop';
@endphp

<div class="max-w-6xl mx-auto space-y-8" x-data="{ activeModule: 0 }">
    
    {{-- ALERT: LOCKED MEMBERSHIP --}}
    @if($lockedByMembership)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h4 class="text-amber-800 font-bold">Access Locked</h4>
                <p class="text-sm text-amber-700 mt-1">
                    Your access to this course is locked because your membership has expired. You can either purchase this course individually or renew your membership to regain access.
                </p>
            </div>
        </div>
    @endif

    {{-- MAIN CONTENT GRID --}}
    <div class="flex flex-col lg:flex-row gap-8">
        
        {{-- LEFT COLUMN: Details & Curriculum --}}
        <div class="flex-1 space-y-8">
            
            {{-- HERO BANNER --}}
            <div class="relative w-full h-64 md:h-80 rounded-3xl overflow-hidden bg-gray-100 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
                <img src="{{ $cover }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-6 md:p-8">
                    <div class="flex gap-2 mb-3">
                        <span class="px-3 py-1 bg-tosca/90 text-white text-xs font-semibold rounded-lg backdrop-blur-sm">Course</span>
                        @if($effectiveAccess)
                            <span class="px-3 py-1 bg-green-500/90 text-white text-xs font-semibold rounded-lg backdrop-blur-sm">Enrolled</span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight drop-shadow-md">
                        {{ $course->title }}
                    </h1>
                </div>
            </div>

            {{-- ABOUT COURSE --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
                <h2 class="text-xl font-bold text-text-main mb-4">About this Course</h2>
                <div class="prose prose-tosca max-w-none text-text-soft">
                    {!! nl2br(e($course->description)) !!}
                </div>
            </div>

            {{-- CURRICULUM ACCORDION --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-text-main">Curriculum</h2>
                    <span class="text-sm font-medium text-text-soft bg-gray-100 px-3 py-1 rounded-full">{{ $course->modules->count() }} Modules</span>
                </div>

                <div class="space-y-4">
                    @forelse($course->modules as $index => $m)
                        <div class="border border-gray-100 rounded-2xl overflow-hidden transition-all duration-300" :class="activeModule === {{ $index }} ? 'border-tosca/30 ring-4 ring-tosca/5 bg-softbg' : 'bg-white hover:border-gray-200'">
                            {{-- Accordion Header --}}
                            <button @click="activeModule = activeModule === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-4 text-left focus:outline-none">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold" :class="activeModule === {{ $index }} ? 'bg-tosca text-white' : 'bg-gray-100 text-gray-500'">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-text-main">{{ $m->title }}</h3>
                                        <p class="text-xs text-text-soft mt-0.5">{{ $m->lessons->count() }} Lessons</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="activeModule === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            {{-- Accordion Body --}}
                            <div x-show="activeModule === {{ $index }}" x-collapse>
                                <div class="px-4 pb-4">
                                    <ul class="mt-2 space-y-2 bg-white rounded-xl border border-gray-50 p-2">
                                        @forelse($m->lessons as $l)
                                            <li>
                                                @if($lockedByMembership)
                                                    <div class="flex items-center justify-between p-3 rounded-lg text-gray-400 bg-gray-50/50">
                                                        <div class="flex items-center gap-3">
                                                            <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                            <span>{{ $l->ordering }}. {{ $l->title }}</span>
                                                        </div>
                                                        <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded bg-gray-200 text-gray-500">Locked</span>
                                                    </div>
                                                @else
                                                    <a href="{{ route('app.lessons.show', $l) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-softbg transition-colors group">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                                                            </div>
                                                            <span class="font-medium text-text-main group-hover:text-tosca transition-colors">{{ $l->ordering }}. {{ $l->title }}</span>
                                                        </div>
                                                        @if($l->is_free)
                                                            <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded bg-tosca-light text-tosca-dark">Free</span>
                                                        @endif
                                                    </a>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="p-3 text-sm text-text-soft text-center">No lessons in this module yet.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-gray-50 rounded-2xl border border-gray-100 text-text-soft">
                            Curriculum is being prepared. Check back later!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Sticky CTA Card --}}
        <div class="w-full lg:w-80 shrink-0">
            <div class="sticky top-24 bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 overflow-hidden relative">
                
                {{-- Decorative background glow --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-tosca-light rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                @if($effectiveAccess)
                    <div class="text-center relative z-10">
                        <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-text-main mb-2">You're Enrolled!</h3>
                        <p class="text-sm text-text-soft mb-6">Ready to jump back into learning?</p>
                        <a href="{{ route('app.my.courses') }}" class="block w-full py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                            Continue Learning
                        </a>
                    </div>
                @else
                    <div class="relative z-10">
                        <div class="mb-6">
                            @if(($course->price ?? 0) > 0)
                                <div class="text-sm text-text-soft uppercase tracking-wider font-semibold mb-1">Price</div>
                                <div class="text-4xl font-extrabold text-text-main tracking-tight">Rp {{ number_format($course->price, 0, ',', '.') }}</div>
                            @else
                                <div class="text-4xl font-extrabold text-tosca tracking-tight">Free</div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @if($hasMembership)
                                <form method="POST" action="{{ route('app.courses.enroll', $course) }}">
                                    @csrf
                                    <button class="w-full py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Enroll via Membership
                                    </button>
                                </form>
                                <p class="text-[11px] text-center text-text-soft mt-2">
                                    Access is tied to your active membership.
                                </p>
                            @else
                                @if(($course->price ?? 0) > 0)
                                    <a href="{{ route('app.courses.checkout', $course) }}" class="flex items-center justify-center w-full py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                                        Buy Now
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('app.courses.enroll', $course) }}">
                                        @csrf
                                        <button class="w-full py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
                                            Enroll for Free
                                        </button>
                                    </form>
                                @endif

                                @if($enr && $enr->access_via === 'membership')
                                    <div class="relative flex items-center py-4">
                                        <div class="flex-grow border-t border-gray-100"></div>
                                        <span class="flex-shrink-0 mx-4 text-xs text-gray-400">or</span>
                                        <div class="flex-grow border-t border-gray-100"></div>
                                    </div>
                                    <a href="{{ route('app.memberships.plans') }}" class="flex items-center justify-center w-full py-3 px-4 bg-white border-2 border-tosca text-tosca font-bold rounded-xl hover:bg-softbg transition-colors">
                                        Renew Membership
                                    </a>
                                @endif
                            @endif
                        </div>

                        <hr class="my-6 border-gray-100">

                        <div class="space-y-4">
                            <h4 class="font-bold text-text-main text-sm">This course includes:</h4>
                            <ul class="space-y-3 text-sm text-text-soft">
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Full lifetime access (if purchased)
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Access on mobile and desktop
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Certificate of completion
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
