@extends('layouts.app')

@section('title','422 Unprocessable Entity')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-6">
  <h1 class="text-7xl font-extrabold text-yellow-500">422</h1>
  <p class="mt-4 text-2xl font-semibold text-gray-800">Data Tidak Valid</p>
  <p class="mt-2 text-gray-600">Permintaan tidak bisa diproses karena ada data yang tidak valid.</p>
<a href="javascript:void(0)"
   onclick="if(history.length>1){history.back()}else{window.location='{{ route('home') }}'}"
   class="mt-6 inline-block px-6 py-3 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600">
  Kembali
</a>
</div>
@endsection
