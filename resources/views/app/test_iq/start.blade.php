<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $test->title }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Subtle decorative background */
    .bg-ornament::before,
    .bg-ornament::after {
      content: "";
      position: absolute;
      inset: auto;
      width: 40rem; height: 40rem;
      filter: blur(80px);
      pointer-events: none;
      z-index: 0;
      opacity: .45;
    }
    .bg-ornament::before {
      top: -10rem; left: -10rem;
      background: radial-gradient(closest-side, #a5b4fc 0%, transparent 70%);
    }
    .bg-ornament::after {
      bottom: -12rem; right: -8rem;
      background: radial-gradient(closest-side, #c7d2fe 10%, transparent 70%);
    }
      /* Responsive ornaments */
    @media (max-width: 640px) {
      .bg-ornament::before, .bg-ornament::after { width: 24rem; height: 24rem; filter: blur(60px); }
      .bg-ornament::before { top: -6rem; left: -8rem; }
      .bg-ornament::after { bottom: -8rem; right: -6rem; }
    }
    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce) {
      * { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; scroll-behavior: auto !important; }
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-indigo-50 via-white to-slate-50 text-slate-800">
  <div class="relative isolate bg-ornament">
    <!-- Page container -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-[max(2.5rem,env(safe-area-inset-top))] pb-[max(2.5rem,env(safe-area-inset-bottom))]">
      <!-- Header / Breadcrumb -->
      <div class="mb-6 flex items-center gap-3">
        <a href="{{ url()->previous() }}" class="group inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 transition -mx-0.5 group-hover:-translate-x-0.5">
            <path fill-rule="evenodd" d="M15.78 5.22a.75.75 0 0 1 0 1.06L9.06 13l6.72 6.72a.75.75 0 1 1-1.06 1.06l-7.25-7.25a.75.75 0 0 1 0-1.06l7.25-7.25a.75.75 0 0 1 1.06 0z" clip-rule="evenodd" />
          </svg>
          <span>Kembali</span>
        </a>
      </div>

      @php
        $total = count($test->questions ?? []);
        $dur   = $test->duration_minutes ?? null;
      @endphp

      <!-- Hero Card -->
      <div class="relative z-[1] overflow-hidden rounded-3xl border border-slate-200 bg-white/90 backdrop-blur shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
        <div class="p-6 md:p-10">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div class="max-w-2xl">
              <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-[12px] font-medium text-indigo-700 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                  <path d="M11.7 2.07a.75.75 0 0 1 .6 0l8.25 3.75a.75.75 0 0 1 .45.68v5.25a8.25 8.25 0 1 1-16.5 0V6.5a.75.75 0 0 1 .45-.68l6.75-3.07Z"/>
                </svg>
                Tes IQ
              </div>
              <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900">{{ $test->title }}</h1>
              @if(!empty($test->description))
                <p class="mt-2 text-slate-600 leading-relaxed">{{ $test->description }}</p>
              @endif

              <!-- Badges / meta -->
              <div class="mt-4 flex flex-wrap items-center gap-2 text-[13px]">
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                  </svg>
                  Durasi: <strong class="font-semibold ml-1">{{ $dur ? $dur.' menit' : '—' }}</strong>
                </span>
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM11.25 7.5A.75.75 0 0 1 12 6.75h.008a.75.75 0 0 1 .742.75v6a.75.75 0 0 1-1.5 0v-6Zm.75 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                  </svg>
                  Jumlah Soal: <strong class="font-semibold ml-1">{{ $total }}</strong>
                </span>
              </div>
            </div>

            <!-- Stat tiles -->
            <div class="grid grid-cols-2 gap-3 shrink-0 w-full md:w-[300px]">
              <div class="rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 p-4 text-center">
                <div class="text-[12px] text-slate-500">Jumlah Soal</div>
                <div class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $total }}</div>
              </div>
              <div class="rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 p-4 text-center">
                <div class="text-[12px] text-slate-500">Durasi</div>
                <div class="text-2xl font-semibold text-slate-900 mt-0.5">{{ $dur ? $dur.' mnt' : '—' }}</div>
              </div>
              <div class="col-span-2 rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <div class="flex items-center gap-2 text-indigo-700 text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm10.28-4.03a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h4.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3Z" clip-rule="evenodd" />
                  </svg>
                  Siap mulai? Pastikan lingkungan tenang.
                </div>
              </div>
            </div>
          </div>

          <!-- Rules & CTA -->
          <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="md:col-span-2">
              <h2 class="text-sm font-semibold tracking-wide text-slate-700 uppercase">Aturan Singkat</h2>
              <ul class="mt-3 space-y-2">
                <li class="flex items-start gap-3">
                  <span class="mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M9 12.75 5.75 9.5a.75.75 0 1 0-1.06 1.06l4 4a.75.75 0 0 0 1.06 0l9-9a.75.75 0 1 0-1.06-1.06L9 12.75Z" clip-rule="evenodd"/></svg>
                  </span>
                  <p class="text-slate-600">Sekali dimulai, <strong>mode kunci</strong> aktif: tidak bisa kembali hingga selesai.</p>
                </li>
                <li class="flex items-start gap-3">
                  <span class="mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path d="M12 3.75A8.25 8.25 0 1 0 20.25 12 8.26 8.26 0 0 0 12 3.75Zm.75 3a.75.75 0 0 0-1.5 0v4.5c0 .414.336.75.75.75h3a.75.75 0 0 0 0-1.5H12.75V6.75Z"/></svg>
                  </span>
                  <p class="text-slate-600">Manfaatkan waktu dengan bijak. <em>Durasi</em> tetap berjalan.</p>
                </li>
                <li class="flex items-start gap-3">
                  <span class="mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5"><path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75S6.615 21.75 12 21.75 21.75 17.385 21.75 12 17.385 2.25 12 2.25Zm-4.03 9.47a.75.75 0 0 1 1.06-1.06L12 13.69l2.97-3.03a.75.75 0 1 1 1.06 1.06l-3.5 3.57a.75.75 0 0 1-1.06 0l-3.5-3.57Z"/></svg>
                  </span>
                  <p class="text-slate-600">Pastikan koneksi stabil & baterai perangkat cukup.</p>
                </li>
              </ul>
            </div>

            <div class="md:col-span-1">
              <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">Siap memulai</div>
                <div class="mt-1 text-xl font-semibold text-slate-900">Tes IQ</div>
                <p class="mt-2 text-sm text-slate-600">Klik tombol di bawah untuk masuk ke sesi ujian.</p>

                <form method="GET" action="{{ route('user.test-iq.start', $test) }}" class="mt-4">
                  @csrf
                  <button type="submit"
                          @class([
                            'group relative w-full inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3.5 text-base font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600/70',
                            'bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-sm' => $total > 0,
                            'bg-slate-200 text-slate-500 cursor-not-allowed' => $total <= 0,
                          ])
                          {{ $total <= 0 ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 transition group-active:scale-95">
                      <path d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm7.53-3.53a.75.75 0 0 0 0 1.06L12.19 12l-2.41 2.47a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z"/>
                    </svg>
                    Mulai Tes
                  </button>
                </form>

                @if($total <= 0)
                  <p class="mt-3 text-xs text-amber-600">Belum ada soal pada tes ini. Hubungi admin.</p>
                @endif

                <p class="mt-3 text-[12px] leading-relaxed text-slate-500">
                  Dengan menekan <em>Mulai Tes</em>, Anda setuju pada aturan ujian di atas.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Subtle gradient footer line -->
        <div class="h-px w-full bg-gradient-to-r from-transparent via-indigo-200/70 to-transparent"></div>

        <!-- Footer note -->
        <div class="px-6 md:px-10 py-4">
          <p class="text-[12px] text-slate-500">Tips: tutup aplikasi lain untuk menghindari distraksi & notifikasi.</p>
        </div>
      </div>
    <!-- Mobile sticky CTA -->
    <div class="md:hidden fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70">
      <div class="max-w-5xl mx-auto px-4 py-3">
        <form method="GET" action="{{ route('user.test-iq.start', $test) }}">
          @csrf
          <button type="submit"
                  @class([
                    'w-full inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3.5 text-base font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600/70',
                    'bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 shadow-sm' => $total > 0,
                    'bg-slate-200 text-slate-500 cursor-not-allowed' => $total <= 0,
                  ])
                  {{ $total <= 0 ? 'disabled' : '' }}>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm7.53-3.53a.75.75 0 0 0 0 1.06L12.19 12l-2.41 2.47a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z"/>
            </svg>
            Mulai Tes
          </button>
        </form>
      </div>
    </div>
$1
</html>
