@extends('layouts.admin')

@section('title','Payments — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q','status','provider']) ? 'true' : 'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- receipt icon --}}
        <svg class="w-7 h-7 opacity-80" viewBox="0 0 24 24" fill="currentColor">
          <path d="M7 3a2 2 0 0 0-2 2v15l3-2 3 2 3-2 3 2V5a2 2 0 0 0-2-2H7Zm2 5h6a1 1 0 1 1 0 2H9a1 1 0 0 1 0-2Zm0 4h6a1 1 0 1 1 0 2H9a1 1 0 0 1 0-2Z"/>
        </svg>
        Payments
      </h1>
      <p class="text-sm opacity-70">Kelola transaksi: filter status/provider, cek nominal & waktu bayar.</p>
    </div>

    <div class="flex items-center gap-2">
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter icon --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTER FORM --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    {{-- Search (user/email/invoice/ref) --}}
    <div class="md:col-span-1">
      <label class="block text-sm font-medium mb-1">Search</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" value="{{ request('q') }}"
               placeholder="Cari nama/email/invoice…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
        </svg>
      </div>
    </div>

    {{-- Status --}}
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <div class="relative">
        @php $st = request('status'); @endphp
        <select name="status" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="" @selected(!$st)>— Status —</option>
          <option value="pending" @selected($st==='pending')>Pending</option>
          <option value="paid"    @selected($st==='paid')>Paid</option>
          <option value="failed"  @selected($st==='failed')>Failed</option>
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Provider --}}
    <div>
      <label class="block text-sm font-medium mb-1">Provider</label>
      <div class="relative">
        @php
          $pv = request('provider');
          $providers = $providers ?? \App\Models\Payment::query()
            ->select('provider')->whereNotNull('provider')->distinct()->pluck('provider')->sort()->values();
        @endphp
        <select name="provider" class="w-full border rounded-xl pl-3 pr-8 py-2">
          <option value="" @selected(!$pv)>— All Providers —</option>
          @foreach($providers as $pr)
            <option value="{{ $pr }}" @selected($pv===$pr)>{{ strtoupper($pr) }}</option>
          @endforeach
        </select>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Actions --}}
    <div class="md:col-span-3 flex items-center gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        Apply
      </button>
      @if(request()->hasAny(['q','status','provider']))
        <a href="{{ route('admin.payments.index') }}"
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
        <span class="font-semibold">{{ $items->total() }}</span>
        <span class="opacity-70">payments found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $items->currentPage() }} / {{ $items->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left">User</th>
            <th class="p-3 text-left">Plan / Course</th>
            <th class="p-3 text-left w-32">Amount</th>
            <th class="p-3 text-left w-28">Status</th>
            <th class="p-3 text-left w-28">Provider</th>
            <th class="p-3 text-left w-40">Paid At</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($items as $p)
            @php
              $paidAt = $p->paid_at ? \Illuminate\Support\Carbon::parse($p->paid_at)->timezone(config('app.timezone','UTC'))->format('Y-m-d H:i') : '—';
              $statusBadge = match($p->status){
                'paid'    => ['bg-green-100','text-green-800','Paid'],
                'pending' => ['bg-amber-100','text-amber-800','Pending'],
                default   => ['bg-red-100','text-red-800','Failed'],
              };
            @endphp
            <tr class="border-t">
              <td class="p-3">
                <div class="font-medium">{{ $p->user?->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $p->user?->email ?? '—' }}</div>
              </td>
              <td class="p-3">
                @if($p->plan)   <div><span class="text-xs opacity-70">Plan:</span> {{ $p->plan->name }}</div>@endif
                @if($p->course) <div><span class="text-xs opacity-70">Course:</span> {{ $p->course->title }}</div>@endif
                @if(!$p->plan && !$p->course) <span class="text-xs opacity-60">—</span> @endif
              </td>
              <td class="p-3">Rp {{ number_format((float)$p->amount, 0, ',', '.') }}</td>
              <td class="p-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                  {{ $statusBadge[2] }}
                </span>
              </td>
              <td class="p-3">
                @if($p->provider)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800">
                    {{ strtoupper($p->provider) }}
                  </span>
                @else
                  <span class="text-xs opacity-60">—</span>
                @endif
              </td>
              <td class="p-3">{{ $paidAt }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  @if(Route::has('admin.payments.show'))
                    <a href="{{ route('admin.payments.show',$p) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Detail">
                      {{-- eye icon --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/></svg>
                      View
                    </a>
                  @endif
                  @if(Route::has('admin.payments.edit'))
                    <a href="{{ route('admin.payments.edit',$p) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                      Edit
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="p-10 text-center text-sm opacity-70">
                No payments found.
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
        <span class="font-semibold">{{ $items->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $items->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $items->total() }}</span>
        results
      </div>
      <div>
        {{ $items->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
