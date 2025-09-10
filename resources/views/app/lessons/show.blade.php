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
        if (in_array($ext, ['mp4','webm','mkv','mov'])) {
            return $url; // biar <video> native, tapi kita tetap pakai iframe container
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
    $isDriveUrl = function (?string $url) {
        return $url && Str::contains($url, 'drive.google.com');
    };

    /** Badge kecil */
    $badge = function (string $t) {
        return '<span class="text-[10px] font-semibold tracking-wide px-1.5 py-0.5 border rounded uppercase bg-gray-50">'.$t.'</span>';
    };

    $isCompleted = optional($progress)->completed_at !== null;

    // ===== Sumber data links: pakai $linksVisible (kalau ada), fallback ke $links =====
    $rawLinks = isset($linksVisible) ? $linksVisible : ($links ?? []);

    // Normalisasi item {title,url,type}
    $norm = fn($i) => [
        'title' => $i['title'] ?? ($i['label'] ?? 'Untitled'),
        'url'   => $i['url']   ?? ($i['href']  ?? null),
        'type'  => Str::lower($i['type'] ?? 'link'),
    ];
    $items = collect($rawLinks)->map($norm)->filter(fn($i)=>!empty($i['url']))->values();

    // 👇 Google Drive ikut dianggap video → tampil di playlist kanan
    $videoItems = $items->filter(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();
    $otherItems = $items->reject(fn($i) => ($i['type'] === 'video') || $isVideoUrl($i['url']))->values();

    // Ambil index aktif dari ?v=
    $active = request()->integer('v', 0);
    if ($active < 0 || $active >= $videoItems->count()) $active = 0;

    // Data player aktif
    $activeVideo = $videoItems->get($active);
    $activeTitle = $activeVideo['title'] ?? null;
    $activeUrl   = $activeVideo['url']   ?? null;
    $activeEmbed = $activeUrl ? $toEmbed($activeUrl) : null;

    // ====== DATA DRIVE DARI CONTROLLER USER (tanpa global) ======
    // Struktur $drive: ['link','my_whitelist'=>['status','verified_at'], 'summary'=>[...]]
    $driveLink       = $drive['link'] ?? null; // <- dari controller: null jika belum approved
    $myWlStatus      = data_get($drive, 'my_whitelist.status', 'none'); // approved/pending/rejected/none
    $myWlVerifiedAt  = data_get($drive, 'my_whitelist.verified_at');
    $sumApproved     = data_get($drive, 'summary.approved', 0);
    $sumPending      = data_get($drive, 'summary.pending', 0);
    $sumRejected     = data_get($drive, 'summary.rejected', 0);
    $sumTotal        = data_get($drive, 'summary.total', 0);

    $activeIsDrive   = $isDriveUrl($activeUrl);
    $driveBlocked    = $activeIsDrive && ($myWlStatus !== 'approved');
@endphp

@section('content')
<div class="max-w-6xl mx-auto">
  {{-- Header --}}
  <header class="mb-6 border-b pb-4">
    <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">{{ $lesson->title }}</h1>
    @if(isset($course) && $course)
      <p class="mt-1 text-sm text-gray-500">
        Bagian dari:
        <a href="{{ route('app.courses.show', $course) }}" class="underline hover:no-underline">
          {{ $course->title ?? 'Kelas' }}
        </a>
      </p>
    @endif
  </header>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_18rem] gap-6 items-start">
    {{-- ================= MAIN ================= --}}
    <main class="space-y-8">

      {{-- ===== Player utama (jika ada video/drive) ===== --}}
      @if($videoItems->count())
        <section aria-labelledby="player-section">
          <h2 id="player-section" class="sr-only">Pemutar Video</h2>

          {{-- Jika konten Google Drive & user belum approved, tampilkan pesan blokir --}}
          @if($driveBlocked)
            <div class="rounded-lg border bg-amber-50 text-amber-900 p-4">
              <div class="font-semibold mb-1">Akses Google Drive belum di-approve</div>
              <p class="text-sm">
                Email kamu belum ada di whitelist untuk file ini.
                Status: <strong>{{ ucfirst($myWlStatus) }}</strong>.
                @if($myWlStatus === 'pending')
                  Mohon tunggu persetujuan atau hubungi admin.
                @elseif($myWlStatus === 'rejected')
                  Akses ditolak. Hubungi admin jika ini keliru.
                @else
                  Hubungi admin untuk ditambahkan ke whitelist.
                @endif
              </p>
              @if($driveLink && $myWlStatus === 'approved')
                <div class="mt-3">
                  <a href="{{ $driveLink }}" target="_blank" rel="noopener"
                     class="inline-flex items-center gap-2 px-3 py-2 rounded bg-gray-900 text-white hover:bg-gray-800">
                    Buka Drive (tab baru)
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M12.5 2.5a1 1 0 011 1V8a1 1 0 11-2 0V6.414L6.707 11.207a1 1 0 01-1.414-1.414L10.586 5H9a1 1 0 110-2h3.5z"></path><path d="M5 9a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 110 2H7a3 3 0 01-3-3v-4a1 1 0 011-1z"></path></svg>
                  </a>
                </div>
              @endif
            </div>
          @else
            <div class="aspect-video rounded-lg overflow-hidden border bg-black">
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
            <div class="mt-2 text-sm text-gray-700 font-medium">{{ $activeTitle }}</div>
          @endif
        </section>
      @endif

      {{-- ===== Konten teks/HTML ($blocks) ===== --}}
      @if(!empty($blocks))
        <section aria-labelledby="konten-utama">
          <h2 id="konten-utama" class="text-base font-semibold text-gray-900">Konten</h2>
          <div class="prose max-w-none mt-3">
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
        </section>
      @endif

      {{-- ===== Materi Non-Video ===== --}}
      @if($otherItems->count())
        <section aria-labelledby="materi-utama">
          <h2 id="materi-utama" class="text-base font-semibold text-gray-900">Materi (Non-Video)</h2>

          <div class="mt-4 grid sm:grid-cols-2 gap-3">
            @foreach($otherItems as $item)
              @php
                $t     = Str::lower($item['type'] ?? 'link');
                $url   = $item['url']   ?? '';
                $title = $item['title'] ?? $url;
                $isPdf = $t === 'pdf' || Str::endsWith(Str::lower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf');
                $isGDrive = Str::contains($url, 'drive.google.com');
                $preview = $isGDrive ? $toEmbed($url) : ($isPdf ? $url.'#toolbar=0&view=FitH' : null);
                $blocked = $isGDrive && $myWlStatus !== 'approved';
              @endphp

              <div class="group flex items-start gap-3 p-3 border rounded-lg bg-white hover:bg-gray-50 transition">
                <div class="mt-0.5 shrink-0">{!! $badge($t) !!}</div>
                <div class="min-w-0 w-full">
                  <div class="font-medium text-gray-900 truncate flex items-center gap-2">
                    {{ $title }}
                    @if($isGDrive)
                      @if($myWlStatus === 'approved')
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-100 text-green-700 border border-green-200">Drive Approved</span>
                      @elseif($myWlStatus === 'pending')
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200">Drive Pending</span>
                      @elseif($myWlStatus === 'rejected')
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 border border-red-200">Drive Rejected</span>
                      @else
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 border">Drive Unknown</span>
                      @endif
                    @endif
                  </div>

                  <div class="mt-1">
                    @if($isGDrive && $blocked)
                      <span class="inline-flex items-center text-sm text-gray-500">
                        Akses diblokir (minta approve)
                      </span>
                    @else
                      <a class="inline-flex items-center text-sm text-blue-700 hover:underline"
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
                    <div class="mt-2 rounded border overflow-hidden">
                      <iframe class="w-full h-52" src="{{ $preview }}" loading="lazy"></iframe>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endif

      {{-- ===== Resources (relasi) ===== --}}
      @if(isset($resources) && $resources->count())
        <section aria-labelledby="resources-section">
          <h2 id="resources-section" class="text-base font-semibold text-gray-900">Resources (Tambahan)</h2>
          <ul class="mt-3 space-y-2">
            @foreach($resources as $r)
              <li>
                <a class="inline-flex items-center gap-2 px-3 py-2 border rounded-lg hover:bg-gray-50"
                   href="{{ route('app.resources.show', $r) }}">
                  {!! $badge('resource') !!}
                  <span class="font-medium">{{ $r->title }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </section>
      @endif

      {{-- ===== Kuis (lock-aware UI) ===== --}}
      @if($lesson->quiz)
        @php $canStartQuiz = $isCompleted; @endphp
        <section aria-labelledby="quiz-section">
          <div class="flex items-center justify-between gap-3">
            <h2 id="quiz-section" class="text-base font-semibold text-gray-900">Kuis</h2>
            @if($canStartQuiz)
              <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded bg-green-50 text-green-700 border border-green-200">
                Terbuka
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded bg-amber-50 text-amber-700 border border-amber-200">
                Terkunci
              </span>
            @endif
          </div>

          @if($canStartQuiz)
            <form method="POST" action="{{ route('app.quiz.start', $lesson) }}" class="mt-3">
              @csrf
              <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-300">
                Mulai Kuis
              </button>
            </form>
          @else
            <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-amber-800 text-sm">
              Selesaikan pelajaran ini dulu untuk membuka kuis. Centang <strong>“Tandai selesai”</strong> lalu klik <strong>Simpan</strong>.
            </div>
            <button type="button" class="mt-3 px-4 py-2 rounded-lg border bg-gray-100 text-gray-500 cursor-not-allowed" disabled>
              Mulai Kuis (terkunci)
            </button>
          @endif
        </section>
      @endif

      {{-- ===== Prev/Next ===== --}}
      <nav class="pt-4 border-t">
        <div class="mt-4 flex flex-wrap gap-2">
          @if($prev)
            <a class="px-3 py-2 border rounded-lg hover:bg-gray-50" href="{{ route('app.lessons.show', $prev) }}">← Sebelumnya</a>
          @endif
          @if($next)
            <a class="px-3 py-2 border rounded-lg hover:bg-gray-50" href="{{ route('app.lessons.show', $next) }}">Berikutnya →</a>
          @endif
        </div>
      </nav>
    </main>

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-full lg:w-72 space-y-6">
      {{-- Playlist kanan (YouTube/Vimeo/Loom/Drive) --}}
      @if($videoItems->count())
        <div class="rounded-lg border bg-white">
          <div class="px-4 py-3 border-b bg-gray-50 font-semibold">Playlist</div>
          <div class="max-h-[60vh] overflow-y-auto divide-y">
            @foreach($videoItems as $i => $v)
              @php
                $isAct = $i === $active;
                $vt = $v['title'] ?? 'Untitled';
                $vu = $v['url']   ?? null;
                $isDriveItem = $isDriveUrl($vu);
                $locked = $isDriveItem && $myWlStatus !== 'approved';
              @endphp
              <a href="{{ route('app.lessons.show', [$lesson, 'v' => $i]) }}"
                 class="block px-4 py-3 hover:bg-gray-50 {{ $isAct ? 'bg-blue-50' : '' }}">
                <div class="flex items-start gap-2">
                  <div class="mt-0.5">
                    @if($locked)
                      {{-- Lock icon --}}
                      <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1.75a4.75 4.75 0 00-4.75 4.75v2H6A2.75 2.75 0 003.25 11.25v6A2.75 2.75 0 006 20.75h12a2.75 2.75 0 002.75-2.75v-6A2.75 2.75 0 0018 8.5h-1.25V6.5A4.75 4.75 0 0012 1.75zm-3.25 6.75V6.5a3.25 3.25 0 016.5 0v2h-6.5z"/>
                      </svg>
                    @else
                      {{-- Play icon --}}
                      <svg class="w-4 h-4 {{ $isAct ? 'text-blue-600' : 'opacity-60' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M8.5 7.5v9l8-4.5-8-4.5Z"/></svg>
                    @endif
                  </div>
                  <div class="min-w-0">
                    <div class="font-medium truncate flex items-center gap-2">
                      {{ $vt }}
                      @if($isDriveItem)
                        @if($myWlStatus === 'approved')
                          <span class="text-[10px] px-1.5 py-0.5 rounded bg-green-100 text-green-700 border border-green-200">Drive</span>
                        @elseif($myWlStatus === 'pending')
                          <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                        @elseif($myWlStatus === 'rejected')
                          <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 border border-red-200">Rejected</span>
                        @else
                          <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 border">Unknown</span>
                        @endif
                      @endif
                    </div>
                    <div class="text-xs opacity-60 truncate">{{ $vu }}</div>
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Progress panel --}}
      <div class="p-4 bg-white border rounded-lg">
        <div class="flex items-center justify-between">
          <div class="font-semibold">Progress</div>
          @if($isCompleted)
            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded bg-green-50 text-green-700 border border-green-200">
              Selesai
            </span>
          @else
            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded bg-gray-50 text-gray-600 border">
              Belum selesai
            </span>
          @endif
        </div>

        <form method="POST" action="{{ route('app.lessons.progress', $lesson) }}" class="mt-3 space-y-3">
          @csrf
          <input type="hidden" name="progress[watched]" value="1">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="completed" value="1" @checked($isCompleted)>
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

      {{-- Google Drive Access panel (tanpa status global) --}}
      <div class="p-4 bg-white border rounded-lg">
        <div class="font-semibold mb-2">Google Drive Access</div>
        <dl class="text-sm space-y-1.5">
          <div class="flex justify-between gap-3">
            <dt class="text-gray-600">Link</dt>
            <dd class="text-right">
              @if($driveLink && $myWlStatus === 'approved')
                <a href="{{ $driveLink }}" class="text-blue-700 hover:underline" target="_blank" rel="noopener">Buka</a>
              @else
                <span class="opacity-60">—</span>
              @endif
            </dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-600">Status Kamu</dt>
            <dd class="text-right">
              @switch($myWlStatus)
                @case('approved') <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 border border-green-200">Approved</span> @break
                @case('pending')  <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200">Pending</span> @break
                @case('rejected') <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 border border-red-200">Rejected</span> @break
                @default          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 border">None</span>
              @endswitch
              @if($myWlVerifiedAt)
                <span class="ml-1 text-xs opacity-70">({{ $myWlVerifiedAt }})</span>
              @endif
            </dd>
          </div>
          <div class="flex justify-between gap-3">
            <dt class="text-gray-600">Whitelist</dt>
            <dd class="text-right text-xs">
              <span class="px-1.5 py-0.5 rounded bg-green-100 text-green-700 border border-green-200">A {{ $sumApproved }}</span>
              <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200 ml-1">P {{ $sumPending }}</span>
              <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-700 border border-red-200 ml-1">R {{ $sumRejected }}</span>
              <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 border ml-1">Total {{ $sumTotal }}</span>
            </dd>
          </div>
        </dl>
      </div>
    </aside>
  </div>
</div>
@endsection
