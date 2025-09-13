@extends('app.layouts.base')
@section('title', 'Hasil '.$test->title)

@section('content')
<div class="max-w-4xl mx-auto py-10">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold">Hasil: {{ $test->title }}</h1>
    <a href="{{ route('user.test-iq.show', $test) }}"
       class="px-4 py-2 rounded-xl border hover:bg-gray-50">Ulangi Test</a>
  </div>

  @if(session('status'))
    <div class="mb-4 p-3 bg-emerald-50 text-emerald-800 rounded">{{ session('status') }}</div>
  @endif

  @if($result)
    @php
      $questions = array_values($test->questions ?? []);
      $total = count($questions);
      $score = (int) ($result['score'] ?? 0);
      $pct   = $total > 0 ? round($score / $total * 100) : 0;
      $badge =
        $pct >= 85 ? ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Excellent'] :
        ($pct >= 70 ? ['bg' => 'bg-blue-100',    'text' => 'text-blue-800',    'label' => 'Good'] :
        ($pct >= 50 ? ['bg' => 'bg-amber-100',   'text' => 'text-amber-800',   'label' => 'Fair'] :
                      ['bg' => 'bg-rose-100',    'text' => 'text-rose-800',    'label' => 'Need Practice']));
      $answers = $result['answers'] ?? []; // key = step number (karena fallback)
    @endphp

    <div class="grid gap-4 md:grid-cols-3 mb-6">
      <div class="md:col-span-2 bg-white border rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-2">
          <div class="text-gray-600">Skor</div>
          <span class="px-2 py-1 text-xs rounded {{ $badge['bg'] }} {{ $badge['text'] }}">{{ $badge['label'] }}</span>
        </div>
        <div class="flex items-end gap-3">
          <div class="text-5xl font-bold">{{ $score }}</div>
          <div class="text-gray-500">/ {{ $total }}</div>
        </div>
        <div class="mt-4">
          <div class="text-sm text-gray-600 mb-1">Persentase</div>
          <div class="w-full h-2 bg-gray-200 rounded">
            <div class="h-2 rounded bg-indigo-600" style="width: {{ $pct }}%"></div>
          </div>
          <div class="text-right text-xs text-gray-500 mt-1">{{ $pct }}%</div>
        </div>
      </div>

      <div class="bg-white border rounded-2xl p-6 shadow-sm">
        <div class="text-gray-600 mb-2">Ringkasan</div>
        <div class="space-y-1 text-sm">
          <div><span class="text-gray-500">Waktu Kerja:</span> <span class="font-medium">{{ $result['duration_sec'] ?? '-' }} detik</span></div>
          <div>
            <span class="text-gray-500">Tanggal:</span>
            <span class="font-medium">
              {{ \Carbon\Carbon::parse($result['submitted_at'])->timezone(config('app.timezone','Asia/Jakarta'))->format('d M Y H:i') }}
            </span>
          </div>
        </div>
        <a href="{{ route('user.test-iq.show', $test) }}"
           class="mt-4 inline-flex w-full items-center justify-center px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
          Coba Lagi
        </a>
      </div>
    </div>

    {{-- Review Jawaban --}}
    <div class="bg-white border rounded-2xl p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="font-semibold">Review Jawaban</div>
        <div class="text-sm text-gray-500">Hijau = benar, Merah = salah</div>
      </div>

      <div class="space-y-3">
        @foreach($answers as $step => $ans)
          @php
            $idx = ((int)$step) - 1;
            $q   = $questions[$idx] ?? null;
            $correct = $q['answer'] ?? null;
            $isRight = ($ans === $correct);
          @endphp
          <div class="rounded-xl border p-4 {{ $isRight ? 'border-emerald-300 bg-emerald-50' : 'border-rose-300 bg-rose-50' }}">
            <div class="text-sm text-gray-600 mb-1">Soal {{ $step }}</div>
            <div class="font-medium mb-2">{{ $q['q'] ?? '—' }}</div>
            <div class="text-sm">
              <div><span class="text-gray-600">Jawabanmu:</span> <span class="font-medium">{{ $ans ?? '—' }}</span></div>
              <div><span class="text-gray-600">Kunci:</span> <span class="font-medium">{{ $correct ?? '—' }}</span></div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @else
    <div class="p-6 bg-white border rounded-2xl shadow-sm">
      <p class="text-gray-600 mb-4">Belum ada hasil test untuk kamu.</p>
      <a href="{{ route('user.test-iq.show', $test) }}"
         class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Mulai Test</a>
    </div>
  @endif
</div>
@endsection
