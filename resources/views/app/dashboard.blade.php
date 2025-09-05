@extends('app.layouts.base')
@section('title','Dashboard')
@section('content')
<h1 class="text-xl font-semibold mb-4">Halo, {{ $user->name }}</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="p-4 bg-white border rounded">
    <div class="text-sm text-gray-500">Courses Saya</div>
    <div class="text-3xl font-bold">{{ $stats['courses_count'] }}</div>
  </div>
  <div class="p-4 bg-white border rounded">
    <div class="text-sm text-gray-500">Membership Aktif</div>
    <div class="text-lg">{{ $stats['active_membership']->plan->name ?? '—' }}</div>
  </div>
  <div class="p-4 bg-white border rounded">
    <div class="text-sm text-gray-500">Attempt Terakhir</div>
    <div class="text-lg">{{ optional($stats['last_attempt'])->score ?? '—' }}</div>
  </div>
</div>
@endsection
