@extends('layouts.admin')

@section('title','Psych Attempts')

@section('content')
<form method="GET" class="bg-white rounded-xl shadow p-4 mb-6"
      :class="$root.theme==='navy' ? 'bg-[#0f1a33] text-white border border-white/10' : 'bg-white text-[#102a43] border border-blue-100'">
  <div class="grid md:grid-cols-5 gap-4">
    <div>
      <label class="block text-sm font-semibold mb-1">Cari</label>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama/email/ID attempt/result key"
             class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Test</label>
      <select name="test_id" class="w-full border rounded-lg px-3 py-2">
        <option value="">— Semua —</option>
        @foreach($tests as $t)
          <option value="{{ $t->id }}" @selected(request('test_id')==$t->id)>{{ $t->title }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Status</label>
      <select name="status" class="w-full border rounded-lg px-3 py-2">
        <option value="">— Semua —</option>
        <option value="in-progress" @selected(request('status')==='in-progress')>In Progress</option>
        <option value="submitted" @selected(request('status')==='submitted')>Submitted</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Mulai (dari)</label>
      <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Mulai (sampai)</label>
      <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded-lg px-3 py-2">
    </div>
  </div>
  <div class="mt-4 flex items-center gap-2">
    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold">Terapkan</button>
    <a href="{{ route('admin.psy-attempts.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Reset</a>
  </div>
</form>

<div class="rounded-xl shadow overflow-x-auto"
     :class="$root.theme==='navy' ? 'bg-[#0f1a33] text-white border border-white/10' : 'bg-white text-[#102a43] border border-blue-100'">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="text-left border-b" :class="$root.theme==='navy' ? 'border-white/10' : 'border-blue-100'">
        <th class="px-4 py-3">ID</th>
        <th class="px-4 py-3">User</th>
        <th class="px-4 py-3">Test</th>
        <th class="px-4 py-3">Started</th>
        <th class="px-4 py-3">Submitted</th>
        <th class="px-4 py-3">Durasi</th>
        <th class="px-4 py-3">Skor</th>
        <th class="px-4 py-3">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($attempts as $a)
        @php
          $started = $a->started_at?->format('Y-m-d H:i');
          $submitted = $a->submitted_at?->format('Y-m-d H:i');
          $dur = $a->started_at
              ? \Carbon\Carbon::parse($submitted ?? now())->diff(\Carbon\Carbon::parse($a->started_at))->format('%H:%I:%S')
              : '—';
          $scoreSummary = '';
          if (is_array($a->score_json)) {
            if (isset($a->score_json['total'])) $scoreSummary = 'Total: '.$a->score_json['total'];
            elseif (isset($a->score_json['score'])) $scoreSummary = 'Score: '.$a->score_json['score'];
            elseif (count($a->score_json)) { $k = array_key_first($a->score_json); $scoreSummary = $k.': '.$a->score_json[$k]; }
          }
        @endphp
        <tr class="border-b last:border-0" :class="$root.theme==='navy' ? 'border-white/10' : 'border-blue-100'">
          <td class="px-4 py-3 font-medium">#{{ $a->id }}</td>
          <td class="px-4 py-3">
            <div class="font-semibold">{{ $a->user?->name ?? '—' }}</div>
            <div class="opacity-70">{{ $a->user?->email ?? '' }}</div>
          </td>
          <td class="px-4 py-3">{{ $a->test?->title ?? '—' }}</td>
          <td class="px-4 py-3">{{ $started ?? '—' }}</td>
          <td class="px-4 py-3">
            @if($submitted)
              <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>{{ $submitted }}</span>
            @else
              <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-500"></span>—</span>
            @endif
          </td>
          <td class="px-4 py-3">{{ $dur }}</td>
          <td class="px-4 py-3">{{ $scoreSummary ?: '—' }}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <a href="{{ route('admin.psy-attempts.show',$a) }}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white">Detail</a>
              <form method="POST" action="{{ route('admin.psy-attempts.destroy',$a) }}" onsubmit="return confirm('Hapus attempt #{{ $a->id }}?');">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 rounded-lg bg-red-600 text-white">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="px-4 py-8 text-center opacity-70">Belum ada data.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $attempts->links() }}</div>
@endsection
