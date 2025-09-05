<!doctype html>
<html lang="id" class="scroll-smooth">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — BERKEMAH</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            ivory: { 100: '#F7FAFC' },
            bluecamp: {
              950: '#081225',
              900: '#0B1D3A',
              800: '#12325F',
              700: '#1E3A8A',
              600: '#2F60C4',
              500: '#3B82F6',
              400: '#93C5FD',
              300: '#BFDBFE',
              200: '#DBEAFE',
              100: '#EFF6FF',
              50: '#F8FBFF'
            },
            aqua: '#22D3EE'
          },
          fontFamily: {
            serif: ['"Playfair Display"', 'serif'],
            sans: ['Poppins', 'ui-sans-serif', 'system-ui']
          },
          dropShadow: {
            aqua: '0 0 14px rgba(34,211,238,.35)'
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="min-h-[100dvh] md:grid md:grid-cols-12 bg-gradient-to-t from-bluecamp-950 via-bluecamp-800 to-bluecamp-500 text-ivory-100 font-sans overflow-x-hidden">

  <!-- LEFT BRANDING (disembunyikan di HP) -->
  <div class="hidden md:block md:col-span-7 relative overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center"
      style="background-image:url('{{ asset('assets/images/foto-belajar.jpg') }}')"></div>

    <div class="absolute inset-0 bg-gradient-to-tr from-bluecamp-950/95 via-bluecamp-800/75 to-bluecamp-900/10"></div>
    <div class="absolute inset-0 pointer-events-none"
      style="background: radial-gradient(120% 120% at 30% 40%, rgba(8,18,37,0) 50%, rgba(8,18,37,.55) 100%);"></div>

    <div class="relative h-full flex flex-col justify-between">
      <div class="absolute top-4 left-1/2 -translate-x-1/2 px-4">
        <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Logo BERKEMAH"
          class="h-28 sm:h-32 drop-shadow-lg">
      </div>

      <!-- Headline -->
      <div class="p-10 lg:p-14 text-center mt-20">
        <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl leading-tight drop-shadow">
          Bootcamp IT<br>Murah &amp; Affordable
        </h1>
        <p class="mt-6 max-w-2xl mx-auto text-base text-ivory-100/85">
          <span class="font-semibold">BERKEMAH</span> menghadirkan pengalaman <strong>Bootcamp IT</strong>
          dengan suasana belajar layaknya berkemah: seru, kolaboratif, dan mendalam.
          Sekali bayar sudah termasuk semua materi, modul, mentoring, dan project nyata.<br><br>
          <span class="font-semibold text-aqua">#SekaliBayarDapatSemua</span>
        </p>
        <div class="mt-8 mx-auto h-[3px] w-56 bg-gradient-to-r from-aqua to-transparent drop-shadow-aqua rounded-full"></div>
      </div>

      <!-- CAMPUS values -->
      <div class="p-8 lg:p-14">
        <div class="mb-6">
          <p class="text-xs uppercase tracking-[0.25em] text-ivory-100/70">Core Values</p>
          <h3 class="text-2xl font-semibold text-aqua drop-shadow-aqua">CAMPUS</h3>
        </div>
        <div class="relative">
          <div class="pointer-events-none absolute -top-3 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-aqua/60 to-transparent"></div>
          <div class="grid grid-cols-3 gap-6 sm:grid-cols-6">
            <!-- C -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">C</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">Curiosity</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Semangat belajar teknologi tanpa batas.</p>
            </div>
            <!-- A -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">A</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">Accessibility</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Harga terjangkau, materi mudah diakses.</p>
            </div>
            <!-- M -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">M</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">Mastery</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Dari basic hingga advanced skill.</p>
            </div>
            <!-- P -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">P</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">Practice</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Real project portfolio-ready.</p>
            </div>
            <!-- U -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">U</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">User-Centric</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Sesuai kebutuhan industri & user.</p>
            </div>
            <!-- S -->
            <div class="group text-center">
              <div class="mx-auto w-16 h-16 rounded-full border-2 border-aqua bg-bluecamp-900/70 flex items-center justify-center transition group-hover:scale-110 group-hover:drop-shadow-aqua">
                <span class="text-aqua font-bold text-xl">S</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-aqua">Safety</h4>
              <p class="mt-1 text-[11px] leading-snug text-ivory-100/80">Belajar aman & komunitas suportif.</p>
            </div>
          </div>
          <div class="pointer-events-none mt-8 h-[1px] bg-gradient-to-r from-transparent via-aqua/60 to-transparent"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT LOGIN -->
  <div class="md:col-span-5 min-h-[100dvh] flex items-center">
    <div class="w-full mx-auto max-w-lg md:max-w-2xl lg:max-w-3xl px-4 sm:px-8 py-10 sm:py-16">
      <div class="bg-bluecamp-900/60 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-aqua/40 shadow-2xl">
        <div class="p-6 sm:p-10 md:p-12">
          <div class="mb-8 sm:mb-10 text-center">
            <p class="text-sm sm:text-base uppercase tracking-[0.22em] text-ivory-100/80">Selamat Datang</p>
            <h2 class="mt-2 text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight">Masuk ke Akun BERKEMAH</h2>
            <p class="mt-3 sm:mt-4 text-sm sm:text-lg text-ivory-100/70 italic">
              Anywhere, anytime learning — tetap ramah di dompet
            </p>
          </div>

          <form method="POST" action="{{ route('login') }}" class="space-y-6 sm:space-y-8">
            @csrf

            <div>
              <label for="email" class="block text-sm sm:text-base mb-2 sm:mb-3">Email</label>
              <input id="email" name="email" type="email" required autofocus autocomplete="username"
                value="{{ old('email') }}"
                class="w-full rounded-xl sm:rounded-2xl bg-bluecamp-950/60 border border-bluecamp-700 
                       text-ivory-100 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua 
                       px-4 sm:px-6 py-3.5 sm:py-5 text-base sm:text-xl">
            </div>

            <div>
              <label for="password" class="block text-sm sm:text-base mb-2 sm:mb-3">Password</label>
              <input id="password" name="password" type="password" required autocomplete="current-password"
                class="w-full rounded-xl sm:rounded-2xl bg-bluecamp-950/60 border border-bluecamp-700 
                       text-ivory-100 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua 
                       px-4 sm:px-6 py-3.5 sm:py-5 text-base sm:text-xl">
            </div>

            <label class="inline-flex items-center gap-2 sm:gap-3 text-sm sm:text-lg">
              <input type="checkbox" name="remember" class="rounded border-bluecamp-700 bg-transparent text-aqua focus:ring-aqua w-4 h-4 sm:w-5 sm:h-5">
              <span>Ingat saya</span>
            </label>

            <button type="submit"
              class="w-full py-3.5 sm:py-5 rounded-xl sm:rounded-2xl bg-bluecamp-700 border border-aqua/60 
                     font-semibold sm:font-bold text-lg sm:text-2xl text-white transition 
                     hover:bg-bluecamp-900 hover:border-aqua 
                     hover:shadow-[0_0_16px_#3B82F6,0_0_28px_#06B6D4,0_0_44px_#3B82F6] 
                     focus:ring focus:ring-aqua">
              Masuk
            </button>

            <div class="text-center mt-3 sm:mt-5">
              <a href="{{ route('register') }}" class="text-sm sm:text-lg underline hover:text-aqua">
                Belum punya akun? Daftar
              </a>
            </div>
          </form>

          <div class="mt-8 sm:mt-10 text-center text-xs sm:text-sm text-ivory-100/60">
            © {{ date('Y') }} <span class="font-semibold">BERKEMAH</span>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
