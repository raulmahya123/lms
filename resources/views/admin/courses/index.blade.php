{{-- resources/views/admin/courses/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Kursus — Admin')

@section('content')
    @php
        use Illuminate\Support\Str;
        use Illuminate\Support\Facades\Storage;
    @endphp

    <div x-data="{ q: @js(request('q') ?? ''), published: @js(request('published') ?? ''), showFilters: false }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    E-Learning
                </div>
                <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Manajemen Kursus</h1>
                <p class="text-sm text-text-soft mt-1">Kelola semua materi pembelajaran, modul, dan status publikasi.</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="showFilters=!showFilters"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>
                <a href="{{ route('admin.courses.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Kursus Baru
                </a>
            </div>
        </div>

        {{-- FILTERS / SEARCH --}}
        <form method="GET" x-show="showFilters" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
              class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                <div>
                    <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Status Publikasi</label>
                    <div class="relative">
                        <select name="published" x-model="published" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="1">Dipublikasi</option>
                            <option value="0">Draft</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-end gap-3 pt-6 md:pt-0 border-t border-gray-50 md:border-none">
                    <button class="w-full md:w-auto px-6 py-3 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors flex-1 text-center">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['q', 'published']) && (request('q') !== null || request('published') !== ''))
                        <a href="{{ route('admin.courses.index') }}"
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
                    <span class="text-sm font-bold text-text-main">Total {{ $courses->total() }} Kursus</span>
                    
                    @if (request('published') !== null && request('published') !== '')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                            Status: {{ request('published') === '1' ? 'Published' : 'Draft' }}
                        </span>
                    @endif

                    @if (request('q'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                            Pencarian: "{{ request('q') }}"
                        </span>
                    @endif
                </div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider">
                    Halaman {{ $courses->currentPage() }} dari {{ $courses->lastPage() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
                            <th class="px-6 py-4 font-bold">Informasi Kursus</th>
                            <th class="px-6 py-4 font-bold text-right">Harga</th>
                            <th class="px-6 py-4 font-bold text-center">Modul</th>
                            <th class="px-6 py-4 font-bold text-center">Status</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($courses as $c)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-4">
                                        @php
                                            $rel = ltrim($c->cover ?? '', '/');
                                            if (Str::startsWith($rel, 'storage/')) {
                                                $rel = Str::after($rel, 'storage/');
                                            }
                                            $src = $rel ? asset('storage/'.$rel) : null;
                                            $filename = $rel ? basename($rel) : null;
                                            $exists = $rel ? Storage::disk('public')->exists($rel) : false;
                                        @endphp

                                        <div class="shrink-0 w-24 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 relative flex items-center justify-center">
                                            @if ($src && $exists)
                                                <img src="{{ $src }}" alt="Cover {{ $c->title }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-text-main text-base mb-1 line-clamp-1">{{ $c->title }}</div>
                                            @if (!empty($c->description))
                                                <div class="text-sm text-text-soft line-clamp-1">
                                                    {{ strip_tags($c->description) }}
                                                </div>
                                            @endif
                                            <div class="text-xs text-text-soft font-medium mt-1">
                                                Diperbarui: {{ optional($c->updated_at)->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    @if ($c->is_free)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                                            Gratis
                                        </span>
                                    @else
                                        <div class="font-extrabold text-text-main whitespace-nowrap">
                                            Rp {{ number_format((float) ($c->price ?? 0), 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center min-w-[3rem] h-8 rounded-lg bg-gray-50 border border-gray-200 font-bold text-text-main text-sm">
                                        {{ $c->modules_count }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($c->is_published)
                                        <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border bg-tosca-light/50 text-tosca-dark border-tosca-light">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border bg-gray-100 text-gray-700 border-gray-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.courses.edit', $c) }}" class="inline-flex items-center justify-center p-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-tosca transition-colors" title="Edit Kursus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $c) }}" class="inline js-delete-form" data-title="{{ $c->title }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="js-delete inline-flex items-center justify-center p-2 bg-white border border-gray-200 text-gray-400 rounded-lg hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors" title="Hapus Kursus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="py-16 text-center flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 rounded-3xl bg-softbg flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-text-main mb-2">Belum Ada Kursus</h3>
                                        <p class="text-text-soft max-w-sm mb-6">Tambahkan kursus pertama Anda untuk mulai mengelola materi dan modul pembelajaran.</p>
                                        <a href="{{ route('admin.courses.create') }}" class="px-6 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Kursus Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($courses->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex justify-center">
                    {{ $courses->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function() {
                function bindDeleteButtons() {
                    document.querySelectorAll('.js-delete').forEach(btn => {
                        if (btn.dataset.bound) return; 
                        btn.dataset.bound = '1';

                        btn.addEventListener('click', (e) => {
                            const form = e.currentTarget.closest('form.js-delete-form');
                            const title = form?.dataset.title || 'item ini';

                            Swal.fire({
                                title: 'Hapus Kursus?',
                                html: `Semua data terkait kursus <br><b class="text-text-main">${title}</b><br> akan dihapus permanen.`,
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
                                if (res.isConfirmed) form.submit();
                            });
                        });
                    });
                }

                document.addEventListener('DOMContentLoaded', bindDeleteButtons);
                document.addEventListener('turbo:load', bindDeleteButtons);
                document.addEventListener('livewire:navigated', bindDeleteButtons);
            })();
        </script>
    @endpush
@endsection
