@extends('app.layouts.base')
@section('title', $lesson->title)

@php
    use Illuminate\Support\Str;

    /**
     * Kembalikan URL <iframe src> yang aman untuk berbagai provider.
     * Mendukung: YouTube, Vimeo, Loom, Google Drive, dan fallback URL asli.
     */
    $toEmbed = function (?string $url) {
        if (!$url) return '';

        $u = Str::of($url);

        // --- YouTube ---
        // contoh: https://www.youtube.com/watch?v=ID  → https://www.youtube-nocookie.com/embed/ID
        //         https://youtu.be/ID                 → https://www.youtube-nocookie.com/embed/ID
        if (Str::contains($url, ['youtube.com', 'youtu.be'])) {
            // ambil id
            if ($u->contains('watch?v=')) {
                $id = $u->after('watch?v=')->before('&');
            } elseif ($u->contains('youtu.be/')) {
                $id = $u->after('youtu.be/')->before('?');
            } elseif ($u->contains('/shorts/')) {
                $id = $u->after('/shorts/')->before('?');
            } else {
                $id = '';
            }
            return $id ? "https://www.youtube-nocookie.com/embed/{$id}" : $url;
        }

        // --- Vimeo ---
        // https://vimeo.com/123456 → https://player.vimeo.com/video/123456
        if (Str::contains($url, 'vimeo.com')) {
            $id = $u->afterLast('/')->before('?');
            return $id ? "https://player.vimeo.com/video/{$id}" : $url;
        }

        // --- Loom ---
        // https://www.loom.com/share/UUID → https://www.loom.com/embed/UUID
        if (Str::contains($url, 'loom.com')) {
            $id = $u->after('loom.com/')->after('/')->before('?'); // share/<id> atau embed/<id>
            if ($u->contains('/share/'))  return "https://www.loom.com/embed/{$id}";
            if ($u->contains('/embed/'))  return $url;
        }

        // --- Google Drive File ---
        // https://drive.google.com/file/d/<ID>/view → https://drive.google.com/file/d/<ID>/preview
        if (Str::contains($url, 'drive.google.com')) {
            if ($u->contains('/file/d/')) {
                $id = $u->after('/file/d/')->before('/');
                return "https://drive.google.com/file/d/{$id}/preview";
            }
            // public preview links
            if ($u->contains('/uc?id=')) {
                $id = $u->after('uc?id=')->before('&');
                return "https://drive.google.com/file/d/{$id}/preview";
            }
        }

        return $url;
    };

    /**
     * Badge kecil untuk tipe materi/link
     */
    $badge = function (string $t) {
        return '<span class="text-[10px] font-semibold tracking-wide px-1.5 py-0.5 border rounded uppercase bg-gray-50">'.$t.'</span>';
    };

    // helper kecil untuk status selesai
    $isCompleted = optional($progress)->completed_at !== null;
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

      {{-- ===== Materi (dari $links / content_url) ===== --}}
      @if(!empty($links))
        <section aria-labelledby="materi-utama">
          <h2 id="materi-utama" class="text-base font-semibold text-gray-900">Materi</h2>

          {{-- pisahkan video vs non-video agar tidak ambigu --}}
          @php
            $videos = collect($links)->filter(fn($i) => ($i['type'] ?? 'link') === 'video');
            $others = collect($links)->reject(fn($i) => ($i['type'] ?? 'link') === 'video');
          @endphp

          {{-- Video embeds --}}
          @if($videos->count())
            <div class="mt-3 space-y-6">
              @foreach($videos as $item)
                @php
                  $url   = $item['url'] ?? ($item['href'] ?? '');
                  $title = $item['title'] ?? ($item['label'] ?? 'Video');
                  $embed = $toEmbed($url);
                @endphp
                <figure class="space-y-2">
                  <div class="aspect-video rounded-lg overflow-hidden border">
                    <iframe
                      class="w-full h-full"
                      src="{{ $embed }}"
                      title="{{ $title }}"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen
                      loading="lazy"></iframe>
                  </div>
                  <figcaption class="text-sm text-gray-600">{{ $title }}</figcaption>
                </figure>
              @endforeach
            </div>
          @endif

          {{-- Non-video links/files --}}
          @if($others->count())
            <div class="mt-4 grid sm:grid-cols-2 gap-3">
              @foreach($others as $item)
                @php
                  $t     = Str::lower($item['type'] ?? 'link');
                  $url   = $item['url'] ?? ($item['href'] ?? '');
                  $title = $item['title'] ?? ($item['label'] ?? $url);
                  $isPdf = $t === 'pdf' || Str::endsWith(Str::lower(parse_url($url, PHP_URL_PATH) ?? ''), '.pdf');
                  $isGDrive = Str::contains($url, 'drive.google.com');
                @endphp

                <div class="group flex items-start gap-3 p-3 border rounded-lg bg-white hover:bg-gray-50 transition">
                  <div class="mt-0.5 shrink-0">
                    {!! $badge($t) !!}
                  </div>
                  <div class="min-w-0">
                    <div class="font-medium text-gray-900 truncate">{{ $title }}</div>
                    <div class="mt-1">
                      <a class="inline-flex items-center text-sm text-blue-700 hover:underline"
                         href="{{ $url }}" target="_blank" rel="noopener">
                        Buka {{ strtoupper($t) }}
                        <svg class="ms-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                          <path d="M12.5 2.5a1 1 0 011 1V8a1 1 0 11-2 0V6.414L6.707 11.207a1 1 0 01-1.414-1.414L10.586 5H9a1 1 0 110-2h3.5z"></path>
                          <path d="M5 9a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 110 2H7a3 3 0 01-3-3v-4a1 1 0 011-1z"></path>
                        </svg>
                      </a>
                    </div>

                    {{-- Preview ringkas untuk PDF / Drive (opsional) --}}
                    @if($isPdf || $isGDrive)
                      @php
                        $preview = $isGDrive ? $toEmbed($url) : $url.'#toolbar=0&view=FitH';
                      @endphp
                      <div class="mt-2 rounded border overflow-hidden">
                        <iframe class="w-full h-52" src="{{ $preview }}" loading="lazy"></iframe>
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
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
        @php
            $canStartQuiz = $isCompleted;
        @endphp
        <section aria-labelledby="quiz-section">
          <div class="flex items-center justify-between gap-3">
            <h2 id="quiz-section" class="text-base font-semibold text-gray-900">Kuis</h2>
            @if($canStartQuiz)
              <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded bg-green-50 text-green-700 border border-green-200">
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0L3.293 9.957a1 1 0 111.414-1.414l3.043 3.043 6.543-6.543a1 1 0 011.414 0z"/></svg>
                Terbuka
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded bg-amber-50 text-amber-700 border border-amber-200">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 5a3 3 0 116 0v3H9V6z"/></svg>
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
              Selesaikan pelajaran ini dulu untuk membuka kuis.
              Centang <strong>“Tandai selesai”</strong> lalu klik <strong>Simpan</strong> pada panel Progress.
            </div>
            <button type="button"
                    class="mt-3 px-4 py-2 rounded-lg border bg-gray-100 text-gray-500 cursor-not-allowed"
                    disabled
                    title="Selesaikan pelajaran dulu untuk membuka kuis">
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
    <aside class="w-full lg:w-72">
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
    </aside>
  </div>
</div>
@endsection
