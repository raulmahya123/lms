@extends('layouts.admin')
@section('title','Payments')

@section('content')

{{-- FILTER BAR --}}
<form method="GET" class="mb-6">
  <div class="bg-white border rounded-2xl p-4 flex flex-wrap items-center gap-3 shadow-sm">
    {{-- Status Select --}}
    <div class="relative">
      <select name="status" class="pl-9 pr-4 py-2 border rounded-xl focus:ring-blue-600 focus:border-blue-600">
        <option value="">— Status —</option>
        @foreach(['pending','paid','failed'] as $st)
          <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
      <div class="absolute left-3 top-2.5 text-gray-400">
        {{-- Filter Icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
        </svg>
      </div>
    </div>

    {{-- Buttons --}}
    <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">
      {{-- Filter icon --}}
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
      </svg>
      <span>Apply</span>
    </button>

    @if(request('status'))
      <a href="{{ route('admin.payments.index') }}"
         class="flex items-center gap-1 px-3 py-2 border rounded-xl text-sm hover:bg-gray-50">
        {{-- Reset icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Reset
      </a>
    @endif
  </div>
</form>

{{-- TABLE --}}
<div class="rounded-2xl border bg-white shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100 sticky top-0">
      <tr>
        <th class="p-3">#</th>
        <th class="p-3 text-left">User</th>
        <th class="p-3 text-left">Plan / Course</th>
        <th class="p-3 text-left">Amount</th>
        <th class="p-3 text-left">Status</th>
        <th class="p-3 text-left">Provider</th>
        <th class="p-3 text-left">Paid At</th>
        <th class="p-3 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $p)
        <tr class="border-t hover:bg-gray-50">
          <td class="p-3">{{ $p->id }}</td>
          <td class="p-3">
            {{ $p->user?->name }}
            <div class="text-xs text-gray-500">{{ $p->user?->email }}</div>
          </td>
          <td class="p-3">
            @if($p->plan) <div>Plan: {{ $p->plan->name }}</div>@endif
            @if($p->course) <div>Course: {{ $p->course->title }}</div>@endif
          </td>
          <td class="p-3">Rp {{ number_format($p->amount,0,',','.') }}</td>
          <td class="p-3">
            @if($p->status==='paid')
              <span class="px-2 py-1 text-xs rounded-full bg-green-50 text-green-700 border border-green-200">Paid</span>
            @elseif($p->status==='pending')
              <span class="px-2 py-1 text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
            @else
              <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-700 border border-red-200">Failed</span>
            @endif
          </td>
          <td class="p-3">{{ $p->provider }}</td>
          <td class="p-3">{{ $p->paid_at }}</td>
          <td class="p-3 text-center">
            <a href="{{ route('admin.payments.show',$p) }}"
               class="inline-flex items-center gap-1 px-2 py-1 border rounded-lg text-blue-600 hover:bg-gray-50 text-xs">
              {{-- Eye Icon --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              Detail
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="p-6 text-center">
            <div class="flex flex-col items-center gap-2 text-gray-500">
              {{-- Empty icon --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2h6v2a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v2h-2"/>
              </svg>
              <p>No payments found.</p>
              @if(request()->hasAny(['status']) && request('status'))
                <a href="{{ route('admin.payments.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border rounded-xl hover:bg-gray-50">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                  Reset Filters
                </a>
              @endif
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- PAGINATION --}}
@if($items->hasPages())
  <div class="mt-4 bg-white border rounded-2xl p-3 flex items-center justify-between text-sm">
    <div class="text-gray-500">
      Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} results
    </div>
    <div>
      {{ $items->withQueryString()->links() }}
    </div>
  </div>
@endif

@endsection
