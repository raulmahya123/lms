{{-- resources/views/admin/certificate_templates/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Certificate Templates — BERKEMAH')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->has('q') ? 'true' : 'false' }} }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    {{-- ribbon/bookmark icon --}}
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5 3h14v18l-7-3-7 3V3Z" />
                    </svg>
                    Certificate Templates
                </h1>
                <p class="text-sm opacity-70">Template latar, field dinamis, dan status aktif/nonaktif.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.certificate-templates.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                    </svg>
                    New Template
                </a>
                <button type="button" @click="showFilters=!showFilters"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
                    {{-- filter icon --}}
                    <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z" />
                    </svg>
                    Filters
                </button>
            </div>
        </div>

        {{-- FILTERS --}}
        <form method="GET" x-show="showFilters" x-transition
            class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
            <div class="col-span-1">
                <label class="block text-sm font-medium mb-1">Search name</label>
                <div class="relative">
                    <input type="text" name="q" x-model="q" placeholder="Search template…"
                        class="w-full border rounded-xl pl-10 pr-3 py-2">
                    {{-- search icon --}}
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z" />
                    </svg>
                </div>
            </div>

            <div class="col-span-1 flex items-end gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    {{-- funnel icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z" />
                    </svg>
                    Apply
                </button>

                @if (request()->has('q') && request('q') !== '')
                    <a href="{{ route('admin.certificate-templates.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                        {{-- reset icon --}}
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
            {{-- header strip + badges --}}
            <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                <div class="text-sm">
                    <span class="font-semibold">{{ $templates->total() }}</span>
                    <span class="opacity-70">templates found</span>

                    @if (request('q'))
                        <span
                            class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
                            {{-- badge search --}}
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z" />
                            </svg>
                            “{{ request('q') }}”
                        </span>
                    @endif
                </div>
                <div class="text-xs opacity-70">Page {{ $templates->currentPage() }} / {{ $templates->lastPage() }}</div>
            </div>

            {{-- table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left w-20">ID</th>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Background</th>
                            <th class="p-3 text-left w-28">Active</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse($templates as $t)
                            <tr class="border-t align-top">
                                <td class="p-3 font-semibold text-gray-700">
                                    <span title="{{ $t->id }}">#{{ Str::of($t->id)->substr(0, 8) }}</span>
                                </td>

                                <td class="p-3">
                                    <div class="font-semibold">{{ $t->name }}</div>
                                    @if (!empty($t->description))
                                        <div class="text-xs text-gray-600 max-w-lg">
                                            {{ Str::limit(strip_tags($t->description), 120) }}</div>
                                    @endif
                                </td>

                                <td class="p-3">
                                    @php
                                        // normalisasi preview: kalau URL relatif (storage/public), buatkan URL publik
                                        $bg = $t->background_url;
                                        if (
                                            $bg &&
                                            !Str::startsWith($bg, ['http://', 'https://', '/storage/', 'storage/'])
                                        ) {
                                            $bg = \Illuminate\Support\Facades\Storage::disk('public')->url(
                                                ltrim($bg, '/'),
                                            );
                                        } elseif ($bg && Str::startsWith($bg, 'storage/')) {
                                            $bg = "/$bg";
                                        }
                                    @endphp

                                    @if ($bg)
                                        <a href="{{ $bg }}" target="_blank"
                                            class="inline-flex items-center gap-2 text-blue-600 hover:underline">
                                            {{-- image icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M4.5 5.75A2.75 2.75 0 0 1 7.25 3h9.5A2.75 2.75 0 0 1 19.5 5.75v12.5A2.75 2.75 0 0 1 16.75 21h-9.5A2.75 2.75 0 0 1 4.5 18.25V5.75Zm3 2a1.25 1.25 0 1 0 0 2.5A1.25 1.25 0 0 0 7.5 7.75Z" />
                                            </svg>
                                            Preview
                                        </a>
                                    @else
                                        <span class="text-xs opacity-60">—</span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    @if ($t->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800">
                                            {{-- check icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 0 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 0 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z" />
                                            </svg>
                                            Yes
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                            {{-- minus icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M6.75 12a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9A.75.75 0 0 1 6.75 12Z" />
                                            </svg>
                                            No
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.certificate-templates.show', $t) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="View">
                                            {{-- eye icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 6.75c-5.25 0-8.25 5.25-8.25 5.25S6.75 17.25 12 17.25 20.25 12 20.25 12 17.25 6.75 12 6.75Zm0 7.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('admin.certificate-templates.edit', $t) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="Edit">
                                            {{-- pencil icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.certificate-templates.destroy', $t) }}"
                                            class="inline js-delete-form"
                                            data-title="{{ $t->name ?? 'Template ID: ' . $t->id }}">
                                            @csrf @method('DELETE')

                                            <button type="button" {{-- penting: button, bukan submit --}}
                                                class="js-delete-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition"
                                                title="Delete">
                                                {{-- trash icon --}}
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-10">
                                    <div class="flex flex-col items-center justify-center text-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                            {{-- empty icon --}}
                                            <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M6.75 3A2.75 2.75 0 0 0 4 5.75v12.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25V9.5a.75.75 0 0 0-.22-.53l-5.75-5.75A.75.75 0 0 0 13.5 3h-6.75Z" />
                                            </svg>
                                        </div>
                                        <div class="text-lg font-semibold">Belum ada template</div>
                                        <p class="text-sm opacity-70 max-w-md">Buat template pertama untuk mulai
                                            menerbitkan sertifikat.</p>
                                        <a href="{{ route('admin.certificate-templates.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                                            </svg>
                                            Create Template
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION STRIP --}}
            <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="text-sm opacity-70">
                    Showing <span class="font-semibold">{{ $templates->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold">{{ $templates->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold">{{ $templates->total() }}</span> results
                </div>
                <div>{{ $templates->withQueryString()->links() }}</div>
            </div>
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
                            const title = form?.dataset.title || 'template ini';

                            Swal.fire({
                                title: 'Hapus certificate template?',
                                html: `Template <b>${title}</b> akan dihapus permanen.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus',
                                cancelButtonText: 'Batal',
                                reverseButtons: true,
                                focusCancel: true,
                                cancelButtonColor: '#5726dcff',

                                confirmButtonColor: '#dc2626'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    if (!form.dataset.submitting) {
                                        form.dataset.submitting = '1';
                                        const b = form.querySelector('.js-delete-btn');
                                        if (b) {
                                            b.disabled = true;
                                            b.textContent = 'Menghapus…';
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
    @endpush

@endsection
