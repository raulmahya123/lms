@extends('layouts.admin')
@section('title','Q&A Threads — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters: {{ request()->hasAny(['q','status']) ? 'true' : 'false' }} }" class="space-y-6">

  {{-- HEADER --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 0 1 9 9c0 1.86-.54 3.59-1.47 5.05L22 21l-3.05-2.47A8.97 8.97 0 0 1 12 21a9 9 0 1 1 0-18Z"/></svg>
        Q&A Threads
      </h1>
      <p class="text-sm opacity-70">Kelola topik diskusi per kursus/pelajaran, tandai jawaban terbaik, dan status.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.qa-threads.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New Thread
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTERS --}}
  <form method="GET" x-show="showFilters" x-transition class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search title</label>
      <div class="relative">
        <input name="q" x-model="q" placeholder="Search thread…" class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Status</label>
      @php $status = request('status'); @endphp
      <div class="relative">
        <select name="status" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="" @selected(!$status)>All</option>
          <option value="open" @selected($status==='open')>Open</option>
          <option value="resolved" @selected($status==='resolved')>Resolved</option>
          <option value="closed" @selected($status==='closed')>Closed</option>
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6Z"/></svg>
        <svg class="w-4 h-4 absolute right-2.5 top-3 opacity-60" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
      </div>
    </div>
    <div class="flex items-end gap-2">
      <button class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">Apply</button>
      @if(request()->hasAny(['q','status']))
        <a href="{{ route('admin.qa-threads.index') }}" class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Reset</a>
      @endif
    </div>
  </form>

  {{-- TABLE --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $threads->total() }}</span>
        <span class="opacity-70">threads found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $threads->currentPage() }} / {{ $threads->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="p-3 text-left">Title</th>
            <th class="p-3 text-left">Course / Lesson</th>
            <th class="p-3 text-left w-28">Replies</th>
            <th class="p-3 text-left w-32">Status</th>
            <th class="p-3 text-center w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($threads as $t)
            <tr class="border-t">
              <td class="p-3">
                <div class="font-medium">{{ $t->title }}</div>
                <div class="text-xs text-gray-500">by {{ $t->user->name ?? '—' }}</div>
              </td>
              <td class="p-3">
                <div class="text-sm">{{ $t->course->title ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ $t->lesson->title ?? '—' }}</div>
              </td>
              <td class="p-3">{{ $t->replies_count ?? $t->replies()->count() }}</td>
              <td class="p-3">
                @php
                  $color = $t->status==='open'?'bg-amber-100 text-amber-800':($t->status==='resolved'?'bg-emerald-100 text-emerald-800':'bg-gray-100 text-gray-700');
                @endphp
                <span class="px-2 py-0.5 rounded-full text-xs {{ $color }}">{{ ucfirst($t->status) }}</span>
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.qa-threads.show',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">View</a>
                  <a href="{{ route('admin.qa-threads.edit',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</a>
                  <form method="POST" action="{{ route('admin.qa-threads.destroy',$t) }}" onsubmit="return confirm('Delete thread?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="p-10 text-center text-sm opacity-70">Belum ada thread.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing <span class="font-semibold">{{ $threads->firstItem() ?? 0 }}</span> to
        <span class="font-semibold">{{ $threads->lastItem() ?? 0 }}</span> of
        <span class="font-semibold">{{ $threads->total() }}</span> results
      </div>
      <div>{{ $threads->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
