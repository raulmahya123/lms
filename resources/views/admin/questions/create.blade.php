@extends('layouts.app')

@section('title','Tambah Pertanyaan')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold text-[#1D1C1A]">Tambah Pertanyaan</h1>
    <button form="questionForm" class="px-4 py-2 rounded-2xl bg-[#7A2C2F] text-white hover:opacity-90">Simpan</button>
  </div>

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

  <form id="questionForm" method="POST" action="{{ route('admin.questions.store') }}" class="bg-white border rounded-2xl p-6 space-y-6">
    @csrf
    @include('admin.questions._form', ['question' => null, 'quizzes' => $quizzes])
  </form>

  <a href="{{ route('admin.questions.index') }}" class="inline-flex items-center gap-2 text-[#1D1C1A]">← Kembali ke daftar</a>
</div>
@endsection
