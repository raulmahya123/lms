{{-- resources/views/admin/qa_threads/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Q&A Thread — '.$thread->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    {{-- Judul Thread --}}
    <div class="bg-white shadow rounded-2xl p-6 mb-6">
        <h1 class="text-2xl font-bold mb-2">{{ $thread->title }}</h1>
        <p class="text-gray-600 mb-4">
            Ditulis oleh: <span class="font-medium">{{ $thread->user->name }}</span>
            <span class="text-sm text-gray-400">({{ $thread->created_at->diffForHumans() }})</span>
        </p>
        <div class="prose max-w-none">
            {!! nl2br(e($thread->body)) !!}
        </div>
    </div>

    {{-- Daftar Reply --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold">Jawaban ({{ $thread->replies->count() }})</h2>
        @forelse($thread->replies as $reply)
            <div class="bg-gray-50 border rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-medium">{{ $reply->user->name }}</span>
                        <span class="text-sm text-gray-500"> • {{ $reply->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Tombol tandai jawaban --}}
                    <form method="POST" action="{{ route('admin.qa-replies.answer', $reply) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-3 py-1 text-sm rounded-lg 
                                   {{ $reply->is_answer ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                            {{ $reply->is_answer ? '✔ Jawaban Terpilih' : 'Tandai Jawaban' }}
                        </button>
                    </form>
                </div>
                <div class="mt-2 text-gray-700">
                    {!! nl2br(e($reply->body)) !!}
                </div>
            </div>
        @empty
            <p class="text-gray-500">Belum ada jawaban.</p>
        @endforelse
    </div>
</div>
@endsection
