{{-- resources/views/admin/certificate_issues/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Certificate Issues — BERKEMAH')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div x-data="{ q: @js(request('q') ?? ''), type: @js(request('assessment_type') ?? ''), showFilters: {{ request()->hasAny(['q', 'assessment_type']) ? 'true' : 'false' }} }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    {{-- chat-bubble/certificate icon --}}
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16v12H5.17L4 17.17V4Z" />
                        <path d="M8 8h8v2H8V8Zm0 3h5v2H8v-2Z" />
                    </svg>
                    Certificate Issues
                </h1>
                <p class="text-sm opacity-70">Daftar sertifikat terbit beserta serial, user, course/test, skor, dan jenis
                    assessment.</p>
            </div>

            <div class="flex items-center gap-2">
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
                <label class="block text-sm font-medium mb-1">Search serial / user</label>
                <div class="relative">
                    <input name="q" x-model="q" placeholder="Search serial or user…"
                        class="w-full border rounded-xl pl-10 pr-3 py-2">
                    {{-- search icon --}}
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z" />
                    </svg>
                </div>
            </div>

            <div class="col-span-1">
                <label class="block text-sm font-medium mb-1">Assessment Type</label>
                <div class="relative">
                    <select name="assessment_type" x-model="type" class="w-full border rounded-xl pl-10 pr-8 py-2">
                        <option value="">All</option>
                        <option value="course">Course</option>
                        <option value="psych">Psych Test</option>
                    </select>
                    {{-- status icon --}}
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 7.5h12a.75.75 0 1 1 0 1.5H6Z" />
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
                @if (request()->hasAny(['q', 'assessment_type']) && (request('q') !== '' || request('assessment_type') !== ''))
                    <a href="{{ route('admin.certificate-issues.index') }}"
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
                    <span class="font-semibold">{{ $issues->total() }}</span>
                    <span class="opacity-70">issues found</span>

                    @if (request('assessment_type') !== '')
                        <span
                            class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                            {{-- badge type --}}
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z" />
                            </svg>
                            Type: {{ ucfirst(request('assessment_type')) }}
                        </span>
                    @endif
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
                <div class="text-xs opacity-70">Page {{ $issues->currentPage() }} / {{ $issues->lastPage() }}</div>
            </div>

            {{-- table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left w-20">ID</th>
                            <th class="p-3 text-left">Serial</th>
                            <th class="p-3 text-left">User</th>
                            <th class="p-3 text-left">Course/Test</th>
                            <th class="p-3 text-left w-28">Type</th>
                            <th class="p-3 text-left w-24">Score</th>
                            <th class="p-3 text-left w-44">Issued</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse($issues as $i)
                            <tr class="border-t align-top">
                                <td class="p-3 font-semibold text-gray-700">
                                    <span title="{{ $i->id }}">#{{ Str::of($i->id)->substr(0, 8) }}</span>
                                </td>

                                <td class="p-3 font-mono">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate max-w-[180px]"
                                            title="{{ $i->serial }}">{{ $i->serial }}</span>
                                        {{-- copy serial (non-blocking) --}}
                                        <button type="button" class="p-1 rounded hover:bg-gray-100"
                                            x-on:click="navigator.clipboard.writeText('{{ $i->serial }}')"
                                            title="Copy serial">
                                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M8 5.75A2.75 2.75 0 0 1 10.75 3h6.5A2.75 2.75 0 0 1 20 5.75v6.5A2.75 2.75 0 0 1 17.25 15h-6.5A2.75 2.75 0 0 1 8 12.25v-6.5Zm-2 4A2.75 2.75 0 0 0 3.25 12.5v5.75A2.75 2.75 0 0 0 6 21h5.75A2.75 2.75 0 0 0 14.5 18.25V17H6a2 2 0 0 1-2-2v-5.5Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <td class="p-3">{{ $i->user->name ?? '—' }}</td>
                                <td class="p-3">
                                    @if ($i->assessment_type === 'psych')
                                        <span class="inline-flex items-center gap-1">
                                            {{-- brain icon --}}
                                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M8.5 6.5A3.5 3.5 0 0 1 12 3a3.5 3.5 0 0 1 3.5 3.5A3.25 3.25 0 0 1 19 9.75 3.25 3.25 0 0 1 15.75 13H9.5A3.5 3.5 0 0 1 8.5 6.5Z" />
                                            </svg>
                                            Psych Test
                                        </span>
                                    @else
                                        <span class="truncate max-w-[320px]"
                                            title="{{ $i->course->title ?? '' }}">{{ $i->course->title ?? '—' }}</span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    @php $isCourse = $i->assessment_type === 'course'; @endphp
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $isCourse ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            @if ($isCourse)
                                                <path
                                                    d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75V15l-7.5-3-7.5 3V6.75Z" />
                                            @else
                                                <path
                                                    d="M12 2.75a5.25 5.25 0 0 1 5.25 5.25v.5a3.5 3.5 0 1 1 0 7h-1.5a3 3 0 0 1-3 3H9.5A3.5 3.5 0 1 1 9.5 8h.25A5.25 5.25 0 0 1 12 2.75Z" />
                                            @endif
                                        </svg>
                                        {{ $isCourse ? 'Course' : 'Psych' }}
                                    </span>
                                </td>

                                <td class="p-3">
                                    @php
                                        $score = $i->score;
                                        $scoreText = is_null($score)
                                            ? '—'
                                            : rtrim(rtrim(number_format($score, 2), '0'), '.');
                                    @endphp
                                    <span class="tabular-nums">{{ $scoreText }}</span>
                                </td>

                                <td class="p-3 text-sm text-gray-600">
                                    @if ($i->issued_at)
                                        <time datetime="{{ $i->issued_at->toIso8601String() }}"
                                            title="{{ $i->issued_at->format('r') }}">
                                            {{ $i->issued_at->format('Y-m-d H:i') }}
                                        </time>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.certificate-issues.show', $i) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="Detail">
                                            {{-- eye icon --}}
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M12 6.75c-5.25 0-8.25 5.25-8.25 5.25S6.75 17.25 12 17.25 20.25 12 20.25 12 17.25 6.75 12 6.75Zm0 7.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                                            </svg>
                                            Detail
                                        </a>
                                        <form method="POST" action="{{ route('admin.certificate-issues.destroy', $i) }}"
                                            class="inline js-delete-form"
                                            data-title="{{ $i->recipient_name ?? 'Issue ID: ' . $i->id }}">
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
                                <td colspan="8" class="p-10 text-center text-sm opacity-70">Belum ada sertifikat
                                    terbit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION STRIP --}}
            <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="text-sm opacity-70">
                    Showing <span class="font-semibold">{{ $issues->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold">{{ $issues->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold">{{ $issues->total() }}</span> results
                </div>
                <div>{{ $issues->withQueryString()->links() }}</div>
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
                            const title = form?.dataset.title || 'issue ini';
                            Swal.fire({
                                title: 'Hapus certificate issue?',
                                html: `Issue <b>${title}</b> akan dihapus permanen.`,
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
