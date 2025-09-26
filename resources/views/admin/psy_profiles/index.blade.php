@extends('layouts.admin')
@section('title', 'Psych Profiles')

@section('content')
{{-- <div class="max-w-6xl mx-auto">
  @if(session('ok'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-3 py-2 text-sm">
      {{ session('ok') }}
    </div>
  @endif --}}

  <header class="flex items-center justify-between gap-3 mb-4">
    <h1 class="text-2xl font-semibold text-gray-900">Psych Profiles</h1>
    <a href="{{ route('admin.psy-profiles.create') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-blue-600 text-white hover:opacity-90">
      + New Profile
    </a>
  </header>

  <form method="get" class="flex flex-wrap items-end gap-3 mb-4">
    <div>
      <label class="block text-xs text-gray-500 mb-1">Filter Test</label>
      <select name="test_id" class="rounded-lg border-gray-300">
        <option value="">— All —</option>
        @foreach($tests as $t)
          <option value="{{ $t->id }}" @selected($testId===$t->id)>{{ $t->name }} ({{ $t->track }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-500 mb-1">Keyword</label>
      <input type="text" name="q" value="{{ $q }}" class="rounded-lg border-gray-300" placeholder="key / name / desc">
    </div>
    <button class="px-3 py-2 rounded-lg border border-gray-300">Apply</button>
  </form>

  <div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="text-left px-3 py-2">Test</th>
          <th class="text-left px-3 py-2">Key</th>
          <th class="text-left px-3 py-2">Name</th>
          <th class="text-left px-3 py-2">Range</th>
          <th class="text-left px-3 py-2">Updated</th>
          <th class="px-3 py-2"></th>
        </tr>
      </thead>
      <tbody>
      @forelse($profiles as $p)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $p->test->name ?? '—' }} <span class="text-xs text-gray-500">({{ $p->test->track ?? '-' }})</span></td>
          <td class="px-3 py-2 font-mono">{{ $p->key }}</td>
          <td class="px-3 py-2">{{ $p->name }}</td>
          <td class="px-3 py-2">{{ $p->min_total }} – {{ $p->max_total }}</td>
          <td class="px-3 py-2 text-gray-500">{{ optional($p->updated_at)->diffForHumans() }}</td>
          <td class="px-3 py-2 text-right">
            <a href="{{ route('admin.psy-profiles.edit',$p) }}" class="text-blue-700 hover:underline mr-2">Edit</a>
            <form action="{{ route('admin.psy-profiles.destroy',$p) }}" method="post" class="inline"
                  onsubmit="return confirm('Hapus profile ini?')">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="px-3 py-6 text-center text-gray-500" colspan="6">Belum ada profile.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $profiles->links() }}
  </div>
</div>
@endsection
