@extends('app.layouts.base')
@section('title', $lesson->title)

@php
use Illuminate\Support\Str;

/** Konversi URL ke embed iframe */
$toEmbed = function (?string $url) {
if (!$url) return '';
$u = Str::of($url);

// YouTube
if (Str::contains($url, ['youtube.com', 'youtu.be'])) {
if ($u->contains('watch?v=')) $id = $u->after('watch?v=')->before('&');
elseif ($u->contains('youtu.be/')) $id = $u->after('youtu.be/')->before('?');
elseif ($u->contains('/shorts/')) $id = $u->after('/shorts/')->before('?');
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
    if (in_array($ext, ['mp4','webm','mkv','mov'])) {
    return $url;
    }

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

    $isCompleted = optional($progress)->completed_at !== null;

    // ===== Sumber data links: pakai $linksVisible (kalau ada), fallback ke $links =====
    $rawLinks = isset($linksVisible) ? $linksVisible : ($links ?? []);

    // Normalisasi item {title,url,type}
    $norm = fn($i) => [
    'title' => $i['title'] ?? ($i['label'] ?? 'Untitled'),
    'url' => $i['url'] ?? ($i['href'] ?? null),
    'type' => Str::lower($i['type'] ?? 'link'),
    ];
    $items = collect($rawLinks)->map($norm)->filter(fn($i)=>!empty($i['url']))->values();

    // 👇 Google Drive ikut dianggap video → tampil di playlist kanan
    $videoItems = $items->filter(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();
    $otherItems = $items->reject(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();

    // Ambil index aktif dari ?v=
    $active = request()->integer('v', 0);
    if ($active < 0 || $active>= $videoItems->count()) $active = 0;

      // Data player aktif
      $activeVideo = $videoItems->get($active);
      $activeTitle = $activeVideo['title'] ?? null;
      $activeUrl = $activeVideo['url'] ?? null;
      $activeEmbed = $activeUrl ? $toEmbed($activeUrl) : null;

      // ====== DATA DRIVE DARI CONTROLLER USER ======
      // Struktur $drive: ['link','my_whitelist'=>['status','verified_at'], 'summary'=>[...]]
      $driveLink = $drive['link'] ?? null; // null jika belum approved
      $myWlStatus = data_get($drive, 'my_whitelist.status', 'none'); // approved/pending/rejected/none
      $myWlVerifiedAt = data_get($drive, 'my_whitelist.verified_at');
      $sumApproved = data_get($drive, 'summary.approved', 0);
      $sumPending = data_get($drive, 'summary.pending', 0);
      $sumRejected = data_get($drive, 'summary.rejected', 0);
      $sumTotal = data_get($drive, 'summary.total', 0);

      $activeIsDrive = $isDriveUrl($activeUrl);
      $driveBlocked = $activeIsDrive && ($myWlStatus !== 'approved');

      /** util chip status */
      $chip = function ($label, $tone = 'gray') {
      $tones = [
      'green' => 'bg-green-50 text-green-700 border-green-200',
      'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
      'red' => 'bg-red-50 text-red-700 border-red-200',
      'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
      'gray' => 'bg-gray-50 text-gray-700 border-gray-200',
      'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
      ];
      $c = $tones[$tone] ?? $tones['gray'];
      return "<span class=\"inline-flex items-center text-xs px-2 py-0.5 rounded border $c\">$label</span>";
      };
      @endphp

      @section('content')
      <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <header class="mb-6">
          <div class="rounded-2xl border bg-white/90 backdrop-blur p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">{{ $lesson->title }}</h1>
                @if(isset($course) && $course)
                <p class="mt-1 text-sm text-gray-600">
                  Bagian dari
                  <a href="{{ route('app.courses.show', $course) }}" class="font-medium text-blue-700 hover:text-blue-800 hover:underline">
                    {{ $course->title ?? 'Kelas' }}
                  </a>
                </p>
                @endif
              </div>
              <div class="flex items-center gap-2">
                {!! $isCompleted ? $chip('Selesai','green') : $chip('Belum selesai','gray') !!}
                @if($lesson->quiz)
                {!! $isCompleted ? $chip('Kuis Terbuka','green') : $chip('Kuis Terkunci','amber') !!}
                @endif
              </div>
            </div>
          </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_20rem] gap-6 items-start">
          {{-- ================= MAIN ================= --}}
          <main class="space-y-8">
            {{-- ===== Player utama (jika ada video/drive) ===== --}}
            @if($videoItems->count())
            <section aria-labelledby="player-section">
              <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50/70">
                  <h2 id="player-section" class="text-sm font-semibold text-gray-800">Pemutar Materi</h2>
                  @if($activeTitle)
                  <p class="mt-0.5 text-xs text-gray-500 line-clamp-1">{{ $activeTitle }}</p>
                  @endif
                </div>

                <div class="p-4">
                  @if($driveBlocked)
                  <div class="rounded-xl border bg-amber-50 text-amber-900 p-4">
                    <div class="font-semibold mb-1">Akses Google Drive belum di-approve</div>
                    <p class="text-sm">
                      Email kamu belum ada di whitelist untuk file ini.
                      Status: <strong class="capitalize">{{ $myWlStatus }}</strong>.
                      @if($myWlStatus === 'pending')
                      Mohon tunggu persetujuan atau hubungi admin.
                      @elseif($myWlStatus === 'rejected')
                      Akses ditolak. Hubungi admin jika ini keliru.
                      @else
                      Hubungi admin untuk ditambahkan ke whitelist.
                      @endif
                    </p>
                    @if($driveLink && $myWlStatus === 'approved')
                    <a href="{{ $driveLink }}" target="_blank" rel="noopener"
                      class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
                      Buka Drive (tab baru)
                      <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M12.5 2.5a1 1 0 011 1V8a1 1 0 11-2 0V6.414L6.707 11.207a1 1 0 01-1.414-1.414L10.586 5H9a1 1 0 110-2h3.5z"></path>
                        <path d="M5 9a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 110 2H7a3 3 0 01-3-3v-4a1 1 0 011-1z"></path>
                      </svg>
                    </a>
                    @endif
                  </div>
                  @else
                  <div class="aspect-video rounded-xl overflow-hidden border bg-black">
                    @if($activeEmbed)
                    <iframe
                      class="w-full h-full"
                      src="{{ $activeEmbed }}"
                      title="{{ $activeTitle }}"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen
                      loading="lazy"></iframe>
                    @else
                    <div class="w-full h-full grid place-items-center text-white">
                      URL video tidak valid.
                    </div>
                    @endif
                  </div>
                  @endif
                </div>
              </div>
            </section>
            @endif

            {{-- ===== Konten teks/HTML ($blocks) ===== --}}
            @if(!empty($blocks))
            <section aria-labelledby="konten-utama">
              <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50/70">
                  <h2 id="konten-utama" class="text-sm font-semibold text-gray-800">Konten</h2>
                </div>
                <div class="prose prose-slate max-w-none px-5 py-5">
                  @foreach($blocks as $b)
                  @switch($b['type'] ?? 'text')
                  @case('text')
                  <p>{{ $b['body'] ?? '' }}</p>
                  @break
                  @case('note')
                  <div class="p-3 border-l-4 border-yellow-400 bg-yellow-50 rounded">{{ $b['body'] ?? '' }}</div>
                  @break
                  @case('html')
                  {!! $b['body'] ?? '' !!}
                  @break
                  @default
                  <p>{{ $b['body'] ?? '' }}</p>
                  @endswitch
                  @endforeach
                </div>
              </div>
            </section>
            @endif

            {{-- ===== Materi Non-Video ===== --}}
            @if($otherItems->count())
            <section aria-labelledby="materi-utama">
              <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50/70">
                  <h2 id="materi-utama" class="text-sm font-semibold text-gray-800">Materi (Non-Video)</h2>
                </div>

                <div class="p-4 grid sm:grid-cols-2 gap-3">
                  @foreach($otherItems as $item)
                  @php
                  $t = Str::lower($item['type'] ?? 'link');
                  $url = $item['url'] ?? '';
                  $title = $item['title'] ?? $url;
                  $isPdf = $t === 'pdf' || Str::endsWith(Str::lower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf');
                  $isGDrive = Str::contains($url, 'drive.google.com');
                  $preview = $isGDrive ? $toEmbed($url) : ($isPdf ? $url.'#toolbar=0&view=FitH' : null);
                  $blocked = $isGDrive && $myWlStatus !== 'approved';
                  @endphp

                  <div class="group flex items-start gap-3 p-3 border rounded-xl bg-white hover:bg-gray-50 transition-shadow hover:shadow-sm">
                    <div class="mt-0.5 shrink-0">{!! $badge($t) !!}</div>
                    <div class="min-w-0 w-full">
                      <div class="font-medium text-gray-900 truncate flex items-center gap-2">
                        {{ $title }}
                        @if($isGDrive)
                        @if($myWlStatus === 'approved')
                        {!! $chip('Drive Approved','green') !!}
                        @elseif($myWlStatus === 'pending')
                        {!! $chip('Drive Pending','amber') !!}
                        @elseif($myWlStatus === 'rejected')
                        {!! $chip('Drive Rejected','red') !!}
                        @else
                        {!! $chip('Drive Unknown','gray') !!}
                        @endif
                        @endif
                      </div>

                      <div class="mt-1">
                        @if($isGDrive && $blocked)
                        <span class="inline-flex items-center text-sm text-gray-500">
                          Akses diblokir (minta approve)
                        </span>
                        @else
                        <a class="inline-flex items-center text-sm text-blue-700 hover:underline hover:text-blue-800"
                          href="{{ $url }}" target="_blank" rel="noopener">
                          Buka {{ strtoupper($t) }}
                          <svg class="ms-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M12.5 2.5a1 1 0 011 1V8a1 1 0 11-2 0V6.414L6.707 11.207a1 1 0 01-1.414-1.414L10.586 5H9a1 1 0 110-2h3.5z"></path>
                            <path d="M5 9a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 110 2H7a3 3 0 01-3-3v-4a1 1 0 011-1z"></path>
                          </svg>
                        </a>
                        @endif
                      </div>

                      @if($blocked)
                      <div class="mt-2 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded p-2">
                        Akses Google Drive kamu <strong>{{ $myWlStatus }}</strong>.
                        Minta admin untuk approve agar bisa membuka file.
                      </div>
                      @elseif($preview)
                      <div class="mt-2 rounded-lg border overflow-hidden">
                        <iframe class="w-full h-52" src="{{ $preview }}" loading="lazy"></iframe>
                      </div>
                      @endif
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </section>
            @endif

            {{-- ===== Resources (relasi) ===== --}}
            @if(isset($resources) && $resources->count())
            <section aria-labelledby="resources-section">
              <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50/70">
                  <h2 id="resources-section" class="text-sm font-semibold text-gray-800">Resources (Tambahan)</h2>
                </div>
                <ul class="p-4 space-y-2">
                  @foreach($resources as $r)
                  <li>
                    <a class="inline-flex items-center gap-2 px-3 py-2 border rounded-xl hover:bg-gray-50 hover:shadow-sm transition"
                      href="{{ route('app.resources.show', $r) }}">
                      {!! $badge('resource') !!}
                      <span class="font-medium text-gray-900">{{ $r->title }}</span>
                    </a>
                  </li>
                  @endforeach
                </ul>
              </div>
            </section>
            @endif

            {{-- ===== Kuis (lock-aware UI) ===== --}}
            @if($lesson->quiz)
            @php $canStartQuiz = $isCompleted; @endphp
            <section aria-labelledby="quiz-section">
              <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b bg-gray-50/70 flex items-center justify-between">
                  <h2 id="quiz-section" class="text-sm font-semibold text-gray-800">Kuis</h2>
                  {!! $canStartQuiz ? $chip('Terbuka','green') : $chip('Terkunci','amber') !!}
                </div>

                <div class="p-5">
                  @if($canStartQuiz)
                  <form method="POST" action="{{ route('app.quiz.start', $lesson) }}">
                    @csrf
                    <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-300">
                      Mulai Kuis
                    </button>
                  </form>
                  @else
                  <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-amber-800 text-sm">
                    Selesaikan pelajaran ini dulu untuk membuka kuis. Centang <strong>“Tandai selesai”</strong> lalu klik <strong>Simpan</strong>.
                  </div>
                  <button type="button" class="mt-3 px-4 py-2 rounded-lg border bg-gray-100 text-gray-500 cursor-not-allowed" disabled>
                    Mulai Kuis (terkunci)
                  </button>
                  @endif

                  @if ($errors->has('quiz'))
                  <div class="mt-3 text-sm text-red-600">
                    {{ $errors->first('quiz') }}
                  </div>
                  @endif
                </div>
              </div>
            </section>
            @endif

            {{-- ===== Prev/Next ===== --}}
            <nav class="pt-2">
              <div class="mt-2 flex flex-wrap gap-2">
                @if($prev)
                <a class="px-3 py-2 border rounded-lg bg-white hover:bg-gray-50 hover:shadow-sm transition"
                  href="{{ route('app.lessons.show', $prev) }}">← Sebelumnya</a>
                @endif
                @if($next)
                <a class="px-3 py-2 border rounded-lg bg-white hover:bg-gray-50 hover:shadow-sm transition"
                  href="{{ route('app.lessons.show', $next) }}">Berikutnya →</a>
                @endif
              </div>
            </nav>
          </main>

          {{-- ================= SIDEBAR ================= --}}
          <aside class="w-full lg:w-80 space-y-6 lg:sticky lg:top-6">
            {{-- Playlist kanan --}}
            @if($videoItems->count())
            <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
              <div class="px-4 py-3 border-b bg-gray-50/70 font-semibold">Playlist</div>
              <div class="max-h-[60vh] overflow-y-auto divide-y">
                @foreach($videoItems as $i => $v)
                @php
                $isAct = $i === $active;
                $vt = $v['title'] ?? 'Untitled';
                $vu = $v['url'] ?? null;
                $isDriveItem = $isDriveUrl($vu);
                $locked = $isDriveItem && $myWlStatus !== 'approved';
                @endphp
                <a href="{{ route('app.lessons.show', [$lesson, 'v' => $i]) }}"
                  class="block px-4 py-3 transition {{ $isAct ? 'bg-blue-50/70' : 'hover:bg-gray-50' }}">
                  <div class="flex items-start gap-3">
                    <div class="mt-0.5 shrink-0">
                      @if($locked)
                      <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1.75a4.75 4.75 0 00-4.75 4.75v2H6A2.75 2.75 0 003.25 11.25v6A2.75 2.75 0 006 20.75h12a2.75 2.75 0 002.75-2.75v-6A2.75 2.75 0 0018 8.5h-1.25V6.5A4.75 4.75 0 0012 1.75zm-3.25 6.75V6.5a3.25 3.25 0 016.5 0v2h-6.5z" />
                      </svg>
                      @else
                      <svg class="w-4 h-4 {{ $isAct ? 'text-blue-600' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.5 7.5v9l8-4.5-8-4.5Z" />
                      </svg>
                      @endif
                    </div>
                    <div class="min-w-0">
                      <div class="font-medium truncate flex items-center gap-2">
                        <span class="{{ $isAct ? 'text-blue-900' : 'text-gray-900' }}">{{ $vt }}</span>
                        @if($isDriveItem)
                        @if($myWlStatus === 'approved')
                        {!! $chip('Drive','green') !!}
                        @elseif($myWlStatus === 'pending')
                        {!! $chip('Pending','amber') !!}
                        @elseif($myWlStatus === 'rejected')
                        {!! $chip('Rejected','red') !!}
                        @else
                        {!! $chip('Unknown','gray') !!}
                        @endif
                        @endif
                      </div>
                      <div class="text-[11px] text-gray-500 truncate">{{ $vu }}</div>
                    </div>
                  </div>
                </a>
                @endforeach
              </div>
            </div>
            @endif

            {{-- Progress panel --}}
            <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
              <div class="px-4 py-3 border-b bg-gray-50/70 flex items-center justify-between">
                <div class="font-semibold">Progress</div>
                {!! $isCompleted ? $chip('Selesai','green') : $chip('Belum selesai','gray') !!}
              </div>

              <div class="p-4">
                <form method="POST" action="{{ route('app.lessons.progress', $lesson) }}" class="space-y-3">
                  @csrf

                  {{-- Tonton --}}
                  <label class="flex items-center gap-2 text-sm">
                    <input class="rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                      type="checkbox" name="progress[watched]" value="1"
                      @checked(optional($progress)->watched)>
                    Sudah ditonton
                  </label>

                  {{-- Selesai --}}
                  <label class="flex items-center gap-2 text-sm">
                    <input class="rounded border-gray-300 text-gray-900 focus:ring-gray-400"
                      type="checkbox" name="completed" value="1"
                      @checked(optional($progress)->completed_at)>
                    Tandai selesai
                  </label>

                  <button class="w-full px-3 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Simpan
                  </button>
                </form>


                @if ($errors->has('quiz'))
                <div class="mt-3 text-sm text-red-600">
                  {{ $errors->first('quiz') }}
                </div>
                @endif
              </div>
            </div>

            {{-- Google Drive Access panel --}}
            <div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
              <div class="px-4 py-3 border-b bg-gray-50/70 font-semibold">Google Drive Access</div>
              <div class="p-4">
                <dl class="text-sm space-y-3">
                  <div class="flex items-center justify-between gap-3">
                    <dt class="text-gray-600">Link</dt>
                    <dd class="text-right">
                      @if($driveLink && $myWlStatus === 'approved')
                      <a href="{{ $driveLink }}" class="text-blue-700 hover:underline hover:text-blue-800" target="_blank" rel="noopener">Buka</a>
                      @else
                      <span class="opacity-60">—</span>
                      @endif
                    </dd>
                  </div>
                  <div class="flex items-center justify-between gap-3">
                    <dt class="text-gray-600">Status Kamu</dt>
                    <dd class="text-right">
                      @switch($myWlStatus)
                      @case('approved') {!! $chip('Approved','green') !!} @break
                      @case('pending') {!! $chip('Pending','amber') !!} @break
                      @case('rejected') {!! $chip('Rejected','red') !!} @break
                      @default {!! $chip('None','gray') !!}
                      @endswitch
                      @if($myWlVerifiedAt)
                      <span class="ml-1 text-xs text-gray-500">({{ $myWlVerifiedAt }})</span>
                      @endif
                    </dd>
                  </div>
                  <div class="flex items-center justify-between gap-3">
                    <dt class="text-gray-600">Whitelist</dt>
                    <dd class="text-right text-xs">
                      {!! $chip('A '.$sumApproved,'green') !!}
                      <span class="mx-0.5"></span>
                      {!! $chip('P '.$sumPending,'amber') !!}
                      <span class="mx-0.5"></span>
                      {!! $chip('R '.$sumRejected,'red') !!}
                      <span class="mx-0.5"></span>
                      {!! $chip('Total '.$sumTotal,'gray') !!}
                    </dd>
                  </div>
                </dl>

                {{-- resources/views/app/lessons/show.blade.php --}}
                @if(in_array($drive['my_whitelist']['status'] ?? 'none', ['none','rejected','pending']))
                <form method="POST" action="{{ route('lessons.drive.request', $lesson) }}" class="mt-4">
                  @csrf
                  <button type="submit"
                    class="w-full px-4 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-60"
                    @if(($drive['my_whitelist']['status'] ?? 'none' )==='pending' ) disabled @endif>
                    @if(($drive['my_whitelist']['status'] ?? 'none')==='pending')
                    Menunggu persetujuan…
                    @else
                    Ajukan akses Drive
                    @endif
                  </button>
                </form>
                @endif
              </div>
            </div>
          </aside>
        </div>
      </div>
      @endsection