@extends('layouts.admin')

@section('title','Tambah Pertanyaan — BERKEMAH')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-blue-900">Tambah Pertanyaan</h1>
      <p class="text-sm text-blue-700/70 mt-1">Isi detail pertanyaan untuk kuis yang dipilih.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.questions.index') }}"
         class="px-3 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        ← Kembali
      </a>
      <button form="questionForm"
              class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-700 to-blue-600 text-white font-semibold hover:from-blue-800 hover:to-blue-700 shadow-md transition">
        Simpan
      </button>
    </div>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
      <div class="font-semibold text-red-800 mb-2">Periksa kembali isian kamu:</div>
      <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form Card --}}
  <form id="questionForm"
        method="POST"
        action="{{ route('admin.questions.store') }}"
        class="rounded-2xl border border-blue-100 bg-white/90 p-6 shadow-lg backdrop-blur space-y-6">
    @csrf
    @include('admin.questions._form', ['question' => null, 'quizzes' => $quizzes])
  </form>

  {{-- Back link --}}
  <a href="{{ route('admin.questions.index') }}"
     class="inline-flex items-center gap-2 text-sm text-blue-800 hover:underline">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" viewBox="0 0 24 24" fill="currentColor">
      <path d="M15 19 8 12l7-7"/>
    </svg>
    Kembali ke daftar pertanyaan
  </a>
</div>
@endsection
