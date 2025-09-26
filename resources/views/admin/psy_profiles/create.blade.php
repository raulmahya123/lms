@extends('layouts.admin')
@section('title','Create Psych Profile')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-semibold mb-4">Create Psych Profile</h1>

  @if($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="post" action="{{ route('admin.psy-profiles.store') }}">
    @include('admin.psy_profiles.form', ['profile' => null])
  </form>
</div>
@endsection
