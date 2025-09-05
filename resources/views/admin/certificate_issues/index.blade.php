@extends('layouts.admin')
@section('title','Certificate Issues — BERKEMAH')

@section('content')
<div x-data="{ q:@js(request('q')??''), showFilters: {{ request()->hasAny(['q','assessment_type'])?'true':'false' }} }" class="space-y-6">

  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v12H5.17L4 17.17V4Z"/><path d="M8 8h8v2H8V8Zm0 3h5v2H8v-2Z"/></svg>
        Certificate Issues
      </h1>
      <p class="text-sm opacity-70">Daftar sertifikat yang diterbitkan: serial, user, course/test, skor, dan jenis assessment.</p>
    </div>
    <div class="flex items-center gap-2">
      <button type="button" @click="showFilters=!showFilters" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Filters</button>
    </div>
  </div>

  <form method="GET" x-show="showFilters" x-transition class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Search serial / user</label>
      <div class="relative">
        <input name="q" x-model="q" placeholder="Search serial or user…" class="w-full border rounded-xl pl-10 pr-3 py-2">
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Z"/></svg>
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Assessment Type</label>
      @php $type = request('assessment_type'); @endphp
      <div class="relative">
        <select name="assessment_type" class="w-full border rounded-xl pl-10 pr-8 py-2">
          <option value="" @selected(!$type)>All</option>
          <option value="course" @selected($type==='course')>Course</option>
          <option value="psych"  @selected($type==='psych')>Psych Test</option>
        </select>
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7.5h12a.75.75 0 1 1 0 1.5H6Z"/></svg>
      </div>
    </div>
    <div class="flex items-end gap-2">
      <button class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800">Apply</button>
      @if(request()->hasAny(['q','assessment_type']))
        <a href="{{ route('admin.certificate-issues.index') }}" class="px-4 py-2 rounded-xl border hover:bg-gray-50">Reset</a>
      @endif
    </div>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm"><span class="font-semibold">{{ $issues->total() }}</span> issues found</div>
      <div class="text-xs opacity-70">Page {{ $issues->currentPage() }} / {{ $issues->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="p-3 text-left w-20">ID</th>
            <th class="p-3 text-left">Serial</th>
            <th class="p-3 text-left">User</th>
            <th class="p-3 text-left">Course/Test</th>
            <th class="p-3 text-left w-24">Type</th>
            <th class="p-3 text-left w-20">Score</th>
            <th class="p-3 text-left w-40">Issued</th>
            <th class="p-3 text-center w-40">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($issues as $i)
            <tr class="border-t">
              <td class="p-3 font-semibold text-gray-700">#{{ $i->id }}</td>
              <td class="p-3 font-mono">{{ $i->serial }}</td>
              <td class="p-3">{{ $i->user->name ?? '—' }}</td>
              <td class="p-3">{{ $i->course->title ?? '—' }}</td>
              <td class="p-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $i->assessment_type==='course'?'bg-blue-100 text-blue-800':'bg-purple-100 text-purple-800' }}">
                  {{ ucfirst($i->assessment_type) }}
                </span>
              </td>
              <td class="p-3">{{ is_null($i->score) ? '—' : rtrim(rtrim(number_format($i->score,2), '0'),'.') }}</td>
              <td class="p-3 text-sm text-gray-600">{{ optional($i->issued_at)->format('Y-m-d H:i') }}</td>
              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.certificate-issues.show',$i) }}" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Detail</a>
                  <form method="POST" action="{{ route('admin.certificate-issues.destroy',$i) }}" onsubmit="return confirm('Delete issue?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="p-10 text-center text-sm opacity-70">Belum ada sertifikat terbit.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t bg-gray-50 flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-sm opacity-70">Showing <span class="font-semibold">{{ $issues->firstItem() ?? 0 }}</span> to <span class="font-semibold">{{ $issues->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $issues->total() }}</span> results</div>
      <div>{{ $issues->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
@endsection
