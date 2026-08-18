@extends('layouts.admin')
@section('title', 'Manajemen Kuis — Admin')

@section('content')
    <div x-data="{ q: @js(request('q') ?? ''), showFilters: false }" class="space-y-6 pb-12">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Evaluasi Pembelajaran
                </div>
                <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Kuis</h1>
                <p class="text-sm text-text-soft mt-1">Kelola kuis untuk menguji pemahaman materi di setiap pelajaran.</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="showFilters=!showFilters"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>
                <a href="{{ route('admin.quizzes.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Kuis Baru
                </a>
            </div>
        </div>

        {{-- FILTERS / SEARCH --}}
        <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
              class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Pilih Pelajaran</label>
                    <div class="relative">
                        <select name="lesson_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="">— Semua Pelajaran —</option>
                            @php
                                $__lessons = \App\Models\Lesson::select('id', 'title')->orderBy('id', 'desc')->get();
                            @endphp
                            @foreach ($__lessons as $ls)
                                <option value="{{ $ls->id }}" @selected(request('lesson_id') == $ls->id)>
                                    {{ Str::limit($ls->title, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Cari Judul</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="q" x-model="q" placeholder="Masukkan kata kunci..."
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" />
                    </div>
                </div>

                <div class="flex items-end gap-3 pt-6 md:pt-0 border-t border-gray-50 md:border-none">
                    <button class="w-full md:w-auto px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors flex-1 text-center">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['lesson_id', 'q']))
                        <a href="{{ route('admin.quizzes.index') }}"
                           class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- TABLE CARD --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-50 bg-softbg/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-bold text-text-main">Total {{ $quizzes->total() }} Kuis</span>
                    
                    @if (request('lesson_id'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                            Pelajaran Spesifik
                        </span>
                    @endif

                    @if (request('q'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                            Pencarian: "{{ request('q') }}"
                        </span>
                    @endif
                </div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
                    Halaman {{ $quizzes->currentPage() }} dari {{ $quizzes->lastPage() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                            <th class="px-6 py-4 font-bold">Informasi Kuis</th>
                            <th class="px-6 py-4 font-bold">Pelajaran (Lesson)</th>
                            <th class="px-6 py-4 font-bold text-center">Jumlah Soal</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($quizzes as $qz)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                
                                {{-- Informasi Kuis --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-text-main text-base mb-1">{{ $qz->title }}</div>
                                    <div class="text-xs text-text-soft line-clamp-2 max-w-sm">{{ $qz->description ?? 'Tidak ada deskripsi' }}</div>
                                </td>

                                {{-- Pelajaran --}}
                                <td class="px-6 py-4">
                                    @if($qz->lesson)
                                        <a href="{{ route('admin.lessons.edit', $qz->lesson_id) }}" class="inline-flex items-center gap-2 text-sm font-bold text-tosca hover:text-tosca-dark transition-colors truncate max-w-[250px]" title="{{ $qz->lesson->title }}">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span class="truncate">{{ $qz->lesson->title }}</span>
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-gray-400 italic">Tanpa Pelajaran</span>
                                    @endif
                                </td>
                                
                                {{-- Jumlah Soal --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.questions.index', ['quiz_id' => $qz->id]) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-50 border border-gray-200 text-gray-700 hover:bg-tosca hover:text-white hover:border-tosca transition-colors group">
                                        <span class="mr-1.5 w-5 h-5 rounded-md bg-white border border-gray-200 group-hover:bg-tosca-dark group-hover:border-tosca-dark group-hover:text-white flex items-center justify-center text-[10px]">
                                            {{ $qz->questions_count ?? $qz->questions()->count() }}
                                        </span>
                                        Kelola Soal
                                    </a>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.quizzes.edit', $qz) }}" class="inline-flex items-center justify-center p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-tosca transition-colors" title="Edit Kuis">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.quizzes.destroy', $qz) }}" class="inline js-delete-form" data-title="{{ $qz->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="js-delete-btn inline-flex items-center justify-center p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Kuis">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="py-16 text-center flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Kuis</h3>
                                        <p class="text-text-soft max-w-sm mb-6">Tambahkan kuis untuk menguji pemahaman pengguna di setiap pelajaran.</p>
                                        <a href="{{ route('admin.quizzes.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Kuis Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quizzes->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
                    {{ $quizzes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function() {
                function bindDeleteButtons() {
                    document.querySelectorAll('.js-delete-btn').forEach(btn => {
                        if (btn.dataset.bound) return; 
                        btn.dataset.bound = '1';

                        btn.addEventListener('click', (e) => {
                            const form = e.currentTarget.closest('form.js-delete-form');
                            const title = form?.dataset.title || 'item ini';

                            Swal.fire({
                                title: 'Hapus Kuis?',
                                html: `Kuis <br><b class="text-text-main">${title}</b><br> beserta seluruh soalnya akan dihapus permanen.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Hapus',
                                cancelButtonText: 'Batal',
                                reverseButtons: true,
                                focusCancel: true,
                                customClass: {
                                    popup: 'rounded-3xl',
                                    confirmButton: 'rounded-xl font-bold px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white border-0',
                                    cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border-0'
                                }
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    if (!form.dataset.submitting) {
                                        form.dataset.submitting = '1';
                                        const b = form.querySelector('.js-delete-btn');
                                        if (b) {
                                            b.disabled = true;
                                            b.innerHTML = '<span class="animate-spin mr-2">⏳</span>';
                                        }
                                        form.submit();
                                    }
                                }
                            });
                        });
                    });
                }

                document.addEventListener('DOMContentLoaded', bindDeleteButtons);
                document.addEventListener('turbo:load', bindDeleteButtons);
                document.addEventListener('livewire:navigated', bindDeleteButtons);
            })();
        </script>

        @if (session('ok'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('ok')),
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        customClass: { popup: 'rounded-2xl' }
                    });
                });
            </script>
        @endif
    @endpush
@endsection
