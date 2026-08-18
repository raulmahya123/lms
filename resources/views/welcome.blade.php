{{-- resources/views/welcome.blade.php --}}
@extends('app.layouts.base')

@section('title', 'BERKEMAH - LMS Coding Modern')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
  .landing-shell { background: #f7fbfa; color: #10201d; }
  .text-balance { text-wrap: balance; }
  .section-kicker {
    display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px;
    border: 1px solid rgba(15,157,138,.16); background: rgba(15,157,138,.08);
    color: #087A6C; padding: .42rem .8rem; font-size: .72rem; font-weight: 800;
    letter-spacing: .08em; text-transform: uppercase;
  }
  .soft-card {
    background: rgba(255,255,255,.88); border: 1px solid rgba(15,157,138,.12);
    box-shadow: 0 18px 50px rgba(16,32,29,.07); backdrop-filter: blur(18px);
  }
  .lift { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
  .lift:hover { transform: translateY(-5px); box-shadow: 0 24px 70px rgba(15,157,138,.14); border-color: rgba(15,157,138,.24); }
  .progress-track { height: .45rem; border-radius: 999px; background: #dff5f1; overflow: hidden; }
  .progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg,#0F9D8A,#075E54); }
  .logo-ticker { mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent); }
  .logo-track { animation: ticker 28s linear infinite; }
  @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
  @media (prefers-reduced-motion: reduce) {
    .logo-track { animation: none; }
    .lift, .lift:hover { transition: none; transform: none; }
  }
</style>
@endpush

@section('content')
@php
  $isGuest = auth()->guest();
  $fallbackCover = asset('assets/images/foto-belajar.jpg');
  $heroImage = asset('assets/images/foto-berkemah.png');
  $statsCards = [
    ['label' => 'Kelas', 'value' => $stats['courses'] ?? 0, 'tone' => 'bg-[#0F9D8A]'],
    ['label' => 'Modul', 'value' => $stats['modules'] ?? 0, 'tone' => 'bg-slate-900'],
    ['label' => 'Pelajaran', 'value' => $stats['lessons'] ?? 0, 'tone' => 'bg-emerald-600'],
    ['label' => 'Enrollment', 'value' => $stats['enrollments'] ?? 0, 'tone' => 'bg-cyan-700'],
    ['label' => 'Kuis', 'value' => $stats['quizzes'] ?? 0, 'tone' => 'bg-teal-800'],
  ];
  $techLogos = collect([
    ['name' => 'Laravel', 'src' => asset('assets/logos/laravel.png')],
    ['name' => 'Vue', 'src' => asset('assets/logos/vue.png')],
    ['name' => 'React', 'src' => asset('assets/logos/react.png')],
    ['name' => 'Tailwind', 'src' => asset('assets/logos/tailwind.png')],
    ['name' => 'Node', 'src' => asset('assets/logos/node.png')],
    ['name' => 'Python', 'src' => asset('assets/logos/python.png')],
    ['name' => 'Postgres', 'src' => asset('assets/logos/postgres.png')],
    ['name' => 'Docker', 'src' => asset('assets/logos/docker.png')],
  ]);
@endphp

<div class="landing-shell">
  <section class="relative overflow-hidden bg-[radial-gradient(circle_at_18%_16%,rgba(15,157,138,.16),transparent_28%),radial-gradient(circle_at_82%_18%,rgba(7,94,84,.13),transparent_26%),linear-gradient(180deg,#f2fffc,#ffffff_70%)]">
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#0F9D8A]/40 to-transparent"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 lg:pt-24 lg:pb-16">
      <div class="grid lg:grid-cols-[1.02fr_.98fr] gap-12 items-center">
        <div>
          <span class="section-kicker">Belajar coding lebih terarah</span>
          <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight text-balance">
            Upgrade skill digitalmu lewat kelas online yang rapi, praktis, dan terukur.
          </h1>
          <p class="mt-6 max-w-2xl text-base sm:text-lg leading-8 text-slate-600">
            BERKEMAH membantu pemula, mahasiswa, dan fresh graduate belajar coding dari dasar sampai siap membuat portfolio dengan modul, kuis, forum, sertifikat, dan assessment psikologi.
          </p>

          <div class="mt-7 grid sm:grid-cols-2 gap-3 max-w-2xl">
            @foreach (['Roadmap belajar jelas', 'Project praktik bertahap', 'Kuis dan progres otomatis', 'Sertifikat digital'] as $benefit)
              <div class="flex items-center gap-3 rounded-2xl bg-white/80 border border-[#0F9D8A]/12 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm">
                <span class="grid h-6 w-6 place-items-center rounded-full bg-[#DFF5F1] text-[#087A6C]">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                {{ $benefit }}
              </div>
            @endforeach
          </div>

          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="#kelas-terbaru" class="inline-flex justify-center items-center gap-2 rounded-2xl bg-[#0F9D8A] px-7 py-4 text-sm font-extrabold text-white shadow-lg shadow-[#0F9D8A]/25 hover:bg-[#087A6C] transition">
              Jelajah Kelas
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            @guest
              <a href="{{ route('register') }}" class="inline-flex justify-center items-center gap-2 rounded-2xl border border-[#0F9D8A]/20 bg-white px-7 py-4 text-sm font-extrabold text-[#087A6C] hover:bg-[#DFF5F1] transition">
                Daftar Gratis
              </a>
            @else
              <a href="{{ route('app.dashboard') }}" class="inline-flex justify-center items-center gap-2 rounded-2xl border border-slate-200 bg-white px-7 py-4 text-sm font-extrabold text-slate-800 hover:border-[#0F9D8A]/30 transition">
                Buka Dashboard
              </a>
            @endguest
          </div>
        </div>

        <div class="relative">
          <div class="soft-card rounded-[2rem] p-3 lift">
            <div class="relative overflow-hidden rounded-[1.45rem] bg-slate-900">
              <img src="{{ $heroImage }}" alt="BERKEMAH online learning" class="h-[350px] sm:h-[470px] w-full object-cover opacity-95">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent"></div>
              <div class="absolute left-5 right-5 bottom-5 rounded-2xl bg-white/90 backdrop-blur px-5 py-4 shadow-xl">
                <div class="flex items-center justify-between gap-4">
                  <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#087A6C]">Learning progress</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">Belajar, latihan, diskusi, lalu dapat sertifikat.</p>
                  </div>
                  <div class="shrink-0 rounded-2xl bg-[#0F9D8A] px-4 py-3 text-center text-white">
                    <div class="text-xl font-black">{{ number_format($stats['courses'] ?? 0) }}+</div>
                    <div class="text-[10px] font-bold uppercase">kelas</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="hidden lg:block absolute -left-8 top-10 rounded-2xl bg-white px-5 py-4 shadow-xl border border-[#0F9D8A]/10">
            <p class="text-xs text-slate-500">Member aktif</p>
            <p class="text-2xl font-black text-[#075E54]">{{ number_format($stats['enrollments'] ?? 0) }}+</p>
          </div>
          <div class="hidden lg:block absolute -right-6 bottom-20 rounded-2xl bg-slate-950 px-5 py-4 shadow-xl">
            <p class="text-xs text-white/60">Assessment</p>
            <p class="text-lg font-black text-white">IQ + Psy Test</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-white py-6 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach ($statsCards as $item)
          <div class="soft-card rounded-2xl p-5 lift">
            <div class="flex items-center gap-3">
              <span class="h-10 w-10 rounded-2xl {{ $item['tone'] }} grid place-items-center text-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
              </span>
              <div>
                <div class="text-2xl font-black text-slate-950">{{ number_format($item['value']) }}</div>
                <div class="text-xs font-semibold text-slate-500">{{ $item['label'] }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-8 bg-[#f7fbfa]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-5">
        <div class="flex items-center justify-between gap-4">
          <p class="text-sm font-bold text-slate-500">Trusted technologies behind the learning experience</p>
          <span class="hidden sm:inline-flex text-xs font-bold text-[#087A6C]">Modern stack</span>
        </div>
        <div class="logo-ticker overflow-hidden rounded-3xl bg-white border border-slate-100 py-5">
          <div class="logo-track flex w-max items-center gap-10 px-8">
            @foreach ($techLogos->concat($techLogos) as $logo)
              <div class="flex min-w-36 items-center gap-3">
                <img src="{{ $logo['src'] }}" alt="{{ $logo['name'] }}" class="h-8 w-8 object-contain">
                <span class="text-sm font-bold text-slate-600">{{ $logo['name'] }}</span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sticky top-16 z-30 border-y border-slate-100 bg-white/86 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div class="flex items-center gap-2 overflow-x-auto">
        @php
          $chips = [
            ['label' => 'Kelas Terbaru', 'href' => '#kelas-terbaru'],
            ['label' => 'Populer', 'href' => '#kelas-populer'],
            ['label' => 'Tes Psikologi', 'href' => '#tes-psikologi'],
            ['label' => 'Paket', 'href' => '#paket'],
            ['label' => 'Kupon', 'href' => '#kupon'],
          ];
        @endphp
        @foreach ($chips as $chip)
          <a href="{{ $chip['href'] }}" class="whitespace-nowrap rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 hover:border-[#0F9D8A]/30 hover:bg-[#DFF5F1] hover:text-[#087A6C] transition">{{ $chip['label'] }}</a>
        @endforeach
        <span class="min-w-4 flex-1"></span>
        @auth
          <a href="{{ route('app.dashboard') }}" class="whitespace-nowrap rounded-full bg-slate-950 px-4 py-2 text-xs font-bold text-white">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="whitespace-nowrap rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700">Login</a>
          <a href="{{ route('register') }}" class="whitespace-nowrap rounded-full bg-[#0F9D8A] px-4 py-2 text-xs font-bold text-white">Daftar</a>
        @endauth
      </div>
    </div>
  </section>

  @auth
    @php
      $user = auth()->user();
      $initial = strtoupper(mb_substr($user->name ?? 'U', 0, 1));
      $membershipLabel = ($isMember ?? false) ? 'Member aktif' : 'Belum membership';
    @endphp
    <section id="profil" class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="soft-card rounded-3xl p-6 sm:p-8">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4">
              <div class="grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-[#0F9D8A] to-[#075E54] text-2xl font-black text-white shadow-lg shadow-[#0F9D8A]/20">{{ $initial }}</div>
              <div>
                <p class="text-xs font-black uppercase tracking-wider text-[#087A6C]">Profil belajar</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <span class="mt-2 inline-flex rounded-full bg-[#DFF5F1] px-3 py-1 text-xs font-bold text-[#087A6C]">{{ $membershipLabel }}</span>
              </div>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
              <a href="{{ route('app.my.courses') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-xs font-bold hover:bg-[#DFF5F1]">My Courses</a>
              <a href="{{ route('app.certificates.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-xs font-bold hover:bg-[#DFF5F1]">Certificates</a>
              <a href="{{ route('app.payments.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-xs font-bold hover:bg-[#DFF5F1]">Payments</a>
              <a href="{{ route('app.memberships.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-xs font-bold hover:bg-[#DFF5F1]">Memberships</a>
              <a href="{{ route('app.psytests.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-xs font-bold hover:bg-[#DFF5F1]">Psy Tests</a>
              <a href="{{ route('profile.edit') }}" class="rounded-xl bg-[#0F9D8A] px-4 py-2 text-center text-xs font-bold text-white hover:bg-[#087A6C]">Edit Profile</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endauth

  <section class="py-20 bg-[#f7fbfa]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl">
        <span class="section-kicker">Kenapa BERKEMAH?</span>
        <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-slate-950">Belajar terasa lebih fokus, aktif, dan punya hasil nyata.</h2>
      </div>
      <div class="mt-10 grid md:grid-cols-3 gap-6">
        @foreach ([
          ['title' => 'Materi Terarah', 'desc' => 'Kurikulum dibuat bertahap agar learner tahu mulai dari mana dan lanjut ke mana.', 'items' => ['Roadmap jelas', 'Modul ringkas', 'Urutan belajar rapi']],
          ['title' => 'Belajar Aktif', 'desc' => 'Kelas tidak berhenti di teori, ada kuis, forum, dan latihan yang membuat materi menempel.', 'items' => ['Kuis interaktif', 'Forum tanya-jawab', 'Progress tracking']],
          ['title' => 'Hasil Nyata', 'desc' => 'Setiap pembelajaran diarahkan menjadi output yang bisa ditunjukkan dan dievaluasi.', 'items' => ['Portfolio project', 'Sertifikat digital', 'Assessment diri']],
        ] as $feature)
          <div class="soft-card rounded-3xl p-7 lift">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#DFF5F1] text-[#087A6C]">
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="mt-5 text-xl font-black text-slate-950">{{ $feature['title'] }}</h3>
            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $feature['desc'] }}</p>
            <ul class="mt-5 space-y-2">
              @foreach ($feature['items'] as $item)
                <li class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                  <span class="h-1.5 w-1.5 rounded-full bg-[#0F9D8A]"></span>{{ $item }}
                </li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section id="kelas-terbaru" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-section-heading kicker="Kelas Terbaru" title="Mulai dari kelas paling baru." subtitle="Konten fresh untuk skill coding, web, backend, frontend, dan praktik digital." />
      <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($latestCourses as $course)
          @include('partials.landing-course-card', ['course' => $course, 'fallbackCover' => $fallbackCover, 'isGuest' => $isGuest, 'popular' => false])
        @empty
          <div class="sm:col-span-2 lg:col-span-3 soft-card rounded-3xl p-8 text-center font-bold text-slate-600">Belum ada kelas terbaru.</div>
        @endforelse
      </div>
    </div>
  </section>

  <section id="kelas-populer" class="py-20 bg-[#f7fbfa]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-section-heading kicker="Kelas Populer" title="Kelas yang paling sering dipilih learner." subtitle="Bagian ini membantu user cepat menemukan kelas dengan demand tinggi." />
      <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($popularCourses as $course)
          @include('partials.landing-course-card', ['course' => $course, 'fallbackCover' => $fallbackCover, 'isGuest' => $isGuest, 'popular' => true])
        @empty
          <div class="sm:col-span-2 lg:col-span-3 soft-card rounded-3xl p-8 text-center font-bold text-slate-600">Belum ada kelas populer.</div>
        @endforelse
      </div>
    </div>
  </section>

  <section id="tes-psikologi" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-8 items-start">
        <div>
          <span class="section-kicker">Tes Psikologi</span>
          <h2 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-slate-950">Kenali cara belajarmu sebelum melangkah lebih jauh.</h2>
          <p class="mt-4 text-slate-600 leading-8">Section assessment dibuat berbeda dari course card agar terasa analitis, premium, dan relevan untuk self-development learner.</p>
          @unless ($isMember ?? false)
            <div class="mt-6 rounded-3xl bg-slate-950 p-6 text-white">
              <p class="text-sm font-black uppercase tracking-wider text-[#DFF5F1]">Upgrade membership</p>
              <p class="mt-2 text-sm leading-7 text-white/75">Non-member tetap bisa melihat daftar tes. Akses penuh dibuka melalui paket belajar.</p>
              <a href="#paket" class="mt-5 inline-flex rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-950">Lihat Paket</a>
            </div>
          @endunless
        </div>
        <div class="grid sm:grid-cols-2 gap-5">
          @foreach (($iqTests ?? collect())->take(2) as $test)
            @php $questionCount = is_countable($test->questions ?? null) ? count($test->questions) : 0; @endphp
            <div class="soft-card rounded-3xl p-6 lift">
              <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">Tes IQ</span>
              <h3 class="mt-4 text-lg font-black text-slate-950">{{ $test->title ?? 'Tes IQ' }}</h3>
              <p class="mt-2 text-sm leading-6 text-slate-500 line-clamp-2">{{ $test->description ?? 'Ukur kemampuan penalaran dengan soal bertahap.' }}</p>
              <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                <span class="rounded-full bg-[#DFF5F1] px-3 py-1">{{ $test->duration_minutes ?: 15 }} menit</span>
                <span class="rounded-full bg-[#DFF5F1] px-3 py-1">{{ $questionCount }} soal</span>
              </div>
              <a href="{{ ($isMember ?? false) ? route('app.test-iq.show', $test) : '#paket' }}" class="mt-6 inline-flex rounded-2xl bg-[#0F9D8A] px-5 py-3 text-sm font-black text-white">Mulai Tes</a>
            </div>
          @endforeach

          @foreach (($psyTests ?? collect())->take(4) as $test)
            @php $questions = (int)($test->questions_count ?? 0); @endphp
            <div class="soft-card rounded-3xl p-6 lift">
              <span class="rounded-full bg-[#DFF5F1] px-3 py-1 text-xs font-black text-[#087A6C]">{{ strtoupper($test->type ?? 'Psy Test') }}</span>
              <h3 class="mt-4 text-lg font-black text-slate-950">{{ $test->name }}</h3>
              <p class="mt-2 text-sm leading-6 text-slate-500">{{ ucfirst($test->track ?? 'general') }} assessment untuk memahami preferensi belajar.</p>
              <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                <span class="rounded-full bg-slate-100 px-3 py-1">{{ max(5, round($questions * .75)) }} menit</span>
                <span class="rounded-full bg-slate-100 px-3 py-1">{{ $questions }} soal</span>
              </div>
              @if ($isMember ?? false)
                <form method="POST" action="{{ route('app.psy.attempts.start', $test) }}" class="mt-6">
                  @csrf
                  <button class="rounded-2xl bg-[#0F9D8A] px-5 py-3 text-sm font-black text-white">Mulai Tes</button>
                </form>
              @else
                <a href="#paket" class="mt-6 inline-flex rounded-2xl bg-slate-200 px-5 py-3 text-sm font-black text-slate-500">Upgrade</a>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section id="forum" class="py-20 bg-[#f7fbfa]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-section-heading kicker="Forum Tanya-Jawab" title="Belajar tidak sendirian." subtitle="Thread terbaru dari learner dan mentor membuat platform terasa aktif." />
      <div class="mt-10 grid md:grid-cols-3 gap-6">
        @forelse (($latestThreads ?? collect()) as $thread)
          <a href="{{ route('app.qa-threads.show', $thread) }}" class="soft-card rounded-3xl p-6 lift block">
            <div class="flex items-center justify-between gap-3">
              <span class="rounded-full px-3 py-1 text-xs font-black {{ ($thread->status ?? 'open') === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($thread->status ?? 'open') }}</span>
              <span class="text-xs font-semibold text-slate-400">{{ $thread->created_at?->diffForHumans() }}</span>
            </div>
            <h3 class="mt-5 line-clamp-2 text-lg font-black text-slate-950">{{ $thread->title }}</h3>
            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ strip_tags($thread->body ?? 'Diskusi terbaru dari komunitas BERKEMAH.') }}</p>
            <div class="mt-6 flex items-center justify-between text-xs font-bold text-slate-500">
              <span class="truncate">{{ $thread->user?->name ?? 'Learner' }}</span>
              <span>{{ $thread->course?->title ?? 'General' }}</span>
              <span>{{ $thread->replies_count ?? 0 }} replies</span>
            </div>
          </a>
        @empty
          <div class="md:col-span-3 soft-card rounded-3xl p-8 text-center font-bold text-slate-600">Belum ada forum tanya-jawab.</div>
        @endforelse
      </div>
    </div>
  </section>

  <section id="paket" class="py-20 bg-slate-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mx-auto text-center">
        <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-wider text-[#DFF5F1]">Paket Belajar</span>
        <h2 class="mt-5 text-3xl sm:text-4xl font-black tracking-tight">Pricing yang jelas untuk mulai belajar.</h2>
        <p class="mt-4 text-white/65 leading-8">Pilih paket sesuai ritme belajar. Satu card dapat dibuat recommended dari data plan.</p>
      </div>
      <div class="mt-12 grid md:grid-cols-3 gap-6">
        @forelse ($plans as $plan)
          @php
            $rawFeatures = $plan->features ?? null;
            $features = collect();
            if (is_array($rawFeatures)) {
              $features = collect($rawFeatures);
            } elseif (is_string($rawFeatures)) {
              $decoded = json_decode($rawFeatures, true);
              $features = json_last_error() === JSON_ERROR_NONE && is_array($decoded)
                ? collect($decoded)
                : collect(preg_split('/\r\n|\r|\n/', trim($rawFeatures)) ?: []);
            }
            if ($features->filter(fn ($feature) => filled($feature))->isEmpty()) {
              $features = collect(['Akses kelas pilihan', 'Kuis dan sertifikat', 'Forum komunitas', 'Progress tracking']);
            }
            $isRecommended = $loop->iteration === 2 || (bool)($plan->is_recommended ?? false);
          @endphp
          <div class="relative rounded-3xl border {{ $isRecommended ? 'border-[#0F9D8A] bg-white text-slate-950 scale-[1.02]' : 'border-white/10 bg-white/7 text-white' }} p-7 lift">
            @if ($isRecommended)
              <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[#0F9D8A] px-4 py-1.5 text-xs font-black text-white">Recommended</span>
            @endif
            <h3 class="text-xl font-black">{{ $plan->name ?? 'Plan' }}</h3>
            <div class="mt-5">
              <span class="text-4xl font-black">Rp {{ number_format((int)($plan->price ?? 0), 0, ',', '.') }}</span>
              <span class="text-sm {{ $isRecommended ? 'text-slate-500' : 'text-white/55' }}">/{{ $plan->period ?? 'bulan' }}</span>
            </div>
            <ul class="mt-7 space-y-3">
              @foreach ($features->filter(fn ($feature) => filled($feature))->take(6) as $feature)
                <li class="flex gap-3 text-sm font-semibold {{ $isRecommended ? 'text-slate-700' : 'text-white/75' }}">
                  <span class="mt-1 h-2 w-2 rounded-full bg-[#0F9D8A]"></span>
                  {{ is_array($feature) ? ($feature['label'] ?? json_encode($feature)) : $feature }}
                </li>
              @endforeach
            </ul>
            <div class="mt-8">
              @auth
                <form method="POST" action="{{ route('app.memberships.subscribe', $plan) }}">
                  @csrf
                  <button class="w-full rounded-2xl {{ $isRecommended ? 'bg-[#0F9D8A] text-white' : 'bg-white text-slate-950' }} px-5 py-3 text-sm font-black">Pilih Paket</button>
                </form>
              @else
                <a href="{{ route('register') }}" class="block w-full rounded-2xl {{ $isRecommended ? 'bg-[#0F9D8A] text-white' : 'bg-white text-slate-950' }} px-5 py-3 text-center text-sm font-black">Daftar Dulu</a>
              @endauth
            </div>
          </div>
        @empty
          <div class="md:col-span-3 rounded-3xl border border-white/10 bg-white/7 p-8 text-center font-bold text-white/70">Belum ada paket tersedia.</div>
        @endforelse
      </div>
    </div>
  </section>

  <section id="kupon" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <x-section-heading kicker="Kupon Aktif" title="Promo yang mudah ditemukan." subtitle="Voucher tampil bersih, kuat secara visual, tapi tetap profesional." />
      <div class="mt-10 grid md:grid-cols-3 gap-6">
        @forelse ($activeCoupons as $coupon)
          <div class="soft-card rounded-3xl p-6 lift relative overflow-hidden">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-[#DFF5F1]"></div>
            <div class="relative">
              <p class="text-xs font-black uppercase tracking-wider text-slate-400">Kode Kupon</p>
              <h3 class="mt-2 font-mono text-3xl font-black tracking-wider text-slate-950">{{ $coupon->code }}</h3>
              <div class="mt-5 inline-flex rounded-2xl bg-[#0F9D8A] px-4 py-2 text-xl font-black text-white">{{ number_format($coupon->discount_percent, 0) }}% OFF</div>
              @php
                $from = $coupon->valid_from ? \Carbon\Carbon::parse($coupon->valid_from)->format('d M Y') : 'Sekarang';
                $until = $coupon->valid_until ? \Carbon\Carbon::parse($coupon->valid_until)->format('d M Y') : 'Tanpa batas';
              @endphp
              <p class="mt-4 text-xs font-semibold text-slate-500">{{ $from }} sampai {{ $until }}</p>
              <a href="{{ route('app.memberships.plans') }}" class="mt-6 inline-flex rounded-2xl bg-[#DFF5F1] px-5 py-3 text-sm font-black text-[#087A6C]">Lihat Paket</a>
            </div>
          </div>
        @empty
          <div class="md:col-span-3 soft-card rounded-3xl p-8 text-center font-bold text-slate-600">Belum ada kupon aktif.</div>
        @endforelse
      </div>
    </div>
  </section>

  @guest
    <section class="py-20 bg-[#f7fbfa]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#075E54] via-[#087A6C] to-[#0F9D8A] px-6 py-14 text-center text-white shadow-2xl shadow-[#0F9D8A]/20 sm:px-12">
          <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(90deg,#fff 1px,transparent 1px),linear-gradient(#fff 1px,transparent 1px); background-size: 32px 32px;"></div>
          <div class="relative max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight">Mulai gratis, upgrade kapan saja.</h2>
            <p class="mt-4 text-white/80 leading-8">Buat akun untuk mulai menyusun progres belajar, mengambil kelas, mengikuti kuis, dan mengakses rekomendasi belajar.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex rounded-2xl bg-white px-8 py-4 text-sm font-black text-[#075E54]">Buat Akun</a>
          </div>
        </div>
      </div>
    </section>
  @endguest
</div>
@endsection
