@extends('layouts.app')

@section('title', 'Explore Courses')

@section('content')
@php
  use Illuminate\Support\Facades\Auth;
  use App\Models\Enrollment;

  $q = request('q');

  // daftar course yang sudah user miliki
  $myIds = Auth::check() 
      ? Enrollment::where('user_id', Auth::id())->pluck('course_id')->all()
      : [];
@endphp

<div class="space-y-8">

    {{-- HEADER & SEARCH --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-text-main flex items-center gap-2">
                Explore Courses
            </h1>
            <p class="text-text-soft mt-2">Discover new skills and level up your career.</p>
        </div>
        
        <div class="w-full md:w-96">
            <form method="GET" action="{{ route('app.courses.index') }}" class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="q" value="{{ $q }}" placeholder="Search courses..." 
                    class="block w-full pl-10 pr-20 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm focus:ring-2 focus:ring-tosca focus:border-tosca transition-shadow text-text-main">
                <button type="submit" class="absolute inset-y-1.5 right-1.5 px-4 bg-tosca text-white text-sm font-medium rounded-xl hover:bg-tosca-dark transition-colors shadow-sm">
                    Search
                </button>
            </form>
            @if($q)
                <div class="mt-2 text-sm text-text-soft flex items-center justify-between">
                    <span>Showing results for "<strong>{{ $q }}</strong>"</span>
                    <a href="{{ route('app.courses.index') }}" class="text-tosca hover:underline">Clear search</a>
                </div>
            @endif
        </div>
    </div>

    {{-- COURSE GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($courses as $c)
            @php
                $img = $c->cover_url ?: 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=600&auto=format&fit=crop';
                $isEnrolled = in_array($c->id, $myIds);
            @endphp

            <a href="{{ route('app.courses.show', $c) }}" class="group bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden hover:shadow-tosca/10 hover:border-tosca/20 transition-all flex flex-col h-full">
                
                {{-- COVER IMAGE --}}
                <div class="relative h-48 w-full bg-softbg overflow-hidden">
                    <img src="{{ $img }}" alt="{{ $c->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    {{-- OVERLAY GRADIENT --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    @if($isEnrolled)
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-tosca-light text-tosca-dark shadow-sm backdrop-blur-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Enrolled
                            </span>
                        </div>
                    @endif
                </div>

                {{-- CARD BODY --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="text-lg font-bold text-text-main leading-tight group-hover:text-tosca transition-colors line-clamp-2 mb-2">
                        {{ $c->title }}
                    </h3>
                    
                    @if($c->short_description)
                        <p class="text-sm text-text-soft line-clamp-2 mb-4">
                            {{ $c->short_description }}
                        </p>
                    @endif

                    <div class="mt-auto">
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-text-soft bg-gray-50 px-2 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                {{ $c->modules_count }} Modules
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-text-soft bg-gray-50 px-2 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ $c->enrollments_count }} Enrolled
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center bg-white rounded-3xl border border-gray-50 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-text-main">No courses found</h3>
                <p class="text-sm text-text-soft mt-1">Try adjusting your search query.</p>
                @if($q)
                    <a href="{{ route('app.courses.index') }}" class="mt-4 px-5 py-2 rounded-xl bg-tosca-light text-tosca-dark font-medium text-sm hover:bg-tosca hover:text-white transition-colors">
                        Clear Search
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($courses->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $courses->withQueryString()->links('vendor.pagination.tailwind') }}
        </div>
    @endif

</div>
@endsection
