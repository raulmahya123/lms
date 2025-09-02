@extends('layouts.admin')

@section('title','View Option — BERKEMAH')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- eye icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 4.5c-7 0-10 7.5-10 7.5s3 7.5 10 7.5 10-7.5 10-7.5-3-7.5-10-7.5Zm0 12a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/>
        </svg>
        Option Detail
      </h1>
      <p class="text-sm opacity-70">Detail opsi jawaban dan keterkaitannya dengan question.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.options.edit',$option) }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition"
         title="Edit option">
        {{-- pencil icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
          <path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/>
        </svg>
        Edit
      </a>

      <form action="{{ route('admin.options.destroy',$option) }}" method="POST"
            onsubmit="return confirm('Yakin hapus opsi ini?')">
        @csrf @method('DELETE')
        <button type="submit"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 transition"
                title="Delete option">
          {{-- trash icon --}}
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9.75 3a1 1 0 0 0-.94.66L8.5 4.5H6a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5h-2.5l-.31-.84a1 1 0 0 0-.94-.66h-4.5ZM6.75 8a.75.75 0 0 1 .75.75v8a1.75 1.75 0 0 0 1.75 1.75h4.5A1.75 1.75 0 0 0 15.5 16.75v-8a.75.75 0 0 1 1.5 0v8a3.25 3.25 0 0 1-3.25 3.25h-4.5A3.25 3.25 0 0 1 6 16.75v-8A.75.75 0 0 1 6.75 8Z"/>
          </svg>
          Delete
        </button>
      </form>

      <a href="{{ route('admin.options.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition"
         title="Back to list">
        {{-- back icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.28 6.22a.75.75 0 0 1 0 1.06L6.56 11h11.19a.75.75 0 0 1 0 1.5H6.56l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z"/></svg>
        Back
      </a>
    </div>
  </div>

  {{-- CARD DETAIL --}}
  <div class="rounded-2xl border bg-white p-6 space-y-6">
    <div class="grid md:grid-cols-2 gap-6">
      {{-- ID --}}
      <div>
        <div class="text-sm font-medium text-gray-600 flex items-center gap-2">
          {{-- tag icon --}}
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M9.21 3.42A2.25 2.25 0 0 1 10.8 3h5.95A2.25 2.25 0 0 1 19 5.25V11.2c0 .6-.24 1.17-.66 1.6l-5.54 5.54a2.25 2.25 0 0 1-3.18 0l-5.9-5.9a2.25 2.25 0 0 1 0-3.18L9.21 3.42Z"/></svg>
          ID
        </div>
        <div class="mt-1 text-lg font-semibold">#{{ $option->id }}</div>
      </div>

      {{-- CORRECT --}}
      <div>
        <div class="text-sm font-medium text-gray-600 flex items-center gap-2">
          {{-- status icon --}}
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 7.5h10a4.5 4.5 0 1 1 0 9H7a4.5 4.5 0 1 1 0-9Z"/></svg>
          Correct?
        </div>
        <div class="mt-1">
          @if($option->is_correct)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5Zm-1.03 12.03 4.47-4.47a.75.75 0 1 0-1.06-1.06l-3.94 3.94-1.41-1.41a.75.75 0 1 0-1.06 1.06l1.94 1.94a.75.75 0 0 0 1.06 0Z"/>
              </svg>
              True
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-200 text-gray-700">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.59l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.65l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.715-4.715-4.715-4.715a.75.75 0 0 1 0-1.06Z"/>
              </svg>
              False
            </span>
          @endif
        </div>
      </div>
    </div>

    {{-- QUESTION --}}
    <div>
      <div class="text-sm font-medium text-gray-600 flex items-center gap-2">
        {{-- question icon --}}
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25a9.75 9.75 0 1 1 0 19.5 9.75 9.75 0 0 1 0-19.5ZM12 17a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm0-9a3.5 3.5 0 0 0-3.5 3.5.75.75 0 0 0 1.5 0 2 2 0 1 1 2.86 1.8c-.87.38-1.36 1.2-1.36 2.2v.25a.75.75 0 0 0 1.5 0v-.25c0-.42.17-.67.56-.84A3.5 3.5 0 0 0 12 8Z"/></svg>
        Question
      </div>
      <div class="mt-1 text-base">
        {{ \Illuminate\Support\Str::limit($option->question->prompt ?? '-', 200) }}
      </div>
    </div>

    {{-- OPTION TEXT --}}
    <div>
      <div class="text-sm font-medium text-gray-600 flex items-center gap-2">
        {{-- text icon --}}
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.5 6.75A.75.75 0 0 1 5.25 6h13.5a.75.75 0 0 1 0 1.5H5.25A.75.75 0 0 1 4.5 6.75ZM5.25 10.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5Zm0 4.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z"/>
        </svg>
        Option Text
      </div>
      <div class="mt-1 whitespace-pre-line text-base">
        {{ $option->text }}
      </div>
    </div>

    {{-- TIMESTAMPS (opsional) --}}
    <div class="grid md:grid-cols-2 gap-6 text-xs text-gray-600">
      <div>Created: <span class="font-medium">{{ optional($option->created_at)->format('Y-m-d H:i') }}</span></div>
      <div>Updated: <span class="font-medium">{{ optional($option->updated_at)->format('Y-m-d H:i') }}</span></div>
    </div>
  </div>

  {{-- BACK ONLY (mobile duplicate) --}}
  <div class="md:hidden">
    <a href="{{ route('admin.options.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition w-full justify-center">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.28 6.22a.75.75 0 0 1 0 1.06L6.56 11h11.19a.75.75 0 0 1 0 1.5H6.56l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z"/></svg>
      Back
    </a>
  </div>

</div>
@endsection
