    @extends('app.layouts.base')
@section('title','Plans')
@section('content')
<h1 class="text-xl font-semibold mb-4">Semua Plan</h1>
<div class="grid md:grid-cols-3 gap-4">
@foreach($plans as $p)
  <div class="p-4 bg-white border rounded">
    <div class="font-semibold">{{ $p->name }}</div>
    <div class="text-2xl mt-1">Rp {{ number_format($p->price,0,',','.') }}</div>
    <div class="text-xs text-gray-600 mt-1">{{ $p->period }}</div>
  </div>
@endforeach
</div>
@endsection
