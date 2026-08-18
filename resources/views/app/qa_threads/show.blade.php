@extends('layouts.app')
@section('title', $thread->title)

@section('content')
@php($me = \Illuminate\Support\Facades\Auth::user())
<div class="max-w-5xl mx-auto space-y-8 pb-12">

  {{-- Header & Breadcrumb --}}
  <div>
    <a href="{{ route('app.qa-threads.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-text-soft hover:text-tosca transition-colors mb-6">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      Kembali ke Forum
    </a>

    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8">
      <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($thread->status === 'resolved')
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Selesai (Resolved)
              </span>
            @elseif($thread->status === 'closed')
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-700 border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ditutup (Closed)
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Aktif (Open)
              </span>
            @endif
          </div>
          
          <h1 class="text-3xl font-extrabold text-text-main leading-tight mb-4">{{ $thread->title }}</h1>
          
          <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-text-soft">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca-dark flex items-center justify-center font-bold">
                {{ substr($thread->user?->name ?? 'U', 0, 1) }}
              </div>
              <span class="text-text-main font-bold">{{ $thread->user?->name ?? 'User' }}</span>
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              {{ $thread->created_at?->format('d M Y, H:i') }}
            </div>
          </div>
        </div>

        @if($me && $me->id === $thread->user_id)
          <div class="shrink-0 flex items-center gap-2">
            <a href="{{ route('app.qa-threads.edit',$thread) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-sm hover:bg-gray-50 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              Edit
            </a>
            <form action="{{ route('app.qa-threads.destroy',$thread) }}" method="POST" class="inline" id="formDeleteThread">
              @csrf @method('DELETE')
              <button type="button" onclick="confirmDeleteThread()" class="px-4 py-2 bg-white border border-red-200 text-red-600 font-bold rounded-lg text-sm hover:bg-red-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus
              </button>
            </form>
          </div>
        @endif
      </div>

      @if($thread->course || $thread->lesson)
        <div class="flex flex-wrap items-center gap-3 py-4 border-t border-b border-gray-50 mb-6 bg-gray-50/50 rounded-xl px-4">
          <span class="text-xs font-bold text-text-soft uppercase tracking-wider">Terkait:</span>
          @if($thread->course)
            <div class="flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-bold text-text-main shadow-sm">
              <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              {{ $thread->course->title }}
            </div>
          @endif
          @if($thread->lesson)
            <div class="flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-bold text-text-main shadow-sm">
              <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              {{ $thread->lesson->title }}
            </div>
          @endif
        </div>
      @endif

      @if($thread->body)
        <div class="prose prose-slate max-w-none text-text-main leading-relaxed">
          {!! nl2br(e($thread->body)) !!}
        </div>
      @endif
    </div>
  </div>

  {{-- Replies Section --}}
  <div>
    <h2 class="text-xl font-bold text-text-main mb-6 flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
      </div>
      Balasan ({{ $thread->replies->count() }})
    </h2>

    <div class="space-y-6 mb-8">
      @forelse($thread->replies as $r)
        <div class="bg-white rounded-2xl border {{ $r->is_answer ? 'border-tosca shadow-sm shadow-tosca/10' : 'border-gray-100 shadow-sm' }} p-6 relative">
          @if($r->is_answer)
            <div class="absolute -top-3 right-6 bg-tosca text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1 shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Jawaban Terbaik
            </div>
          @endif

          <div class="flex flex-col md:flex-row gap-6">
            <div class="shrink-0 flex items-start gap-3">
              <div class="w-10 h-10 rounded-full {{ $r->is_answer ? 'bg-tosca text-white' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center font-bold text-lg">
                {{ substr($r->user?->name ?? 'U', 0, 1) }}
              </div>
              <div class="md:hidden">
                <div class="font-bold text-text-main">{{ $r->user?->name ?? 'User' }}</div>
                <div class="text-xs text-text-soft">{{ $r->created_at?->diffForHumans() }}</div>
              </div>
            </div>
            
            <div class="flex-1 min-w-0">
              <div class="hidden md:block mb-3">
                <div class="font-bold text-text-main text-lg">{{ $r->user?->name ?? 'User' }}</div>
                <div class="text-sm text-text-soft">{{ $r->created_at?->diffForHumans() }}</div>
              </div>
              
              <div class="prose prose-sm max-w-none text-text-main leading-relaxed mb-4">
                {!! nl2br(e($r->body)) !!}
              </div>

              <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                @if(!$r->is_answer && $me && $me->id === $thread->user_id)
                  <form method="POST" action="{{ route('app.qa-replies.answer',$r) }}" class="inline" id="formMarkAnswer-{{ $r->id }}">
                    @csrf @method('PATCH')
                    <button type="button" onclick="confirmMarkAnswer('{{ $r->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 font-bold text-xs transition-colors border border-green-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                      Tandai Sebagai Jawaban
                    </button>
                  </form>
                @endif

                @if($me && $me->id === $r->user_id)
                  <form method="POST" action="{{ route('app.qa-replies.destroy',$r) }}" class="inline" id="formDeleteReply-{{ $r->id }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDeleteReply('{{ $r->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      Hapus
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-8 text-center border-dashed">
          <div class="w-16 h-16 rounded-full bg-white mx-auto flex items-center justify-center mb-4 shadow-sm">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
          </div>
          <h3 class="text-lg font-bold text-text-main mb-1">Belum ada balasan</h3>
          <p class="text-sm text-text-soft">Jadilah yang pertama untuk memberikan jawaban atau tanggapan pada diskusi ini.</p>
        </div>
      @endforelse
    </div>

    {{-- Reply form --}}
    @if($thread->status !== 'closed')
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8">
        <h3 class="font-bold text-lg text-text-main mb-4">Tulis Balasan</h3>
        <form method="POST" action="{{ route('app.qa-threads.replies.store',$thread) }}" class="space-y-4">
          @csrf
          <div>
            <textarea name="body" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all resize-y placeholder-gray-400" placeholder="Ketik balasan Anda di sini..." required>{{ old('body') }}</textarea>
            @error('body') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          <div class="flex justify-end">
            <button type="submit" class="px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
              Kirim Balasan
            </button>
          </div>
        </form>
      </div>
    @else
      <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200">
        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        <h3 class="font-bold text-gray-700">Diskusi Ditutup</h3>
        <p class="text-sm text-gray-500 mt-1">Diskusi ini telah ditutup dan tidak dapat menerima balasan baru.</p>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDeleteThread() {
    Swal.fire({
      title: 'Hapus Diskusi?',
      text: "Semua balasan di dalam diskusi ini juga akan ikut terhapus permanen.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-3xl',
        confirmButton: 'rounded-xl font-bold px-6 py-2.5',
        cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 text-gray-700'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('formDeleteThread').submit();
      }
    });
  }

  function confirmDeleteReply(id) {
    Swal.fire({
      title: 'Hapus Balasan?',
      text: "Balasan Anda akan dihapus secara permanen.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-3xl',
        confirmButton: 'rounded-xl font-bold px-6 py-2.5',
        cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 text-gray-700'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('formDeleteReply-' + id).submit();
      }
    });
  }

  function confirmMarkAnswer(id) {
    Swal.fire({
      title: 'Tandai Sebagai Jawaban?',
      text: "Balasan ini akan disorot sebagai solusi yang menyelesaikan pertanyaan Anda.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0F9D8A',
      cancelButtonColor: '#9ca3af',
      confirmButtonText: 'Ya, tandai',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-3xl',
        confirmButton: 'rounded-xl font-bold px-6 py-2.5',
        cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 text-gray-700'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('formMarkAnswer-' + id).submit();
      }
    });
  }
</script>
@endpush
