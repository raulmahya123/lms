@extends('layouts.admin')

@section('title','Attempt #'.$attempt->id)

@section('content')
<a href="{{ route('admin.psy-attempts.index') }}" class="inline-flex items-center gap-2 text-blue-600 mb-4">
  ← Kembali
</a>

<div class="grid md:grid-cols-3 gap-6">
  {{-- Kiri: Info Attempt --}}
  <div class="md:col-span-1 space-y-6">
    <div class="bg-white rounded-xl shadow p-5">
      <h2 class="text-lg font-bold mb-3">Info Attempt</h2>
      <dl class="text-sm grid grid-cols-3 gap-2">
        <dt class="text-gray-500">ID</dt>
        <dd class="col-span-2 font-semibold">#{{ $attempt->id }}</dd>

        <dt class="text-gray-500">User</dt>
        <dd class="col-span-2">
          <div class="font-semibold">{{ $attempt->user?->name ?? '—' }}</div>
          <div class="text-gray-500">{{ $attempt->user?->email ?? '' }}</div>
        </dd>

        <dt class="text-gray-500">Test</dt>
        <dd class="col-span-2">{{ $attempt->test?->title ?? '—' }}</dd>

        <dt class="text-gray-500">Started</dt>
        <dd class="col-span-2">{{ $attempt->started_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>

        <dt class="text-gray-500">Submitted</dt>
        <dd class="col-span-2">{{ $attempt->submitted_at?->format('Y-m-d H:i:s') ?? '—' }}</dd>

        <dt class="text-gray-500">Durasi</dt>
        <dd class="col-span-2">
          @if(!is_null($durationSeconds))
            @php
              $h = floor($durationSeconds/3600);
              $m = floor(($durationSeconds%3600)/60);
              $s = $durationSeconds%60;
            @endphp
            {{ sprintf('%02d:%02d:%02d',$h,$m,$s) }}
          @else
            —
          @endif
        </dd>

        <dt class="text-gray-500">Result Key</dt>
        <dd class="col-span-2">{{ $attempt->result_key ?? '—' }}</dd>
      </dl>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
      <h2 class="text-lg font-bold mb-3">Skor (score_json)</h2>
      @if(is_array($attempt->score_json) && count($attempt->score_json))
        <pre class="text-xs bg-gray-50 rounded-lg p-3 overflow-x-auto">{{ json_encode($attempt->score_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
      @else
        <p class="text-sm text-gray-500">Tidak ada.</p>
      @endif
    </div>

    <div class="bg-white rounded-xl shadow p-5">
      <h2 class="text-lg font-bold mb-3">Rekomendasi</h2>
      <div class="prose max-w-none">
        {!! nl2br(e($attempt->recommendation_text ?? '—')) !!}
      </div>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
      <form method="POST" action="{{ route('admin.psy-attempts.destroy',$attempt) }}"
            onsubmit="return confirm('Hapus attempt #{{ $attempt->id }} beserta jawabannya?');">
        @csrf @method('DELETE')
        <button class="w-full px-4 py-2 rounded-lg bg-red-600 text-white font-semibold">Hapus Attempt</button>
      </form>
    </div>
  </div>

  {{-- Kanan: Jawaban --}}
  <div class="md:col-span-2">
    <div class="bg-white rounded-xl shadow p-5">
      <h2 class="text-lg font-bold mb-4">Jawaban</h2>

      @if($attempt->answers->count())
        <ol class="space-y-4">
          @foreach($attempt->answers as $ans)
            <li class="border rounded-lg p-4">
              <div class="mb-2 text-sm text-gray-500">Q#{{ $ans->question?->ordering ?? '-' }}</div>
              <div class="font-semibold mb-2">
                {{ $ans->question?->text ?? '(Pertanyaan tidak ditemukan)' }}
              </div>

              {{-- Pilihan yang dipilih / value --}}
              <div class="text-sm">
                @if($ans->option)
                  <div><span class="font-semibold">Jawaban:</span> {{ $ans->option->label }}</div>
                  @if(!is_null($ans->option->value))
                    <div class="text-gray-600">Value: {{ $ans->option->value }}</div>
                  @endif
                  @if(!is_null($ans->option->weight))
                    <div class="text-gray-600">Weight: {{ $ans->option->weight }}</div>
                  @endif
                @elseif(isset($ans->value))
                  <div><span class="font-semibold">Jawaban (free value):</span> {{ $ans->value }}</div>
                @else
                  <div class="text-gray-500">Tidak ada jawaban</div>
                @endif
              </div>
            </li>
          @endforeach
        </ol>
      @else
        <p class="text-sm text-gray-500">Belum ada jawaban.</p>
      @endif
    </div>
  </div>
</div>
@endsection
