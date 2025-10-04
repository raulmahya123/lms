{{-- resources/views/welcome.blade.php --}}
@extends('app.layouts.base')

@section('title', 'Belajar Coding')

@push('styles')
<style>
    [x-cloak]{display:none}
    .text-balance{text-wrap:balance}

    /* Motion */
    .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
    .hover-lift:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(2,6,23,.08)}
    .shine{position:relative;overflow:hidden}
    .shine::after{content:"";position:absolute;inset:-100% -60% auto;height:160%;width:30%;
        background:linear-gradient(120deg,transparent,rgba(255,255,255,.35),transparent);
        transform:skewX(-20deg);animation:shine 3.8s ease-in-out infinite}
    @keyframes shine{0%{left:-60%}60%,100%{left:120%}}

    /* Divider & chips */
    .divider{height:3px;width:68px;border-radius:999px;background:linear-gradient(90deg,#0ea5e9,#2563eb,#1e40af)}
    .chip{border:1px solid rgba(2,6,23,.08);background:#fff;border-radius:9999px;padding:.4rem .7rem}

    /* Cards */
    .card{background:#fff;border:1px solid rgba(2,6,23,.06);border-radius:1rem;box-shadow:0 4px 24px rgba(2,6,23,.06)}
    .card-lg{border-radius:1.25rem}
    .card-ghost{background:rgba(255,255,255,.9);backdrop-filter:saturate(120%) blur(6px)}
    .card-locked{position:relative;overflow:hidden}
    .card-locked::after{content:"";position:absolute;inset:0;background:rgba(2,6,23,.45);backdrop-filter:blur(2px)}
    .lock-badge{position:absolute;top:.75rem;right:.75rem;display:inline-flex;align-items:center;gap:.4rem;
        padding:.35rem .55rem;border-radius:.6rem;background:rgba(15,23,42,.75);color:#fff;font-size:.72rem;font-weight:600;z-index:2}
    .lock-badge svg{width:14px;height:14px}
    .btn-disabled{opacity:.85;pointer-events:none}

    /* Progress */
    .progress-rail{height:6px;width:100%;background:#e2e8f0;border-radius:999px;overflow:hidden}
    .progress-fill{height:100%;background:linear-gradient(90deg,#2563eb,#1e40af)}

    /* Course card */
    .course-card{background:#fff;border:1px solid rgba(2,6,23,.06);border-radius:1rem;box-shadow:0 8px 30px rgba(2,6,23,.06)}
    .course-card .thumb{position:relative;aspect-ratio:16/9;overflow:hidden;background:#f1f5f9}
    .course-card .thumb img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
    .course-card:hover .thumb img{transform:scale(1.06)}
    .course-pill{position:absolute;top:.6rem;right:.6rem;z-index:10;display:inline-flex;align-items:center;gap:.35rem;
        padding:.35rem .55rem;border-radius:999px;font-size:.7rem;font-weight:700;background:#fff;color:#0f172a;border:1px solid rgba(2,6,23,.08);box-shadow:0 6px 20px rgba(2,6,23,.08)}

    /* Profile */
    .avatar-dot{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:9999px;
        background:#e0f2fe;color:#1d4ed8;font-weight:700;border:1px solid rgba(2,6,23,.06)}

    /* Logo ticker */
    .logo-viewport{overflow:hidden}
    .logo-track{display:flex;gap:1rem;align-items:center;white-space:nowrap;width:max-content;animation:logo-scroll 28s linear infinite}
    .logo-track:hover{animation-play-state:paused}
    .logo-chip{height:40px;width:40px;border-radius:9999px;background:#fff;display:flex;align-items:center;justify-content:center;
        border:1px solid rgba(15,23,42,.08);box-shadow:0 2px 10px rgba(2,6,23,.06);padding:.5rem;filter:grayscale(1) opacity(.85);transition:.2s}
    .logo-chip:hover{filter:grayscale(0) opacity(1);transform:translateY(-2px) scale(1.02)}
    .logo-chip img{height:100%;width:auto;object-fit:contain}
    @keyframes logo-scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
</style>
@endpush

@section('content')

@php
  /* === Auth helper === */
  $isGuest = auth()->guest();
@endphp

{{-- ===================== HERO ===================== --}}
<section class="relative overflow-hidden bg-gradient-to-b from-sky-50 via-white to-white">
  <!-- Dekor -->
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
    <div class="absolute inset-0 opacity-[.06]"
         style="background-size:22px 22px;
                background-image:radial-gradient(circle at 1px 1px, rgba(2,6,23,.45) 1px, transparent 0);">
    </div>
    <div class="absolute left-1/3 top-[280px] -translate-x-1/2 w-[780px] h-[780px] rounded-full blur-3xl
                bg-[radial-gradient(circle_at_center,_rgba(96,165,250,0.30),_transparent_60%)]"></div>
    <div class="absolute left-[54%] top-[320px] -translate-x-1/2 w-[600px] h-[600px] rounded-full border border-blue-300/60"></div>
    <div class="absolute left-[54%] top-[320px] -translate-x-1/2 w-[480px] h-[480px] rounded-full border border-blue-300/40"></div>
    <div class="absolute left-[54%] top-[320px] -translate-x-1/2 w-[360px] h-[360px] rounded-full border border-blue-300/30"></div>
    <div class="absolute left-[54%] top-[320px] -translate-x-1/2 -translate-y-1/2 w-[520px] h-[220px]
                rounded-b-[520px] bg-gradient-to-b from-sky-100 to-blue-100 shadow-[0_24px_80px_rgba(2,6,23,.08)]"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 grid lg:grid-cols-2 gap-10 z-10">
    <div class="flex flex-col justify-center">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-medium w-max">
        <span class="h-2 w-2 rounded-full bg-sky-500"></span> Belajar Fleksibel, Karier Melesat
      </div>

      <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold leading-tight text-balance">
        Kuasai <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-900">Skill Programming</span>
        lewat <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-900 to-sky-900">praktik nyata</span>
      </h1>

      <p class="mt-4 text-gray-700 max-w-2xl">
        Kelas terarah + tugas real, kuis interaktif, tracking progres, dan sertifikat. Cocok buat pemula yang mau mulai,
        sampai pro yang butuh naik level cepat.
      </p>

      <ul class="mt-4 grid grid-cols-2 gap-2 max-w-md text-sm text-slate-700">
        <li class="flex items-center gap-2"><span class="text-blue-700">✓</span> Roadmap jelas</li>
        <li class="flex items-center gap-2"><span class="text-blue-700">✓</span> Project portfolio</li>
        <li class="flex items-center gap-2"><span class="text-blue-700">✓</span> Kuis & pembahasan</li>
        <li class="flex items-center gap-2"><span class="text-blue-700">✓</span> Sertifikat verifiable</li>
      </ul>

      <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <a href="#kursus-baru" class="shine hover-lift px-5 py-3 rounded-xl bg-blue-600 text-white text-center">
          <span class="inline-flex items-center gap-2">
            Jelajah Kelas
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </span>
        </a>

        @guest
        <a href="{{ route('register') }}" class="hover-lift px-5 py-3 rounded-xl border text-center hover:bg-gray-50 bg-white">
          <span class="inline-flex items-center gap-2">
            Daftar Gratis
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </span>
        </a>
        @endguest
      </div>

      {{-- stats mini --}}
      <div class="mt-8 grid grid-cols-3 sm:grid-cols-5 gap-3">
        @php
          $statItems = [
            ['label' => 'Kelas', 'value' => $stats['courses'] ?? 0],
            ['label' => 'Modul', 'value' => $stats['modules'] ?? 0],
            ['label' => 'Pelajaran', 'value' => $stats['lessons'] ?? 0],
            ['label' => 'Enrollment', 'value' => $stats['enrollments'] ?? 0],
            ['label' => 'Kuis', 'value' => $stats['quizzes'] ?? 0],
          ];
        @endphp
        @foreach ($statItems as $s)
          <div class="card p-4 text-center hover-lift">
            <div class="text-2xl font-semibold text-blue-900">{{ number_format($s['value']) }}</div>
            <div class="text-xs text-gray-600 mt-1">{{ $s['label'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="relative">
      <img src="{{ asset('assets/images/KKN.jpeg') }}" alt="Belajar"
           class="w-full h-72 sm:h-96 object-cover rounded-2xl shadow-2xl border border-blue-100" />
    </div>
  </div>

  {{-- LOGO TICKER --}}
  <div class="py-6 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      @php $logos = ['laravel.png','vue.png']; @endphp
      <div class="flex flex-wrap items-center gap-3">
        @foreach ($logos as $logo)
          <div class="logo-chip">
            <img src="{{ asset('assets/logos/' . $logo) }}" alt="{{ pathinfo($logo, PATHINFO_FILENAME) }}">
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section class="py-4 bg-white border-y">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center gap-2 text-sm">
    <a href="#kursus-baru" class="chip bg-sky-50 text-blue-800 hover:bg-sky-100">Kelas Terbaru</a>
    <a href="#kursus-populer" class="chip bg-sky-50 text-blue-800 hover:bg-sky-100">Populer</a>
    <a href="#psi" class="chip bg-sky-50 text-blue-800 hover:bg-sky-100">Tes Psikologi</a>
    <a href="#plans" class="chip bg-sky-50 text-blue-800 hover:bg-sky-100">Paket</a>
    <a href="#kupon" class="chip bg-sky-50 text-blue-800 hover:bg-sky-100">Kupon</a>
    <div class="ms-auto flex items-center gap-2">
      @auth
      <a href="{{ route('app.dashboard') }}" class="px-3 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-800 hover-lift">Dashboard</a>
      @else
      <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 hover-lift">Masuk</a>
      <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 hover-lift">Daftar</a>
      @endauth
    </div>
  </div>
</section>

{{-- ===================== PROFIL ===================== --}}
@auth
@php
  $u = auth()->user();
  $initial = strtoupper(mb_substr($u->name ?? 'U', 0, 1));
  $membershipText = $isMember ?? false ? 'Member Aktif' : 'Belum Berlangganan';
  $completedPct = (int) ($u->profile_progress_percent ?? 0);
@endphp
<section id="profil" class="py-8 bg-gradient-to-b from-white to-sky-50/40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card card-lg p-5 sm:p-6 hover-lift">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="avatar-dot">{{ $initial }}</span>
          <div>
            <div class="font-semibold text-blue-950 leading-tight">{{ $u->name }}</div>
            <div class="text-xs text-slate-600">{{ $u->email }}</div>
            <div class="mt-1 inline-flex items-center gap-2 text-xs">
              <span class="px-2 py-0.5 rounded-full bg-sky-100 text-blue-800">{{ $membershipText }}</span>
              @if ($completedPct > 0)
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">{{ $completedPct }}% profil</span>
              @endif
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
          <a href="{{ route('app.my.courses') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">My Courses</a>
          <a href="{{ route('app.certificates.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Certificates</a>
          <a href="{{ route('app.payments.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Payments</a>
          <a href="{{ route('app.memberships.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Memberships</a>
          <a href="{{ route('app.psytests.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Psy Tests</a>
          <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">Edit Profile</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endauth

{{-- ===================== KEUNGGULAN ===================== --}}
<section class="py-12 bg-gradient-to-b from-white to-sky-50/40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-extrabold text-blue-900">
          Kenapa BERKEMAH?
        </h2>
        <p class="text-sm text-gray-600">
          Belajar terarah, langsung praktik, hasil yang kerasa. Bukan cuma nonton—kamu beneran naik level.
        </p>
      </div>

      <a href="#kursus-baru"
         class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover-lift">
        Lihat Kelas Baru
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
      <div class="card p-6 hover-lift">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-2xl bg-sky-100 flex items-center justify-center text-sky-700 text-lg">⚡</div>
          <div>
            <div class="font-semibold text-blue-900">Materi Terarah</div>
            <div class="text-xs text-blue-700/80">Roadmap jelas • No bingung lagi</div>
          </div>
        </div>
        <p class="mt-3 text-sm text-gray-600">
          Kurikulum disusun step-by-step dari dasar ke mahir. Setiap modul punya tujuan jelas dan checklist progress.
        </p>
        <ul class="mt-3 space-y-1.5 text-sm text-gray-700">
          <li>• Path pemula → siap kerja</li>
          <li>• Studi kasus & best practice</li>
          <li>• Tugas kecil tiap bab</li>
        </ul>
      </div>

      <div class="card p-6 hover-lift">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700 text-lg">🧩</div>
          <div>
            <div class="font-semibold text-blue-900">Belajar Aktif</div>
            <div class="text-xs text-blue-700/80">Kuis, kode, & feedback</div>
          </div>
        </div>
        <p class="mt-3 text-sm text-gray-600">
          Bukan sekadar video—ada kuis interaktif, challenge ngoding, dan pembahasan biar paham beneran.
        </p>
        <ul class="mt-3 space-y-1.5 text-sm text-gray-700">
          <li>• Kuis real-time + pembahasan</li>
          <li>• Project mini tiap section</li>
          <li>• Forum tanya-jawab dengan mentor</li>
        </ul>
      </div>

      <div class="card p-6 hover-lift">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-2xl bg-sky-100 flex items-center justify-center text-sky-700 text-lg">🎯</div>
          <div>
            <div class="font-semibold text-blue-900">Hasil Nyata</div>
            <div class="text-xs text-blue-700/80">Portfolio & sertifikat</div>
          </div>
        </div>
        <p class="mt-3 text-sm text-gray-600">
          Setiap kelas diarahkan ke output nyata: aplikasi sederhana, komponen reusable, sampai sertifikat kelulusan.
        </p>
        <ul class="mt-3 space-y-1.5 text-sm text-gray-700">
          <li>• Portfolio-ready project</li>
          <li>• Sertifikat verifiable</li>
          <li>• Rekomendasi next skill</li>
        </ul>
      </div>
    </div>

    <div class="sm:hidden mt-6">
      <a href="#kursus-baru"
         class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover-lift">
        Mulai Jelajah Kelas
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </a>
    </div>
  </div>
</section>

{{-- ===================== KELAS TERBARU ===================== --}}
<section id="kursus-baru" class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-bold text-blue-900">Kelas Terbaru</h2>
        <p class="mt-2 text-gray-600">Konten fresh, langsung praktik.</p>
      </div>
      <a href="{{ auth()->check() ? route('app.courses.index') : route('register') }}" class="hidden sm:inline-flex items-center gap-2 text-blue-700 hover:underline hover-lift">Lihat Semua →</a>
    </div>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @forelse ($latestCourses as $course)
        @php
          $cover = $course->cover
              ? asset('storage/' . ltrim($course->cover, '/'))
              : asset('assets/images/placeholder-course.png');
          $level = trim($course->level ?? '') ?: 'All Levels';
          $modules = (int) ($course->modules_count ?? 0);
          $students = (int) ($course->enrollments_count ?? 0);
          $pp = (int) ($course->progress_percent ?? 0);
          $pd = (int) ($course->progress_done ?? 0);
          $pt = max(1, (int) ($course->progress_total ?? 0));
          $title = trim($course->title ?? '') ?: 'Kelas Tanpa Judul';
          $isComplete = $pp >= 100;
          $courseUrl = auth()->check() ? route('app.courses.show', $course) : route('register');
        @endphp

        <a href="{{ $courseUrl }}" class="group course-card overflow-hidden hover-lift block h-full flex flex-col {{ $isGuest ? 'card-locked' : '' }}">
          @if ($isGuest)
            <div class="lock-badge">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.105 0 2 .895 2 2v3H10v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z" />
              </svg>
              Terkunci
            </div>
          @endif

          <div class="thumb">
            @if (!$isGuest && $pp > 0)
              <span class="course-pill">
                {{ $pp }}%
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                </svg>
              </span>
            @endif
            <img src="{{ $cover }}" alt="{{ $title }}">
          </div>

          <div class="p-4 flex-1">
            <div class="text-xs font-medium text-blue-700/90">{{ $level }}</div>
            <h3 class="font-semibold text-blue-950 leading-snug line-clamp-2">{{ $title }}</h3>

            <div class="mt-2 text-sm text-gray-600 flex items-center gap-3">
              <span>{{ $modules }} modul</span><span>•</span><span>{{ $students }} siswa</span>
            </div>

            @if (!$isGuest && isset($course->progress_percent))
              <div class="mt-3">
                <div class="progress-rail">
                  <div class="progress-fill" style="width: {{ max(0, min(100, $pp)) }}%"></div>
                </div>
                <div class="mt-1 flex items-center justify-between text-xs text-gray-600">
                  <span>{{ $pp }}% selesai</span><span>{{ $pd }}/{{ $pt }} pelajaran</span>
                </div>
              </div>
            @endif

            @if (!$isGuest && $isComplete)
              <div class="mt-3 inline-flex items-center gap-2 text-emerald-700 text-xs font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                100% selesai — selamat!
              </div>
            @endif
          </div>
        </a>
      @empty
        <div class="sm:col-span-2 lg:col-span-3">
          <div class="p-6 card bg-sky-50 border-sky-100 text-blue-900">Belum ada kelas terbaru.</div>
        </div>
      @endforelse
    </div>
  </div>
</section>

{{-- ===================== KELAS POPULER ===================== --}}
<section id="kursus-populer" class="py-12 bg-gradient-to-b from-white to-sky-50/40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-bold text-blue-900">Kelas Populer</h2>
        <p class="mt-2 text-gray-600">Paling banyak diikuti.</p>
      </div>
    </div>

    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse ($popularCourses as $course)
        @php
          $cover = $course->cover
              ? asset('storage/' . ltrim($course->cover, '/'))
              : asset('assets/images/placeholder-course.png');
          $pp = (int) ($course->progress_percent ?? 0);
          $pd = (int) ($course->progress_done ?? 0);
          $pt = max(1, (int) ($course->progress_total ?? 0));
          $title = trim($course->title ?? '') ?: 'Kelas';
        @endphp

        <a href="{{ auth()->check() ? route('app.courses.show', $course) : route('register') }}" class="group card card-lg overflow-hidden hover-lift transition block {{ $isGuest ? 'card-locked' : '' }}">
          @if ($isGuest)
            <div class="lock-badge">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.105 0 2 .895 2 2v3H10v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z" />
              </svg>
              Terkunci
            </div>
          @endif

          <div class="relative aspect-[16/9] overflow-hidden bg-gray-100">
            @if (!$isGuest && $pp > 0)
              <div class="absolute top-2 right-2 z-10">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-semibold bg-white/90 border border-slate-200 text-slate-700">
                  {{ $pp }}%
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                  </svg>
                </span>
              </div>
            @endif

            <img src="{{ $cover }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
          </div>
          <div class="p-4">
            <div class="text-xs text-blue-700/80 font-medium">Populer</div>
            <h3 class="mt-1 font-semibold line-clamp-2 text-blue-950">{{ $course->title }}</h3>
            <div class="mt-2 text-xs text-gray-600 flex items-center gap-3">
              <span>{{ $course->modules_count ?? 0 }} modul</span><span>•</span><span>{{ $course->enrollments_count ?? 0 }} siswa</span>
            </div>

            @if (!$isGuest && isset($course->progress_percent))
              <div class="mt-3">
                <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-600 to-blue-800" style="width: {{ $pp }}%"></div>
                </div>
                <div class="mt-1 text-xs text-gray-600 flex items-center justify-between">
                  <span>{{ $pp }}% selesai</span><span>{{ $pd }}/{{ $pt }} pelajaran</span>
                </div>
              </div>
            @endif
          </div>
        </a>
      @empty
        <div class="sm:col-span-2 lg:col-span-3">
          <div class="p-6 card bg-sky-50 border-sky-100 text-blue-900">Belum ada kelas populer.</div>
        </div>
      @endforelse
    </div>
  </div>
</section>

{{-- ===================== TES PSIKOLOGI (PSI) ===================== --}}
<section id="psi" class="relative py-14">
  <div class="pointer-events-none absolute inset-0 -z-10">
    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-sky-200/50 blur-3xl"></div>
    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-blue-200/40 blur-3xl"></div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Tes Psikologi</h2>
        <p class="mt-2 text-slate-600 max-w-2xl">Kenali kekuatan & preferensimu. Hasil langsung dengan rekomendasi otomatis.</p>
      </div>
      <a href="{{ route('app.psytests.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition hover-lift">
        Lihat Semua Tes
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
      </a>
    </div>

    @if (($isMember ?? false) !== true)
      <div class="mt-6 card p-4 bg-gradient-to-r from-blue-50 to-sky-50 border-blue-100 hover-lift">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center">🔒</div>
            <div>
              <div class="font-semibold text-blue-900">Akses Tes Premium Terkunci</div>
              <div class="text-sm text-blue-800/80">Buka semua Tes IQ & Psikologi dengan berlangganan paket.</div>
            </div>
          </div>
          <a href="#plans" class="shine inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover-lift">
            Lihat Paket
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
        </div>
      </div>
    @endif

    @php
      $__iq = isset($iqTests) ? $iqTests : collect();
      $canAccessPsi = ($isMember ?? false) === true;
    @endphp

    @if ($__iq->count())
      <div class="mt-8">
        <div class="flex items-center justify-between">
          <h3 class="text-lg sm:text-xl font-bold text-slate-900">Tes IQ</h3>
          @unless ($canAccessPsi)
          <a href="#plans" class="text-sm inline-flex items-center gap-1 text-blue-700 hover:underline">Buka akses dengan paket →</a>
          @endunless
        </div>

        <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($__iq as $t)
            @php
              $qs = is_array($t->questions ?? null) ? count($t->questions) : 0;
              $est = $t->duration_minutes ?: max(5, round($qs * 0.75));
            @endphp

            <div class="group card card-lg overflow-hidden transition hover-lift {{ $canAccessPsi ? '' : 'card-locked' }}">
              <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

              @unless ($canAccessPsi)
                <div class="lock-badge">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.105 0 2 .895 2 2v3H10v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z" />
                  </svg>
                  Terkunci
                </div>
              @endunless

              <div class="p-5">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                  <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    {{ $est }} menit
                  </span>
                  <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">IQ</span>
                  @if ($qs > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-teal-50 text-teal-700">{{ $qs }} soal</span>
                  @endif
                </div>

                <h4 class="mt-3 text-lg font-semibold text-slate-900 line-clamp-2">
                  {{ $t->title ?? 'Tes IQ' }}
                </h4>

                @if (!empty($t->description))
                <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $t->description }}</p>
                @endif

                <div class="mt-5 flex items-center gap-2">
                  @if ($canAccessPsi)
                    <a href="{{ route('user.test-iq.show', ['testIq' => $t->getRouteKey()]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:brightness-105 active:brightness-95 transition text-sm">
                      Mulai Tes
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                      </svg>
                    </a>

                    @auth
                      @php
                        $last = collect($t->submissions ?? [])->where('user_id', auth()->id())->values()->last();
                      @endphp
                      @if ($last)
                        <a href="{{ route('user.test-iq.result', $t) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-slate-700 hover:bg-slate-50 text-sm transition">
                          Lihat Hasil
                        </a>
                      @endif
                    @endauth
                  @else
                    <a href="#plans" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-white bg-gradient-to-r from-slate-500 to-slate-700 btn-disabled text-sm">
                      Upgrade untuk Akses
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    @php
      $__psy = isset($psyTests) ? $psyTests : collect();
      $typeColors = [
        'likert' => 'from-blue-600 to-blue-800',
        'mcq'    => 'from-sky-600 to-blue-700',
        'iq'     => 'from-emerald-500 to-teal-500',
        'disc'   => 'from-amber-500 to-orange-500',
        'big5'   => 'from-blue-700 to-indigo-800',
        'custom' => 'from-slate-500 to-slate-700',
      ];
    @endphp

    @if ($__psy->count())
      <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 md:[&>*]:snap-none [&>*]:snap-start overflow-x-auto md:overflow-visible scroll-smooth">
        @foreach ($__psy as $t)
          @php
            $type = strtolower($t->type ?? 'custom');
            $grad = $typeColors[$type] ?? $typeColors['custom'];
            $qs = (int) ($t->questions_count ?? 0);
            $est = max(5, round($qs * 0.75));
          @endphp

          <div class="min-w-[88%] sm:min-w-0 group card card-lg overflow-hidden transition hover-lift {{ $isMember ?? false ? '' : 'card-locked' }}">
            <div class="h-1.5 bg-gradient-to-r {{ $grad }}"></div>

            @unless ($isMember ?? false)
              <div class="lock-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.105 0 2 .895 2 2v3H10v-3c0-1.105.895-2 2-2zm0-7a4 4 0 00-4 4v2h8V8a4 4 0 00-4-4z" />
                </svg>
                Terkunci
              </div>
            @endunless

            <div class="p-5">
              <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                  </svg>
                  {{ $est }} menit
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700">
                  {{ strtoupper($t->type ?? 'custom') }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-sky-50 text-sky-700">
                  {{ ucfirst($t->track ?? 'general') }}
                </span>
              </div>

              <h3 class="mt-3 text-lg font-semibold text-slate-900 line-clamp-2">{{ $t->name }}</h3>

              <div class="mt-1 text-xs text-slate-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                {{ $qs }} soal
              </div>

              @if (!empty($t->description))
                <p class="mt-3 text-sm text-slate-600">{{ $t->description }}</p>
              @endif

              <div class="mt-5 flex items-center gap-2">
                @if ($isMember ?? false)
                  <form method="POST" action="{{ route('app.psy.attempts.start', $t) }}">
                    @csrf
                    <button class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-white bg-gradient-to-r {{ $grad }} hover:brightness-105 active:brightness-95 transition text-sm">
                      Mulai Tes
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                      </svg>
                    </button>
                  </form>

                  <a href="{{ route('app.psytests.show', $t->slug ?: $t->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-slate-700 hover:bg-slate-50 text-sm transition">
                    Detail
                  </a>
                @else
                  <a href="#plans" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-white bg-gradient-to-r from-slate-500 to-slate-700 btn-disabled text-sm">
                    Upgrade untuk Akses
                  </a>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="mt-8 card border-dashed p-8 text-center">
        <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Belum ada tes tersedia</h3>
        <p class="mt-1 text-slate-600">Saat tes sudah aktif, kamu bisa mulai dari sini.</p>
        <a href="{{ route('app.psytests.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
          Jelajahi Tes
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </a>
      </div>
    @endif
  </div>
</section>

{{-- ===================== FORUM TANYA-JAWAB ===================== --}}
<section id="forum" class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-bold text-blue-900">Forum Tanya-Jawab</h2>
        <p class="mt-2 text-gray-600">Tanya apa saja soal materi. Dapat bantuan dari mentor & komunitas.</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('app.qa-threads.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover-lift">Buat Thread</a>
        <a href="{{ route('app.qa-threads.index') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 hover-lift">Lihat Semua</a>
      </div>
    </div>

    @php $__threads = isset($latestThreads) ? $latestThreads : collect(); @endphp

    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($__threads as $t)
        <a href="{{ route('app.qa-threads.show', $t) }}" class="group card card-lg p-5 block hover-lift">
          <div class="flex items-start justify-between gap-3">
            <span class="inline-block text-xs px-2 py-0.5 rounded-full
              {{ ($t->status ?? 'open') === 'resolved' ? 'bg-emerald-100 text-emerald-700' : (($t->status ?? 'open') === 'closed' ? 'bg-gray-200 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
              {{ ucfirst($t->status ?? 'open') }}
            </span>
            <span class="text-xs text-gray-500">{{ $t->created_at?->diffForHumans() }}</span>
          </div>

          <h3 class="mt-3 font-semibold text-blue-950 line-clamp-2 group-hover:underline">
            {{ $t->title }}
          </h3>

          @if (!empty($t->body))
            <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ strip_tags($t->body) }}</p>
          @endif

          <div class="mt-4 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2 truncate">
              <span class="font-medium text-gray-700">{{ $t->user?->name ?? 'User' }}</span>
              @if ($t->course) <span>• {{ $t->course->title }}</span> @endif
              @if ($t->lesson) <span>• {{ $t->lesson->title }}</span> @endif
            </div>
            <div>💬 {{ $t->replies_count ?? 0 }}</div>
          </div>
        </a>
      @empty
        <div class="sm:col-span-2 lg:col-span-3">
          <div class="p-6 card bg-sky-50 border-sky-100 text-blue-900">
            Belum ada diskusi terbaru. <a class="underline" href="{{ route('app.qa-threads.create') }}">Mulai bertanya</a>.
          </div>
        </div>
      @endforelse
    </div>

    <div class="mt-6 flex items-center gap-3 sm:hidden">
      <a href="{{ route('app.qa-threads.create') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover-lift">Buat Thread</a>
      <a href="{{ route('app.qa-threads.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border hover-lift">Lihat Semua</a>
    </div>
  </div>
</section>

{{-- ===================== PLANS ===================== --}}
<section id="plans" class="py-12 bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-bold">Paket Belajar</h2>
        <p class="mt-2 text-blue-100">Akses fleksibel sesuai kebutuhanmu.</p>
      </div>
    </div>

    <div class="mt-8 grid md:grid-cols-3 gap-6">
      @forelse ($plans as $plan)
        <div class="rounded-2xl border border-white/15 p-6 bg-white/10 backdrop-blur hover-lift">
          <div class="flex items-baseline justify-between">
            <h3 class="text-xl font-semibold">{{ $plan->name ?? 'Plan' }}</h3>
            @if ($plan->is_recommended ?? false)
              <span class="text-xs px-2 py-1 rounded-full bg:white/20 bg-white/20">Rekomendasi</span>
            @endif
          </div>

          <div class="mt-3">
            @php $price = (int) ($plan->price ?? 0); @endphp
            <div class="text-3xl font-extrabold">Rp {{ number_format($price, 0, ',', '.') }}</div>
            <div class="text-xs text-blue-100 mt-1">/ {{ ($plan->period ?? 'monthly') === 'yearly' ? 'tahun' : 'bulan' }}</div>
          </div>

          <ul class="mt-4 space-y-2 text-sm">
            <li>✓ Akses {{ $plan->plan_courses_count ?? 0 }} kelas terpilih</li>
            <li>✓ Kuis & Sertifikat</li>
            <li>✓ Pelacakan Progres</li>
            <li>✓ Dukungan Komunitas</li>
          </ul>

          <div class="mt-6">
            @auth
              <form method="POST" action="{{ route('app.memberships.subscribe', $plan) }}">
                @csrf
                <button class="shine w-full px-4 py-2 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50">
                  Pilih Paket
                </button>
              </form>
            @else
              <a href="{{ route('register') }}" class="shine w-full inline-flex justify-center px-4 py-2 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50">
                Daftar untuk Memilih
              </a>
            @endauth
          </div>
        </div>
      @empty
        <div class="md:col-span-3">
          <div class="rounded-2xl border border-white/20 p-6 bg-white/10">Belum ada paket tersedia.</div>
        </div>
      @endforelse
    </div>
  </div>
</section>

{{-- ===================== COUPONS ===================== --}}
<section id="kupon" class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4">
      <div>
        <div class="divider"></div>
        <h2 class="section-title mt-3 text-2xl sm:text-3xl font-bold text-blue-900">Kupon Aktif</h2>
        <p class="mt-2 text-gray-600">Gunakan saat checkout untuk potongan harga.</p>
      </div>
    </div>

    <div class="mt-6 grid md:grid-cols-3 gap-4">
      @forelse ($activeCoupons as $cp)
        <div class="card p-5 bg-gradient-to-br from-white to-sky-50 hover-lift">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-600">Kode Kupon</div>
              <div class="text-xl font-bold tracking-wide text-blue-900">{{ $cp->code }}</div>
            </div>
            <span class="px-2 py-1 text-xs rounded-lg bg-blue-600 text-white">
              {{ number_format($cp->discount_percent, 0) }}%
            </span>
          </div>

          <div class="mt-3 text-xs text-gray-600">
            @php
              $vf = $cp->valid_from ? \Carbon\Carbon::parse($cp->valid_from)->isoFormat('D MMM Y') : 'Sekarang';
              $vu = $cp->valid_until ? \Carbon\Carbon::parse($cp->valid_until)->isoFormat('D MMM Y') : 'Tanpa batas';
            @endphp
            Berlaku: {{ $vf }} — {{ $vu }}
          </div>

          <a href="{{ route('app.memberships.plans') }}" class="mt-4 shine inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover-lift">
            Lihat Paket
          </a>
        </div>
      @empty
        <div class="md:col-span-3">
          <div class="p-6 card bg-sky-50 border-sky-100 text-blue-900">Belum ada kupon aktif.</div>
        </div>
      @endforelse
    </div>
  </div>

  @push('scripts')
  <script>
    if (location.hash === '#alerts') {
      const el = document.getElementById('alerts');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  </script>
  @endpush
</section>

{{-- ===================== CTA ===================== --}}
@guest
<section class="py-12 sm:py-16 bg-gradient-to-r from-sky-300 via-blue-600 to-blue-800">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-8 items-center text-white">
    <div>
      <h2 class="text-2xl sm:text-3xl font-bold">Mulai Gratis, Upgrade Kapan Saja</h2>
      <p class="mt-2 text-blue-100">Akses kelas dasar tanpa biaya. Belajar dulu, upgrade kalau sudah siap.</p>
    </div>
    <div class="flex md:justify-end">
      <a href="{{ route('register') }}" class="shine px-5 py-3 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50 hover-lift">
        Buat Akun
      </a>
    </div>
  </div>
</section>
@endguest

@endsection
