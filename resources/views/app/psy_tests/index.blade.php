@extends('app.layouts.base')

@section('title','Tes Psikologi')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
  <div class="flex items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-semibold">Tes Psikologi</h1>
    <a href="{{ route('home') }}" class="text-sm text-blue-600">← Beranda</a>
  </div>

  <form method="GET" class="grid md:grid-cols-4 gap-3 mb-6">
    <input name="q" value="{{ $q }}" placeholder="Cari tes…"
           class="border rounded-lg px-3 py-2 md:col-span-2">
    <select name="track" class="border rounded-lg px-3 py-2">
      <option value="">Semua Track</option>
      @foreach($tracks as $t)
        <option value="{{ $t }}" @selected($track===$t)>{{ ucfirst($t) }}</option>
      @endforeach
    </select>
    <select name="type" class="border rounded-lg px-3 py-2">
      <option value="">Semua Tipe</option>
      @foreach($types as $t)
        <option value="{{ $t }}" @selected($type===$t)>{{ strtoupper($t) }}</option>
      @endforeach
    </select>
    <div class="md:col-span-4 flex gap-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Filter</button>
      @if(request()->hasAny(['q','track','type']))
        <a href="{{ route('app.psytests.index') }}" class="px-4 py-2 border rounded-lg">Reset</a>
      @endif
    </div>
  </form>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($tests as $t)
      <a href="{{ route('app.psytests.show', $t->slug ?: $t->id) }}"
         class="block border bg-white rounded-xl p-4 hover:shadow transition">
        <div class="text-xs text-gray-500 mb-1">
          {{ strtoupper($t->type) }} • {{ ucfirst($t->track) }}
        </div>
        <div class="font-semibold text-lg">{{ $t->name }}</div>
        @if($t->description)
          <p class="text-gray-600 mt-1 line-clamp-2">{{ $t->description }}</p>
        @endif
      </a>
    @empty
      <div class="col-span-full p-6 text-center text-gray-600 bg-white border rounded-xl">
        Tidak ada tes ditemukan.
      </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $tests->links() }}</div>
</div>
@endsection
