@extends('layouts.admin')
@section('title','Create Coupon')

@section('content')
<form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-5 bg-white p-6 rounded shadow max-w-2xl">
  @csrf
  <div>
    <label class="block text-sm font-medium mb-1">Code</label>
    <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code') }}" required>
    @error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Discount (%)</label>
    <input type="number" name="discount_percent" class="w-full border rounded px-3 py-2" value="{{ old('discount_percent',10) }}" min="0" max="100" required>
    @error('discount_percent') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">Valid From</label>
      <input type="datetime-local" name="valid_from" class="w-full border rounded px-3 py-2" value="{{ old('valid_from') }}">
    </div>
    <div>
      <label class="block text-sm">Valid Until</label>
      <input type="datetime-local" name="valid_until" class="w-full border rounded px-3 py-2" value="{{ old('valid_until') }}">
    </div>
  </div>
  <div>
    <label class="block text-sm">Usage Limit</label>
    <input type="number" name="usage_limit" class="w-full border rounded px-3 py-2" value="{{ old('usage_limit') }}" min="1">
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
  </div>
</form>
@endsection
