@extends('layouts.admin')

@section('title','Psy Profiles')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-bold">Daftar Psy Profile</h1>
  <a href="{{ route('admin.psy-profiles.create') }}" 
     class="px-3 py-2 rounded bg-indigo-600 text-white">Tambah Baru</a>
</div>

@if(session('success'))
  <div class="mb-3 p-3 bg-green-50 text-green-800 rounded">
    {{ session('success') }}
  </div>
@endif

{{-- Filter --}}
<form method="get" class="mb-4">
  <select name="test_id" class="border rounded px-3 py-2">
    <option value="">Semua Tes</option>
    @foreach($tests as $id=>$title)
      <option value="{{ $id }}" @selected($testId==$id)>{{ $title }}</option>
    @endforeach
  </select>
  <button class="px-3 py-2 border rounded">Filter</button>
</form>

<table class="w-full border text-left">
  <thead class="bg-gray-50">
    <tr>
      <th class="p-2 border">#</th>
      <th class="p-2 border">Test</th>
      <th class="p-2 border">Key</th>
      <th class="p-2 border">Nama</th>
      <th class="p-2 border">Range</th>
      <th class="p-2 border">Deskripsi</th>
      <th class="p-2 border">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($profiles as $p)
      <tr>
        <td class="p-2 border">{{ $p->id }}</td>
        <td class="p-2 border">{{ $p->test->title ?? '-' }}</td>
        <td class="p-2 border">{{ $p->key }}</td>
        <td class="p-2 border">{{ $p->name }}</td>
        <td class="p-2 border">{{ $p->min_total }} - {{ $p->max_total }}</td>
        <td class="p-2 border">{{ Str::limit($p->description,50) }}</td>
        <td class="p-2 border">
          <a href="{{ route('admin.psy-profiles.edit',$p) }}" 
             class="px-2 py-1 rounded bg-yellow-500 text-white">Edit</a>
          <form method="POST" action="{{ route('admin.psy-profiles.destroy',$p) }}" class="inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Hapus data ini?')" 
                    class="px-2 py-1 rounded bg-red-600 text-white">Hapus</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" class="p-3 text-center text-gray-500">Belum ada data</td></tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4">
  {{ $profiles->links() }}
</div>
@endsection
