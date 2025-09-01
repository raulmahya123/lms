@extends('layouts.admin')
@section('title','Payment Detail')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-3xl space-y-6">
  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-gray-500">User</div>
      <div class="font-medium">{{ $payment->user?->name }}</div>
      <div class="text-sm text-gray-600">{{ $payment->user?->email }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Item</div>
      <div>
        @if($payment->plan) Plan: <strong>{{ $payment->plan->name }}</strong><br>@endif
        @if($payment->course) Course: <strong>{{ $payment->course->title }}</strong>@endif
      </div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Amount</div>
      <div>Rp {{ number_format($payment->amount,0,',','.') }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Status</div>
      <div>{{ ucfirst($payment->status) }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Provider</div>
      <div>{{ $payment->provider }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Reference</div>
      <div>{{ $payment->reference }}</div>
    </div>
    <div class="md:col-span-2">
      <div class="text-xs text-gray-500">Paid At</div>
      <div>{{ $payment->paid_at }}</div>
    </div>
  </div>

  <hr>

  <form method="POST" action="{{ route('admin.payments.update',$payment) }}" class="grid md:grid-cols-2 gap-4">
    @csrf @method('PUT')
    <div>
      <label class="block text-sm">Status</label>
      <select name="status" class="w-full border rounded px-3 py-2">
        @foreach(['pending','paid','failed'] as $st)
          <option value="{{ $st }}" @selected(old('status',$payment->status)===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm">Provider</label>
      <input name="provider" class="w-full border rounded px-3 py-2" value="{{ old('provider',$payment->provider) }}">
    </div>
    <div>
      <label class="block text-sm">Reference</label>
      <input name="reference" class="w-full border rounded px-3 py-2" value="{{ old('reference',$payment->reference) }}">
    </div>
    <div>
      <label class="block text-sm">Paid At</label>
      <input type="datetime-local" name="paid_at" class="w-full border rounded px-3 py-2"
             value="{{ old('paid_at', optional($payment->paid_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="md:col-span-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 rounded border ml-2">Back</a>
    </div>
  </form>
</div>
@endsection
