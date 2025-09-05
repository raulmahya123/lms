@extends('app.layouts.base')
@section('title','Payment '.$payment->reference)
@section('content')
<h1 class="text-xl font-semibold mb-4">Payment Detail</h1>
<div class="bg-white border rounded p-4">
  <div>Reference: <strong>{{ $payment->reference }}</strong></div>
  <div>Amount: <strong>Rp {{ number_format($payment->amount,0,',','.') }}</strong></div>
  <div>Status: <strong>{{ $payment->status }}</strong></div>
  <div>Paid at: <strong>{{ optional($payment->paid_at)->format('d M Y H:i') ?? '—' }}</strong></div>
  <div class="mt-3">
    Item:
    @if($payment->plan) Plan <strong>{{ $payment->plan->name }}</strong>
    @elseif($payment->course) Course <strong>{{ $payment->course->title }}</strong>
    @else — @endif
  </div>
</div>
@endsection
