<!doctype html>
<html lang="id" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>422 — Data Tidak Valid</title>

  <!-- Tailwind Standalone -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root { color-scheme: light dark; }
    .bg-animated {
      background: radial-gradient(1000px 600px at 90% -10%, rgba(234,179,8,.20), transparent 60%),
                  radial-gradient(800px 500px at -10% 110%, rgba(251,191,36,.18), transparent 60%),
                  linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.95));
    }
    @media (prefers-color-scheme: dark) {
      .bg-animated {
        background: radial-gradient(1000px 600px at 90% -10%, rgba(234,179,8,.25), transparent 60%),
                    radial-gradient(800px 500px at -10% 110%, rgba(251,191,36,.25), transparent 60%),
                    linear-gradient(180deg, #0b1220, #0b1220);
      }
    }
    .floaty {
      animation: floaty 6s ease-in-out infinite;
    }
    @keyframes floaty {
      0%, 100% { transform: translateY(0px) }
      50% { transform: translateY(-10px) }
    }
  </style>
</head>

<body class="min-h-screen bg-animated text-slate-800 dark:text-slate-100 flex items-center justify-center px-6">

  <div class="max-w-3xl w-full text-center space-y-6">
    <!-- Badge -->
    <div class="inline-flex items-center gap-2 rounded-full border border-yellow-200/70 dark:border-yellow-500/30 bg-yellow-50/80 dark:bg-yellow-500/10 px-3 py-1 text-xs text-yellow-700 dark:text-yellow-400">
      <span class="h-2 w-2 rounded-full bg-yellow-500 animate-pulse"></span>
      <span>422 Unprocessable Entity</span>
    </div>

    <!-- Big Code -->
    <h1 class="text-7xl sm:text-8xl font-extrabold text-yellow-500">422</h1>

    <!-- Title & Message -->
    <p class="text-2xl font-semibold">Data Tidak Valid</p>
    <p class="text-gray-600 dark:text-gray-300 max-w-xl mx-auto">
      Permintaan tidak bisa diproses karena ada data yang tidak valid.  
      Periksa kembali input Anda dan coba lagi.
    </p>

    <!-- Actions -->
    <div class="mt-6 flex flex-wrap justify-center gap-3">
      <a href="javascript:void(0)"
         onclick="if(history.length>1){history.back()}else{window.location='{{ route('home') }}'}"
         class="inline-flex items-center gap-2 rounded-xl bg-yellow-500 px-5 py-3 text-white shadow hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400/60">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>

      <a href="{{ url('/') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-yellow-300 dark:border-yellow-500/30 bg-white/80 dark:bg-white/5 px-5 py-3 hover:bg-yellow-50 dark:hover:bg-yellow-500/10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
        </svg>
        Beranda
      </a>
    </div>

    <!-- Illustration -->
    <div class="mt-10 max-w-sm mx-auto floaty">
      <svg viewBox="0 0 400 400" class="w-full drop-shadow-lg" aria-hidden="true">
        <defs>
          <linearGradient id="g422" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#FACC15"/>
            <stop offset="100%" stop-color="#FBBF24"/>
          </linearGradient>
        </defs>
        <circle cx="200" cy="200" r="160" fill="url(#g422)" opacity="0.12"/>
        <g stroke="url(#g422)" stroke-width="6" fill="none">
          <path d="M200 120v80"/>
          <circle cx="200" cy="260" r="8" fill="url(#g422)"/>
        </g>
        <text x="200" y="85" text-anchor="middle" font-size="64" font-weight="800" fill="url(#g422)">422</text>
      </svg>
    </div>
  </div>

</body>
</html>
