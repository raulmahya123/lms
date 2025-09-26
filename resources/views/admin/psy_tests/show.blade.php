{{-- resources/views/admin/psy_tests/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Test Detail — BERKEMAH')

@section('content')
@php
  /** @var \App\Models\PsyTest $psy_test */
@endphp

<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-3xl font-bold">Test Detail</h1>
      <p class="text-sm text-gray-600">Informasi test & tindakan cepat.</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.psy-tests.index') }}"
         class="px-3 py-2 border rounded-xl">← Back</a>
      <a href="{{ route('admin.psy-tests.edit', $psy_test) }}"
         class="px-3 py-2 border rounded-xl">Edit</a>
      <form method="POST" action="{{ route('admin.psy-tests.destroy', $psy_test) }}"
            onsubmit="return confirm('Hapus test ini?')">
        @csrf @method('DELETE')
        <button class="px-3 py-2 border rounded-xl text-red-600">Delete</button>
      </form>
    </div>
  </div>

  <div class="rounded-2xl border bg-white">
    <div class="p-6 space-y-4">
      <div>
        <div class="text-xs text-gray-500 mb-1">Nama</div>
        <div class="text-lg font-semibold">{{ $psy_test->name }}</div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="border rounded-xl p-4">
          <div class="text-xs text-gray-500 mb-1">Slug</div>
          <div class="font-mono break-all">{{ $psy_test->slug ?? '—' }}</div>
        </div>
        <div class="border rounded-xl p-4">
          <div class="text-xs text-gray-500 mb-1">Status</div>
          <div class="font-semibold">
            {{ ($psy_test->is_active ?? false) ? 'Active' : 'Inactive' }}
          </div>
        </div>
      </div>

      @if(!empty($psy_test->description))
        <div>
          <div class="text-xs text-gray-500 mb-1">Deskripsi</div>
          <div class="border rounded-xl p-4 leading-relaxed">
            {!! nl2br(e($psy_test->description)) !!}
          </div>
        </div>
      @endif
    </div>

    <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-between">
      <div class="text-sm">
        Pertanyaan: <span class="font-semibold">{{ $psy_test->questions()->count() }}</span>
      </div>
      <a href="{{ route('admin.psy-tests.questions.index', $psy_test) }}"
         class="px-4 py-2 rounded-xl bg-blue-600 text-white">Kelola Pertanyaan</a>
    </div>
  </div>
</div>
@endsection
