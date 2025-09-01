@extends('layouts.admin')
@section('title','Memberships')

@section('content')
<form method="GET" class="mb-4 flex items-center gap-2">
  <select name="status" class="border rounded px-3 py-2">
    <option value="">— Status —</option>
    @foreach(['pending','active','inactive'] as $st)
      <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
  @if(request('status')) <a href="{{ route('admin.memberships.index') }}" class="underline text-sm">Reset</a> @endif
</form>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">#</th>
        <th class="p-2 text-left">User</th>
        <th class="p-2 text-left">Plan</th>
        <th class="p-2 text-left">Status</th>
        <th class="p-2 text-left">Activated</th>
        <th class="p-2 text-left">Expires</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $m)
        <tr class="border-t">
          <td class="p-2">{{ $m->id }}</td>
          <td class="p-2">{{ $m->user?->name }} <span class="text-gray-500 text-xs">{{ $m->user?->email }}</span></td>
          <td class="p-2">{{ $m->plan?->name }}</td>
          <td class="p-2">{{ ucfirst($m->status) }}</td>
          <td class="p-2">{{ $m->activated_at }}</td>
          <td class="p-2">{{ $m->expires_at }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.memberships.show',$m) }}" class="text-blue-600 underline">Detail</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="7">No memberships.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $items->withQueryString()->links() }}</div>
@endsection
