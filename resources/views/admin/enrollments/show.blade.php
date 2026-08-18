@extends('layouts.admin')
@section('title', 'Detail Pendaftaran — Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Detail Akses
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Pendaftaran #{{ $enrollment->id }}</h1>
            <p class="text-sm text-text-soft mt-1">Rincian akses pengguna ke dalam materi course.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Ubah Status
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold">Gagal memperbarui status!</p>
                <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('ok') || session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex gap-3 text-sm text-green-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">{{ session('ok') ?? session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Left Column: User & Course --}}
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
                        {{ strtoupper(substr($enrollment->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-text-main text-xl">{{ $enrollment->user?->name ?? 'Pengguna Tidak Ditemukan' }}</div>
                        <div class="text-sm text-text-soft mt-1">{{ $enrollment->user?->email ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Materi Pembelajaran
                    </h2>
                </div>
                <div class="p-8">
                    @if($enrollment->course)
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div>
                                <div class="font-bold text-text-main text-lg">{{ $enrollment->course->title }}</div>
                                <div class="text-sm font-medium text-text-soft mt-1 line-clamp-2">
                                    {{ Str::limit(strip_tags($enrollment->course->description), 100) }}
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.courses.edit', $enrollment->course_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Lihat Course
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-gray-400 italic font-medium">Data course telah dihapus atau tidak ditemukan.</div>
                    @endif
                </div>
            </div>

            {{-- Form Update Status --}}
            <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Pembaruan Akses
                    </h2>
                    <span class="text-xs font-bold text-text-soft bg-gray-100 px-2.5 py-1 rounded-md">Update Data</span>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Status Pendaftaran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                                    <option value="pending" @selected(old('status', $enrollment->status) === 'pending')>Menunggu (Pending)</option>
                                    <option value="active" @selected(old('status', $enrollment->status) === 'active')>Aktif (Active)</option>
                                    <option value="inactive" @selected(old('status', $enrollment->status) === 'inactive')>Nonaktif (Inactive)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-main mb-2">Mulai Berlaku (Activated At)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                @php
                                    $activatedAtValue = old('activated_at', optional($enrollment->activated_at)->format('Y-m-d\TH:i'));
                                @endphp
                                <input type="datetime-local" name="activated_at" value="{{ $activatedAtValue }}"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex justify-end">
                    <button type="submit" class="px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Status & Timeline --}}
        <div class="md:col-span-1 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-base font-extrabold text-text-main flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status Saat Ini
                    </h2>
                </div>
                <div class="p-6">
                    @php
                        $badge = match ($enrollment->status) {
                            'active' => ['bg-green-50', 'text-green-700', 'border-green-200', 'Aktif', 'bg-green-500'],
                            'inactive' => ['bg-red-50', 'text-red-700', 'border-red-200', 'Nonaktif', 'bg-red-500'],
                            'pending' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'Menunggu', 'bg-amber-500'],
                            default => ['bg-gray-50', 'text-gray-700', 'border-gray-200', ucfirst($enrollment->status), 'bg-gray-500'],
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
                        Log Data
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Dibuat Pada</div>
                        <div class="font-bold text-text-main">{{ $enrollment->created_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Terakhir Diperbarui</div>
                        <div class="font-bold text-text-main">{{ $enrollment->updated_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Mulai Berlaku</div>
                        <div class="font-bold {{ $enrollment->activated_at ? 'text-green-600' : 'text-gray-400' }}">{{ $enrollment->activated_at?->format('d M Y, H:i') ?? 'Belum ada catatan' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
