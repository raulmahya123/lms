@extends('app.layouts.base')
@section('title','Payments')
@section('content')
<h1 class="text-xl font-semibold mb-4">Riwayat Pembayaran</h1>
<div class="bg-white border rounded overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">Ref</th>
        <th class="p-2 text-left">Item</th>
        <th class="p-2">Jumlah</th>
        <th class="p-2">Status</th>
        <th class="p-2"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $p)
        <tr class="border-t">
          <td class="p-2">{{ $p->reference }}</td>
          <td class="p-2">
            @if($p->plan) Plan: {{ $p->plan->name }}
            @elseif($p->course) Course: {{ $p->course->title }}
            @else — @endif
          </td>
          <td class="p-2 text-center">Rp {{ number_format($p->amount,0,',','.') }}</td>
          <td class="p-2 text-center">
            <span class="px-2 py-0.5 rounded text-xs {{ $p->status==='paid'?'bg-emerald-100 text-emerald-800':'bg-amber-100 text-amber-800' }}">{{ $p->status }}</span>
          </td>
          <td class="p-2 text-right">
            <a href="{{ route('app.payments.show',$p) }}" class="text-blue-700 hover:underline">Detail</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="5">Belum ada pembayaran.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
