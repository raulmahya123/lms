@extends('layouts.admin')
@section('title','Edit Coupon')

@section('content')
<form method="POST" action="{{ route('admin.coupons.update',$coupon) }}" class="space-y-5 bg-white p-6 rounded shadow max-w-2xl">
  @csrf @method('PUT')

  <div>
    <label class="block text-sm font-medium mb-1">Code</label>
    <input class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $coupon->code }}" disabled>
    <p class="text-xs text-gray-500 mt-1">Code is immutable.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Discount (%)</label>
    <input type="number" name="discount_percent" class="w-full border rounded px-3 py-2"
           value="{{ old('discount_percent',$coupon->discount_percent) }}" min="0" max="100" required>
    @error('discount_percent') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">Valid From</label>
      <input type="datetime-local" name="valid_from" class="w-full border rounded px-3 py-2"
             value="{{ old('valid_from', optional($coupon->valid_from)->format('Y-m-d\TH:i')) }}">
    </div>
    <div>
      <label class="block text-sm">Valid Until</label>
      <input type="datetime-local" name="valid_until" class="w-full border rounded px-3 py-2"
             value="{{ old('valid_until', optional($coupon->valid_until)->format('Y-m-d\TH:i')) }}">
    </div>
  </div>

  <div>
    <label class="block text-sm">Usage Limit</label>
    <input type="number" name="usage_limit" class="w-full border rounded px-3 py-2"
           value="{{ old('usage_limit',$coupon->usage_limit) }}" min="1">
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded border">Back</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
  </div>
</form>
@endsection
