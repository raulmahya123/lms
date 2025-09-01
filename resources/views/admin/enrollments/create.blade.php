@extends('layouts.app')

@section('title','Tambah Enrollment')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold text-[#1D1C1A]">Tambah Enrollment</h1>
    <button form="enrollmentForm" class="px-4 py-2 rounded-2xl bg-[#7A2C2F] text-white hover:opacity-90">
      Simpan
    </button>
  </div>

  {{-- ALERT ERROR --}}
  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
      <div class="font-semibold mb-2">Periksa kembali isian kamu:</div>
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- FORM --}}
  <form id="enrollmentForm" method="POST" action="{{ route('admin.enrollments.store') }}" class="bg-white border rounded-2xl p-6 space-y-6">
    @csrf
    @include('admin.enrollments._form', [
      'enrollment'  => null,
      'users'       => $users ?? collect(),
      'courses'     => $courses ?? collect(),
      'memberships' => $memberships ?? collect(),
    ])
  </form>

  <div>
    <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center gap-2 text-[#1D1C1A]">
      ← Kembali ke daftar
    </a>
  </div>
</div>
@endsection
