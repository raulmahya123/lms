{{-- delacc.blade.php --}}
@extends('layouts.app')
@section('title','Edit Profile — Delete Account')

@section('content')
  @include('profile.partials.nav')
  <div class="bg-red-50 border border-red-200 rounded-xl p-6">
    @include('profile.partials.delete-user-form')
  </div>
@endsection
