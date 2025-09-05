@extends('layouts.app')

@section('title','404 Not Found')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-6">
  <h1 class="text-7xl font-extrabold text-blue-600">404</h1>
  <p class="mt-4 text-2xl font-semibold text-gray-800">Halaman Tidak Ditemukan</p>
  <p class="mt-2 text-gray-600">Oops! Halaman atau data yang Anda cari tidak tersedia.</p>
  <a href="{{ url('/') }}" 
     class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
     Kembali ke Beranda
  </a>
</div>
@endsection
