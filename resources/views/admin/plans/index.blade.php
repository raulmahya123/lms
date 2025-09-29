@extends('layouts.admin')

@section('title', 'Plans — BERKEMAH')

@section('content')
    <div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q', 'period', 'min', 'max']) ? 'true' : 'false' }} }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    {{-- cube icon --}}
                    <svg class="w-7 h-7 opacity-80" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2 3 7v10l9 5 9-5V7l-9-5Zm0 2.2 6.5 3.6L12 11 5.5 7.8 12 4.2ZM5 9.1l6 3.3v7.6L5 16.7V9.1Zm14 0v7.6l-6 3.3v-7.6l6-3.3Z" />
                    </svg>
                    Plans
                </h1>
                <p class="text-sm opacity-70">Kelola paket berlangganan: harga, periode, cakupan course, & jumlah member.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.plans.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                    </svg>
                    New Plan
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

        {{-- FILTER FORM --}}
        <form method="GET" x-show="showFilters" x-transition
            class="rounded-2xl border bg-white p-4 grid md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="q" x-model="q" value="{{ request('q') }}"
                        placeholder="Cari nama plan…" class="w-full border rounded-xl pl-10 pr-3 py-2">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z" />
                    </svg>
                </div>
            </div>

            {{-- Period --}}
            <div>
                <label class="block text-sm font-medium mb-1">Period</label>
                <div class="relative">
                    @php $period = request('period'); @endphp
                    <select name="period" class="w-full border rounded-xl pl-10 pr-8 py-2">
                        <option value="" @selected(!$period)>All</option>
                        <option value="monthly" @selected($period === 'monthly')>Monthly</option>
                        <option value="yearly" @selected($period === 'yearly')>Yearly</option>
                        <option value="lifetime" @selected($period === 'lifetime')>Lifetime</option>
                    </select>
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z" />
                    </svg>
                    <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            {{-- Price range --}}
            <div>
                <label class="block text-sm font-medium mb-1">Price range</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="min" value="{{ request('min') }}" placeholder="Min"
                        class="w-1/2 border rounded-xl px-3 py-2">
                    <input type="number" name="max" value="{{ request('max') }}" placeholder="Max"
                        class="w-1/2 border rounded-xl px-3 py-2">
                </div>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-4 flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    Apply
                </button>
                @if (request()->hasAny(['q', 'period', 'min', 'max']))
                    <a href="{{ route('admin.plans.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- FLASH (opsional) --}}
        @if (session('ok'))
            <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                {{ session('ok') }}
            </div>
        @endif

        {{-- TABLE CARD --}}
        <div class="rounded-2xl border bg-white overflow-hidden">
            {{-- header strip --}}
            <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                <div class="text-sm">
                    <span class="font-semibold">{{ $plans->total() }}</span>
                    <span class="opacity-70">plans found</span>
                </div>
                <div class="text-xs opacity-70">Page {{ $plans->currentPage() }} / {{ $plans->lastPage() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left w-28">Price</th>
                            <th class="p-3 text-left w-28">Period</th>
                            <th class="p-3 text-left w-28">Courses</th>
                            <th class="p-3 text-left w-28">Members</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse($plans as $p)
                            <tr class="border-t">
                                <td class="p-3 font-medium">{{ $p->name }}</td>
                                <td class="p-3">Rp {{ number_format((float) $p->price, 0, ',', '.') }}</td>
                                <td class="p-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($p->period) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-800">
                                        {{ $p->plan_courses_count ?? $p->planCourses()->count() }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-800">
                                        {{ $p->memberships_count ?? $p->memberships()->count() }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (Route::has('admin.plans.show'))
                                            <a href="{{ route('admin.plans.show', $p) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                                title="View">
                                                {{-- eye icon --}}
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" />
                                                </svg>
                                                View
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.plans.edit', $p) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="Edit">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.plans.destroy', $p) }}"
                                            class="inline js-delete-form"
                                            data-title="Plan {{ $p->name ?? '—' }}{{ isset($p->period) ? ' · ' . $p->period : '' }}">
                                            @csrf @method('DELETE')

                                            <button type="button" {{-- penting: button, bukan submit --}}
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
                                <td colspan="7" class="p-10 text-center text-sm opacity-70">
                                    No plans.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- pagination strip --}}
            <div
                class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3 text-sm">
                <div class="opacity-70">
                    Showing
                    <span class="font-semibold">{{ $plans->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-semibold">{{ $plans->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold">{{ $plans->total() }}</span>
                    results
                </div>
                <div>
                    {{ $plans->withQueryString()->links() }}
                </div>
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
                            const title = form?.dataset.title || 'item ini';

                            Swal.fire({
                                title: 'Hapus plan?',
                                html: `Plan <b>${title}</b> akan dihapus permanen.`,
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
