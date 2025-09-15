<!doctype html>
<html lang="id" class="h-full antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>404 — Halaman Tidak Ditemukan</title>

  <!-- Tailwind Standalone -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    :root {
      color-scheme: light dark;
    }
    .bg-animated {
      background: radial-gradient(1200px 600px at 80% -10%, rgba(59,130,246,.20), transparent 60%),
                  radial-gradient(900px 500px at -10% 110%, rgba(99,102,241,.18), transparent 60%),
                  linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.95));
    }
    @media (prefers-color-scheme: dark) {
      .bg-animated {
        background: radial-gradient(1200px 600px at 80% -10%, rgba(59,130,246,.25), transparent 60%),
                    radial-gradient(900px 500px at -10% 110%, rgba(139,92,246,.25), transparent 60%),
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

  <div class="max-w-4xl w-full grid items-center gap-12 lg:grid-cols-2">
    <!-- Left content -->
    <div>
      <div class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 dark:border-white/10 bg-white/70 dark:bg-white/5 px-3 py-1 text-xs">
        <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
        <span>404 Not Found</span>
      </div>

      <h1 class="mt-4 text-6xl sm:text-7xl font-extrabold leading-none tracking-tight">
        Halaman <span class="bg-gradient-to-r from-blue-500 via-cyan-400 to-violet-500 bg-clip-text text-transparent">tidak ada</span>
      </h1>

      <p class="mt-5 text-lg text-slate-600 dark:text-slate-300">
        Sepertinya halaman yang kamu cari hilang, dipindahkan, atau tidak tersedia.
      </p>

      <!-- Buttons -->
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/60">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75L12 3l9 6.75M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
          </svg>
          Beranda
        </a>

        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 bg-white/70 dark:bg-white/5 px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Kembali
        </a>

        @auth
          <a href="{{ route('dashboard') }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-white/10 px-5 py-3 hover:bg-slate-50 dark:hover:bg-white/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
            Dashboard
          </a>
        @endauth
      </div>
    </div>

    <!-- Right illustration -->
    <div class="relative mx-auto w-full max-w-md">
      <svg viewBox="0 0 400 400" class="w-full drop-shadow-xl floaty" aria-hidden="true">
        <defs>
          <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#60A5FA"/>
            <stop offset="100%" stop-color="#A78BFA"/>
          </linearGradient>
        </defs>
        <circle cx="200" cy="200" r="160" fill="url(#g1)" opacity="0.12"/>
        <g stroke="url(#g1)" stroke-width="6" fill="none">
          <rect x="70" y="120" width="260" height="160" rx="22"/>
          <path d="M110 170l60 60M170 170l-60 60"/>
          <path d="M230 170h70M230 200h70M230 230h50"/>
        </g>
        <text x="200" y="85" text-anchor="middle" font-size="64" font-weight="800" fill="url(#g1)">404</text>
      </svg>
    </div>
  </div>

</body>
</html>
