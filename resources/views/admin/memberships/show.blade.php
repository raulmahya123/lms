@extends('layouts.admin')
@section('title','Membership Detail')

@section('content')
<div class="mb-6 flex items-center gap-2">
  <h1 class="text-2xl font-extrabold">Membership #{{ $membership->id }}</h1>
</div>

<div class="rounded-2xl border bg-white shadow p-6 space-y-4">
  <div>
    <span class="text-sm text-gray-500">User</span>
    <div class="text-lg">{{ $membership->user?->name }}</div>
  </div>

  <div>
    <span class="text-sm text-gray-500">Plan</span>
    <div class="text-lg">{{ $membership->plan?->name }}</div>
  </div>

  <div>
    <span class="text-sm text-gray-500">Status</span>
    <div>
      <span class="px-2 py-1 text-xs rounded-full 
        @if($membership->status==='active') bg-green-50 text-green-700 border border-green-200
        @elseif($membership->status==='expired') bg-red-50 text-red-700 border border-red-200
        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
        {{ ucfirst($membership->status) }}
      </span>
    </div>
  </div>

  <div>
    <span class="text-sm text-gray-500">Expired At</span>
    <div class="text-lg">{{ $membership->expired_at }}</div>
  </div>

  <div class="text-xs text-gray-400">
    Created {{ $membership->created_at }} · Updated {{ $membership->updated_at }}
  </div>
</div>
@endsection
