@extends('layouts.admin')
@section('title','Certificate Templates — BERKEMAH')

@section('content')
<div x-data="{ q:@js(request('q')??''), showFilters: {{ request()->has('q')?'true':'false' }} }" class="space-y-6">

  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3h14v18l-7-3-7 3V3Z"/></svg>
        Certificate Templates
      </h1>
      <p class="text-sm opacity-70">Template latar, field dinamis, dan status aktif/nonaktif.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.certificate-templates.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700">New Template</a>
      <button type="button" @click="showFilters=!showFilters" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Filters</button>
    </div>
  </div>

  <form method="GET" x-show="showFilters" x-transition class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search name</label>
      <div class="relative">
        <input name="q" x-model="q" placeholder="Search template…" class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
      </div>
    </div>
    <div class="flex items-end">
      <button class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800">Apply</button>
      @if(request()->has('q'))
        <a href="{{ route('admin.certificate-templates.index') }}" class="ml-2 px-4 py-2 rounded-xl border hover:bg-gray-50">Reset</a>
      @endif
    </div>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm"><span class="font-semibold">{{ $templates->total() }}</span> templates found</div>
      <div class="text-xs opacity-70">Page {{ $templates->currentPage() }} / {{ $templates->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="p-3 text-left w-16">ID</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Background</th>
            <th class="p-3 text-left w-24">Active</th>
            <th class="p-3 text-center w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($templates as $t)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $t->id }}</td>
              <td class="p-3">{{ $t->name }}</td>
              <td class="p-3">
                @if($t->background_url)
                  <a href="{{ $t->background_url }}" target="_blank" class="text-blue-600 underline">Preview</a>
                @else
                  <span class="text-xs opacity-60">—</span>
                @endif
              </td>
              <td class="p-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $t->is_active ? 'bg-emerald-100 text-emerald-800':'bg-gray-100 text-gray-700' }}">
                  {{ $t->is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.certificate-templates.show',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">View</a>
                  <a href="{{ route('admin.certificate-templates.edit',$t) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</a>
                  <form method="POST" action="{{ route('admin.certificate-templates.destroy',$t) }}" onsubmit="return confirm('Delete template?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-10 text-center text-sm opacity-70">Belum ada template.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">Showing <span class="font-semibold">{{ $templates->firstItem() ?? 0 }}</span> to <span class="font-semibold">{{ $templates->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $templates->total() }}</span> results</div>
      <div>{{ $templates->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
