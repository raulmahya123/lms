@extends('layouts.admin')
@section('title','Payments')

@section('content')
<form method="GET" class="mb-4 flex items-center gap-2">
  <select name="status" class="border rounded px-3 py-2">
    <option value="">— Status —</option>
    @foreach(['pending','paid','failed'] as $st)
      <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
  @if(request('status')) <a href="{{ route('admin.payments.index') }}" class="underline text-sm">Reset</a> @endif
</form>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">#</th>
        <th class="p-2 text-left">User</th>
        <th class="p-2 text-left">Plan/Course</th>
        <th class="p-2 text-left">Amount</th>
        <th class="p-2 text-left">Status</th>
        <th class="p-2 text-left">Provider</th>
        <th class="p-2 text-left">Paid At</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $p)
        <tr class="border-t">
          <td class="p-2">{{ $p->id }}</td>
          <td class="p-2">{{ $p->user?->name }} <span class="text-xs text-gray-500">{{ $p->user?->email }}</span></td>
          <td class="p-2">
            @if($p->plan) Plan: {{ $p->plan->name }} @endif
            @if($p->course) <div>Course: {{ $p->course->title }}</div> @endif
          </td>
          <td class="p-2">Rp {{ number_format($p->amount,0,',','.') }}</td>
          <td class="p-2">{{ ucfirst($p->status) }}</td>
          <td class="p-2">{{ $p->provider }}</td>
          <td class="p-2">{{ $p->paid_at }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.payments.show',$p) }}" class="text-blue-600 underline">Detail</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="8">No payments.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $items->withQueryString()->links() }}</div>
@endsection
