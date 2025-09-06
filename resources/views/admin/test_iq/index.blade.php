@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Test IQ</h1>
  <a href="{{ route('admin.test-iq.create') }}" class="px-3 py-2 rounded bg-indigo-600 text-white">Buat Baru</a>
</div>

<form class="mb-4">
  <input type="text" name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Cari judul/desc...">
  <button class="px-3 py-2 border rounded">Cari</button>
</form>

@if(session('success'))
  <div class="mb-3 p-3 bg-green-50 text-green-800 rounded">{{ session('success') }}</div>
@endif

<table class="w-full text-left border">
  <thead class="bg-gray-50">
    <tr>
      <th class="p-2 border">#</th>
      <th class="p-2 border">Judul</th>
      <th class="p-2 border">Aktif</th>
      <th class="p-2 border">Soal</th>
      <th class="p-2 border">Durasi</th>
      <th class="p-2 border">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tests as $t)
    <tr>
      <td class="p-2 border">{{ $t->id }}</td>
      <td class="p-2 border">{{ $t->title }}</td>
      <td class="p-2 border">
        <form action="{{ route('admin.test-iq.toggle', $t) }}" method="POST">
          @csrf
          <button class="text-sm px-2 py-1 rounded {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
            {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
          </button>
        </form>
      </td>
      <td class="p-2 border">{{ $t->totalQuestions() }}</td>
      <td class="p-2 border">{{ $t->duration_minutes }} mnt</td>
      <td class="p-2 border">
        <a class="text-indigo-600" href="{{ route('admin.test-iq.edit', $t) }}">Edit</a>
        <form action="{{ route('admin.test-iq.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus test ini?')">
          @csrf @method('DELETE')
          <button class="text-red-600 ml-2">Hapus</button>
        </form>
      </td>
    </tr>
    @empty
      <tr><td colspan="6" class="p-3 text-gray-500">Belum ada data.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4">
  {{ $tests->links() }}
</div>
@endsection
