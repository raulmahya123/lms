@extends('app.layouts.base')

@section('title', $test->name)

@push('styles')
<style>
  .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .6rem;border-radius:999px;background:#f8fafc;border:1px solid #e5e7eb;font-size:.78rem}
  .stat{display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;border-radius:10px;background:#f9fafb;border:1px solid #eef2f7}
  .btn{border-radius:12px;padding:.65rem 1rem;font-weight:600}
  .btn-primary{background:#16a34a;color:#fff}
  .btn-primary:hover{filter:brightness(.95)}
  .btn-muted{border:1px solid #e5e7eb;background:#fff}
  .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px}
  .card:hover{box-shadow:0 14px 40px rgba(2,6,23,.06)}
  .list li{padding:.35rem 0;border-bottom:1px dashed #eef2f7}
  .list li:last-child{border-bottom:0}
  .badge{font-size:.72rem;padding:.2rem .45rem;border-radius:999px;background:#eef2ff;color:#3730a3}
</style>
@endpush

@section('content')
@php
  $slugId = $test->slug ?: $test->id;
  $hasQuestions = ($test->questions_count ?? 0) > 0;
@endphp

<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

  {{-- Header + CTA --}}
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div class="min-w-0">
      <a href="{{ route('app.psytests.index') }}" class="text-sm text-blue-600">← Semua tes</a>
      <h1 class="text-2xl font-semibold mt-2">{{ $test->name }}</h1>

      <div class="mt-2 flex flex-wrap items-center gap-2">
        <span class="chip">{{ strtoupper($test->type) }}</span>
        <span class="chip">{{ ucfirst($test->track) }}</span>
        <span class="chip">{{ $test->questions_count }} Soal</span>
        @if(!empty($test->time_limit_min))
          <span class="chip">⏳ {{ (int)$test->time_limit_min }} menit</span>
        @endif
        @if(!$hasQuestions)
          <span class="badge">Belum ada soal</span>
        @endif
      </div>

      @if($test->description)
        <p class="text-gray-700 mt-3 leading-relaxed">{{ $test->description }}</p>
      @endif
    </div>

    <div class="shrink-0">
      @if($hasQuestions)
        <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}" class="flex gap-2">
          @csrf
          <button class="btn btn-primary">Mulai / Lanjutkan</button>
          <a href="{{ route('app.psytests.show', $slugId) }}" class="btn btn-muted hidden sm:inline-flex">Detail</a>
        </form>
      @else
        <button class="btn btn-muted opacity-60 cursor-not-allowed" disabled>Tidak bisa mulai</button>
      @endif
    </div>
  </div>

  {{-- Info ringkas --}}
  <div class="grid sm:grid-cols-3 gap-3">
    <div class="stat">
      <div>🧩</div>
      <div>
        <div class="text-xs text-gray-500">Jumlah Soal</div>
        <div class="font-semibold">{{ $test->questions_count }}</div>
      </div>
    </div>
    <div class="stat">
      <div>🗂️</div>
      <div>
        <div class="text-xs text-gray-500">Tipe</div>
        <div class="font-semibold">{{ strtoupper($test->type) }}</div>
      </div>
    </div>
    <div class="stat">
      <div>🕒</div>
      <div>
        <div class="text-xs text-gray-500">Batas Waktu</div>
        <div class="font-semibold">
          {{ !empty($test->time_limit_min) ? (int)$test->time_limit_min.' menit' : 'Tanpa batas' }}
        </div>
      </div>
    </div>
  </div>

  {{-- Daftar Soal (preview) --}}
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b font-semibold flex items-center justify-between">
      <div>Daftar Soal</div>
      @if($hasQuestions)
        <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}">
          @csrf
          <button class="btn btn-muted">Mulai Sekarang</button>
        </form>
      @endif
    </div>

    <div class="divide-y">
      @forelse($test->questions as $q)
        <div class="px-4 py-4">
          <div class="font-medium">#{{ $loop->iteration }}. {{ $q->prompt }}</div>
          @if($q->options->count())
            <ul class="list pl-5 text-gray-600 mt-2">
              @foreach($q->options as $op)
                <li>• {{ $op->label }}</li>
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
