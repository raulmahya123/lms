@extends('layouts.admin')
@section('title','Coupons')

@section('content')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex items-center gap-2">
    <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Search code...">
    <button class="px-3 py-2 bg-gray-800 text-white rounded">Search</button>
    @if(request('q')) <a href="{{ route('admin.coupons.index') }}" class="underline text-sm">Reset</a> @endif
  </form>

  <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Coupon</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">#</th>
        <th class="p-2 text-left">Code</th>
        <th class="p-2 text-left">Discount</th>
        <th class="p-2 text-left">Valid</th>
        <th class="p-2 text-left">Usage</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($coupons as $c)
        <tr class="border-t">
          <td class="p-2">{{ $c->id }}</td>
          <td class="p-2 font-mono">{{ $c->code }}</td>
          <td class="p-2">{{ $c->discount_percent }}%</td>
          <td class="p-2">
            {{ $c->valid_from ?? '—' }} — {{ $c->valid_until ?? '—' }}
          </td>
          <td class="p-2">{{ $c->redemptions_count ?? $c->redemptions()->count() }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.coupons.edit',$c) }}" class="text-blue-600 underline">Edit</a>
            <form method="POST" action="{{ route('admin.coupons.destroy',$c) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 underline" onclick="return confirm('Delete coupon?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="6">No coupons.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $coupons->withQueryString()->links() }}</div>
@endsection
