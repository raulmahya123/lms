{{-- resources/views/admin/psy_attempts/index.blade.php --}}
@extends('layouts.admin')
@section('title','Psych Attempts')

@section('content')
@php
  use Carbon\Carbon;

  $q         = request('q');
  $testId    = request('test_id');
  $status    = request('status');
  $dateFrom  = request('date_from');
  $dateTo    = request('date_to');
@endphp

<div x-data="{ q:@js($q ?? ''), showFilters: {{ request()->hasAny(['q','test_id','status','date_from','date_to'])?'true':'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- chat-bubble icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a8 8 0 1 1-6.32 12.9L3 21l6.1-2.68A8 8 0 0 1 12 2Z"/></svg>
        Psych Attempts
      </h1>
      <p class="text-sm opacity-70">Riwayat pengerjaan tes: user, test, waktu mulai/submit, durasi, dan skor.</p>
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

  {{-- FILTERS / SEARCH --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-5 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Cari</label>
      <div class="relative">
        <input type="text" name="q" x-model="q"
               placeholder="Nama/email/ID attempt/result key"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        {{-- search icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Test</label>
      <div class="relative">
        <select name="test_id" class="w-full border rounded-xl pl-10 pr-3 py-2">
          <option value="">— Semua —</option>
          @foreach($tests as $t)
            <option value="{{ $t->id }}" @selected($testId==$t->id)>{{ $t->title }}</option>
          @endforeach
        </select>
        {{-- book icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3.75 5.25A2.25 2.25 0 0 1 6 3h4.5A2.25 2.25 0 0 1 12.75 5.25v13.5A2.25 2.25 0 0 0 10.5 16.5H6A2.25 2.25 0 0 0 3.75 18.75V5.25Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      <div class="relative">
        <select name="status" class="w-full border rounded-xl pl-10 pr-3 py-2">
          <option value="">— Semua —</option>
          <option value="in-progress" @selected($status==='in-progress')>In Progress</option>
          <option value="submitted"   @selected($status==='submitted')>Submitted</option>
        </select>
        {{-- switch icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M7 7.5h10a4.5 4.5 0 1 1 0 9H7a4.5 4.5 0 1 1 0-9Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Mulai (dari)</label>
      <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border rounded-xl px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Mulai (sampai)</label>
      <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border rounded-xl px-3 py-2">
    </div>

    <div class="md:col-span-5 flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        {{-- funnel icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Terapkan
      </button>
      @if(request()->hasAny(['q','test_id','status','date_from','date_to']) && ($q||$testId||$status||$dateFrom||$dateTo))
        <a href="{{ route('admin.psy-attempts.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          {{-- reset icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a6.75 6.75 0 1 0 6.53 8.4.75.75 0 1 1 1.46.3 8.25 8.25 0 1 1-1.92-7.17V5.25a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5h1.86A6.73 6.73 0 0 0 12 5.25Z"/></svg>
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
        <span class="font-semibold">{{ $attempts->total() }}</span>
        <span class="opacity-70">attempts found</span>

        @if($testId)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Test filter active
          </span>
        @endif
        @if($status)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Status: {{ ucfirst(str_replace('-',' ',$status)) }}
          </span>
        @endif
        @if($q)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
            “{{ $q }}”
          </span>
        @endif
        @if($dateFrom || $dateTo)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 3A.75.75 0 0 1 7.5 2.25h9a.75.75 0 0 1 .75.75V6H6.75V3Z"/></svg>
            Range: {{ $dateFrom ?: '…' }} → {{ $dateTo ?: '…' }}
          </span>
        @endif
      </div>
      <div class="text-xs opacity-70">Page {{ $attempts->currentPage() }} / {{ $attempts->lastPage() }}</div>
    </div>

    {{-- table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="px-4 py-3 text-left w-20">ID</th>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Test</th>
            <th class="px-4 py-3 text-left w-40">Started</th>
            <th class="px-4 py-3 text-left w-40">Submitted</th>
            <th class="px-4 py-3 text-left w-28">Durasi</th>
            <th class="px-4 py-3 text-left w-40">Skor</th>
            <th class="px-4 py-3 text-center w-44">Aksi</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($attempts as $a)
            @php
              $startedAt  = $a->started_at ? Carbon::parse($a->started_at) : null;
              $submittedAt= $a->submitted_at ? Carbon::parse($a->submitted_at) : null;

              $started   = $startedAt ? $startedAt->format('Y-m-d H:i') : null;
              $submitted = $submittedAt ? $submittedAt->format('Y-m-d H:i') : null;

              // Durasi aman: jika belum submit, hitung sampai sekarang
              if ($startedAt) {
                $to   = $submittedAt ?: now();
                $diff = $to->diff($startedAt);
                $dur  = sprintf('%02d:%02d:%02d', ($diff->days*24)+$diff->h, $diff->i, $diff->s);
              } else {
                $dur = '—';
              }

              // Ringkas skor dari JSON
              $scoreSummary = '—';
              $score = $a->score_json;
              if (is_array($score) && !empty($score)) {
                if (isset($score['total'])) {
                  $scoreSummary = 'Total: '.$score['total'];
                } elseif (isset($score['score'])) {
                  $scoreSummary = 'Score: '.$score['score'];
                } else {
                  $k = array_key_first($score);
                  $scoreSummary = $k.': '.$score[$k];
                }
              }
            @endphp
            <tr class="border-t">
              <td class="px-4 py-3 font-semibold text-gray-700">#{{ $a->id }}</td>

              <td class="px-4 py-3">
                <div class="font-semibold">{{ $a->user?->name ?? '—' }}</div>
                @if($a->user?->email)
                  <div class="text-xs opacity-70">{{ $a->user->email }}</div>
                @endif
              </td>

              <td class="px-4 py-3">
                <div class="truncate max-w-[320px]" title="{{ $a->test?->title ?? '' }}">{{ $a->test?->title ?? '—' }}</div>
              </td>

              <td class="px-4 py-3">
                @if($started)
                  <time datetime="{{ $startedAt->toIso8601String() }}" title="{{ $startedAt->format('r') }}">{{ $started }}</time>
                @else
                  —
                @endif
              </td>

              <td class="px-4 py-3">
                @if($submitted)
                  <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <time datetime="{{ $submittedAt->toIso8601String() }}" title="{{ $submittedAt->format('r') }}">{{ $submitted }}</time>
                  </span>
                @else
                  <span class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> —
                  </span>
                @endif
              </td>

              <td class="px-4 py-3"><span class="tabular-nums">{{ $dur }}</span></td>

              <td class="px-4 py-3">
                <div class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                  {{-- chart icon --}}
                  <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M4.5 19.5h15a.75.75 0 0 0 0-1.5H5.25V4.5a.75.75 0 0 0-1.5 0v15Z"/><path d="M8.25 12.75A.75.75 0 0 1 9 12h1.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v-4.5Zm4.5-6a.75.75 0 0 1 .75-.75H15a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1-.75-.75V6.75Zm4.5 3a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v7.5a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1-.75-.75V9.75Z"/></svg>
                  <span class="tabular-nums">{{ $scoreSummary }}</span>
                </div>
              </td>

              <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.psy-attempts.show',$a) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Detail">
                    {{-- eye --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 6.75c-5.25 0-8.25 5.25-8.25 5.25S6.75 17.25 12 17.25 20.25 12 20.25 12 17.25 6.75 12 6.75Zm0 7.5a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z"/></svg>
                    Detail
                  </a>

                  <form method="POST" action="{{ route('admin.psy-attempts.destroy',$a) }}"
                        onsubmit="return confirm('Hapus attempt #{{ $a->id }}?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Hapus">
                      {{-- trash --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="px-4 py-10 text-center text-sm opacity-70">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing
        <span class="font-semibold">{{ $attempts->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $attempts->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $attempts->total() }}</span>
        results
      </div>
      <div>
        {{ $attempts->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
