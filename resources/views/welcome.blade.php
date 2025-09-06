{{-- resources/views/welcome.blade.php --}}
@extends('app.layouts.base')

@section('title', 'BERKEMAH — Belajar Teknologi di Alam')

@push('styles')
<style>
  [x-cloak]{display:none}
  .text-balance { text-wrap: balance; }

  /* === Blobs dekoratif, pastikan di belakang konten === */
  .blob       { filter: blur(80px); opacity:.35; }
  .blob-layer { position:absolute; inset:0; pointer-events:none; z-index:-1; }

  /* === Ticker logo bahasa (marquee tanpa <marquee>) === */
  .logo-ticker-mask{
    mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
  }
  .logo-viewport{ overflow:hidden; }
  .logo-track{
    display:flex; gap:1rem; align-items:center; white-space:nowrap;
    width: max-content;
    animation:logo-scroll 28s linear infinite;
  }
  .logo-track:hover{ animation-play-state: paused; }
  @keyframes logo-scroll{
    from{ transform:translateX(0); }
    to  { transform:translateX(-50%); }
  }

  .logo-chip{
    height:40px; width:40px; border-radius:9999px; background:#fff;
    display:flex; align-items:center; justify-content:center;
    border:1px solid rgba(15,23,42,.08); box-shadow:0 2px 10px rgba(2,6,23,.06);
    padding:.5rem;
  }
  .logo-chip img{ height:100%; width:auto; object-fit:contain; }
</style>
@endpush

@section('content')
  {{-- ===================== HERO ===================== --}}
  <section class="relative overflow-hidden bg-gradient-to-b from-sky-50 via-white to-white">
    <div class="blob-layer">
      <div class="absolute -top-24 -right-24 w-[28rem] h-[28rem] rounded-full bg-sky-300 blob"></div>
      <div class="absolute -bottom-24 -left-24 w-[28rem] h-[28rem] rounded-full bg-blue-300 blob"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20 grid lg:grid-cols-2 gap-10 relative z-10">
      <div class="flex flex-col justify-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-medium w-max">
          <span class="h-2 w-2 rounded-full bg-sky-500"></span> Belajar di Alam, Kapan Saja
        </div>
        <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold leading-tight text-balance">
          Upgrade <span class="text-blue-700">Skill Programming</span> kamu dengan <span class="text-blue-900">praktik nyata</span>
        </h1>
        <p class="mt-4 text-gray-600 max-w-2xl">
          Kelas terstruktur, kuis interaktif, tracking progres, hingga sertifikat. Cocok buat pemula sampai pro.
        </p>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <a href="#kursus-baru" class="px-5 py-3 rounded-xl bg-blue-600 text-white text-center hover:bg-blue-700">
            Jelajah Kelas
          </a>
          @guest
            <a href="{{ route('register') }}" class="px-5 py-3 rounded-xl border text-center hover:bg-gray-50">
              Daftar Gratis
            </a>
          @endguest
        </div>

        {{-- stats mini --}}
        <div class="mt-8 grid grid-cols-3 sm:grid-cols-5 gap-3">
          @php
            $statItems = [
              ['label'=>'Kelas','value'=>$stats['courses'] ?? 0],
              ['label'=>'Modul','value'=>$stats['modules'] ?? 0],
              ['label'=>'Pelajaran','value'=>$stats['lessons'] ?? 0],
              ['label'=>'Enrollment','value'=>$stats['enrollments'] ?? 0],
              ['label'=>'Kuis','value'=>$stats['quizzes'] ?? 0],
            ];
          @endphp
          @foreach ($statItems as $s)
            <div class="bg-white/80 backdrop-blur border rounded-xl p-4 text-center">
              <div class="text-2xl font-semibold text-blue-900">{{ number_format($s['value']) }}</div>
              <div class="text-xs text-gray-600 mt-1">{{ $s['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="relative">
        <img src="{{ asset('assets/images/KKN.jpeg') }}"
             alt="Belajar di alam"
             class="w-full h-72 sm:h-96 object-cover rounded-2xl shadow-2xl border border-blue-100" />
        <div class="absolute -bottom-4 -right-4 bg-white/90 backdrop-blur rounded-xl shadow-lg p-4 border">
          <p class="text-xs text-gray-600">Total Pembelajar</p>
          <p class="text-2xl font-bold text-blue-900">{{ number_format(($stats['enrollments'] ?? 0) + 12000) }}+</p>
        </div>
      </div>
    </div>

    {{-- ===================== LOGO TICKER ===================== --}}
    <div class="py-6 border-t">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 logo-viewport logo-ticker-mask">
        @php
          $logos = ['laravel.png','vue.png','react.png','node.png','python.png','golang.png',
                    'docker.png','mysql.png','postgres.png','redis.png','tailwind.png'];
        @endphp
        <div class="logo-track">
          @foreach (array_merge($logos, $logos) as $logo)
            <div class="logo-chip">
              <img src="{{ asset('assets/logos/'.$logo) }}" alt="{{ pathinfo($logo, PATHINFO_FILENAME) }}">
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  {{-- ===================== NAV QUICK LINKS ===================== --}}
  <section class="py-4 bg-white border-y">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center gap-2 text-sm">
      <a href="#kursus-baru" class="px-3 py-2 rounded-full bg-sky-50 text-blue-800 hover:bg-sky-100">Kelas Terbaru</a>
      <a href="#kursus-populer" class="px-3 py-2 rounded-full bg-sky-50 text-blue-800 hover:bg-sky-100">Populer</a>
      <a href="#psi" class="px-3 py-2 rounded-full bg-sky-50 text-blue-800 hover:bg-sky-100">Tes Psikologi</a>
      <a href="#plans" class="px-3 py-2 rounded-full bg-sky-50 text-blue-800 hover:bg-sky-100">Paket</a>
      <a href="#kupon" class="px-3 py-2 rounded-full bg-sky-50 text-blue-800 hover:bg-sky-100">Kupon</a>
      <div class="ms-auto flex items-center gap-2">
        @auth
          <a href="{{ route('app.dashboard') }}" class="px-3 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-800">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50">Masuk</a>
          <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Daftar</a>
        @endauth
      </div>
    </div>
  </section>

  {{-- ===================== KATEGORI ===================== --}}
  <section class="py-10 bg-gradient-to-b from-white to-sky-50/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="text-xl sm:text-2xl font-bold text-blue-900">Kategori Populer</h2>
        <span class="text-sm text-gray-500">Telusuri minatmu</span>
      </div>
      <div class="flex flex-wrap gap-2">
        @foreach ($categories as $cat)
          <a href="{{ auth()->check() ? route('app.courses.index', ['category'=>$cat['key']]) : route('register') }}"
             class="px-3 py-2 rounded-full border bg-white hover:bg-sky-50 text-sm">
            {{ $cat['name'] }}
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ===================== KELAS TERBARU ===================== --}}
  <section id="kursus-baru" class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-end justify-between gap-4">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-blue-900">Kelas Terbaru</h2>
          <p class="mt-2 text-gray-600">Konten fresh, langsung praktik.</p>
        </div>
        <a href="{{ auth()->check() ? route('app.courses.index') : route('register') }}"
           class="hidden sm:inline-flex items-center gap-2 text-blue-700 hover:underline">
          Lihat Semua →
        </a>
      </div>

      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($latestCourses as $course)
          @php $cover = $course->cover_url ?? asset('assets/images/placeholder-course.png'); @endphp
          <a href="{{ auth()->check() ? route('app.courses.show', $course) : route('register') }}"
             class="group bg-white border rounded-2xl overflow-hidden hover:shadow-xl transition block">
            <div class="aspect-[16/9] overflow-hidden bg-gray-100">
              <img src="{{ $cover }}" alt="{{ $course->title }}"
                   class="w-full h-full object-cover group-hover:scale-105 transition" />
            </div>
            <div class="p-4">
              <div class="text-xs font-medium text-blue-700/80">{{ $course->level ?? 'All Levels' }}</div>
              <h3 class="mt-1 font-semibold line-clamp-2 text-blue-950">{{ $course->title }}</h3>
              <div class="mt-2 text-xs text-gray-600 flex items-center gap-3">
                <span>{{ $course->modules_count ?? 0 }} modul</span>
                <span>•</span>
                <span>{{ $course->enrollments_count ?? 0 }} siswa</span>
              </div>
            </div>
          </a>
        @empty
          <div class="sm:col-span-2 lg:col-span-3">
            <div class="p-6 border rounded-2xl bg-sky-50 text-blue-900">
              Belum ada kelas terbaru.
            </div>
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
          <h2 class="text-2xl sm:text-3xl font-bold text-blue-900">Kelas Populer</h2>
          <p class="mt-2 text-gray-600">Paling banyak diikuti.</p>
        </div>
      </div>

      <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($popularCourses as $course)
          @php $cover = $course->cover_url ?? asset('assets/images/placeholder-course.png'); @endphp
          <a href="{{ auth()->check() ? route('app.courses.show', $course) : route('register') }}"
             class="group bg-white border rounded-2xl overflow-hidden hover:shadow-xl transition block">
            <div class="aspect-[16/9] overflow-hidden bg-gray-100">
              <img src="{{ $cover }}" alt="{{ $course->title }}"
                   class="w-full h-full object-cover group-hover:scale-105 transition" />
            </div>
            <div class="p-4">
              <div class="text-xs text-emerald-700/80 font-medium">Populer</div>
              <h3 class="mt-1 font-semibold line-clamp-2 text-blue-950">{{ $course->title }}</h3>
              <div class="mt-2 text-xs text-gray-600 flex items-center gap-3">
                <span>{{ $course->modules_count ?? 0 }} modul</span>
                <span>•</span>
                <span>{{ $course->enrollments_count ?? 0 }} siswa</span>
              </div>
            </div>
          </a>
        @empty
          <div class="sm:col-span-2 lg:col-span-3">
            <div class="p-6 border rounded-2xl bg-sky-50 text-blue-900">
              Belum ada kelas populer.
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  {{-- ===================== TES PSIKOLOGI (PSI) ===================== --}}
{{-- ===================== TES PSIKOLOGI (PSI) ===================== --}}
<section id="psi" class="relative py-14">
  {{-- dekorasi halus --}}
  <div class="pointer-events-none absolute inset-0 -z-10">
    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-sky-200/50 blur-3xl"></div>
    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-blue-200/40 blur-3xl"></div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 text-xs text-blue-700">
          <span class="h-2 w-2 rounded-full bg-blue-600"></span> Penilaian Personal
        </div>
        <h2 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
          Tes Psikologi
        </h2>
        <p class="mt-2 text-slate-600 max-w-2xl">
          Kenali kekuatan & preferensimu. Hasil langsung dengan rekomendasi otomatis.
        </p>
      </div>
      <a href="{{ route('app.psytests.index') }}"
         class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-blue-200 text-blue-700 hover:bg-blue-50 transition">
        Lihat Semua Tes
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
             d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
      </a>
    </div>

    @php
      $__psy = isset($psyTests) ? $psyTests : collect();
      // warna per tipe (fallback 'custom')
      $typeColors = [
        'likert'  => 'from-blue-500 to-indigo-500',
        'mcq'     => 'from-violet-500 to-purple-500',
        'iq'      => 'from-emerald-500 to-teal-500',
        'disc'    => 'from-amber-500 to-orange-500',
        'big5'    => 'from-fuchsia-500 to-pink-500',
        'custom'  => 'from-slate-500 to-slate-700',
      ];
    @endphp

    @if($__psy->count())
      {{-- grid desktop + snap scroll mobile --}}
      <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3
                  md:[&>*]:snap-none [&>*]:snap-start overflow-x-auto md:overflow-visible scroll-smooth">
        @foreach($__psy as $t)
          @php
            $type = strtolower($t->type ?? 'custom');
            $grad = $typeColors[$type] ?? $typeColors['custom'];
            $qs   = (int)($t->questions_count ?? 0);
            // estimasi durasi (≈ 40–60 dtk/soal), minimal 5 menit
            $est  = max(5, round($qs * 0.75));
          @endphp

          <div class="min-w-[88%] sm:min-w-0 group relative rounded-2xl border bg-white/90 backdrop-blur
                      border-slate-200 shadow-sm hover:shadow-xl transition overflow-hidden">
            {{-- stripe gradien --}}
            <div class="h-1.5 bg-gradient-to-r {{ $grad }}"></div>

            <div class="p-4">
              <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
                       d="M12 6v6l4 2"/></svg>
                  {{ $est }} menit
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700">
                  {{ strtoupper($t->type ?? 'custom') }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-teal-50 text-teal-700">
                  {{ ucfirst($t->track ?? 'general') }}
                </span>
              </div>

              <h3 class="mt-3 text-lg font-semibold text-slate-900 line-clamp-2">
                {{ $t->name }}
              </h3>

              <div class="mt-1 text-xs text-slate-500 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
                     d="M4 6h16M4 12h16M4 18h7"/></svg>
                {{ $qs }} soal
              </div>

              @if(!empty($t->description))
                <p class="mt-3 text-sm text-slate-600 line-clamp-2">{{ $t->description }}</p>
              @endif

              <div class="mt-5 flex items-center gap-2">
                <form method="POST" action="{{ route('app.psy.attempts.start', $t) }}">
                  @csrf
                  <button
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-white bg-gradient-to-r {{ $grad }}
                           hover:brightness-105 active:brightness-95 transition text-sm">
                    Mulai Tes
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
                         d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                  </button>
                </form>

                <a href="{{ route('app.psytests.show', $t->slug ?: $t->id) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-slate-700 hover:bg-slate-50 text-sm transition">
                  Detail
                </a>
              </div>
            </div>

            {{-- efek glow saat hover --}}
            <div class="pointer-events-none absolute -inset-px opacity-0 group-hover:opacity-100 transition
                        bg-gradient-to-r {{ $grad }} blur-[16px] rounded-2xl"></div>
          </div>
        @endforeach
      </div>
    @else
      {{-- empty state --}}
      <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
        <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
               d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">Belum ada tes tersedia</h3>
        <p class="mt-1 text-slate-600">Saat tes sudah aktif, kamu bisa mulai dari sini.</p>
        <a href="{{ route('app.psytests.index') }}"
           class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
          Jelajahi Tes
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round"
               d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
      </div>
    @endif
  </div>
</section>


  {{-- ===================== PLANS ===================== --}}
  <section id="plans" class="py-12 bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-end justify-between gap-4">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold">Paket Belajar</h2>
          <p class="mt-2 text-blue-100">Akses fleksibel sesuai kebutuhanmu.</p>
        </div>
      </div>

      <div class="mt-8 grid md:grid-cols-3 gap-6">
        @forelse ($plans as $plan)
          <div class="bg-white/10 backdrop-blur rounded-2xl border border-white/15 p-6">
            <div class="flex items-baseline justify-between">
              <h3 class="text-xl font-semibold">{{ $plan->name ?? 'Plan' }}</h3>
              @if(($plan->is_recommended ?? false))
                <span class="text-xs px-2 py-1 rounded-full bg-white/20">Rekomendasi</span>
              @endif
            </div>
            <div class="mt-3">
              @php $price = $plan->price ?? 0; @endphp
              <div class="text-3xl font-extrabold">Rp {{ number_format($price,0,',','.') }}</div>
              <div class="text-xs text-blue-100 mt-1">/ {{ $plan->billing_cycle ?? 'bulan' }}</div>
            </div>
            <ul class="mt-4 space-y-2 text-sm">
              <li>✓ Akses {{ $plan->planCourses_count ?? $plan->plan_courses_count ?? 0 }} kelas terpilih</li>
              <li>✓ Kuis & Sertifikat</li>
              <li>✓ Pelacakan Progres</li>
              <li>✓ Dukungan Komunitas</li>
            </ul>
            <div class="mt-6">
              @auth
                <form method="POST" action="{{ route('app.checkout.plan', $plan) }}">
                  @csrf
                  <button class="w-full px-4 py-2 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50">
                    Pilih Paket
                  </button>
                </form>
              @else
                <a href="{{ route('register') }}"
                   class="w-full inline-flex justify-center px-4 py-2 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50">
                  Daftar untuk Memilih
                </a>
              @endauth
            </div>
          </div>
        @empty
          <div class="md:col-span-3">
            <div class="bg-white/10 border border-white/20 rounded-2xl p-6">Belum ada paket tersedia.</div>
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
          <h2 class="text-2xl sm:text-3xl font-bold text-blue-900">Kupon Aktif</h2>
          <p class="mt-2 text-gray-600">Gunakan saat checkout untuk potongan harga.</p>
        </div>
      </div>

      <div class="mt-6 grid md:grid-cols-3 gap-4">
        @forelse ($activeCoupons as $cp)
          <div class="border rounded-2xl p-5 bg-gradient-to-br from-white to-sky-50">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-sm text-gray-600">Kode Kupon</div>
                <div class="text-xl font-bold tracking-wide text-blue-900">{{ $cp->code }}</div>
              </div>
              <span class="px-2 py-1 text-xs rounded-lg bg-blue-600 text-white">
                {{ $cp->discount_type === 'percent' ? ($cp->discount_value.'%') : ('Rp '.number_format($cp->discount_value,0,',','.')) }}
              </span>
            </div>
            <div class="mt-3 text-xs text-gray-600">
              @php
                $vf = $cp->valid_from ? \Carbon\Carbon::parse($cp->valid_from)->isoFormat('D MMM Y') : 'Sekarang';
                $vu = $cp->valid_until ? \Carbon\Carbon::parse($cp->valid_until)->isoFormat('D MMM Y') : 'Tanpa batas';
              @endphp
              Berlaku: {{ $vf }} — {{ $vu }}
            </div>
            @auth
              <form method="POST" action="{{ route('app.coupons.validate') }}" class="mt-4">
                @csrf
                <input type="hidden" name="code" value="{{ $cp->code }}">
                <button class="w-full px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-sm">
                  Pakai Kupon
                </button>
              </form>
            @else
              <a href="{{ route('register') }}"
                 class="mt-4 w-full inline-flex justify-center px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-sm">
                Daftar untuk Memakai
              </a>
            @endauth
          </div>
        @empty
          <div class="md:col-span-3">
            <div class="p-6 border rounded-2xl bg-sky-50 text-blue-900">Belum ada kupon aktif.</div>
          </div>
        @endforelse
      </div>
    </div>
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
        <a href="{{ route('register') }}"
           class="px-5 py-3 rounded-xl bg-white text-blue-800 font-semibold hover:bg-blue-50">
          Buat Akun
        </a>
    </div>
    </div>
  </section>
  @endguest
@endsection
