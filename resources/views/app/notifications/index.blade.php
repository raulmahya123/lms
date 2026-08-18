@extends('layouts.app')
@section('title', 'Notifikasi — LMS Enterprise')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-text-main tracking-tight">Pusat Notifikasi</h1>
        <p class="text-sm text-text-soft mt-1">Lihat semua pembaruan dan pemberitahuan penting untukmu.</p>
    </div>
    
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form action="{{ route('app.notifications.readAll') }}" method="POST">
        @csrf @method('PATCH')
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-tosca-light text-tosca-dark font-bold hover:bg-tosca hover:text-white transition-colors text-sm shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Tandai Semua Dibaca
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        @forelse($notifications as $notif)
            <div class="px-6 py-5 border-b border-gray-50 hover:bg-softbg transition-colors flex flex-col sm:flex-row sm:items-start justify-between gap-4 {{ is_null($notif->read_at) ? 'bg-tosca-light/20' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center {{ is_null($notif->read_at) ? 'bg-tosca-light text-tosca' : 'bg-gray-100 text-gray-500' }}">
                        @if(isset($notif->data['type']) && $notif->data['type'] === 'success')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif(isset($notif->data['type']) && $notif->data['type'] === 'warning')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                    </div>
                    
                    <div>
                        <div class="text-sm md:text-base text-text-main {{ is_null($notif->read_at) ? 'font-bold' : 'font-medium' }}">
                            {{ $notif->data['message'] ?? 'Kamu memiliki notifikasi baru' }}
                        </div>
                        @if(isset($notif->data['action_text']) && isset($notif->data['action_url']))
                            <div class="mt-3">
                                <a href="{{ $notif->data['action_url'] }}" class="inline-flex items-center gap-1 text-sm font-bold text-tosca hover:text-tosca-dark hover:underline">
                                    {{ $notif->data['action_text'] }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        @endif
                        <div class="mt-2 text-xs text-text-soft flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                
                @if(is_null($notif->read_at))
                <div class="shrink-0 pt-1 sm:pt-0 pl-14 sm:pl-0">
                    <form action="{{ route('app.notifications.read', $notif->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-tosca/30 text-tosca hover:bg-tosca hover:text-white transition-colors text-xs font-bold shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tandai Dibaca
                        </button>
                    </form>
                </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-text-main mb-1">Belum Ada Notifikasi</h3>
                <p class="text-text-soft text-sm max-w-sm">Semua pemberitahuan tentang aktivitas belajar, kuis, dan langganan akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
    
    @if($notifications->hasPages())
    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
