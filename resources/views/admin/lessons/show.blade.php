@extends('layouts.admin')

@section('title', 'View Lesson — '.$lesson->title)

@push('styles')
<style>
  [x-cloak]{display:none!important}
  .ratio-16by9{position:relative;padding-top:56.25%}
  .ratio-16by9 > *{position:absolute;inset:0;width:100%;height:100%}
</style>
@endpush

@section('content')
<div
  x-data="playerPage({
    videos: @js($videos),
    active: @js($active),
  })"
  x-init="init()"
  class="space-y-4"
  @keydown.window.prevent.left="prev()"
  @keydown.window.prevent.right="next()"
>
  {{-- Breadcrumb --}}
  <div class="text-sm text-gray-500">
    <a href="{{ route('admin.lessons.index') }}" class="hover:underline">Lessons</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">{{ $lesson->module?->course?->title ?? '—' }}</span>
    <span class="mx-1">/</span>
    <span class="text-gray-700">{{ $lesson->module?->title ?? '—' }}</span>
  </div>

  {{-- Header --}}
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold">{{ $lesson->title }}</h1>
      <p class="text-sm opacity-70">
        {{ $lesson->module?->course?->title ?? '-' }} — {{ $lesson->module?->title ?? '-' }}
      </p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.lessons.edit',$lesson) }}"
         class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Edit</a>
      <a href="{{ route('admin.lessons.index') }}"
         class="px-3 py-1.5 rounded-lg border hover:bg-gray-50">Back</a>
    </div>
  </div>

  <div class="grid lg:grid-cols-[1fr,360px] gap-6">
    {{-- Player --}}
    <div class="rounded-2xl border bg-white p-4">
      <template x-if="current">
        <div class="space-y-3">
          <div class="ratio-16by9 rounded-lg overflow-hidden border bg-black/5">
            <template x-if="currentEmbed.type === 'youtube'">
              <iframe :src="currentEmbed.src" frameborder="0" allowfullscreen
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share">
              </iframe>
            </template>

            <template x-if="currentEmbed.type === 'vimeo'">
              <iframe :src="currentEmbed.src" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
            </template>

            <template x-if="currentEmbed.type === 'mp4'">
              <video :src="currentEmbed.src" controls playsinline></video>
            </template>

            <template x-if="currentEmbed.type === 'link'">
              <div class="w-full h-full flex items-center justify-center bg-gray-50">
                <a :href="currentEmbed.src" target="_blank" class="underline text-blue-600">Open Link</a>
              </div>
            </template>
          </div>

          <div class="flex items-center justify-between">
            <div class="min-w-0">
              <div class="font-medium truncate" x-text="current.title || 'Untitled'"></div>
              <a class="text-xs text-blue-600 hover:underline truncate block" :href="current.url" target="_blank" x-text="current.url"></a>
            </div>
            <div class="flex items-center gap-2">
              <button @click="prev()" class="px-2 py-1 rounded border hover:bg-gray-50" title="Prev (←)">Prev</button>
              <button @click="next()" class="px-2 py-1 rounded border hover:bg-gray-50" title="Next (→)">Next</button>
            </div>
          </div>
        </div>
      </template>

      <template x-if="!current">
        <div class="p-10 text-center text-sm opacity-70">No videos.</div>
      </template>
    </div>

    {{-- Playlist / list --}}
    <div class="rounded-2xl border bg-white p-3">
      <div class="px-2 py-1 text-xs text-gray-500">Playlist ( <span x-text="videos.length"></span> )</div>
      <ul class="mt-1 space-y-1 max-h-[70vh] overflow-auto">
        <template x-for="(v, idx) in videos" :key="idx">
          <li>
            <button
              @click="set(idx)"
              class="w-full text-left px-3 py-2 rounded-lg border hover:bg-gray-50 flex items-start gap-2"
              :class="idx === active ? 'bg-gray-50 border-gray-300' : 'border-transparent'"
            >
              {{-- play icon --}}
              <svg class="w-4 h-4 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
              <div class="min-w-0">
                <div class="text-sm font-medium truncate" x-text="v.title || ('Video #' + (idx+1))"></div>
                <div class="text-xs text-gray-500 truncate" x-text="v.url"></div>
              </div>
            </button>
          </li>
        </template>
      </ul>
    </div>
  </div>
</div>

@push('scripts')
<script>
function playerPage({ videos, active }) {
  return {
    videos: Array.isArray(videos) ? videos : [],
    active: Number.isInteger(active) ? active : 0,
    current: null,
    currentEmbed: { type: 'link', src: '' },

    init() {
      this.set(this.active);
    },
    set(i) {
      if (i < 0 || i >= this.videos.length) return;
      this.active = i;
      this.current = this.videos[i] || null;
      this.currentEmbed = this.buildEmbed(this.current?.url || '');
      // update URL query ?v=...
      const u = new URL(window.location.href);
      u.searchParams.set('v', i);
      history.replaceState({}, '', u);
    },
    prev() { this.set(this.active - 1); },
    next() { this.set(this.active + 1); },

    buildEmbed(url) {
      if (!url || typeof url !== 'string') return { type:'link', src:'' };

      // YouTube
      // matches: https://www.youtube.com/watch?v=ID or https://youtu.be/ID
      const ytMatch1 = url.match(/youtube\.com\/watch\?v=([A-Za-z0-9_\-]+)/i);
      const ytMatch2 = url.match(/youtu\.be\/([A-Za-z0-9_\-]+)/i);
      const ytid = ytMatch1?.[1] || ytMatch2?.[1];
      if (ytid) {
        return {
          type: 'youtube',
          src: `https://www.youtube.com/embed/${ytid}?rel=0&modestbranding=1`,
        };
      }

      // Vimeo
      const vimeoMatch = url.match(/vimeo\.com\/(\d+)/i);
      if (vimeoMatch) {
        return {
          type: 'vimeo',
          src: `https://player.vimeo.com/video/${vimeoMatch[1]}`,
        };
      }

      // MP4 / direct video
      if (/\.(mp4|webm|ogg)(\?|#|$)/i.test(url)) {
        return { type: 'mp4', src: url };
      }

      // fallback: link biasa
      return { type: 'link', src: url };
    },
  }
}
</script>
@endpush
@endsection
