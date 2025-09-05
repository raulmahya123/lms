{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','App') — BERKEMAH</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Poppins','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','Helvetica Neue','Arial']
          },
          colors: {
            ivory: { 50:'#F8FBFF',100:'#F7FAFC',200:'#EFF6FF',300:'#DBEAFE',400:'#BFDBFE' },
            bluecamp: { 950:'#081225',900:'#0B1D3A',800:'#12325F',700:'#1E3A8A',600:'#2F60C4',500:'#3B82F6',400:'#93C5FD',300:'#BFDBFE',200:'#DBEAFE',100:'#EFF6FF',50:'#F8FBFF' },
            ink: { 900:'#0B1320',700:'#1D2430',600:'#2A3342' }
          },
          boxShadow: { glow: '0 0 0 3px rgba(59,130,246,0.25)' },
          dropShadow: { brand: '0 10px 24px rgba(59,130,246,.25)' },
          borderRadius: { '2xl':'1rem','3xl':'1.25rem' }
        }
      }
    }
  </script>

  {{-- Alpine.js --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Icons --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  {{-- Poppins font --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body{font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    [x-cloak]{display:none!important}
    .size-9{width:2.25rem;height:2.25rem}
  </style>

  {{-- tempat halaman lain menyuntik CSS tambahan --}}
  @stack('styles')
</head>
<body
  class="min-h-screen bg-ivory-50 text-ink-900 dark:bg-ink-900 dark:text-ivory-100"
  x-data="{
    mobileOpen:false, userMenu:false, isDark:false,
    toggleDark(){ this.isDark = !this.isDark; document.documentElement.classList.toggle('dark', this.isDark); localStorage.setItem('berkemah_dark', this.isDark ? '1' : '0'); },
    init(){ this.isDark = localStorage.getItem('berkemah_dark') === '1'; document.documentElement.classList.toggle('dark', this.isDark); }
  }"
  x-init="init()"
  @keydown.escape="mobileOpen=false; userMenu=false">

  {{-- ================= HEADER ================= --}}
  <header class="sticky top-0 z-40 border-b border-bluecamp-200/50 bg-white/80 backdrop-blur dark:bg-ink-900/80 dark:border-ink-700">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center justify-between gap-3">

        {{-- Brand (logo) --}}
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group">
          <img
            src="{{ asset('assets/images/foto-berkemah.png') }}"
            alt="Logo BERKEMAH"
            class="h-8 w-auto rounded-md ring-1 ring-bluecamp-200/40 dark:ring-ink-700 object-contain bg-white/80 dark:bg-ink-900/80" />
          <span class="text-lg font-semibold tracking-tight group-hover:text-bluecamp-700 dark:group-hover:text-bluecamp-300">
            BERKEMAH
          </span>
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden md:flex items-center gap-2 text-sm">
          @php
            $isActive = fn($names) => request()->routeIs($names)
              ? 'text-bluecamp-700 bg-bluecamp-100 dark:text-bluecamp-200 dark:bg-bluecamp-800/30'
              : 'text-ink-700 hover:text-bluecamp-700 hover:bg-bluecamp-50 dark:text-ivory-100/80 dark:hover:text-bluecamp-200 dark:hover:bg-ink-700';
            $u = Auth::user();
            $isAdmin = $u ? (method_exists($u,'isAdmin') ? $u->isAdmin() : (isset($u->is_admin) && $u->is_admin)) : false;
          @endphp

          <a href="{{ route('home') }}" class="px-3 py-2 rounded-full {{ $isActive('home') }}"><i class="fa-solid fa-house mr-2"></i> Home</a>
          <a href="{{ route('app.courses.index') }}" class="px-3 py-2 rounded-full {{ $isActive('app.courses.index') }}"><i class="fa-solid fa-graduation-cap mr-2"></i> Courses</a>
          <a href="{{ route('app.my.courses') }}" class="px-3 py-2 rounded-full {{ $isActive('app.my.courses') }}"><i class="fa-solid fa-book-open mr-2"></i> My Courses</a>
          <a href="{{ route('app.memberships.index') }}" class="px-3 py-2 rounded-full {{ $isActive('app.memberships.index') }}"><i class="fa-solid fa-id-card mr-2"></i> Memberships</a>
          <a href="{{ route('app.payments.index') }}" class="px-3 py-2 rounded-full {{ $isActive('app.payments.index') }}"><i class="fa-solid fa-wallet mr-2"></i> Payments</a>

          @auth
            <a href="{{ route('app.dashboard') }}" class="px-3 py-2 rounded-full {{ $isActive('app.dashboard') }}"><i class="fa-solid fa-user mr-2"></i> Dashboard User</a>
            @if($isAdmin)
              <a href="{{ route('admin.dashboard') }}"
                 class="px-3 py-2 rounded-full ring-1 ring-bluecamp-300 text-bluecamp-700 hover:bg-bluecamp-50 hover:text-bluecamp-800 {{ request()->routeIs('admin.*') ? 'bg-bluecamp-100 dark:bg-bluecamp-800/30 dark:text-bluecamp-200' : '' }} dark:ring-ink-600 dark:text-bluecamp-200 dark:hover:bg-ink-700">
                <i class="fa-solid fa-shield-halved mr-2"></i> Admin
              </a>
            @endif

            {{-- User dropdown --}}
            <div class="relative ml-1" @click.outside="userMenu=false">
              <button @click="userMenu=!userMenu"
                      class="flex items-center gap-2 px-2 py-1 rounded-full hover:bg-bluecamp-50 focus:outline-none focus:shadow-glow dark:hover:bg-ink-700">
                <span class="inline-flex size-9 items-center justify-center rounded-full bg-bluecamp-500/10 text-bluecamp-700 dark:text-bluecamp-200">
                  {{ strtoupper(substr(Auth::user()->name ?? 'U',0,1)) }}
                </span>
                <span class="hidden lg:inline text-ink-700 dark:text-ivory-100/90">{{ Str::limit(Auth::user()->name ?? 'User', 18) }}</span>
                <i class="fa-solid fa-chevron-down text-ink-700 text-xs dark:text-ivory-100/70"></i>
              </button>

              <div x-cloak x-show="userMenu" x-transition.origin.top.right
                   class="absolute right-0 mt-2 w-64 bg-white border border-bluecamp-200 rounded-xl shadow-xl overflow-hidden dark:bg-ink-900 dark:border-ink-700">
                <div class="px-4 py-3">
                  <p class="text-xs text-ink-600/70 dark:text-ivory-100/60">Masuk sebagai</p>
                  <p class="text-sm font-medium text-ink-900 dark:text-ivory-100 truncate">{{ Auth::user()->email }}</p>
                </div>
                <div class="border-t border-ivory-200 dark:border-ink-700">
                  <a href="{{ route('app.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800"><i class="fa-solid fa-house mr-2"></i>Dashboard User</a>
                  @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800"><i class="fa-solid fa-shield-halved mr-2"></i>Dashboard Admin</a>
                  @endif
                  <a href="{{ route('app.my.courses') }}" class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800"><i class="fa-solid fa-book-open mr-2"></i>Kursus Saya</a>
                </div>
                <div class="border-t border-ivory-200 dark:border-ink-700">
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-ink-800">
                      <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @endauth

          @guest
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-full {{ $isActive('login') }}"><i class="fa-solid fa-right-to-bracket mr-2"></i> Login</a>
            <a href="{{ route('register') }}" class="px-3 py-2 rounded-full text-white bg-bluecamp-600 hover:opacity-90 drop-shadow-brand"><i class="fa-solid fa-user-plus mr-2"></i> Register</a>
          @endguest
        </nav>

        {{-- Mobile buttons --}}
        <div class="md:hidden flex items-center gap-2">
          <button @click="toggleDark()"
                  class="inline-flex items-center justify-center size-9 rounded-full bg-bluecamp-500/10 text-bluecamp-700 hover:bg-bluecamp-500/20 dark:text-bluecamp-200 dark:hover:bg-ink-700"
                  :aria-label="isDark ? 'Switch to light' : 'Switch to dark'">
            <i x-show="!isDark" class="fa-solid fa-moon"></i>
            <i x-show="isDark" class="fa-solid fa-sun"></i>
          </button>

          @auth
            <a href="{{ route('app.dashboard') }}" class="inline-flex items-center justify-center size-9 rounded-full bg-bluecamp-500/10 text-bluecamp-700 hover:bg-bluecamp-500/20 dark:text-bluecamp-200 dark:hover:bg-ink-700">
              <i class="fa-solid fa-user"></i>
            </a>
          @endauth

          <button @click="mobileOpen=!mobileOpen"
                  class="inline-flex items-center justify-center size-9 rounded-full bg-bluecamp-500/10 text-bluecamp-700 hover:bg-bluecamp-500/20 dark:text-bluecamp-200 dark:hover:bg-ink-700"
                  aria-label="Toggle menu">
            <i x-show="!mobileOpen" class="fa-solid fa-bars"></i>
            <i x-show="mobileOpen" class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      {{-- Mobile Drawer --}}
      <div x-cloak x-show="mobileOpen" x-transition class="md:hidden mt-3 border-t border-ivory-200 pt-3 dark:border-ink-700">
        <nav class="grid gap-2 text-sm">
          <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('home') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-house mr-2"></i>Home</a>
          <a href="{{ route('app.courses.index') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.courses.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-graduation-cap mr-2"></i>Courses</a>
          <a href="{{ route('app.my.courses') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.my.courses') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-book-open mr-2"></i>My Courses</a>
          <a href="{{ route('app.memberships.index') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.memberships.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-id-card mr-2"></i>Memberships</a>
          <a href="{{ route('app.payments.index') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.payments.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-wallet mr-2"></i>Payments</a>

          @auth
            <a href="{{ route('app.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.dashboard') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-user mr-2"></i>Dashboard User</a>
            @if($isAdmin)
              <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('admin.dashboard') ? 'bg-ivory-100 text-bluecamp-700' : 'text-bluecamp-700 dark:text-bluecamp-200 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-shield-halved mr-2"></i>Dashboard Admin</a>
            @endif

            <div class="border-t border-ivory-200 my-2 dark:border-ink-700"></div>
            <div class="px-3 py-1 text-xs text-ink-600/70 dark:text-ivory-100/60">Akun</div>
            <div class="px-3 py-2 text-sm text-ink-900 dark:text-ivory-100 truncate">{{ Auth::user()->email }}</div>

            <form method="POST" action="{{ route('logout') }}" class="mt-1">
              @csrf
              <button class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-ink-800"><i class="fa-solid fa-right-from-bracket mr-2"></i>Logout</button>
            </form>
          @endauth

          @guest
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('login') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}"><i class="fa-solid fa-right-to-bracket mr-2"></i>Login</a>
            <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg bg-bluecamp-600 text-white hover:opacity-90"><i class="fa-solid fa-user-plus mr-2"></i>Register</a>
          @endguest
        </nav>
      </div>
    </div>
  </header>

  {{-- Decorative stripe --}}
  <div class="h-1 bg-gradient-to-r from-bluecamp-300 via-bluecamp-500 to-bluecamp-300 dark:from-ink-700 dark:via-ink-600 dark:to-ink-700"></div>

  {{-- ================= CONTENT ================= --}}
  <main class="max-w-7xl mx-auto px-4 py-8">
    @if (session('status'))
      <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-600/30">{{ session('status') }}</div>
    @endif

    @yield('content')
  </main>

  {{-- ================= FOOTER ================= --}}
  <footer class="border-t border-ivory-200 bg-white dark:bg-ink-900 dark:border-ink-700">
    <div class="max-w-7xl mx-auto px-4 py-8 grid gap-6 md:grid-cols-3">
      <div>
        <div class="inline-flex items-center gap-2">
          <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Logo BERKEMAH" class="h-7 w-auto object-contain" />
          <span class="font-semibold">BERKEMAH</span>
        </div>
        <p class="mt-2 text-sm text-ink-600/80 dark:text-ivory-100/70">Belajar teknologi & coding vibes alam. Biru muda + putih tulang biar adem 👌</p>
      </div>
      <div>
        <h4 class="font-semibold mb-2">Tautan</h4>
        <ul class="text-sm text-ink-700 space-y-1 dark:text-ivory-100/90">
          <li><a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Privacy</a></li>
          <li><a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Terms</a></li>
          <li><a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-2">Newsletter</h4>
        <form class="flex gap-2">
          <input type="email" class="flex-1 border border-ivory-200 rounded-xl px-3 py-2 focus:outline-none focus:shadow-glow dark:bg-ink-900 dark:border-ink-700 dark:text-ivory-100" placeholder="Email kamu">
          <button class="px-4 py-2 rounded-xl bg-bluecamp-600 text-white hover:opacity-90">Langganan</button>
        </form>
      </div>
    </div>
    <div class="border-t border-ivory-200 dark:border-ink-700">
      <div class="max-w-7xl mx-auto px-4 py-4 text-sm text-ink-600/80 flex flex-wrap items-center justify-between gap-2 dark:text-ivory-100/70">
        <p>© {{ date('Y') }} BERKEMAH</p>
        <div class="flex items-center gap-4">
          <a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300"><i class="fa-brands fa-facebook"></i></a>
          <a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </footer>

  {{-- tempat halaman lain menyuntik JS tambahan --}}
  @stack('scripts')
</body>
</html>
