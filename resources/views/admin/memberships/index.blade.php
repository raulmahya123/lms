@extends('layouts.admin')
@section('title','Memberships')

@section('content')
{{-- HEADER + CTA --}}
<div class="mb-4 flex items-center justify-between">
  <div class="flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
    </svg>
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide">Memberships</h1>
      <p class="text-xs opacity-70">Kelola status, masa aktif, dan detail membership.</p>
    </div>
  </div>

  <a href="{{ route('admin.memberships.create') }}"
     class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    New
  </a>
</div>

{{-- FILTER BAR (opsional) --}}
<form method="GET" class="mb-6">
  <div class="bg-white border rounded-2xl p-4 flex flex-wrap items-center gap-3 shadow-sm">
    <div class="relative">
      <select name="status" class="pl-9 pr-4 py-2 border rounded-xl focus:ring-blue-600 focus:border-blue-600">
        <option value="">— All Status —</option>
        @foreach(['pending','active','inactive'] as $st)
          <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
      <div class="absolute left-3 top-2.5 text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
        </svg>
      </div>
    </div>

    <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
      </svg>
      Apply
    </button>

    @if(request('status'))
      <a href="{{ route('admin.memberships.index') }}"
         class="flex items-center gap-1 px-3 py-2 border rounded-xl text-sm hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Reset
      </a>
    @endif
  </div>
</form>

{{-- FLASH --}}
@if(session('ok'))
  <div class="mb-4 border border-green-200 bg-green-50 text-green-800 rounded-2xl px-4 py-3">
    {{ session('ok') }}
  </div>
@endif

{{-- TABLE --}}
<div class="rounded-2xl border bg-white shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100 sticky top-0">
      <tr class="text-gray-700">
        <th class="p-3">#</th>
        <th class="p-3 text-left">User</th>
        <th class="p-3 text-left">Plan</th>
        <th class="p-3 text-left">Status</th>
        <th class="p-3 text-left">Activated</th>
        <th class="p-3 text-left">Expires</th>
        <th class="p-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $m)
        <tr class="border-t hover:bg-gray-50">
          <td class="p-3 font-medium">#{{ $m->id }}</td>
          <td class="p-3">
            <div class="font-medium">{{ $m->user?->name ?? '-' }}</div>
            <div class="text-xs text-gray-500">{{ $m->user?->email ?? '-' }}</div>
          </td>
          <td class="p-3">{{ $m->plan?->name ?? '-' }}</td>
          <td class="p-3">
            <span class="px-2 py-1 text-xs rounded-full 
              @if($m->status==='active') bg-green-50 text-green-700 border border-green-200
              @elseif($m->status==='inactive') bg-amber-50 text-amber-700 border border-amber-200
              @else bg-gray-100 text-gray-700 border border-gray-200 @endif">
              {{ ucfirst($m->status) }}
            </span>
          </td>
          <td class="p-3">{{ optional($m->activated_at)->format('Y-m-d H:i') ?? '—' }}</td>
          <td class="p-3">{{ optional($m->expires_at)->format('Y-m-d H:i') ?? '—' }}</td>
          <td class="p-3">
            <div class="flex items-center justify-center gap-1">
              <a href="{{ route('admin.memberships.show',$m) }}"
                 class="inline-flex items-center gap-1 px-2 py-1 border rounded-lg hover:bg-gray-50 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  <circle cx="12" cy="12" r="3" stroke-width="2"/>
                </svg>
                View
              </a>
              <a href="{{ route('admin.memberships.edit',$m) }}"
                 class="inline-flex items-center gap-1 px-2 py-1 border rounded-lg hover:bg-gray-50 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4l9-9-4-4-9 9z"/>
                </svg>
                Edit
              </a>
              <form method="POST" action="{{ route('admin.memberships.destroy',$m) }}" onsubmit="return confirm('Delete this membership?')">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-1 px-2 py-1 border rounded-lg hover:bg-red-50 text-xs text-red-600 border-red-200">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-6 3h8"/>
                  </svg>
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="p-6 text-center text-gray-500">
            Belum ada membership.
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
    <div>{{ $items->withQueryString()->links() }}</div>
  </div>
@endif
@endsection
