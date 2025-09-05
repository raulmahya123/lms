@extends('layouts.admin')
@section('title','Psych Questions — '.$psy_test->name)

@section('content')
<div class="space-y-6">

  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2h12v20l-6-3-6 3V2Z"/></svg>
        Questions • {{ $psy_test->name }}
      </h1>
      <p class="text-sm opacity-70">Kelola pertanyaan & opsi untuk test ini.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.psy-tests.index') }}" class="px-3 py-2 rounded-xl border hover:bg-gray-50">← Back to Tests</a>
      <a href="{{ route('admin.psy-tests.questions.create',$psy_test) }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700">Add Question</a>
    </div>
  </div>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
      <div class="text-sm"><span class="font-semibold">{{ $questions->total() }}</span> questions</div>
      <div class="text-xs opacity-70">Page {{ $questions->currentPage() }} / {{ $questions->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="p-3 text-left w-16">#</th>
            <th class="p-3 text-left">Prompt</th>
            <th class="p-3 text-left w-28">Trait</th>
            <th class="p-3 text-left w-20">Type</th>
            <th class="p-3 text-left w-24">Options</th>
            <th class="p-3 text-center w-48">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($questions as $q)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">{{ $q->ordering }}</td>
              <td class="p-3">
                <div class="line-clamp-2">{{ $q->prompt }}</div>
              </td>
              <td class="p-3">{{ $q->trait_key ?? '—' }}</td>
              <td class="p-3 uppercase">{{ $q->qtype }}</td>
              <td class="p-3">{{ $q->options->count() }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.questions.show',$q) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">View</a>
                  <a href="{{ route('admin.questions.edit',$q) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</a>
                  <form method="POST" action="{{ route('admin.questions.destroy',$q) }}" onsubmit="return confirm('Delete question?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="p-10 text-center text-sm opacity-70">Belum ada pertanyaan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">Showing <span class="font-semibold">{{ $questions->firstItem() ?? 0 }}</span> to <span class="font-semibold">{{ $questions->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $questions->total() }}</span> results</div>
      <div>{{ $questions->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
