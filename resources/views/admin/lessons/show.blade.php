@extends('layouts.admin')
@section('title', 'Lesson — '.$lesson->title)

@section('content')
@php
  // pastikan relasi whitelist tersedia
  $lesson->loadMissing('driveWhitelists.user');

  // video aktif
  $current = $videos[$active] ?? null;
  $url     = is_array($current) ? ($current['url'] ?? null) : null;
  $title   = is_array($current) ? ($current['title'] ?? 'Untitled') : 'Untitled';

  // helper deteksi
  $isYoutube = function (?string $u) {
      return $u && preg_match('~(youtube\.com/watch\?v=|youtu\.be/)~i', $u);
  };
  $ytId = function (?string $u) {
      if(!$u) return null;
      if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $u, $m)) return $m[1];
      if (preg_match('~v=([A-Za-z0-9_-]{6,})~', $u, $m)) return $m[1];
      return null;
  };
  $isDrive = function (?string $u) {
      return $u && str_contains($u, 'drive.google.com');
  };
  $drivePreview = function (?string $u) {
      if(!$u) return null;
      if (preg_match('~drive\.google\.com/file/d/([^/]+)/~', $u, $m)) {
          return "https://drive.google.com/file/d/{$m[1]}/preview";
      }
      if (preg_match('~open\?id=([^&]+)~', $u, $m)) {
          return "https://drive.google.com/file/d/{$m[1]}/preview";
      }
      return $u;
  };

  // ringkasan whitelist
  $wl = $lesson->driveWhitelists ?? collect();
  $total    = $wl->count();
  $approved = $wl->where('status','approved')->count();
  $pending  = $wl->where('status','pending')->count();
  $rejected = $wl->where('status','rejected')->count();

  $driveStatus = $lesson->drive_status ?? null;
  $statusClass = match($driveStatus){
    'approved' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
    'pending'  => 'bg-yellow-100 text-yellow-700',
    default    => 'bg-gray-100 text-gray-700',
  };
@endphp

<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide">{{ $lesson->title }}</h1>
      <p class="text-sm opacity-70">
        {{ $lesson->module?->course?->title ?? '—' }} — {{ $lesson->module?->title ?? '—' }}
      </p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.lessons.index') }}"
         class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">← Back</a>
      <a href="{{ route('admin.lessons.edit', $lesson) }}"
         class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">Edit</a>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    {{-- Player + detail --}}
    <div class="lg:col-span-2 space-y-4">
      {{-- Player --}}
      <div class="rounded-2xl border overflow-hidden bg-black">
        @if($url)
          @if($isYoutube($url))
            @php $id = $ytId($url); @endphp
            @if($id)
              <iframe
                class="w-full aspect-video"
                src="https://www.youtube.com/embed/{{ $id }}"
                title="{{ $title }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            @else
              <div class="p-6 text-white">Tidak bisa mendeteksi ID YouTube dari URL ini.</div>
            @endif
          @elseif($isDrive($url))
            <iframe
              class="w-full aspect-video"
              src="{{ $drivePreview($url) }}"
              title="{{ $title }}"
              allow="autoplay"
              allowfullscreen
            ></iframe>
          @else
            <div class="p-6 bg-white">
              <p class="mb-2 font-medium">Tidak ada embed untuk URL ini:</p>
              <a class="text-blue-600 underline break-all" href="{{ $url }}" target="_blank" rel="noopener">
                {{ $url }}
              </a>
            </div>
          @endif
        @else
          <div class="p-6 text-white">Belum ada URL aktif.</div>
        @endif
      </div>

      {{-- Judul aktif + deskripsi kecil --}}
      <div class="rounded-2xl border bg-white p-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-lg">{{ $title }}</h2>
            <p class="text-xs opacity-70">Item #{{ $active+1 }} dari {{ count($videos) }}</p>
          </div>
          <div>
            @if($lesson->is_free)
              <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Free</span>
            @else
              <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Premium</span>
            @endif
          </div>
        </div>
      </div>

      {{-- Content (jika ada) --}}
      @if(!empty($lesson->content))
        <div class="rounded-2xl border bg-white p-4 prose max-w-none">
          @if(is_array($lesson->content))
            <pre class="text-xs bg-gray-50 p-3 rounded-xl overflow-auto">{{ json_encode($lesson->content, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
          @else
            {!! nl2br(e($lesson->content)) !!}
          @endif
        </div>
      @endif
    </div>

    {{-- Sidebar: playlist + Drive --}}
    <div class="space-y-4">
      {{-- Playlist --}}
      <div class="rounded-2xl border bg-white">
        <div class="px-4 py-3 border-b bg-gray-50 font-semibold">Playlist</div>
        <div class="max-h-[60vh] overflow-y-auto divide-y">
          @forelse($videos as $i => $v)
            @php
              $isActive = $i === $active;
              $vt = $v['title'] ?? 'Untitled';
              $vu = $v['url']   ?? null;
            @endphp
            <a href="{{ route('admin.lessons.show', [$lesson, 'v' => $i]) }}"
               class="block px-4 py-3 hover:bg-gray-50 {{ $isActive ? 'bg-blue-50' : '' }}">
              <div class="flex items-start gap-2">
                <div class="mt-0.5">
                  @if($isActive)
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
                  @else
                    <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
                  @endif
                </div>
                <div class="min-w-0">
                  <div class="font-medium truncate">{{ $vt }}</div>
                  <div class="text-xs opacity-60 truncate">{{ $vu }}</div>
                </div>
              </div>
            </a>
          @empty
            <div class="px-4 py-6 text-sm opacity-70">Belum ada URL konten.</div>
          @endforelse
        </div>
      </div>

      {{-- Google Drive --}}
      <div class="rounded-2xl border bg-white">
        <div class="px-4 py-3 border-b bg-gray-50 font-semibold">Google Drive</div>
        <div class="p-4 space-y-3">
          <div class="flex items-center gap-2 flex-wrap">
            @php $hasLink = !empty($lesson->drive_link ?? null); @endphp
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-xs"
                  title="{{ $hasLink ? $lesson->drive_link : 'No drive link' }}">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M13.06 7.06a3 3 0 0 1 4.24 0l.64.64a3 3 0 0 1 0 4.24l-3.18 3.18a3 3 0 0 1-4.24 0l-.64-.64a.75.75 0 0 1 1.06-1.06l.64.64a1.5 1.5 0 0 0 2.12 0l3.18-3.18a1.5 1.5 0 0 0 0-2.12l-.64-.64a1.5 1.5 0 0 0-2.12 0.0.75.75 0 1 1-1.06-1.06Z"/></svg>
              {{ $hasLink ? 'Link' : 'No Link' }}
            </span>

            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $statusClass }}">
              {{ $driveStatus ? ucfirst($driveStatus) : '—' }}
            </span>

            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 text-xs"
                  title="Approved: {{ $approved }} • Pending: {{ $pending }} • Rejected: {{ $rejected }}">
              WL {{ $total }}/4
            </span>
          </div>

          @if($hasLink)
            <div class="text-xs break-all">
              <a class="text-blue-600 underline" href="{{ $lesson->drive_link }}" target="_blank" rel="noopener">
                {{ $lesson->drive_link }}
              </a>
            </div>
          @endif

          @if(($lesson->driveWhitelists ?? collect())->count())
            <div class="overflow-hidden rounded-xl border">
              <table class="min-w-full text-xs">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="text-left px-3 py-2">Email</th>
                    <th class="text-left px-3 py-2">User</th>
                    <th class="text-left px-3 py-2">Status</th>
                    <th class="text-left px-3 py-2">Verified At</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($lesson->driveWhitelists as $w)
                    @php
                      $badge = match($w->status){
                        'approved' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        default    => 'bg-yellow-100 text-yellow-700',
                      };
                    @endphp
                    <tr class="border-t">
                      <td class="px-3 py-2">{{ $w->email }}</td>
                      <td class="px-3 py-2">
                        @if($w->user)
                          {{ $w->user->name }} <span class="opacity-60">({{ $w->user->email }})</span>
                        @else
                          <span class="opacity-60">—</span>
                        @endif
                      </td>
                      <td class="px-3 py-2">
                        <span class="px-2 py-0.5 rounded {{ $badge }}">{{ ucfirst($w->status) }}</span>
                      </td>
                      <td class="px-3 py-2">{{ $w->verified_at ? $w->verified_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-xs opacity-70">Belum ada whitelist email.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
