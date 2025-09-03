@extends('layouts.admin')

@section('title','Memberships — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q','status','plan_id']) ? 'true' : 'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- list icon --}}
        <svg class="w-7 h-7 text-blue-700/90" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
        Memberships
      </h1>
      <p class="text-sm opacity-70">Kelola status, masa aktif, dan detail membership.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.memberships.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New
      </a>
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
    {{-- Status --}}
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <div class="relative">
        @php $st = request('status'); @endphp
        <select name="status" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="" @selected(!$st)>— All Status —</option>
          <option value="pending"  @selected($st==='pending')>Pending</option>
          <option value="active"   @selected($st==='active')>Active</option>
          <option value="inactive" @selected($st==='inactive')>Inactive</option>
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Plan --}}
    <div>
      <label class="block text-sm font-medium mb-1">Plan</label>
      <div class="relative">
        @php
          $plansList = $plans ?? \App\Models\Plan::orderBy('name')->get(['id','name']);
        @endphp
        <select name="plan_id" class="w-full border rounded-xl pl-3 pr-8 py-2">
          <option value="">— All Plans —</option>
          @foreach($plansList as $p)
            <option value="{{ $p->id }}" @selected(request('plan_id')==$p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
      </div>
    </div>

    {{-- Search user/email --}}
    <div>
      <label class="block text-sm font-medium mb-1">Search user / email</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" value="{{ request('q') }}" placeholder="Ketik nama atau email…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
        </svg>
      </div>
    </div>

    {{-- Actions --}}
    <div class="md:col-span-3 flex items-center gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        Apply
      </button>
      @if(request()->hasAny(['q','status','plan_id']))
        <a href="{{ route('admin.memberships.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- FLASH --}}
  @if(session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
      {{ session('ok') }}
    </div>
  @endif

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    {{-- header strip --}}
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $items->total() }}</span>
        <span class="opacity-70">memberships found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $items->currentPage() }} / {{ $items->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-16">#</th>
            <th class="p-3 text-left">User</th>
            <th class="p-3 text-left">Plan</th>
            <th class="p-3 text-left w-28">Status</th>
            <th class="p-3 text-left w-40">Activated</th>
            <th class="p-3 text-left w-40">Expires</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($items as $m)
            @php
              $from = optional($m->activated_at)?->format('Y-m-d H:i');
              $to   = optional($m->expires_at)?->format('Y-m-d H:i');
              $badge = match($m->status) {
                'active'   => ['bg-green-100','text-green-800','Active'],
                'inactive' => ['bg-amber-100','text-amber-800','Inactive'],
                default    => ['bg-gray-100','text-gray-800', ucfirst($m->status ?? 'pending')],
              };
            @endphp
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $m->id }}</td>
              <td class="p-3">
                <div class="font-medium">{{ $m->user?->name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $m->user?->email ?? '—' }}</div>
              </td>
              <td class="p-3">{{ $m->plan?->name ?? '—' }}</td>
              <td class="p-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                  {{ $badge[2] }}
                </span>
              </td>
              <td class="p-3">{{ $from ?: '—' }}</td>
              <td class="p-3">{{ $to ?: '—' }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  @if(Route::has('admin.memberships.show'))
                    <a href="{{ route('admin.memberships.show',$m) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="View">
                      {{-- eye icon --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/></svg>
                      View
                    </a>
                  @endif
                  <a href="{{ route('admin.memberships.edit',$m) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.memberships.destroy',$m) }}"
                        onsubmit="return confirm('Delete this membership?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="p-10 text-center text-sm opacity-70">
                Belum ada membership.
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
