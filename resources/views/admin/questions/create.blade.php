@extends('layouts.app')

@section('title','Tambah Pertanyaan')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide">Tambah Pertanyaan</h1>
      <p class="text-sm opacity-70 mt-1">Isi detail pertanyaan untuk kuis yang dipilih.</p>
    </div>
    <button form="questionForm"
            class="px-5 py-2 rounded-2xl bg-[#7A2C2F] text-white font-medium hover:opacity-90 shadow">
      Simpan
    </button>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
      <div class="font-semibold mb-2">Periksa kembali isian kamu:</div>
      <ul class="list-disc pl-5 space-y-1 text-sm">
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
        class="bg-white border border-gray-200 rounded-2xl p-6 space-y-6 shadow-sm">
    @csrf
    @include('admin.questions._form', ['question' => null, 'quizzes' => $quizzes])
  </form>

  {{-- Back link --}}
  <a href="{{ route('admin.questions.index') }}"
     class="inline-flex items-center gap-2 text-sm text-[#1D1C1A] hover:underline">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Kembali ke daftar pertanyaan
  </a>
</div>
@endsection
