@extends('layouts.admin')
@section('title','Create Membership')

@section('content')
<div class="mb-6 flex items-center gap-2">
  <h1 class="text-2xl font-extrabold">Create Membership</h1>
</div>

@if($errors->any())
  <div class="mb-4 border border-red-200 bg-red-50 text-red-800 rounded-2xl px-4 py-3">
    {{ $errors->first() }}
  </div>
@endif

<form method="POST" action="{{ route('admin.memberships.store') }}" class="space-y-6 max-w-3xl">
  @include('admin.memberships._form',['submit'=>'Create'])
</form>
@endsection
