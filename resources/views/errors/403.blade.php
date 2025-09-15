<!doctype html>
<html lang="id" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 — Akses Ditolak</title>

  <!-- Tailwind Standalone -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root { color-scheme: light dark; }
    .bg-animated{
      background:
        radial-gradient(1000px 600px at 90% -10%, rgba(244,63,94,.20), transparent 60%),
        radial-gradient(800px 500px at -10% 110%, rgba(239,68,68,.18), transparent 60%),
        linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.95));
    }
    @media (prefers-color-scheme: dark){
      .bg-animated{
        background:
          radial-gradient(1000px 600px at 90% -10%, rgba(244,63,94,.25), transparent 60%),
          radial-gradient(800px 500px at -10% 110%, rgba(239,68,68,.25), transparent 60%),
          linear-gradient(180deg, #0b1220, #0b1220);
      }
    }
    .floaty { animation: floaty 6s ease-in-out infinite; }
    @keyframes floaty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
  </style>
</head>

<body class="min-h-screen bg-animated text-slate-800 dark:text-slate-100 flex items-center justify-center px-6">

  <div class="max-w-4xl w-full grid items-center gap-12 lg:grid-cols-2">
    <!-- Text -->
    <div>
      <div class="inline-flex items-center gap-2 rounded-full border border-rose-200/70 dark:border-rose-500/30 bg-rose-50/80 dark:bg-rose-500/10 px-3 py-1 text-xs text-rose-700 dark:text-rose-300">
        <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
        <span>403 Forbidden</span>
      </div>

      <h1 class="mt-4 text-6xl sm:text-7xl font-extrabold leading-none tracking-tight">
        Akses <span class="bg-gradient-to-r from-rose-500 via-orange-400 to-amber-400 bg-clip-text text-transparent">ditolak</span>
      </h1>

      <p class="mt-5 text-lg text-slate-600 dark:text-slate-300">
        Kamu tidak memiliki izin untuk mengakses halaman ini.
        Jika menurutmu ini keliru, hubungi administrator.
      </p>

      <!-- Actions -->
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-white shadow hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500/60">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
          </svg>
          Beranda
        </a>

        <a href="javascript:void(0)"
           onclick="if(history.length>1){history.back()}else{window.location='{{ url('/') }}'}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white/80 dark:bg-white/5 px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Kembali
        </a>

        @guest
          @if (Route::has('login'))
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 2c-2.667 0-8 1.333-8 4v2h16v-2c0-2.667-5.333-4-8-4z"/>
              </svg>
              Masuk
            </a>
          @endif
        @else
          <a href="{{ route('dashboard') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
            Dashboard
          </a>
        @endguest
      </div>
    </div>

    <!-- Illustration -->
    <div class="relative mx-auto w-full max-w-md">
      <svg viewBox="0 0 400 400" class="w-full drop-shadow-xl floaty" aria-hidden="true">
        <defs>
          <linearGradient id="g403" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#F43F5E"/>
            <stop offset="100%" stop-color="#FB923C"/>
          </linearGradient>
        </defs>
        <circle cx="200" cy="200" r="160" fill="url(#g403)" opacity="0.12"/>
        <g stroke="url(#g403)" stroke-width="6" fill="none">
          <!-- Shield -->
          <path d="M200 110l72 24v52c0 44-32 84-72 96-40-12-72-52-72-96v-52l72-24z"/>
          <!-- Cross -->
          <path d="M170 190l60 60M230 190l-60 60"/>
        </g>
        <text x="200" y="88" text-anchor="middle" font-size="56" font-weight="800" fill="url(#g403)">403</text>
      </svg>
    </div>
  </div>

</body>
</html>
