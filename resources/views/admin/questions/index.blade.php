@extends('layouts.admin')

@section('title','Questions')

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-blue-900">Questions</h1>
    <a href="{{ route('admin.questions.create') }}"
       class="px-4 py-2 rounded-lg bg-blue-700 text-white hover:bg-blue-600">
      + Add Question
    </a>
  </div>

  {{-- Flash message --}}
  @if(session('ok'))
    <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
      {{ session('ok') }}
    </div>
  @endif

  <div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-blue-800 text-white">
        <tr>
          <th class="px-4 py-3 text-left">#</th>
          <th class="px-4 py-3 text-left">Quiz</th>
          <th class="px-4 py-3 text-left">Prompt</th>
          <th class="px-4 py-3 text-left">Type</th>
          <th class="px-4 py-3 text-left">Points</th>
          <th class="px-4 py-3 text-left">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @forelse ($questions as $q)
          <tr>
            <td class="px-4 py-3">{{ $q->id }}</td>
            <td class="px-4 py-3">{{ $q->quiz->title ?? '—' }}</td>
            <td class="px-4 py-3">{{ Str::limit($q->prompt, 50) }}</td>
            <td class="px-4 py-3 uppercase">{{ $q->type }}</td>
            <td class="px-4 py-3">{{ $q->points }}</td>
            <td class="px-4 py-3 space-x-2">
              <a href="{{ route('admin.questions.edit',$q) }}"
                 class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-500 text-xs">Edit</a>
              <form action="{{ route('admin.questions.destroy',$q) }}"
                    method="POST" class="inline-block"
                    onsubmit="return confirm('Yakin hapus pertanyaan ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-500 text-xs">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
              Belum ada pertanyaan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $questions->links() }}
  </div>
@endsection
