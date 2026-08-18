@extends('layouts.admin')
@section('title', 'Detail Thread Q&A — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                Diskusi #{{ $thread->id }}
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Topik Diskusi</h1>
            <p class="text-sm text-text-soft mt-1">Lihat diskusi dan kelola status jawaban terkait course.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.qa-threads.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            @if(Route::has('admin.qa-threads.edit'))
            <a href="{{ route('admin.qa-threads.edit', $thread) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Thread
            </a>
            @endif
        </div>
    </div>

    @if (session('ok') || session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex gap-3 text-sm text-green-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">{{ session('ok') ?? session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Main Post --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-8">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-text-main leading-tight mb-2">{{ $thread->title }}</h2>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-tosca-light flex items-center justify-center text-tosca-dark font-bold text-xs shrink-0 border border-tosca/20">
                                {{ strtoupper(substr($thread->user?->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="text-text-soft font-medium">Oleh <span class="text-gray-700 font-bold">{{ $thread->user->name ?? 'Pengguna Tidak Dikenal' }}</span></span>
                        </div>
                        <span class="text-gray-300">•</span>
                        <span class="text-gray-500 font-medium">{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                
                @php
                    $badge = match($thread->status) {
                        'open'     => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Open', 'bg-amber-500'],
                        'resolved' => ['bg-green-50', 'text-green-700', 'border-green-200', 'Resolved', 'bg-green-500'],
                        'closed'   => ['bg-gray-100', 'text-gray-700', 'border-gray-200', 'Closed', 'bg-gray-500'],
                        default    => ['bg-gray-50', 'text-gray-700', 'border-gray-200', ucfirst($thread->status), 'bg-gray-400'],
                    };
                @endphp
                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider border {{ $badge[0] }} {{ $badge[1] }} {{ $badge[2] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[4] }}"></span>
                    {{ $badge[3] }}
                </span>
            </div>

            <div class="prose max-w-none text-gray-700 bg-gray-50/50 rounded-2xl p-6 border border-gray-100">
                {!! nl2br(e($thread->body)) !!}
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @if($thread->course)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Course: {{ $thread->course->title }}
                    </div>
                @endif
                @if($thread->lesson)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Lesson: {{ $thread->lesson->title }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Replies --}}
    <div class="space-y-6">
        <h2 class="text-xl font-extrabold text-text-main flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Jawaban & Balasan ({{ $thread->replies->count() }})
        </h2>

        <div class="space-y-4">
            @forelse($thread->replies as $reply)
                <div class="bg-white border {{ $reply->is_answer ? 'border-green-200 shadow-md shadow-green-100/50' : 'border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.02)]' }} rounded-2xl overflow-hidden transition-all hover:border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3 text-sm">
                                <div class="w-8 h-8 rounded-full {{ $reply->is_answer ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200' }} flex items-center justify-center font-bold text-sm shrink-0 border">
                                    {{ strtoupper(substr($reply->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $reply->user->name ?? 'Pengguna Tidak Dikenal' }}</div>
                                    <div class="text-xs text-gray-500 font-medium">{{ $reply->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            
                            {{-- Form Tandai Jawaban --}}
                            @if(Route::has('admin.qa-replies.answer'))
                            <form method="POST" action="{{ route('admin.qa-replies.answer', $reply) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border transition-colors
                                           {{ $reply->is_answer 
                                                ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' 
                                                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900' }}">
                                    @if($reply->is_answer)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Jawaban Terpilih
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tandai Jawaban
                                    @endif
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="prose max-w-none text-gray-700 text-sm">
                            {!! nl2br(e($reply->body)) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 border border-dashed border-gray-200 rounded-3xl p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-sm">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Balasan</h3>
                    <p class="text-sm text-gray-500">Belum ada pengguna yang membalas atau menjawab topik ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
