@extends('layouts.admin')
@section('title', 'Detail Membership — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Detail Akses
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Membership #{{ $membership->id }}</h1>
            <p class="text-sm text-text-soft mt-1">Rincian hak akses dan status berlangganan pengguna.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.memberships.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <a href="{{ route('admin.memberships.edit', $membership) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Akses
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: User & Plan --}}
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Informasi Pengguna
                    </h2>
                </div>
                <div class="p-8 flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-tosca-light flex items-center justify-center text-tosca-dark font-extrabold text-2xl border border-tosca/20">
                        {{ strtoupper(substr($membership->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-text-main text-xl">{{ $membership->user?->name ?? 'Pengguna Tidak Ditemukan' }}</div>
                        <div class="text-sm text-text-soft mt-1">{{ $membership->user?->email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Paket Langganan
                    </h2>
                </div>
                <div class="p-8">
                    @if($membership->plan)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="font-bold text-text-main text-lg">{{ $membership->plan->name }}</div>
                                <div class="text-sm font-bold text-tosca-dark mt-1">
                                    Rp {{ number_format((float)$membership->plan->price, 0, ',', '.') }} / 
                                    {{ $membership->plan->period === 'monthly' ? 'Bulan' : ($membership->plan->period === 'yearly' ? 'Tahun' : 'Selamanya') }}
                                </div>
                            </div>
                            <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-100 font-bold text-sm inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ $membership->plan->planCourses()->count() }} Course
                            </div>
                        </div>
                    @else
                        <div class="text-gray-400 italic font-medium">Data paket telah dihapus atau tidak ditemukan.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Status & Timeline --}}
        <div class="md:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status
                    </h2>
                </div>
                <div class="p-6">
                    @php
                        $badge = match ($membership->status) {
                            'active' => ['bg-green-50', 'text-green-700', 'border-green-200', 'Aktif', 'bg-green-500'],
                            'inactive' => ['bg-red-50', 'text-red-700', 'border-red-200', 'Nonaktif', 'bg-red-500'],
                            'pending' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Menunggu', 'bg-amber-500'],
                            'expired' => ['bg-gray-100', 'text-gray-700', 'border-gray-200', 'Kadaluarsa', 'bg-gray-500'],
                            'cancelled' => ['bg-red-50', 'text-red-700', 'border-red-200', 'Dibatalkan', 'bg-red-500'],
                            default => ['bg-gray-50', 'text-gray-700', 'border-gray-200', ucfirst($membership->status ?? 'Unknown'), 'bg-gray-500'],
                        };
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold uppercase tracking-wider border {{ $badge[0] }} {{ $badge[1] }} {{ $badge[2] }} w-full justify-center">
                        <span class="w-2 h-2 rounded-full {{ $badge[4] }}"></span>
                        {{ $badge[3] }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Masa Berlaku
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Mulai Berlaku</div>
                        <div class="font-bold text-text-main">{{ optional($membership->activated_at)->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Kadaluarsa</div>
                        <div class="font-bold text-text-main">{{ optional($membership->expires_at)->format('d M Y, H:i') ?? 'Selamanya' }}</div>
                        @if($membership->expires_at && $membership->expires_at->isPast())
                            <div class="text-xs font-bold text-red-600 mt-1">Telah berakhir</div>
                        @elseif($membership->expires_at)
                            <div class="text-xs font-bold text-green-600 mt-1">
                                Sisa waktu: {{ $membership->expires_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-[11px] font-bold text-text-soft uppercase tracking-wider">Terakhir Diperbarui</p>
                <p class="text-xs font-medium text-gray-500 mt-1">{{ $membership->updated_at?->format('d M Y, H:i') ?? '—' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
