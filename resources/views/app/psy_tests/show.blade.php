@extends('app.layouts.base')

@section('title', $test->name)

@push('styles')
<style>
  :root{
    --blue-1:#1e40af; /* biru gelap */
    --blue-2:#2563eb; /* biru */
    --blue-3:#3b82f6; /* biru terang */
    --ink:#0f172a;
  }

  /* ====== LAYOUT BACKDROP ====== */
  .screen-bg{
    background:
      radial-gradient(1200px 600px at 90% -10%, rgba(59,130,246,.18), transparent 60%),
      radial-gradient(800px 400px at -10% 10%, rgba(37,99,235,.14), transparent 60%),
      linear-gradient(180deg, #ffffff, #f7fbff);
  }

  /* ====== CHIPS / BADGES ====== */
  .chip{
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.4rem .78rem;border-radius:999px;
    background:linear-gradient(90deg,var(--blue-2),var(--blue-3));
    color:#fff;font-size:.74rem;font-weight:600;
    box-shadow:0 6px 20px rgba(37,99,235,.25);
  }
  .badge{
    font-size:.72rem;padding:.24rem .55rem;border-radius:999px;
    background:#eaf2ff;color:#1e3a8a;font-weight:600;border:1px solid #dbe5ff;
  }

  /* ====== BUTTONS ====== */
  .btn{border-radius:12px;padding:.78rem 1.15rem;font-weight:700;transition:.25s ease}
  .btn-primary{
    color:#fff;
    background:linear-gradient(90deg,var(--blue-1),var(--blue-2));
    box-shadow:0 10px 25px rgba(37,99,235,.35), inset 0 0 0 1px rgba(255,255,255,.08);
  }
  .btn-primary:hover{transform:translateY(-1px);filter:brightness(.98)}
  .btn-disabled{background:#e5e7eb;color:#94a3b8;cursor:not-allowed}

  /* ====== CARDS ====== */
  .card{
    background:rgba(255,255,255,.9);
    border:1px solid #e6ecff;border-radius:18px;overflow:hidden;transition:.3s;
    box-shadow:0 10px 32px rgba(30,64,175,.06);
    backdrop-filter:saturate(1.2) blur(2px);
  }
  .card:hover{box-shadow:0 20px 50px rgba(30,64,175,.10)}
  .card-head{
    position:relative;
    background:linear-gradient(90deg,#f8fbff,#ffffff);
    border-bottom:1px solid #e6ecff;
  }
  .card-head:after{
    content:"";
    position:absolute;inset:0;
    background:linear-gradient(90deg,transparent,rgba(59,130,246,.12),transparent);
    pointer-events:none;mask-image:linear-gradient(#000,transparent 70%);
  }

  /* ====== STATS ====== */
  .stat{
    display:flex;align-items:center;gap:.9rem;padding:1rem;border-radius:14px;
    background:linear-gradient(135deg,#ffffff,#f2f7ff);
    border:1px solid #e6ecff;transition:.25s ease
  }
  .stat:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,99,235,.10)}

  /* ====== LIST ====== */
  .list li{padding:.38rem 0;border-bottom:1px dashed #eef2f7}
  .list li:last-child{border-bottom:0}

  /* ====== TYPO ====== */
  .title-grad{
    background-image:linear-gradient(90deg,#1e3a8a,#2563eb,#60a5fa);
    -webkit-background-clip:text;background-clip:text;color:transparent
  }

  /* ====== DECOR ====== */
  .glow-pill{
    position:absolute;inset:auto;right:-16px;top:-16px;width:180px;height:180px;border-radius:100%;
    background:radial-gradient(circle, rgba(59,130,246,.24), transparent 60%);
    filter:blur(14px);pointer-events:none
  }
</style>
@endpush

@section('content')
@php
  $slugId = $test->slug ?: $test->id;
  $hasQuestions = ($test->questions_count ?? 0) > 0;
@endphp

<div class="screen-bg">
  <div class="relative max-w-5xl mx-auto px-4 py-10 space-y-8">

    {{-- Header + CTA (ONLY ONE BUTTON HERE) --}}
    <div class="relative flex flex-col md:flex-row md:items-start md:justify-between gap-6">
      <div class="min-w-0">
        <a href="{{ route('app.psy.tests.index') }}" class="text-sm text-blue-700 hover:underline">← Semua tes</a>
        <h1 class="text-3xl md:text-4xl font-extrabold mt-3 title-grad">
          {{ $test->name }}
        </h1>

        <div class="mt-3 flex flex-wrap items-center gap-2">
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

        @if(!empty(optional($test)->description))
          <p class="text-slate-700 mt-4 leading-relaxed">{{ $test->description }}</p>
        @endif
      </div>

      <div class="shrink-0 relative">
        <span class="glow-pill"></span>
        @if($hasQuestions)
          <form method="POST" action="{{ route('app.psy.attempts.start', $slugId) }}">
            @csrf
            <button class="btn btn-primary">
              🚀 Mulai / Lanjutkan
            </button>
          </form>
        @else
          <button class="btn btn-disabled" disabled>❌ Tidak bisa mulai</button>
        @endif
      </div>
    </div>

    {{-- Info ringkas --}}
    <div class="grid sm:grid-cols-3 gap-4">
      <div class="stat">
        <div class="text-2xl">🧩</div>
        <div>
          <div class="text-xs text-slate-500">Jumlah Soal</div>
          <div class="font-semibold text-slate-900">{{ $test->questions_count }}</div>
        </div>
      </div>
      <div class="stat">
        <div class="text-2xl">🗂️</div>
        <div>
          <div class="text-xs text-slate-500">Tipe</div>
          <div class="font-semibold text-slate-900">{{ strtoupper($test->type) }}</div>
        </div>
      </div>
      <div class="stat">
        <div class="text-2xl">🕒</div>
        <div>
          <div class="text-xs text-slate-500">Batas Waktu</div>
          <div class="font-semibold text-slate-900">
            {{ !empty($test->time_limit_min) ? (int)$test->time_limit_min.' menit' : 'Tanpa batas' }}
          </div>
        </div>
      </div>
    </div>

    {{-- Daftar Soal (preview) --}}
    <div class="card">
      <div class="card-head px-5 py-4 font-semibold flex items-center justify-between">
        <div class="text-[15px] text-blue-900 flex items-center gap-2">
          <span>📋</span> <span>Daftar Soal</span>
        </div>
        {{-- ⛔️ Button kedua DIHAPUS agar hanya ada satu tombol di header atas --}}
      </div>

      <div class="divide-y">
        @forelse($test->questions as $q)
          <div class="px-5 py-4">
            <div class="font-medium text-slate-800">{{ $loop->iteration }}. {{ $q->prompt }}</div>
            @if($q->options->count())
              <ul class="list pl-5 text-slate-600 mt-2">
                @foreach($q->options as $op)
                  <li>• {{ $op->label }}</li>
                @endforeach
              </ul>
            @endif
          </div>
        @empty
          <div class="px-5 py-6 text-slate-600 text-center">⚠️ Belum ada soal pada tes ini.</div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection
