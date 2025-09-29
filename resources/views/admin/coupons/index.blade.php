@extends('layouts.admin')

@section('title', 'Coupons — BERKEMAH')

@section('content')
    <div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q', 'status']) ? 'true' : 'false' }} }" class="space-y-6">

        {{-- HEADER / ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
                    {{-- ticket icon --}}
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3 7a2 2 0 0 1 2-2h3a2 2 0 1 0 4 0h7a2 2 0 0 1 2 2v2a2 2 0 1 1 0 4v2a2 2 0 0 1-2 2h-7a2 2 0 1 0-4 0H5a2 2 0 0 1-2-2V7Z" />
                    </svg>
                    Coupons
                </h1>
                <p class="text-sm opacity-70">Kelola kupon: cari kode, filter status aktif/kadaluarsa/dijadwalkan, lihat
                    pemakaian, edit & hapus.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.coupons.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z" />
                    </svg>
                    New Coupon
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
            class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
            {{-- Search --}}
            <div>
                <label class="block text-sm font-medium mb-1">Search code</label>
                <div class="relative">
                    <input name="q" x-model="q" value="{{ request('q') }}" placeholder="Search code…"
                        class="w-full border rounded-xl pl-10 pr-3 py-2">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z" />
                    </svg>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <div class="relative">
                    @php $status = request('status'); @endphp
                    <select name="status" class="w-full border rounded-xl pl-10 pr-8 py-2">
                        <option value="" @selected(!$status)>All</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="expired" @selected($status === 'expired')>Expired</option>
                        <option value="scheduled" @selected($status === 'scheduled')>Scheduled</option>
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

            {{-- Apply / Reset --}}
            <div class="flex items-end gap-2">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                    Apply
                </button>
                @if (request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.coupons.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- ALERT (opsional) --}}
        @if (session('ok'))
            <div class="p-3 bg-green-100 text-green-700 rounded-xl">
                {{ session('ok') }}
            </div>
        @endif

        {{-- TABLE CARD --}}
        <div class="rounded-2xl border bg-white overflow-hidden">
            {{-- header strip --}}
            <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                <div class="text-sm">
                    <span class="font-semibold">{{ $coupons->total() }}</span>
                    <span class="opacity-70">coupons found</span>
                </div>
                <div class="text-xs opacity-70">Page {{ $coupons->currentPage() }} / {{ $coupons->lastPage() }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left">Code</th>
                            <th class="p-3 text-left w-28">Discount</th>
                            <th class="p-3 text-left">Valid</th>
                            <th class="p-3 text-left w-28">Usage</th>
                            <th class="p-3 text-center w-44">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:hover]:bg-gray-50">
                        @forelse($coupons as $c)
                            @php
                                $from = $c->valid_from ? \Illuminate\Support\Carbon::parse($c->valid_from) : null;
                                $to = $c->valid_until ? \Illuminate\Support\Carbon::parse($c->valid_until) : null;
                                $now = now();
                                $isActive = (!$from || $from->lte($now)) && (!$to || $to->gte($now));
                                $isExpired = $to && $to->lt($now);
                                $statusBadge = $isActive
                                    ? ['bg-green-100', 'text-green-800', 'Active']
                                    : ($isExpired
                                        ? ['bg-red-100', 'text-red-800', 'Expired']
                                        : ['bg-yellow-100', 'text-yellow-800', 'Scheduled']);
                                $used =
                                    $c->redemptions_count ??
                                    ($c->relationLoaded('redemptions')
                                        ? $c->redemptions->count()
                                        : $c->redemptions()->count());
                                $limit = $c->usage_limit ?? '∞';
                            @endphp
                            <tr class="border-t">
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono tracking-wide">{{ $c->code }}</span>
                                        @if ($c->max_discount_amount)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded border text-gray-600">Cap:
                                                {{ number_format($c->max_discount_amount, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3">
                                    @if (!is_null($c->discount_percent))
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs">
                                            {{ rtrim(rtrim(number_format($c->discount_percent, 2), '0'), '.') }}%
                                        </span>
                                    @elseif(!is_null($c->discount_amount))
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs">
                                            Rp {{ number_format($c->discount_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-xs opacity-60">—</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs {{ $statusBadge[0] }} {{ $statusBadge[1] }}">{{ $statusBadge[2] }}</span>
                                        <span class="text-xs text-gray-500">
                                            {{ $from?->format('Y-m-d H:i') ?: '—' }} —
                                            {{ $to?->format('Y-m-d H:i') ?: '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="text-sm">{{ $used }} / {{ $limit }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (Route::has('admin.coupons.show'))
                                            <a href="{{ route('admin.coupons.show', $c) }}"
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
                                        <a href="{{ route('admin.coupons.edit', $c) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                                            title="Edit">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $c) }}"
                                            class="inline js-delete-form"
                                            data-title="Coupon {{ $c->code ?? '—' }}{{ isset($c->discount) ? ' · ' . $c->discount . (($c->type ?? '') === 'percent' ? '%' : '') : '' }}">
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
                                <td colspan="6" class="p-10 text-center text-sm opacity-70">
                                    Belum ada coupon.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination strip --}}
            <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="text-sm opacity-70">
                    Showing
                    <span class="font-semibold">{{ $coupons->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-semibold">{{ $coupons->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold">{{ $coupons->total() }}</span>
                    results
                </div>
                <div>
                    {{ $coupons->withQueryString()->links() }}
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
