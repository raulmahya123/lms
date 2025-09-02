@extends('layouts.admin')

@section('title','Options — BERKEMAH')

@section('content')
<div class="space-y-6">

  {{-- HEADER --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- list icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
        Options
      </h1>
      <p class="text-sm opacity-70">Kelola opsi jawaban untuk setiap question.</p>
    </div>

    <a href="{{ route('admin.options.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
      {{-- plus icon --}}
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
      Add Option
    </a>
  </div>

  {{-- FLASH MESSAGE --}}
  @if(session('ok'))
    <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('ok') }}
    </div>
  @endif

  {{-- TABLE --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $options->total() }}</span>
        <span class="opacity-70">options found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $options->currentPage() }} / {{ $options->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left w-12">#</th>
            <th class="p-3 text-left">Question</th>
            <th class="p-3 text-left">Text</th>
            <th class="p-3 text-left">Correct</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse ($options as $opt)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $opt->id }}</td>
              <td class="p-3">{{ \Illuminate\Support\Str::limit($opt->question->prompt ?? '-', 40) }}</td>
              <td class="p-3">{{ \Illuminate\Support\Str::limit($opt->text, 50) }}</td>
              <td class="p-3">
                @if($opt->is_correct)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                    {{-- check icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 0 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 0 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z"/></svg>
                    True
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-700">
                    {{-- x icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.59l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.65l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.715-4.715-4.715-4.715a.75.75 0 0 1 0-1.06Z"/></svg>
                    False
                  </span>
                @endif
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.options.show',$opt) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition text-blue-700"
                     title="View option">
                    {{-- eye icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5c-7 0-10 7.5-10 7.5s3 7.5 10 7.5 10-7.5 10-7.5-3-7.5-10-7.5Zm0 12a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/></svg>
                    View
                  </a>
                  <a href="{{ route('admin.options.edit',$opt) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition text-yellow-600"
                     title="Edit option">
                    {{-- pencil icon --}}
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/></svg>
                    Edit
                  </a>
                  <form action="{{ route('admin.options.destroy',$opt) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus opsi ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition"
                            title="Delete option">
                      {{-- trash icon --}}
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/></svg>
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="p-10">
                <div class="flex flex-col items-center justify-center gap-3 text-center">
                  <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                    {{-- empty icon --}}
                    <svg class="w-8 h-8 opacity-50" viewBox="0 0 24 24" fill="currentColor"><path d="M6.75 3A2.75 2.75 0 0 0 4 5.75v12.5A2.75 2.75 0 0 0 6.75 21h10.5A2.75 2.75 0 0 0 20 18.25V9.5a.75.75 0 0 0-.22-.53l-5.75-5.75A.75.75 0 0 0 13.5 3h-6.75Z"/></svg>
                  </div>
                  <div class="text-lg font-semibold">Belum ada option</div>
                  <p class="text-sm opacity-70">Tambahkan opsi jawaban pertama untuk question.</p>
                  <a href="{{ route('admin.options.create') }}"
                     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
                    {{-- plus icon --}}
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
                    Add Option
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination strip --}}
    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">
        Showing
        <span class="font-semibold">{{ $options->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $options->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $options->total() }}</span>
        results
      </div>
      <div>
        {{ $options->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
