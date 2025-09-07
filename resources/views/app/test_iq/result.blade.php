@extends('app.layouts.base')

@section('title', 'Hasil '.$test->title)

@section('content')
<div class="max-w-3xl mx-auto py-8">
  <h1 class="text-2xl font-bold mb-4">Hasil Test: {{ $test->title }}</h1>

  @if($result)
    <div class="p-4 border rounded mb-6">
      <div><strong>Skor:</strong> {{ $result['score'] ?? 0 }} / {{ count($test->questions ?? []) }}</div>
      <div><strong>Waktu Kerja:</strong> {{ $result['duration_sec'] ?? '-' }} detik</div>
      <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($result['submitted_at'])->format('d M Y H:i') }}</div>
    </div>
  @else
    <p class="text-gray-600">Belum ada hasil test untuk kamu.</p>
  @endif

  <a href="{{ route('user.test-iq.show', $test) }}" class="px-4 py-2 rounded bg-indigo-600 text-white">Ulangi Test</a>
</div>
@endsection
