{{-- updatepass.blade.php --}}
@extends('layouts.app')
@section('title','Edit Profile — Password')

@section('content')
  @include('profile.partials.nav')
  <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
    @include('profile.partials.update-password-form', ['status' => $status ?? null])
  </div>
@endsection
