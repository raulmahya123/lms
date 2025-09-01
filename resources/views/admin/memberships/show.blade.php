@extends('layouts.admin')
@section('title','Membership Detail')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-3xl">
  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-gray-500">User</div>
      <div class="font-medium">{{ $membership->user?->name }}</div>
      <div class="text-sm text-gray-600">{{ $membership->user?->email }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Plan</div>
      <div class="font-medium">{{ $membership->plan?->name }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Status</div>
      <div class="font-medium">{{ ucfirst($membership->status) }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Activated / Expires</div>
      <div>{{ $membership->activated_at }} — {{ $membership->expires_at }}</div>
    </div>
  </div>

  <hr class="my-6">

  <form method="POST" action="{{ route('admin.memberships.update',$membership) }}" class="grid md:grid-cols-3 gap-4">
    @csrf @method('PUT')
    <div>
      <label class="block text-sm">Status</label>
      <select name="status" class="w-full border rounded px-3 py-2">
        @foreach(['pending','active','inactive'] as $st)
          <option value="{{ $st }}" @selected(old('status',$membership->status)===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm">Activated At</label>
      <input type="datetime-local" name="activated_at" class="w-full border rounded px-3 py-2"
             value="{{ old('activated_at', optional($membership->activated_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div>
      <label class="block text-sm">Expires At</label>
      <input type="datetime-local" name="expires_at" class="w-full border rounded px-3 py-2"
             value="{{ old('expires_at', optional($membership->expires_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="md:col-span-3">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="{{ route('admin.memberships.index') }}" class="px-4 py-2 rounded border ml-2">Back</a>
    </div>
  </form>
</div>
@endsection
