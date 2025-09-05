@extends('layouts.admin')
@section('title','Psych Tests — BERKEMAH')

@section('content')
@php
  $track = request('track');
  $type  = request('type');
@endphp

<div x-data="{ q:@js(request('q')??''), showFilters: {{ request()->hasAny(['q','track','type'])?'true':'false' }} }" class="space-y-6">

  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a8 8 0 1 1-6.32 12.9L3 21l6.1-2.68A8 8 0 0 1 12 2Z"/></svg>
        Psych Tests
      </h1>
      <p class="text-sm opacity-70">Bank tes psikologi: track, tipe, durasi, dan status aktif.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.psy-tests.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700">New Test</a>
      <button type="button" @click="showFilters=!showFilters" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Filters</button>
    </div>
  </div>

  <form method="GET" x-show="showFilters" x-transition class="rounded-2xl border bg-white p-4 grid md:grid-cols-4 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search name/slug</label>
      <div class="relative">
        <input name="q" x-model="q" placeholder="Search test…" class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Track</label>
      <select name="track" class="w-full border rounded-xl py-2">
        <option value="" @selected(!$track)>All</option>
        @foreach($tracks as $t)
          <option value="{{ $t }}" @selected($track===$t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Type</label>
      <select name="type" class="w-full border rounded-xl py-2">
        <option value="" @selected(!$type)>All</option>
        @foreach($types as $t)
          <option value="{{ $t }}" @selected($type===$t)>{{ strtoupper($t) }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800">Apply</button>
      @if(request()->hasAny(['q','track','type']))
        <a href="{{ route('admin.psy-tests.index') }}" class="px-4 py-2 rounded-xl border hover:bg-gray-50">Reset</a>
      @endif
    </div>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm"><span class="font-semibold">{{ $tests->total() }}</span> tests found</div>
      <div class="text-xs opacity-70">Page {{ $tests->currentPage() }} / {{ $tests->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="p-3 text-left w-16">ID</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left w-28">Track</th>
            <th class="p-3 text-left w-24">Type</th>
            <th class="p-3 text-left w-24">Time</th>
            <th class="p-3 text-left w-24">Active</th>
            <th class="p-3 text-center w-48">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($tests as $t)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $t->id }}</td>
              <td class="p-3">
                <div class="font-medium">{{ $t->name }}</div>
                <div class="text-xs text-gray-500">/{{ $t->slug }}</div>
              </td>
              <td class="p-3 capitalize">{{ $t->track }}</td>
              <td class="p-3 uppercase">{{ $t->type }}</td>
              <td class="p-3">{{ $t->time_limit_min ? $t->time_limit_min.' min' : '—' }}</td>
              <td class="p-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $t->is_active ? 'bg-emerald-100 text-emerald-800':'bg-gray-100 text-gray-700' }}">
                  {{ $t->is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.psy-tests.show',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">View</a>
                  <a href="{{ route('admin.psy-tests.edit',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</a>
                  <a href="{{ route('admin.psy-tests.questions.index',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Questions</a>
                  <form method="POST" action="{{ route('admin.psy-tests.destroy',$t) }}" onsubmit="return confirm('Delete test?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="p-10 text-center text-sm opacity-70">Belum ada test.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing <span class="font-semibold">{{ $tests->firstItem() ?? 0 }}</span>
        to <span class="font-semibold">{{ $tests->lastItem() ?? 0 }}</span>
        of <span class="font-semibold">{{ $tests->total() }}</span> results
      </div>
      <div>{{ $tests->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
