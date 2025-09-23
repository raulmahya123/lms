{{-- resources/views/admin/test_iq/index.blade.php --}}
@extends('layouts.admin')
@section('title','IQ Tests — BERKEMAH')

@section('content')
@php
  $q      = request('q');
  $active = request('active'); // '1' | '0' | null
@endphp

<div x-data="{ q:@js($q??''), showFilters: {{ request()->hasAny(['q','active'])?'true':'false' }} }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- brain icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M7.5 3.75A3.75 3.75 0 0 1 11.25 0h1.5A3.75 3.75 0 0 1 16.5 3.75v.39A3.75 3.75 0 0 1 21 7.5a3.75 3.75 0 0 1-1.76 3.18A3.75 3.75 0 0 1 18 18a3.75 3.75 0 0 1-3.75 3.75H9A3.75 3.75 0 0 1 5.25 18v-.39A3.75 3.75 0 0 1 3 13.5a3.75 3.75 0 0 1 1.76-3.18A3.75 3.75 0 0 1 7.5 4.14v-.39Z"/></svg>
        IQ Tests
      </h1>
      <p class="text-sm opacity-70">Kelola bank tes IQ: status aktif, jumlah soal, durasi, cooldown, dan norma.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.test-iq.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        Buat Baru
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FLASH --}}
  @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3">
      {{ session('success') }}
    </div>
  @endif

  {{-- FILTERS / SEARCH --}}
  <form method="GET" x-show="showFilters" x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" value="{{ $q }}" placeholder="Cari judul/deskripsi…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        {{-- search --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Aktif</label>
      <div class="relative">
        <select name="active" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="">Semua</option>
          <option value="1" @selected($active==='1')>Aktif</option>
          <option value="0" @selected($active==='0')>Nonaktif</option>
        </select>
        {{-- toggle --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M7 7.5h10a4.5 4.5 0 1 1 0 9H7a4.5 4.5 0 1 1 0-9Zm0 1.5a3 3 0 1 0 0 6h10a3 3 0 1 0 0-6H7Z"/>
        </svg>
      </div>
    </div>

    <div class="flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        {{-- funnel --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Apply
      </button>
      @if(request()->hasAny(['q','active']) && (($q!==null && $q!=='') || ($active!==null && $active!=='')))
        <a href="{{ route('admin.test-iq.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          {{-- reset --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a6.75 6.75 0 1 0 6.53 8.4.75.75 0 1 1 1.46.3 8.25 8.25 0 1 1-1.92-7.17V5.25a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5h1.86A6.73 6.73 0 0 0 12 5.25Z"/></svg>
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
        <span class="font-semibold">{{ $tests->total() }}</span>
        <span class="opacity-70">tests found</span>

        @if($q)
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
            {{-- search badge --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
            “{{ $q }}”
          </span>
        @endif
        @if($active !== null && $active!=='')
          <span class="ml-2 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
            {{-- active badge --}}
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25l8.25 4.5v10.5L12 21.75 3.75 17.25V6.75L12 2.25Z"/></svg>
            Aktif: {{ $active==='1' ? 'Ya' : 'Tidak' }}
          </span>
        @endif
      </div>
      <div class="text-xs opacity-70">Page {{ $tests->currentPage() }} / {{ $tests->lastPage() }}</div>
    </div>

    {{-- table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left">Judul</th>
            <th class="p-3 text-left w-24">Aktif</th>
            <th class="p-3 text-left w-24">Soal</th>
            <th class="p-3 text-left w-24">Durasi</th>
            <th class="p-3 text-left w-32">Cooldown</th>     {{-- NEW --}}
            <th class="p-3 text-left w-28">Submissions</th>  {{-- NEW --}}
            <th class="p-3 text-left w-24">Norma</th>        {{-- NEW --}}
            <th class="p-3 text-center w-52">Aksi</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($tests as $t)
            @php
              // Soal
              $qCount = is_array($t->questions ?? null) ? count($t->questions) : 0;

              // Durasi
              $min = (int)($t->duration_minutes ?? 0);
              if ($min) {
                if ($min >= 60) {
                  $h = intdiv($min, 60);
                  $m = $min % 60;
                  $durText = $h.'h'.($m ? ' '.$m.'m' : '');
                } else {
                  $durText = $min.'m';
                }
              } else {
                $durText = '—';
              }

              // Cooldown
              $cdVal  = $t->cooldown_value ?? null;
              $cdUnit = $t->cooldown_unit  ?? null;
              $cdText = ($cdVal !== null && $cdUnit) ? ($cdVal.' '.$cdUnit) : '—';

              // Submissions count
              $subsArr = is_array($t->submissions ?? null) ? $t->submissions : [];
              $subsCnt = is_array($subsArr) ? count($subsArr) : 0;

              // Norma exist?
              $hasNorm = !empty(data_get($t, 'meta.norm_table')) && is_array(data_get($t, 'meta.norm_table'));
            @endphp

            <tr class="border-t">
              <td class="p-3">
                <div class="font-semibold">{{ $t->title }}</div>
                @if(!empty($t->description))
                  <div class="text-xs text-gray-600 max-w-xl">
                    {{ \Illuminate\Support\Str::limit(strip_tags($t->description), 120) }}
                  </div>
                @endif
              </td>

              <td class="p-3">
                <form action="{{ route('admin.test-iq.toggle', $t) }}" method="POST">
                  @csrf
                  <button class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $t->is_active ? 'bg-emerald-100 text-emerald-800':'bg-gray-100 text-gray-700' }}"
                          title="Toggle aktif/nonaktif">
                    <span class="inline-block w-2 h-2 rounded-full {{ $t->is_active ? 'bg-emerald-600':'bg-gray-500' }}"></span>
                    {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                  </button>
                </form>
              </td>

              <td class="p-3">
                <div class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                  <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.75 2.75 7.5 12 12.25 21.25 7.5 12 2.75Zm0 9.5L2.75 17l9.25 4.75L21.25 17 12 12.25Z"/></svg>
                  <span class="tabular-nums">{{ $qCount }}</span>
                </div>
              </td>

              <td class="p-3">
                <span class="tabular-nums">{{ $durText }}</span>
              </td>

              <td class="p-3">
                <span class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                  {{-- clock --}}
                  <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm.75 5.25a.75.75 0 0 0-1.5 0v4.25c0 .2.08.39.22.53l2.5 2.5a.75.75 0 1 0 1.06-1.06l-2.28-2.28V7.5Z"/></svg>
                  <span class="tabular-nums">{{ $cdText }}</span>
                </span>
              </td>

              <td class="p-3">
                <span class="inline-flex items-center gap-2 rounded-xl border px-2 py-1 bg-white">
                  {{-- users --}}
                  <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M7.5 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM3 18a5.25 5.25 0 0 1 10.5 0v.75H3V18Zm13.5-6a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Zm4.5 6a3 3 0 0 0-5.35-1.86 6.72 6.72 0 0 1 1.85 4.11V18.75H21V18Z"/></svg>
                  <span class="tabular-nums">{{ $subsCnt }}</span>
                </span>
              </td>

              <td class="p-3">
                @if($hasNorm)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-800">
                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-600"></span> Ada
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                    <span class="inline-block w-2 h-2 rounded-full bg-gray-500"></span> Tidak
                  </span>
                @endif
              </td>

              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                     href="{{ route('admin.test-iq.edit', $t) }}" title="Edit">
                    {{-- pencil --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/></svg>
                    Edit
                  </a>

                  <form action="{{ route('admin.test-iq.destroy', $t) }}" method="POST"
                        class="inline" onsubmit="return confirm('Hapus test ini?')">
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
            <tr>
              <td colspan="8" class="p-10 text-center text-sm opacity-70">Belum ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing
        <span class="font-semibold">{{ $tests->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $tests->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $tests->total() }}</span>
        results
      </div>
      <div>
        {{ $tests->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
