@extends('app.layouts.base')

@section('title','Forum Tanya-Jawab')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Forum Tanya-Jawab</h1>
    <a href="{{ route('app.qa-threads.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Buat Thread</a>
  </div>

  <form method="GET" class="bg-white rounded-xl shadow p-4">
    <div class="grid sm:grid-cols-4 gap-3">
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-1">Cari</label>
        <input name="q" value="{{ request('q') }}" class="w-full border rounded-lg px-3 py-2" placeholder="Judul thread...">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">Status</label>
        <select name="status" class="w-full border rounded-lg px-3 py-2">
          <option value="">— Semua —</option>
          @foreach(['open'=>'Open','resolved'=>'Resolved','closed'=>'Closed'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">Kepemilikan</label>
        <select name="mine" class="w-full border rounded-lg px-3 py-2">
          <option value="">Semua</option>
          <option value="1" @selected(request('mine'))>Punyaku</option>
        </select>
      </div>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Terapkan</button>
      <a href="{{ route('app.qa-threads.index') }}" class="px-4 py-2 rounded-lg bg-gray-100">Reset</a>
    </div>
  </form>

  <div class="bg-white rounded-xl shadow divide-y">
    @forelse($threads as $t)
      <div class="p-4 flex items-start gap-4">
        <div class="flex-1 min-w-0">
          <a href="{{ route('app.qa-threads.show',$t) }}" class="font-semibold text-lg hover:underline">
            {{ $t->title }}
          </a>
          <div class="text-sm text-gray-500 mt-1">
            oleh <span class="font-medium">{{ $t->user?->name ?? 'User' }}</span>
            · {{ $t->created_at?->diffForHumans() }}
            @if($t->course) · Kursus: {{ $t->course->title }} @endif
            @if($t->lesson) · Pelajaran: {{ $t->lesson->title }} @endif
          </div>
          @if($t->body)
            <p class="text-sm text-gray-700 mt-2 line-clamp-2">{{ strip_tags($t->body) }}</p>
          @endif
        </div>
        <div class="shrink-0 text-right space-y-2">
          <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full
            {{ $t->status==='resolved' ? 'bg-emerald-100 text-emerald-700' : ($t->status==='closed' ? 'bg-gray-200 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
            {{ ucfirst($t->status) }}
          </span>
          <div class="text-sm text-gray-600">
            💬 {{ $t->replies_count }}
          </div>
        </div>
      </div>
    @empty
      <div class="p-8 text-center text-gray-500">Belum ada thread.</div>
    @endforelse
  </div>

  <div>{{ $threads->links() }}</div>
</div>
@endsection
