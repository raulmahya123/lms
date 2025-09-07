{{-- updateinformation.blade.php --}}
@extends('layouts.app')
@section('title','Edit Profile — Information')

@section('content')
  @include('profile.partials.nav')
  <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
    @include('profile.partials.update-profile-information-form', [
      'user' => $user,
      'mustVerifyEmail' => $mustVerifyEmail,
      'status' => $status,
    ])
  </div>
@endsection
