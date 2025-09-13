@extends('app.layouts.base')

@section('title', $test->title)

@section('content')
<div class="max-w-3xl mx-auto py-10">
  <div class="bg-white border rounded-2xl shadow-sm p-6 md:p-8">
    <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ $test->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $test->description }}</p>

    @php
      $total = count($test->questions ?? []);
      $dur   = $test->duration_minutes ?? null;
    @endphp

    <div class="grid gap-4 sm:grid-cols-3 mb-6">
      <div class="border rounded-xl p-4 text-center">
        <div class="text-sm text-gray-500">Jumlah Soal</div>
        <div class="text-2xl font-semibold">{{ $total }}</div>
      </div>
      <div class="border rounded-xl p-4 text-center">
        <div class="text-sm text-gray-500">Durasi</div>
        <div class="text-2xl font-semibold">{{ $dur ? $dur.' menit' : '—' }}</div>
      </div>
      <div class="border rounded-xl p-4 text-center">
        <div class="text-sm text-gray-500">Tipe</div>
        <div class="text-2xl font-semibold">Tes IQ</div>
      </div>
    </div>

    <form method="GET" action="{{ route('user.test-iq.start', $test) }}">
      <button type="submit"
        class="w-full md:w-auto px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
        🚀 Mulai Test
      </button>
    </form>
  </div>
</div>
@endsection
