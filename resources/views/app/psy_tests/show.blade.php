@extends('app.layouts.base')

@section('title', $test->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <a href="{{ route('app.psytests.index') }}" class="text-sm text-blue-600">← Semua tes</a>
      <h1 class="text-2xl font-semibold mt-2">{{ $test->name }}</h1>
      <p class="text-gray-600 mt-1">
        {{ strtoupper($test->type) }} • {{ ucfirst($test->track) }} • {{ $test->questions_count }} soal
      </p>
      @if($test->description)
        <p class="text-gray-700 mt-3">{{ $test->description }}</p>
      @endif
    </div>
    <form method="POST" action="{{ route('app.psy.attempts.start', $test) }}">
      @csrf
      <button class="px-4 py-2 bg-green-600 text-white rounded-lg">Mulai / Lanjutkan</button>
    </form>
  </div>

  {{-- (Opsional) daftar soal sebagai preview --}}
  <div class="bg-white border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b font-semibold">Daftar Soal</div>
    <div class="divide-y">
      @forelse($test->questions as $q)
        <div class="px-4 py-3">
          <div class="font-medium">#{{ $loop->iteration }}. {{ $q->text }}</div>
          @if($q->options->count())
            <ul class="list-disc pl-5 text-gray-600 mt-2 space-y-1">
              @foreach($q->options as $op)
                <li>{{ $op->label }}</li>
              @endforeach
            </ul>
          @endif
        </div>
      @empty
        <div class="px-4 py-6 text-gray-600">Belum ada soal pada tes ini.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
