@extends('layouts.app')
@section('title','Forum Tanya-Jawab')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
        Komunitas
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Forum Tanya-Jawab</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('app.qa-threads.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Diskusi Baru
      </a>
    </div>
  </div>

  {{-- Filter Form --}}
  <form method="GET" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
      <div class="sm:col-span-2 relative">
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Diskusi</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input name="q" value="{{ request('q') }}" class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-500" placeholder="Ketik kata kunci...">
        </div>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status</label>
        <div class="relative">
          <select name="status" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
            <option value="">Semua Status</option>
            @foreach(['open'=>'Aktif (Open)','resolved'=>'Selesai (Resolved)','closed'=>'Ditutup (Closed)'] as $k=>$v)
              <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
            @endforeach
          </select>
          <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>
      
      <div>
        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Kepemilikan</label>
        <div class="relative">
          <select name="mine" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
            <option value="">Semua Diskusi</option>
            <option value="1" @selected(request('mine'))>Diskusi Saya</option>
          </select>
          <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex flex-col sm:flex-row items-center gap-3 pt-6 border-t border-gray-50">
      <button class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors">Terapkan Filter</button>
      <a href="{{ route('app.qa-threads.index') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">Reset</a>
    </div>
  </form>

  {{-- Thread List --}}
  <div class="space-y-4">
    @forelse($threads as $t)
      <a href="{{ route('app.qa-threads.show',$t) }}" class="block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-6 group">
        <div class="flex flex-col md:flex-row gap-6">
          <div class="flex-1 min-w-0">
            <div class="flex items-start gap-3 mb-2">
              <h2 class="font-bold text-lg text-text-main group-hover:text-tosca transition-colors line-clamp-2">
                {{ $t->title }}
              </h2>
              @if($t->status === 'resolved')
                <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider border bg-green-50 text-green-700 border-green-200 mt-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  Selesai
                </span>
              @elseif($t->status === 'closed')
                <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider border bg-gray-100 text-gray-700 border-gray-200 mt-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                  Ditutup
                </span>
              @else
                <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider border bg-amber-50 text-amber-700 border-amber-200 mt-0.5">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                  Aktif
                </span>
              @endif
            </div>
            
            @if($t->body)
              <p class="text-sm text-text-soft line-clamp-2 mb-4 leading-relaxed">{{ strip_tags($t->body) }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-text-soft">
              <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 rounded-full bg-tosca text-white flex items-center justify-center text-[10px] font-bold uppercase">
                  {{ substr($t->user?->name ?? 'U', 0, 1) }}
                </div>
                <span class="text-text-main font-bold">{{ $t->user?->name ?? 'User' }}</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $t->created_at?->diffForHumans() }}
              </div>
              
              @if($t->course)
                <div class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  <span class="truncate max-w-[150px]">{{ $t->course->title }}</span>
                </div>
              @endif
              @if($t->lesson)
                <div class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span class="truncate max-w-[150px]">{{ $t->lesson->title }}</span>
                </div>
              @endif
            </div>
          </div>
          
          <div class="shrink-0 md:w-24 flex md:flex-col items-center md:items-end justify-between md:justify-center border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
            <div class="flex flex-col items-center">
              <span class="text-xl font-extrabold text-tosca-dark">{{ $t->replies_count }}</span>
              <span class="text-[10px] font-bold text-text-soft uppercase tracking-wider">Balasan</span>
            </div>
            <div class="md:hidden">
              <span class="text-tosca text-sm font-bold flex items-center gap-1">
                Buka
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </span>
            </div>
          </div>
        </div>
      </a>
    @empty
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-12 text-center flex flex-col items-center justify-center">
        <div class="w-24 h-24 rounded-full bg-softbg flex items-center justify-center mb-6">
          <svg class="w-12 h-12 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
        </div>
        <h3 class="text-2xl font-bold text-text-main mb-2">Belum Ada Diskusi</h3>
        <p class="text-text-soft mb-8 max-w-md">Jadilah yang pertama untuk memulai diskusi! Tanyakan pertanyaan Anda atau bagikan pengetahuan dengan komunitas.</p>
        <a href="{{ route('app.qa-threads.create') }}" class="px-8 py-3.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
          Buat Diskusi Baru
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
      </div>
    @endforelse
  </div>

  <div class="flex justify-center">
    {{ $threads->links() }}
  </div>
</div>
@endsection
