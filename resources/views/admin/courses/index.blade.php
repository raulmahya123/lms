{{-- resources/views/admin/courses/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Courses — BERKEMAH')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div x-data="{ q: @js(request('q') ?? ''), published: @js(request('published') ?? ''), showFilters: false }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.75 5.25A2.25 2.25 0 0 1 6 3h4.5A2.25 2.25 0 0 1 12.75 5.25v13.5A2.25 2.25 0 0 0 10.5 16.5H6A2.25 2.25 0 0 0 3.75 18.75V5.25Zm9 0A2.25 2.25 0 0 1 15 3h4.5A2.25 2.25 0 0 1 21.75 5.25v13.5A2.25 2.25 0 0 0 19.5 16.5H15a2.25 2.25 0 0 0-2.25 2.25V5.25Z" />
                    </svg>
                    Courses
                </h1>
                <p class="text-sm opacity-70">Kelola course, status publish, dan ringkasan modul.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.courses.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                    </svg>
                    New Course
                </a>
                <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z" />
                    </svg>
                    Filters
                </button>
            </div>
        </div>

        {{-- FILTERS / SEARCH --}}
        <form method="GET" x-show="showFilters" x-transition
            class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
            <div class="col-span-1">
                <label class="block text-sm font-medium mb-1">Search title</label>
                <div class="relative">
                    <input type="text" name="q" x-model="q" placeholder="Cari judul course…"
                        class="w-full border rounded-xl pl-10 pr-3 py-2" />
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z" />
                    </svg>
                </div>
            </div>

            <div class="col-span-1">
                <label class="block text-sm font-medium mb-1">Published</label>
                <div class="relative">
                    <select name="published" x-model="published" class="w-full border rounded-xl pl-10 pr-3 py-2">
                        <option value="">All</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M7 7.5h10a4.5 4.5 0 1 1 0 9H7a4.5 4.5 0 1 1 0-9Zm0 1.5a3 3 0 1 0 0 6h10a3 3 0 1 0 0-6H7Z" />
                    </svg>
                </div>
            </div>

            <div class="col-span-1 flex items-end gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z" />
                    </svg>
                    Apply
                </button>
                @if (request()->hasAny(['q', 'published']) && (request('q') !== null || request('published') !== ''))
                    <a href="{{ route('admin.courses.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 5.25a6.75 6.75 0 1 0 6.53 8.4.75.75 0 1 1 1.46.3 8.25 8.25 0 1 1-1.92-7.17V5.25a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5h1.86A6.73 6.73 0 0 0 12 5.25Z" />
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- TABLE CARD --}}
        <div class="rounded-2xl border bg-white overflow-hidden">
            <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                <div class="text-sm">
                    <span class="font-semibold">{{ $courses->total() }}</span>
                    <span class="opacity-70">courses found</span>

                    @if (request('published') !== null && request('published') !== '')
                        <span
                            class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z" />
                            </svg>
                            Published: {{ request('published') === '1' ? 'Yes' : 'No' }}
                        </span>
                    @endif

                    @if (request('q'))
                        <span
                            class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z" />
                            </svg>
                            “{{ request('q') }}”
                        </span>
                    @endif
                </div>
                <div class="text-xs opacity-70">Page {{ $courses->currentPage() }} / {{ $courses->lastPage() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left">Cover</th>
                            <th class="p-3 text-left">Title</th>
                            <th class="p-3 text-left w-36">Pricing</th>
                            <th class="p-3 text-left w-32">Modules</th>
                            <th class="p-3 text-left w-32">Published</th>
                            <th class="p-3 text-left w-44">Updated</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse($courses as $c)
                            <tr class="border-t align-top">
                                {{-- Cover pakai asset() untuk path lokal --}}
                                <td class="p-3">
                                    @if ($c->cover_url)
                                        <img src="{{ asset('storage/covers/' . basename($c->cover_url)) }}"
                                            alt="Cover {{ $c->title }}" class="h-12 w-20 object-cover rounded border">
                                    @else
                                        <div class="h-12 w-20 grid place-items-center border rounded text-xs text-gray-500">
                                            No image</div>
                                    @endif
                                </td>

                                <td class="p-3">
                                    <div class="font-semibold">{{ $c->title }}</div>
                                    @if (!empty($c->description))
                                        <div class="text-xs text-gray-600 max-w-lg">
                                            {{ Str::limit(strip_tags($c->description), 120) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Pricing --}}
                                <td class="p-3">
                                    @if ($c->is_free)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h12A2.25 2.25 0 0 1 20.25 7.5v2.25a1.5 1.5 0 0 0 0 3V15A2.25 2.25 0 0 1 18 17.25H6A2.25 2.25 0 0 1 3.75 15v-2.25a1.5 1.5 0 0 0 0-3V7.5Z" />
                                            </svg>
                                            Gratis
                                        </span>
                                    @else
                                        <div class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                                            <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 3.75a8.25 8.25 0 1 0 8.25 8.25A8.26 8.26 0 0 0 12 3.75Zm.75 4.5a.75.75 0 0 0-1.5 0v.29a3 3 0 0 0-2.25 2.91.75.75 0 0 0 1.5 0 1.5 1.5 0 1 1 1.5 1.5 3 3 0 1 0 3 3 .75.75 0 0 0-1.5 0 1.5 1.5 0 1 1-1.5-1.5 3 3 0 0 0 0-6Z" />
                                            </svg>
                                            <span class="tabular-nums">
                                                Rp {{ number_format((float) ($c->price ?? 0), 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="p-3">
                                    <div class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                                        <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2.75 2.75 7.5 12 12.25 21.25 7.5 12 2.75Zm0 9.5L2.75 17l9.25 4.75L21.25 17 12 12.25Z" />
                                        </svg>
                                        <span class="tabular-nums">{{ $c->modules_count }}</span>
                                    </div>
                                </td>

                                <td class="p-3">
                                    @if ($c->is_published)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 0 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 0 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z" />
                                            </svg>
                                            Published
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75V15a.75.75 0 0 1-.22.53l-4.5 4.5a.75.75 0 0 1-1.28-.53V15.75H6.75A2.25 2.25 0 0 1 4.5 13.5v-6.75Z" />
                                            </svg>
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3 text-xs text-gray-600">
                                    {{ optional($c->updated_at)->format('Y-m-d H:i') }}
                                </td>

                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.courses.edit', $c) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $c) }}"
                                            class="inline js-delete-form" data-title="{{ $c->title }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" {{-- ← penting: button, bukan submit --}}
                                                class="js-delete inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition"
                                                title="Delete">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5Z" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>


                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-10">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M6.75 3A2.75 2.75 0 0 0 4 5.75v12.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25V9.5a.75.75 0 0 0-.22-.53l-5.75-5.75A.75.75 0 0 0 13.5 3h-6.75Z" />
                                            </svg>
                                        </div>
                                        <div class="text-lg font-semibold">Belum ada course</div>
                                        <p class="text-sm opacity-70 max-w-md">Tambahkan course pertama agar tim bisa mulai
                                            membuat modul & materi.</p>
                                        <a href="{{ route('admin.courses.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                                            </svg>
                                            Create Course
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="text-sm opacity-70">
                    Showing <span class="font-semibold">{{ $courses->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold">{{ $courses->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold">{{ $courses->total() }}</span> results
                </div>
                <div>{{ $courses->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function() {
                function bindDeleteButtons() {
                    document.querySelectorAll('.js-delete').forEach(btn => {
                        if (btn.dataset.bound) return; // cegah double binding
                        btn.dataset.bound = '1';

                        btn.addEventListener('click', (e) => {
                            const form = e.currentTarget.closest('form.js-delete-form');
                            const title = form?.dataset.title || 'item ini';

                            Swal.fire({
                                title: 'Hapus course?',
                                html: `Course <b>${title}</b> akan dihapus permanen.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus',
                                cancelButtonText: 'Batal',
                                reverseButtons: true,
                                focusCancel: true,
                                cancelButtonColor: '#5726dcff',

                                confirmButtonColor: '#dc2626'
                            }).then((res) => {
                                if (res.isConfirmed) form.submit();
                            });
                        });
                    });
                }

                // initial load
                document.addEventListener('DOMContentLoaded', bindDeleteButtons);
                // kalau pakai Turbo/Livewire/htmx, re-bind juga:
                document.addEventListener('turbo:load', bindDeleteButtons);
                document.addEventListener('livewire:navigated', bindDeleteButtons);
            })();
        </script>
    @endpush

@endsection
