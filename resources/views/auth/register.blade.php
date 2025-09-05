<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Register — BERKEMAH</title>

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
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
</head>

<body class="min-h-[100dvh] flex items-center justify-center bg-gradient-to-t from-bluecamp-950 via-bluecamp-800 to-bluecamp-500 text-ivory-100 font-sans overflow-x-hidden">

  <!-- CARD REGISTER -->
  <div class="w-full max-w-md sm:max-w-xl lg:max-w-3xl bg-bluecamp-900/70 backdrop-blur-md rounded-2xl border border-aqua/40 shadow-2xl p-6 sm:p-10">
    
    <!-- Header -->
    <div class="text-center mb-8">
      <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Logo BERKEMAH" class="h-20 mx-auto drop-shadow-lg">
      <h1 class="mt-4 text-3xl sm:text-4xl font-bold">Daftar Akun BERKEMAH</h1>
      <p class="mt-2 text-sm sm:text-base text-ivory-100/70 italic">
        Belajar teknologi ala camping ⛺ — murah, seru, kolaboratif
      </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-5 sm:space-y-6">
      @csrf

      <!-- Nama -->
      <div>
        <label for="name" class="block text-sm sm:text-base mb-2">Nama Lengkap</label>
        <input id="name" name="name" type="text" required
          class="w-full rounded-xl bg-bluecamp-950/60 border border-bluecamp-700 text-ivory-100
                 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua
                 px-4 py-3 sm:py-4 text-base sm:text-lg">
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm sm:text-base mb-2">Email</label>
        <input id="email" name="email" type="email" required
          class="w-full rounded-xl bg-bluecamp-950/60 border border-bluecamp-700 text-ivory-100
                 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua
                 px-4 py-3 sm:py-4 text-base sm:text-lg">
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm sm:text-base mb-2">Password</label>
        <input id="password" name="password" type="password" required
          class="w-full rounded-xl bg-bluecamp-950/60 border border-bluecamp-700 text-ivory-100
                 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua
                 px-4 py-3 sm:py-4 text-base sm:text-lg">
      </div>

      <!-- Konfirmasi Password -->
      <div>
        <label for="password_confirmation" class="block text-sm sm:text-base mb-2">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required
          class="w-full rounded-xl bg-bluecamp-950/60 border border-bluecamp-700 text-ivory-100
                 placeholder-ivory-100/50 focus:border-aqua focus:ring-aqua
                 px-4 py-3 sm:py-4 text-base sm:text-lg">
      </div>

      <!-- Tombol -->
      <button type="submit"
        class="w-full py-3.5 sm:py-4 rounded-xl bg-bluecamp-700 border border-aqua/60
               font-semibold text-lg sm:text-xl text-white transition
               hover:bg-bluecamp-900 hover:border-aqua
               hover:shadow-[0_0_16px_#3B82F6,0_0_28px_#06B6D4,0_0_44px_#3B82F6]
               focus:ring focus:ring-aqua">
        Daftar
      </button>

      <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-sm sm:text-base underline hover:text-aqua">
          Sudah punya akun? Login
        </a>
      </div>
    </form>

    <!-- Footer -->
    <div class="mt-8 text-center text-xs sm:text-sm text-ivory-100/60">
      © {{ date('Y') }} <span class="font-semibold">BERKEMAH</span>
    </div>
  </div>
</body>
</html>
