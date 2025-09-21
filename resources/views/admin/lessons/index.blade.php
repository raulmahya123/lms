@extends('layouts.admin')
@section('title','Lessons — BERKEMAH')

@section('content')
<div x-data="{ q: @js(request('q') ?? ''), showFilters:false }" class="space-y-6">

  {{-- HEADER / ACTIONS --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Lesson/Play icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.5 5.75A2.75 2.75 0 0 1 7.25 3h9.5A2.75 2.75 0 0 1 19.5 5.75v12.5A2.75 2.75 0 0 1 16.75 21h-9.5A2.75 2.75 0 0 1 4.5 18.25V5.75Zm5 1.25a.75.75 0 0 0-.75.75v8.5a.75.75 0 0 0 1.14.64l6.5-4.25a.75.75 0 0 0 0-1.28l-6.5-4.25a.75.75 0 0 0-.39-.11Z"/>
        </svg>
        Lessons
      </h1>
      <p class="text-sm opacity-70">Kelola pelajaran per modul. Filter cepat, cari judul, dan aksi edit/hapus.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.lessons.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white shadow hover:bg-blue-700 transition">
        {{-- plus icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/></svg>
        New Lesson
      </a>
      <button type="button" @click="showFilters=!showFilters"
              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border bg-white hover:bg-gray-50 transition">
        {{-- filter icon --}}
        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M3.75 6A.75.75 0 0 1 4.5 5.25h15a.75.75 0 0 1 .6 1.2l-5.4 7.2v4.35a.75.75 0 0 1-1.065.683l-3-1.35A.75.75 0 0 1 10.5 16.5v-2.85l-5.4-7.2A.75.75 0 0 1 3.75 6Z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- FILTERS / SEARCH --}}
  <form method="GET"
        x-show="showFilters"
        x-transition
        class="rounded-2xl border bg-white p-4 grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Module</label>
      <div class="relative">
        <select name="module_id" class="w-full border rounded-xl pl-10 pr-3 py-2">
          <option value="">— All Modules —</option>
          @php
            $__modules = \App\Models\Module::with('course:id,title')
              ->orderBy('course_id')->orderBy('ordering')->get();
          @endphp
          @foreach($__modules as $m)
            <option value="{{ $m->id }}" @selected(request('module_id')==$m->id)>
              {{ $m->course?->title }} — {{ $m->title }}
            </option>
          @endforeach
        </select>
        {{-- list icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
        </svg>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Search title</label>
      <div class="relative">
        <input type="text" name="q" x-model="q" placeholder="Cari judul lesson…"
               class="w-full border rounded-xl pl-10 pr-3 py-2">
        {{-- search icon --}}
        <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 3.75a6.25 6.25 0 1 1 3.94 11.09l3.1 3.1a.75.75 0 1 1-1.06 1.06l-3.1-3.1A6.25 6.25 0 0 1 10 3.75Zm0 1.5a4.75 4.75 0 1 0 0 9.5 4.75 4.75 0 0 0 0-9.5Z"/>
        </svg>
      </div>
    </div>

    <div class="flex items-end gap-2">
      <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
        Apply
      </button>
      @if(request()->hasAny(['module_id','q']))
        <a href="{{ route('admin.lessons.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          Reset
        </a>
      @endif
    </div>
  </form>

  {{-- TABLE CARD --}}
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
      <div class="text-sm">
        <span class="font-semibold">{{ $lessons->total() }}</span>
        <span class="opacity-70">lessons found</span>
      </div>
      <div class="text-xs opacity-70">Page {{ $lessons->currentPage() }} / {{ $lessons->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700 sticky top-0">
          <tr>
            <th class="p-3 text-left">Course</th>
            <th class="p-3 text-left">Module</th>
            <th class="p-3 text-left">Title & Meta</th>
            <th class="p-3 text-left">Content URLs</th>
            <th class="p-3 text-left w-64">Drive</th>
            <th class="p-3 text-left w-28">Ordering</th>
            <th class="p-3 text-left w-24">Free?</th>
            <th class="p-3 text-center w-44">Actions</th>
          </tr>
        </thead>
        <tbody class="[&>tr:hover]:bg-gray-50">
          @forelse($lessons as $l)
            @php
              // --- helper: flatten value to text ---
              $toText = function ($v): string {
                  if (is_array($v)) {
                      $flat = [];
                      $it = function($x) use (&$flat, &$it) {
                          if (is_array($x)) { foreach ($x as $y) $it($y); }
                          else $flat[] = is_scalar($x) ? (string)$x : '';
                      };
                      $it($v);
                      $s = trim(implode(' • ', array_filter($flat)));
                      return $s;
                  }
                  if (is_object($v)) return '';
                  return (string)($v ?? '');
              };

              // --- Content (array | JSON string | string) -> text ringkas ---
              $contentRaw = $l->content;
              if (is_string($contentRaw)) {
                  $decoded = json_decode($contentRaw, true);
                  $contentArr = is_array($decoded) ? $decoded : [$contentRaw];
              } elseif (is_array($contentRaw)) {
                  $contentArr = $contentRaw;
              } else {
                  $contentArr = [];
              }
              $contentText = collect($contentArr)
                  ->flatten()
                  ->filter(fn($v) => is_scalar($v) && trim((string)$v) !== '')
                  ->implode("\n");

              // --- Content URLs normalize ---
              $videos = $l->content_url;
              if (is_string($videos)) {
                  $decoded = json_decode($videos, true);
                  $videos = is_array($decoded) ? $decoded : [];
              }

              // --- Tools / Benefits normalize ---
              $tools = $l->tools;
              if (is_string($tools)) {
                  $json = json_decode($tools, true);
                  $tools = is_array($json) ? $json : array_filter(array_map('trim', explode(',', $tools)));
              }
              if (!is_array($tools)) $tools = [];

              $benefits = $l->benefits;
              if (is_string($benefits)) {
                  $json = json_decode($benefits, true);
                  $benefits = is_array($json) ? $json : array_filter(array_map('trim', explode(',', $benefits)));
              }
              if (!is_array($benefits)) $benefits = [];

              // --- About/Reviews/Syllabus to string (aman untuk strip_tags) ---
              $aboutStr    = $toText($l->about);
              $reviewsStr  = $toText($l->reviews);
              $syllabusStr = $toText($l->syllabus);

              // --- Drive summary ---
              $wl = $l->driveWhitelists ?? collect();
              $total = $wl->count();
              $approved = $wl->where('status','approved')->count();
              $pending  = $wl->where('status','pending')->count();
              $rejected = $wl->where('status','rejected')->count();

              $driveStatus = $l->drive_status ?? null;
              $statusClass = match($driveStatus){
                'approved' => 'bg-green-100 text-green-700',
                'rejected' => 'bg-red-100 text-red-700',
                'pending'  => 'bg-yellow-100 text-yellow-700',
                default    => 'bg-gray-100 text-gray-700',
              };
            @endphp

            <tr class="border-t align-top">
              <td class="p-3">{{ $l->module?->course?->title ?? '-' }}</td>
              <td class="p-3">{{ $l->module?->title ?? '-' }}</td>

              {{-- TITLE + META --}}
              <td class="p-3">
                <div class="font-medium">{{ $l->title }}</div>

                {{-- about (ringkas) --}}
                @if($aboutStr !== '')
                  <div class="text-xs text-gray-600 mt-1">
                    {{ \Illuminate\Support\Str::limit(strip_tags($aboutStr), 120) }}
                  </div>
                @endif

                {{-- content (ringkas) --}}
                @if($contentText !== '')
                  <div class="text-xs text-gray-600 mt-1">
                    <span class="font-medium">Content:</span>
                    {{ \Illuminate\Support\Str::limit(strip_tags($contentText), 120) }}
                  </div>
                @endif

                {{-- tools / benefits badges --}}
                <div class="flex flex-wrap gap-1.5 mt-1">
                  @foreach($tools as $t)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-[11px]">
                      {{ $t }}
                    </span>
                  @endforeach
                  @foreach($benefits as $b)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px]">
                      {{ $b }}
                    </span>
                  @endforeach
                </div>

                {{-- reviews & syllabus (ringkas) --}}
                @if($reviewsStr !== '' || $syllabusStr !== '')
                  <div class="flex flex-wrap items-center gap-2 mt-1 text-[11px] text-gray-600">
                    @if($reviewsStr !== '')
                      <span title="Reviews">
                        <svg class="inline w-3.5 h-3.5 -mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                          <path d="m11.48 3.5.84 2.54c.2.61.76 1.02 1.4 1.02h2.67c1.43 0 2.02 1.83.87 2.66l-2.16 1.56c-.53.38-.75 1.07-.54 1.69l.83 2.53c.45 1.36-1.12 2.49-2.28 1.66l-2.16-1.56a1.5 1.5 0 0 0-1.76 0l-2.16 1.56c-1.16.83-2.73-.3-2.28-1.66l.83-2.53c.21-.62-.01-1.31-.54-1.69L3.74 9.72c-1.16-.83-.56-2.66.87-2.66h2.67c.64 0 1.21-.41 1.4-1.02l.84-2.54c.45-1.36 2.39-1.36 2.95 0Z"/>
                        </svg>
                        {{ \Illuminate\Support\Str::limit(strip_tags($reviewsStr), 80) }}
                      </span>
                    @endif
                    @if($syllabusStr !== '')
                      <span title="Syllabus">{{ \Illuminate\Support\Str::limit(strip_tags($syllabusStr), 80) }}</span>
                    @endif
                  </div>
                @endif
              </td>

              {{-- Content URLs --}}
              <td class="p-3">
                @if(!empty($videos))
                  <div class="flex flex-wrap gap-1.5 max-w-[420px]">
                    @foreach($videos as $i => $video)
                      <a href="{{ route('admin.lessons.show', [$l, 'v' => $i]) }}"
                         class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border hover:bg-gray-50 text-xs"
                         title="Play: {{ $video['title'] ?? 'Untitled' }}">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
                        <span class="truncate max-w-[160px]">{{ $video['title'] ?? 'Untitled' }}</span>
                      </a>
                    @endforeach
                  </div>
                @else
                  <span class="text-xs opacity-60">-</span>
                @endif
              </td>

              {{-- Drive summary --}}
              <td class="p-3">
                <div class="flex items-center gap-2 flex-wrap">
                  @php $hasLink = !empty($l->drive_link ?? null); @endphp
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-xs"
                        title="{{ $hasLink ? $l->drive_link : 'No drive link' }}">
                    {{ $hasLink ? 'Link' : 'No Link' }}
                  </span>

                  @if($hasLink)
                    <a href="{{ $l->drive_link }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-xs hover:bg-gray-50">
                      Open
                    </a>
                  @endif

                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $statusClass }}">
                    {{ $driveStatus ? ucfirst($driveStatus) : '—' }}
                  </span>

                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs"
                        title="Approved: {{ $approved }} • Pending: {{ $pending }} • Rejected: {{ $rejected }}">
                    WL {{ $total }}/4
                  </span>

                  @if($approved > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">
                      Approved: {{ $approved }}
                    </span>
                  @endif

                  @if($pending > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs">
                      Pending: {{ $pending }}
                    </span>
                  @endif
                </div>
              </td>

              <td class="p-3">{{ $l->ordering }}</td>

              <td class="p-3">
                @if($l->is_free)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">Yes</span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">No</span>
                @endif
              </td>

              <td class="p-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.lessons.show',$l) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition"
                     title="View / Play">
                    View
                  </a>
                  <a href="{{ route('admin.lessons.edit',$l) }}"
                     class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border hover:bg-gray-50 transition" title="Edit">
                    Edit
                  </a>
                  <form method="POST" action="{{ route('admin.lessons.destroy',$l) }}"
                        onsubmit="return confirm('Delete this lesson?')">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition" title="Delete">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="p-10 text-center text-sm opacity-70">
                Belum ada lesson.
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
        <span class="font-semibold">{{ $lessons->firstItem() ?? 0 }}</span>
        to
        <span class="font-semibold">{{ $lessons->lastItem() ?? 0 }}</span>
        of
        <span class="font-semibold">{{ $lessons->total() }}</span>
        results
      </div>
      <div>
        {{ $lessons->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
