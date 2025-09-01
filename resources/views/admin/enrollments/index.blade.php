@extends('layouts.admin')
@section('title','Enrollments')

@section('content')
<form method="GET" class="mb-4 flex items-center gap-2">
  <select name="status" class="border rounded px-3 py-2">
    <option value="">— Status —</option>
    @foreach(['pending','active','inactive'] as $st)
      <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
  @if(request('status')) <a href="{{ route('admin.enrollments.index') }}" class="underline text-sm">Reset</a> @endif
</form>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2">#</th>
        <th class="p-2 text-left">User</th>
        <th class="p-2 text-left">Course</th>
        <th class="p-2 text-left">Status</th>
        <th class="p-2 text-left">Activated</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $e)
        <tr class="border-t">
          <td class="p-2">{{ $e->id }}</td>
          <td class="p-2">{{ $e->user?->name }} <span class="text-xs text-gray-500">{{ $e->user?->email }}</span></td>
          <td class="p-2">{{ $e->course?->title }}</td>
          <td class="p-2">{{ ucfirst($e->status) }}</td>
          <td class="p-2">{{ $e->activated_at }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.enrollments.show',$e) }}" class="text-blue-600 underline">Detail</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="6">No enrollments.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $items->withQueryString()->links() }}</div>
@endsection
