@extends('app.layouts.base')
@section('title','Memberships — BERKEMAH')
@section('content')
  <h1 class="text-2xl font-bold mb-6">Memberships</h1>
  @if(isset($memberships) && $memberships->count())
    <div class="grid md:grid-cols-3 gap-6">
      @foreach($memberships as $m)
        <div class="p-6 bg-white border rounded-xl">
          <div class="text-xl font-semibold">{{ $m->name }}</div>
          <div class="mt-2 text-gray-600">{{ $m->description ?? '—' }}</div>
        </div>
      @endforeach
    </div>
  @else
    <div class="p-6 bg-white border rounded-xl">Belum ada membership.</div>
  @endif
@endsection
