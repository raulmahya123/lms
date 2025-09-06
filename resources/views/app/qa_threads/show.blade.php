@extends('app.layouts.base')

@section('title',$thread->title)

@section('content')
@php($me = \Illuminate\Support\Facades\Auth::user())
<div class="max-w-5xl mx-auto space-y-6">
  <div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold">{{ $thread->title }}</h1>
        <div class="text-sm text-gray-500 mt-1">
          oleh <span class="font-medium">{{ $thread->user?->name ?? 'User' }}</span>
          · {{ $thread->created_at?->format('Y-m-d H:i') }}
          @if($thread->course) · Kursus: {{ $thread->course->title }} @endif
          @if($thread->lesson) · Pelajaran: {{ $thread->lesson->title }} @endif
        </div>
      </div>
      <div class="shrink-0 space-x-2 text-right">
        <span class="inline-block px-2 py-0.5 text-xs rounded-full
          {{ $thread->status==='resolved' ? 'bg-emerald-100 text-emerald-700' : ($thread->status==='closed' ? 'bg-gray-200 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
          {{ ucfirst($thread->status) }}
        </span>
        @if($me && $me->id === $thread->user_id)
          <a href="{{ route('app.qa-threads.edit',$thread) }}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white">Edit</a>
          <form action="{{ route('app.qa-threads.destroy',$thread) }}" method="POST" class="inline"
                onsubmit="return confirm('Hapus thread ini?');">
            @csrf @method('DELETE')
            <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white">Hapus</button>
          </form>
        @endif
      </div>
    </div>

    @if($thread->body)
      <div class="prose max-w-none mt-4">{!! nl2br(e($thread->body)) !!}</div>
    @endif
  </div>

  {{-- Replies --}}
  <div class="bg-white rounded-xl shadow">
    <div class="p-4 border-b"><h2 class="font-semibold">Balasan ({{ $thread->replies->count() }})</h2></div>

    @forelse($thread->replies as $r)
      <div class="p-4 border-b last:border-0">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="text-sm text-gray-500">
              <span class="font-medium">{{ $r->user?->name ?? 'User' }}</span>
              · {{ $r->created_at?->diffForHumans() }}
            </div>
            <div class="mt-2 whitespace-pre-line">{{ $r->body }}</div>
          </div>
          <div class="shrink-0 text-right space-y-2">
            @if($r->is_answer)
              <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">✔ Jawaban</span>
            @endif

            {{-- Mark as answer: hanya pemilik thread --}}
            @if(!$r->is_answer && $me && $me->id === $thread->user_id)
              <form method="POST" action="{{ route('app.qa-replies.answer',$r) }}">
                @csrf @method('PATCH')
                <button class="text-xs px-2 py-1 rounded bg-emerald-600 text-white"
                        onclick="return confirm('Tandai balasan ini sebagai jawaban?')">
                  Tandai Jawaban
                </button>
              </form>
            @endif

            {{-- Hapus reply: hanya pemilik reply --}}
            @if($me && $me->id === $r->user_id)
              <form method="POST" action="{{ route('app.qa-replies.destroy',$r) }}"
                    onsubmit="return confirm('Hapus balasan ini?')">
                @csrf @method('DELETE')
                <button class="text-xs px-2 py-1 rounded bg-red-600 text-white">Hapus</button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="p-8 text-center text-gray-500">Belum ada balasan.</div>
    @endforelse
  </div>

  {{-- Reply form --}}
  <div class="bg-white rounded-xl shadow p-6">
    <h3 class="font-semibold mb-3">Tulis Balasan</h3>
    <form method="POST" action="{{ route('app.qa-threads.replies.store',$thread) }}" class="space-y-3">
      @csrf
      <textarea name="body" rows="5" class="w-full border rounded-lg px-3 py-2" required>{{ old('body') }}</textarea>
      @error('body') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      <div>
        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Kirim Balasan</button>
      </div>
    </form>
  </div>
</div>
@endsection
