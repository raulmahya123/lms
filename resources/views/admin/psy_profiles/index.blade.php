@extends('layouts.admin')
@section('title', 'Psych Profiles')

@section('content')
    <div x-data="{ 
            q: @js(request('q') ?? ''), 
            showFilters: {{ request()->hasAny(['q','track','test_id']) ? 'true' : 'false' }} 
        }" 
        class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    {{-- list icon --}}
                    <svg class="w-7 h-7 text-blue-700/90" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    Psych Profiles
                </h1>
                <p class="text-sm opacity-70">Kelola profil psikometri, rentang skor, dan keterkaitan ke tes.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.psy-profiles.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/>
                    </svg>
                    New
                </a>
                <button type="button" @click="showFilters=!showFilters"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
                    {{-- filter icon --}}
                    <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/>
                    </svg>
                    Filters
                </button>
            </div>
        </div>

        {{-- FLASH --}}
        @if (session('ok'))
            <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                {{ session('ok') }}
            </div>
        @endif

        {{-- FILTER FORM (match Memberships style; fitur: Track, Test, Keyword) --}}
        <form method="GET" x-show="showFilters" x-transition
              class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
            {{-- Track --}}
            <div>
                <label class="block text-sm font-medium mb-1">Track</label>
                <div class="relative">
                    @php
                        $trackReq = request('track');
                        // Ambil daftar track dari database jika tidak disupply controller
                        $tracksList = $tracks ?? \App\Models\PsyTest::query()
                            ->select('track')->distinct()->pluck('track')->filter()->values();
                    @endphp
                    <select name="track" class="w-full border rounded-xl pl-3 pr-8 py-2">
                        <option value="">— All Tracks —</option>
                        @foreach ($tracksList as $t)
                            <option value="{{ $t }}" @selected($trackReq === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            {{-- Test --}}
            <div>
                <label class="block text-sm font-medium mb-1">Test</label>
                <div class="relative">
                    @php
                        $testIdReq = request('test_id');
                        $testsList = $tests ?? \App\Models\PsyTest::orderBy('name')->get(['id','name','track']);
                    @endphp
                    <select name="test_id" class="w-full border rounded-xl pl-3 pr-8 py-2">
                        <option value="">— All Tests —</option>
                        @foreach ($testsList as $t)
                            <option value="{{ $t->id }}" @selected($testIdReq == $t->id)>{{ $t->name }} ({{ $t->track }})</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            {{-- Keyword --}}
            <div>
                <label class="block text-sm font-medium mb-1">Keyword</label>
                <div class="relative">
                    <input type="text" name="q" x-model="q" value="{{ request('q') }}"
                           placeholder="Cari key / name / description…"
                           class="w-full border rounded-xl pl-10 pr-3 py-2">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
                    </svg>
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-3 flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    Apply
                </button>
                @if (request()->hasAny(['q','track','test_id']))
                    <a href="{{ route('admin.psy-profiles.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- TABLE CARD --}}
        <div class="rounded-2xl border bg-white overflow-hidden">
            {{-- header strip --}}
            <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                <div class="text-sm">
                    <span class="font-semibold">{{ $profiles->total() }}</span>
                    <span class="opacity-70">profiles found</span>
                </div>
                <div class="text-xs opacity-70">Page {{ $profiles->currentPage() }} / {{ $profiles->lastPage() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left">Test</th>
                            <th class="p-3 text-left">Key</th>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Range</th>
                            <th class="p-3 text-left">Updated</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse ($profiles as $p)
                            @php
                                $titleForDelete = trim(($p->name ?: $p->key ?: 'Profile') . ' — ' . ($p->test->name ?? '-'));
                            @endphp
                            <tr class="border-t">
                                <td class="p-3">
                                    <div class="font-medium">{{ $p->test->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">({{ $p->test->track ?? '-' }})</div>
                                </td>
                                <td class="p-3 font-mono">{{ $p->key }}</td>
                                <td class="p-3">{{ $p->name }}</td>
                                <td class="p-3">{{ $p->min_total }} – {{ $p->max_total }}</td>
                                <td class="p-3 text-gray-500">{{ optional($p->updated_at)->diffForHumans() }}</td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (Route::has('admin.psy-profiles.show'))
                                            <a href="{{ route('admin.psy-profiles.show', $p) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                               title="View">
                                                {{-- eye icon --}}
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/>
                                                </svg>
                                                View
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.psy-profiles.edit', $p) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                           title="Edit">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.psy-profiles.destroy', $p) }}"
                                              class="inline js-delete-form"
                                              data-title="{{ $titleForDelete }}">
                                            @csrf @method('DELETE')

                                            <button type="button"
                                                    class="js-delete-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition"
                                                    title="Delete">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-sm opacity-70">
                                    Belum ada profile.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- pagination strip --}}
            <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
                <div class="opacity-70">
                    Showing
                    <span class="font-semibold">{{ $profiles->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-semibold">{{ $profiles->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold">{{ $profiles->total() }}</span>
                    results
                </div>
                <div>
                    {{ $profiles->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function () {
                function bindDeleteButtons() {
                    document.querySelectorAll('.js-delete-btn').forEach(btn => {
                        if (btn.dataset.bound) return;
                        btn.dataset.bound = '1';

                        btn.addEventListener('click', (e) => {
                            const form  = e.currentTarget.closest('form.js-delete-form');
                            const title = form?.dataset.title || 'profile ini';

                            Swal.fire({
                                title: 'Hapus profile?',
                                html: `Data <b>${title}</b> akan dihapus permanen.`,
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
