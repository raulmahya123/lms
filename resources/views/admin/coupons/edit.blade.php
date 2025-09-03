@extends('layouts.admin')
@section('title','Edit Coupon')

@section('content')
{{-- HEADER ala "Modules" --}}
<div class="mb-4">
  <div class="flex items-center gap-2 text-blue-900 font-semibold text-xl">
    {{-- icon tiket/kupon --}}
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
      <path d="M3 7a2 2 0 012-2h6a2 2 0 002 2 2 2 0 002-2h4a2 2 0 012 2v2a2 2 0 110 4v2a2 2 0 01-2 2h-4a2 2 0 01-2-2 2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
    </svg>
    Edit Coupon
  </div>
  <p class="text-[13px] text-gray-500">
    Ubah diskon, masa berlaku, dan batas pemakaian. Kode kupon tidak dapat diubah.
  </p>
</div>

{{-- CARD FORM --}}
<form method="POST" action="{{ route('admin.coupons.update',$coupon) }}"
      class="space-y-5 bg-white p-6 rounded-lg border max-w-2xl"
      x-data="{
        unlimited: {{ old('usage_limit', $coupon->usage_limit) === null ? 'true' : 'false' }},
        toggleUnlimited(e){
          this.unlimited = e.target.checked;
          if(this.unlimited){
            // kosongkan value supaya dianggap unlimited (null) di controller
            $refs.usage_limit.value = '';
          }
        }
      }">
  @csrf @method('PUT')

  {{-- Error summary (opsional) --}}
  @if ($errors->any())
    <div class="p-3 rounded bg-red-50 text-red-700 text-sm">
      <ul class="list-disc list-inside space-y-0.5">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- CODE (read-only) --}}
  <div>
    <label class="block text-sm font-medium mb-1">Code</label>
    <input class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-700" 
           value="{{ $coupon->code }}" disabled>
    <p class="text-xs text-gray-500 mt-1">Code is immutable.</p>
  </div>

  {{-- DISCOUNT (%) --}}
  <div>
    <label class="block text-sm font-medium mb-1">Discount (%)</label>
    <input type="number" name="discount_percent" step="1"
           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-200"
           value="{{ old('discount_percent',$coupon->discount_percent) }}" min="0" max="100" required>
    @error('discount_percent') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- VALID FROM / UNTIL --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Valid From</label>
      <input type="datetime-local" name="valid_from"
             class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-200"
             value="{{ old('valid_from', optional($coupon->valid_from)->format('Y-m-d\TH:i')) }}">
      <p class="text-xs text-gray-500 mt-1">Kosongkan jika langsung berlaku.</p>
      @error('valid_from') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Valid Until</label>
      <input type="datetime-local" name="valid_until"
             class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-200"
             value="{{ old('valid_until', optional($coupon->valid_until)->format('Y-m-d\TH:i')) }}">
      <p class="text-xs text-gray-500 mt-1">Kosongkan untuk tanpa tanggal berakhir.</p>
      @error('valid_until') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  {{-- USAGE LIMIT + UNLIMITED --}}
  <div>
    <label class="block text-sm font-medium mb-1">Usage Limit</label>
    <div class="flex items-center gap-3">
      <input type="number" name="usage_limit" x-ref="usage_limit"
             :disabled="unlimited"
             class="w-40 border rounded px-3 py-2 focus:ring-2 focus:ring-blue-200 disabled:bg-gray-100"
             value="{{ old('usage_limit',$coupon->usage_limit) }}" min="1" placeholder="e.g. 100">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" @change="toggleUnlimited" :checked="unlimited"
               class="rounded border-gray-300">
        <span>Unlimited</span>
      </label>
    </div>
    <p class="text-xs text-gray-500 mt-1">Centang <em>Unlimited</em> untuk tanpa batas pemakaian.</p>
    @error('usage_limit') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- ACTIONS --}}
  <div class="flex items-center gap-2 pt-2">
    <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">Back</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Update</button>
  </div>
</form>
@endsection
