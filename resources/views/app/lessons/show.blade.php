{{-- resources/views/app/lessons/show.blade.php --}}
@extends('app.layouts.base') {{-- <<< layout TANPA navbar --}}
@section('title', $lesson->title)

@php
use Illuminate\Support\Str;

/* ===================== Helpers ===================== */

/** Konversi URL ke embed iframe */
$toEmbed = function (?string $url) {
    if (!$url) return '';
    $u = Str::of($url);

    // YouTube
    if (Str::contains($url, ['youtube.com', 'youtu.be'])) {
        if ($u->contains('watch?v='))      $id = $u->after('watch?v=')->before('&');
        elseif ($u->contains('youtu.be/')) $id = $u->after('youtu.be/')->before('?');
        elseif ($u->contains('/shorts/'))  $id = $u->after('/shorts/')->before('?');
        else $id = '';
        return $id ? "https://www.youtube-nocookie.com/embed/{$id}" : $url;
    }
    // Vimeo
    if (Str::contains($url, 'vimeo.com')) {
        $id = $u->afterLast('/')->before('?');
        return $id ? "https://player.vimeo.com/video/{$id}" : $url;
    }
    // Loom
    if (Str::contains($url, 'loom.com')) {
        $id = $u->after('loom.com/')->after('/')->before('?'); // share/<id> | embed/<id>
        if ($u->contains('/share/')) return "https://www.loom.com/embed/{$id}";
        if ($u->contains('/embed/')) return $url;
    }
    // Google Drive
    if (Str::contains($url, 'drive.google.com')) {
        if ($u->contains('/file/d/')) {
            $id = $u->after('/file/d/')->before('/');
            return "https://drive.google.com/file/d/{$id}/preview";
        }
        if ($u->contains('/uc?id=')) {
            $id = $u->after('uc?id=')->before('&');
            return "https://drive.google.com/file/d/{$id}/preview";
        }
    }
    // File video langsung
    $ext = Str::lower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    if (in_array($ext, ['mp4','webm','mkv','mov'])) return $url;

    return $url;
};

/** Deteksi URL video (provider populer + ekstensi umum) */
$isVideoUrl = function (?string $url) {
    if (!$url) return false;
    $providers = ['youtube.com','youtu.be','vimeo.com','loom.com','drive.google.com'];
    if (Str::contains($url, $providers)) return true;
    $ext = Str::lower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    return in_array($ext, ['mp4','webm','mkv','mov']);
};

/** Apakah URL adalah Google Drive */
$isDriveUrl = fn (?string $url) => $url && Str::contains($url, 'drive.google.com');

/** Badge kecil */
$badge = fn (string $t) =>
    '<span class="inline-flex items-center text-[10px] font-semibold tracking-wide px-1.5 py-0.5 border rounded uppercase bg-gray-50">'.$t.'</span>';

/** Chip status kecil */
$chip = function ($label, $tone = 'gray') {
    $tones = [
        'green'  => 'bg-green-50 text-green-700 border-green-200',
        'amber'  => 'bg-amber-50 text-amber-700 border-amber-200',
        'red'    => 'bg-red-50 text-red-700 border-red-200',
        'blue'   => 'bg-blue-50 text-blue-700 border-blue-200',
        'gray'   => 'bg-gray-50 text-gray-700 border-gray-200',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
    ];
    $c = $tones[$tone] ?? $tones['gray'];
    return "<span class=\"inline-flex items-center text-xs px-2 py-0.5 rounded border $c\">$label</span>";
};

/** Jadikan nilai (array/json/csv/string) → array list rapi */
$toList = function ($value): array {
    if (is_array($value)) return array_values(array_filter(array_map('trim', $value)));
    if (is_string($value) && $value !== '') {
        // coba JSON
        $decoded = json_decode($value, true);
        if (is_array($decoded)) return array_values(array_filter(array_map('trim', $decoded)));
        // fallback CSV / baris
        if (str_contains($value, ',')) return array_values(array_filter(array_map('trim', explode(',', $value))));
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $value))));
    }
    return [];
};

/** Jadikan nilai (array/object/string) → string aman buat textarea/prose */
$stringify = function ($value): string {
    if (is_string($value)) return $value;
    if (is_array($value) || is_object($value)) {
        // tampilkan sebagai bullet nanti; tapi untuk fallback:
        return json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }
    return '';
};

/* ===================== Data utama ===================== */

$isCompleted = optional($progress)->completed_at !== null;

$rawLinks = isset($linksVisible) ? $linksVisible : ($links ?? []);
$norm = fn($i) => [
    'title' => $i['title'] ?? ($i['label'] ?? 'Untitled'),
    'url'   => $i['url']   ?? ($i['href']  ?? null),
    'type'  => Str::lower($i['type'] ?? 'link'),
];
$items = collect($rawLinks)->map($norm)->filter(fn($i)=>!empty($i['url']))->values();

$videoItems = $items->filter(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();
$otherItems = $items->reject(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();

$active = request()->integer('v', 0);
if ($active < 0 || $active >= $videoItems->count()) $active = 0;

$activeVideo = $videoItems->get($active);
$activeTitle = $activeVideo['title'] ?? null;
$activeUrl   = $activeVideo['url']   ?? null;
$activeEmbed = $activeUrl ? $toEmbed($activeUrl) : null;

/* ====== DRIVE (dari controller user) ====== */
$driveLink       = $drive['link'] ?? null;
$myWlStatus      = data_get($drive, 'my_whitelist.status', 'none'); // approved|pending|rejected|none
$myWlVerifiedAt  = data_get($drive, 'my_whitelist.verified_at');
$sumApproved     = data_get($drive, 'summary.approved', 0);
$sumPending      = data_get($drive, 'summary.pending', 0);
$sumRejected     = data_get($drive, 'summary.rejected', 0);
$sumTotal        = data_get($drive, 'summary.total', 0);

$activeIsDrive = $isDriveUrl($activeUrl);
$driveBlocked  = $activeIsDrive && ($myWlStatus !== 'approved');

/* ====== ABOUT / SYLLABUS / REVIEWS ====== */
$aboutStr    = $stringify($lesson->about ?? '');
$syllabusArr = $toList($lesson->syllabus ?? []);
$reviewsArr  = $toList($lesson->reviews ?? []);
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

  {{-- HEADER --}}
  <header class="bg-white rounded-3xl border border-gray-50 p-6 md:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.02)] flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('app.courses.show', $course) }}" class="text-sm font-semibold text-tosca hover:text-tosca-dark transition-colors flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Course
        </a>
      </div>
      <h1 class="text-2xl md:text-3xl font-extrabold text-text-main leading-tight">{{ $lesson->title }}</h1>
    </div>
    <div class="flex items-center gap-3">
      @if($isCompleted)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-green-100 text-green-700 shadow-sm border border-green-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          Completed
        </span>
      @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 shadow-sm border border-gray-200">
          <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          In Progress
        </span>
      @endif

      @if($lesson->quiz)
        @if($isCompleted)
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-tosca-light text-tosca-dark shadow-sm border border-tosca/20">
            Quiz Unlocked
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold bg-amber-50 text-amber-700 shadow-sm border border-amber-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            Quiz Locked
          </span>
        @endif
      @endif
    </div>
  </header>

  <div class="flex flex-col lg:flex-row gap-6 items-start">
    
    {{-- MAIN CONTENT (LEFT) --}}
    <main class="w-full lg:flex-1 space-y-6">

      {{-- PLAYER SECTION --}}
      @if($videoItems->count())
      <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-softbg/50">
          <div>
            <h2 class="text-sm font-bold text-text-main uppercase tracking-wider">Video Player</h2>
            @if($activeTitle)
              <p class="text-xs text-text-soft mt-0.5">{{ $activeTitle }}</p>
            @endif
          </div>
        </div>
        <div class="p-4 md:p-6">
          @if($driveBlocked)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              </div>
              <div>
                <h4 class="text-amber-800 font-bold text-lg">Google Drive Access Required</h4>
                <p class="text-sm text-amber-700 mt-1">
                  Your email is not on the whitelist for this file. Current status: <strong class="capitalize">{{ $myWlStatus }}</strong>.
                </p>
                <div class="mt-4">
                  @if($myWlStatus === 'pending')
                    <span class="px-3 py-1.5 bg-amber-200/50 text-amber-800 rounded-lg text-sm font-medium">Awaiting approval...</span>
                  @elseif($myWlStatus === 'rejected')
                    <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium">Access denied. Contact admin.</span>
                  @else
                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Please request access.</span>
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="aspect-video w-full rounded-2xl overflow-hidden bg-black shadow-inner">
              @if($activeEmbed)
                <iframe class="w-full h-full"
                        src="{{ $activeEmbed }}"
                        title="{{ $activeTitle }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen loading="lazy"></iframe>
              @else
                <div class="w-full h-full flex items-center justify-center text-white/50 text-sm">Invalid video URL</div>
              @endif
            </div>
          @endif
        </div>
      </section>
      @endif

      {{-- TWO COLUMNS FOR DETAILS --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- ABOUT --}}
        @if($aboutStr !== '')
        <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
          <h2 class="text-sm font-bold text-text-main uppercase tracking-wider mb-4">About this Lesson</h2>
          <div class="prose prose-sm prose-tosca max-w-none text-text-soft">
            {!! nl2br(e($aboutStr)) !!}
          </div>
        </section>
        @endif

        {{-- SYLLABUS --}}
        @if(count($syllabusArr))
        <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6">
          <h2 class="text-sm font-bold text-text-main uppercase tracking-wider mb-4">Syllabus</h2>
          <ul class="space-y-3">
            @foreach($syllabusArr as $point)
              <li class="flex items-start gap-3 text-sm text-text-soft">
                <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ $point }}</span>
              </li>
            @endforeach
          </ul>
        </section>
        @endif
      </div>

      {{-- NON-VIDEO MATERIALS --}}
      @if($otherItems->count())
      <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 bg-softbg/50">
          <h2 class="text-sm font-bold text-text-main uppercase tracking-wider">Materials (Non-Video)</h2>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          @foreach($otherItems as $item)
            @php
              $t = Str::lower($item['type'] ?? 'link');
              $url = $item['url'] ?? '';
              $title = $item['title'] ?? $url;
            @endphp
            <a href="{{ $url }}" target="_blank" rel="noopener" class="group flex items-start gap-4 p-4 rounded-2xl border border-gray-100 hover:border-tosca/30 hover:bg-softbg/50 transition-all">
              <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center shrink-0 group-hover:bg-tosca-light group-hover:text-tosca transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
              </div>
              <div class="min-w-0">
                <h4 class="text-sm font-bold text-text-main group-hover:text-tosca transition-colors truncate">{{ $title }}</h4>
                <p class="text-xs text-text-soft mt-0.5 uppercase tracking-wide">{{ $t }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </section>
      @endif

      {{-- RESOURCES --}}
      @if(isset($resources) && $resources->count())
      <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 bg-softbg/50">
          <h2 class="text-sm font-bold text-text-main uppercase tracking-wider">Additional Resources</h2>
        </div>
        <div class="p-6">
          <div class="flex flex-wrap gap-3">
            @foreach($resources as $r)
              <a href="{{ route('app.resources.show', $r) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-100 bg-white hover:border-tosca hover:bg-softbg text-sm font-medium text-text-main transition-colors">
                <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                {{ $r->title }}
              </a>
            @endforeach
          </div>
        </div>
      </section>
      @endif

      {{-- QUIZ & NAVIGATION --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($lesson->quiz)
          <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 flex flex-col justify-center items-center text-center">
            <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Lesson Quiz</h3>
            @if($isCompleted)
              <p class="text-sm text-text-soft mb-6">Test your knowledge on this topic.</p>
              <form method="POST" action="{{ route('app.quiz.start', $lesson) }}" class="w-full">
                @csrf
                <button class="w-full py-3 px-4 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-colors shadow-sm shadow-purple-600/20">
                  Start Quiz
                </button>
              </form>
            @else
              <p class="text-sm text-text-soft mb-4">Complete this lesson first to unlock the quiz.</p>
              <button disabled class="w-full py-3 px-4 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed">
                Quiz Locked
              </button>
            @endif
          </section>
        @endif

        <nav class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-6 flex flex-col justify-center gap-3">
          <h3 class="text-sm font-bold text-text-main uppercase tracking-wider mb-2">Navigation</h3>
          @if($prev)
            <a href="{{ route('app.lessons.show', $prev) }}" class="flex items-center justify-between w-full p-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg group transition-all">
              <span class="text-sm font-medium text-text-main group-hover:text-tosca transition-colors">Previous Lesson</span>
              <svg class="w-5 h-5 text-gray-400 group-hover:text-tosca transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
          @endif
          @if($next)
            <a href="{{ route('app.lessons.show', $next) }}" class="flex items-center justify-between w-full p-3 rounded-xl border border-gray-100 hover:border-tosca hover:bg-softbg group transition-all">
              <span class="text-sm font-medium text-text-main group-hover:text-tosca transition-colors">Next Lesson</span>
              <svg class="w-5 h-5 text-gray-400 group-hover:text-tosca transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
          @endif
        </nav>
      </div>

    </main>

    {{-- SIDEBAR (RIGHT) --}}
    <aside class="w-full lg:w-80 shrink-0 space-y-6 lg:sticky lg:top-24">
      
      {{-- PROGRESS CARD --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden relative">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-tosca-light rounded-full blur-2xl opacity-50 pointer-events-none"></div>
        <div class="px-6 py-5 border-b border-gray-50 relative z-10">
          <h3 class="font-bold text-text-main">Mark as Complete</h3>
        </div>
        <div class="p-6 relative z-10">
          <form method="POST" action="{{ route('app.lessons.progress', $lesson) }}" class="space-y-4">
            @csrf
            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-softbg cursor-pointer transition-colors">
              <input type="checkbox" name="progress[watched]" value="1" @checked(optional($progress)->watched) class="w-5 h-5 rounded border-gray-300 text-tosca focus:ring-tosca">
              <span class="text-sm font-medium text-text-main">I have watched the video</span>
            </label>
            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-softbg cursor-pointer transition-colors">
              <input type="checkbox" name="completed" value="1" @checked(optional($progress)->completed_at) class="w-5 h-5 rounded border-gray-300 text-tosca focus:ring-tosca">
              <span class="text-sm font-medium text-text-main">Mark lesson as complete</span>
            </label>
            <button type="submit" class="w-full py-3 px-4 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">
              Save Progress
            </button>
          </form>
        </div>
      </div>

      {{-- PLAYLIST --}}
      @if($videoItems->count())
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 bg-softbg/50">
          <h3 class="font-bold text-text-main">Video Playlist</h3>
        </div>
        <div class="max-h-[300px] overflow-y-auto p-2">
          @foreach($videoItems as $i => $v)
            @php
              $isAct = $i === $active;
              $vt = $v['title'] ?? 'Untitled';
            @endphp
            <a href="{{ route('app.lessons.show', [$lesson, 'v' => $i]) }}" class="flex items-start gap-3 p-3 rounded-xl transition-colors {{ $isAct ? 'bg-tosca-light/50 border border-tosca/20' : 'hover:bg-gray-50 border border-transparent' }}">
              <div class="mt-0.5 shrink-0">
                @if($isAct)
                  <div class="w-6 h-6 rounded-full bg-tosca text-white flex items-center justify-center">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                  </div>
                @else
                  <div class="w-6 h-6 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                  </div>
                @endif
              </div>
              <div class="min-w-0">
                <p class="text-sm font-medium truncate {{ $isAct ? 'text-tosca-dark' : 'text-text-main' }}">{{ $vt }}</p>
                <p class="text-[10px] text-text-soft uppercase tracking-wider mt-0.5">Video {{ $i + 1 }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </div>
      @endif

    </aside>

  </div>
</div>
@endsection
