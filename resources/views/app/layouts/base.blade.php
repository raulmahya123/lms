{{-- resources/views/app/layouts/base.blade.php — Tosca Enterprise --}}
<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'App') — BERKEMAH</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','Helvetica Neue','Arial']
          },
          colors: {
            tosca: {
              light: '#DFF5F1',
              DEFAULT: '#0F9D8A',
              dark: '#087A6C',
              deep: '#075E54',
            },
            softbg: '#F4FBF9',
            text: {
              main: '#1F2937',
              soft: '#6B7280'
            }
          },
          boxShadow: {
            glow:'0 0 0 3px rgba(15,157,138,0.15)',
            card:'0 8px 30px rgba(0,0,0,.04)',
            'card-hover':'0 20px 60px rgba(15,157,138,.1)',
          },
          borderRadius:{ '2xl':'1rem','3xl':'1.25rem','4xl':'2rem'},
        }
      }
    }
  </script>

  {{-- Alpine.js --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Inter Font --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    body{font-family:'Inter',ui-sans-serif,system-ui,-apple-system,sans-serif}
    [x-cloak]{display:none!important}
    /* Smooth nav link underline */
    .nav-link{position:relative;transition:color .2s}
    .nav-link::after{content:'';position:absolute;left:50%;bottom:-4px;height:2px;width:0;background:linear-gradient(90deg,#0F9D8A,#087A6C);transition:all .25s ease;transform:translateX(-50%);border-radius:2px}
    .nav-link:hover::after,.nav-link.active::after{width:70%}
    .nav-link:hover,.nav-link.active{color:#0F9D8A}
    .dark body{background:#071511;color:#e8f7f4}
    .dark header{background:rgba(7,21,17,.88);border-color:rgba(255,255,255,.08)}
    .dark footer{background:linear-gradient(180deg,#071511,#0b211c);border-color:rgba(255,255,255,.08)}
    .dark .landing-shell{background:#071511;color:#e8f7f4}
    .dark .soft-card{background:rgba(12,32,27,.86);border-color:rgba(148,220,209,.16);box-shadow:0 18px 60px rgba(0,0,0,.28)}
    .dark .nav-link{color:#b8d6d1}
  </style>

  @stack('styles')
</head>
<body class="min-h-screen bg-white text-text-main antialiased"
      x-data="{ mobileOpen: false, userMenu: false, darkMode: localStorage.getItem('berkemah-dark') === '1' }"
      x-init="$watch('darkMode', value => { localStorage.setItem('berkemah-dark', value ? '1' : '0'); document.documentElement.classList.toggle('dark', value); }); document.documentElement.classList.toggle('dark', darkMode)"
      @keydown.escape="mobileOpen=false; userMenu=false">

@php
  use Illuminate\Support\Str;
  $u = auth()->user();
  $isAdmin = $u ? (method_exists($u, 'isAdmin') ? $u->isAdmin() : isset($u->is_admin) && $u->is_admin) : false;
@endphp

{{-- ================= NAVBAR ================= --}}
<header class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100/80">
  {{-- Accent line --}}
  <div class="h-[2px] bg-gradient-to-r from-tosca-deep via-tosca to-tosca-dark"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">

      {{-- Brand --}}
      <a href="{{ route('home') }}" class="flex items-center gap-2.5 group shrink-0">
        <div class="w-9 h-9 bg-gradient-to-br from-tosca to-tosca-dark rounded-xl flex items-center justify-center text-white shadow-sm shadow-tosca/20 group-hover:shadow-md group-hover:shadow-tosca/30 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
          <span class="text-lg font-extrabold text-text-main tracking-tight">BERKE<span class="text-tosca">MAH</span></span>
          <span class="ml-1.5 px-1.5 py-0.5 text-[9px] font-bold rounded bg-tosca-light text-tosca-dark uppercase tracking-widest">Beta</span>
        </div>
      </a>

      {{-- Desktop Nav --}}
      <nav class="hidden lg:flex items-center gap-1">
        @php
          $navLinks = [
            ['route' => 'home', 'label' => 'Home', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'app.courses.index', 'label' => 'Courses', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['route' => 'app.my.courses', 'label' => 'My Learning', 'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5'],
            ['route' => 'app.memberships.index', 'label' => 'Membership', 'icon' => 'M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z'],
            ['route' => 'app.payments.index', 'label' => 'Payments', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
          ];
        @endphp
        @foreach($navLinks as $link)
          <a href="{{ route($link['route']) }}"
             class="nav-link px-3 py-2 text-sm font-medium {{ request()->routeIs($link['route']) ? 'active text-tosca' : 'text-text-soft' }}">
            {{ $link['label'] }}
          </a>
        @endforeach
      </nav>

      {{-- Right Actions --}}
      <div class="flex items-center gap-2">
        <button type="button"
                @click="darkMode=!darkMode"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-text-soft transition-all hover:border-tosca/30 hover:bg-softbg hover:text-tosca dark:border-white/10 dark:bg-white/10 dark:text-white"
                aria-label="Toggle dark mode">
          <svg x-show="!darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
          <svg x-cloak x-show="darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        </button>
        @auth
          @if ($isAdmin)
            <a href="{{ route('admin.dashboard') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-tosca-dark bg-tosca-light hover:bg-tosca hover:text-white transition-all">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Admin
            </a>
          @endif

          {{-- User Dropdown --}}
          <div class="relative hidden sm:block" @click.outside="userMenu=false">
            <button @click="userMenu=!userMenu" class="flex items-center gap-2 pl-1.5 pr-3 py-1 rounded-full border border-gray-200 hover:border-tosca/30 hover:bg-softbg transition-all focus:outline-none focus:ring-2 focus:ring-tosca/20">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-tosca to-tosca-dark flex items-center justify-center text-white text-sm font-bold shadow-sm">
                {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
              </div>
              <span class="text-sm font-semibold text-text-main hidden md:block max-w-[120px] truncate">{{ $u->name ?? 'User' }}</span>
              <svg class="w-4 h-4 text-text-soft transition-transform" :class="userMenu && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-cloak x-show="userMenu"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-black/8 border border-gray-100 overflow-hidden z-50">
              <div class="px-4 py-3 bg-softbg border-b border-gray-100">
                <p class="text-xs text-text-soft">Masuk sebagai</p>
                <p class="text-sm font-bold text-text-main truncate">{{ $u->email ?? '' }}</p>
              </div>
              <div class="py-1">
                <a href="{{ route('app.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-text-main hover:bg-softbg hover:text-tosca transition-colors">
                  <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                  Dashboard
                </a>
                <a href="{{ route('app.certificates.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-text-main hover:bg-softbg hover:text-tosca transition-colors">
                  <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                  Certificates
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-text-main hover:bg-softbg hover:text-tosca transition-colors">
                  <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  Edit Profile
                </a>
                @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-text-main hover:bg-softbg hover:text-tosca transition-colors">
                  <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  Admin Panel
                </a>
                @endif
              </div>
              <div class="border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                  </button>
                </form>
              </div>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-text-soft hover:text-tosca transition-colors">Login</a>
          <a href="{{ route('register') }}" class="hidden sm:inline-flex px-5 py-2.5 rounded-xl bg-gradient-to-r from-tosca to-tosca-dark text-white text-sm font-bold hover:shadow-lg hover:shadow-tosca/25 transition-all">Daftar</a>
        @endauth

        {{-- Mobile Hamburger --}}
        <button @click="mobileOpen=!mobileOpen" class="lg:hidden p-2 rounded-xl hover:bg-softbg transition-colors" aria-label="Toggle menu">
          <svg x-show="!mobileOpen" class="w-6 h-6 text-text-main" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <svg x-show="mobileOpen" x-cloak class="w-6 h-6 text-text-main" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>

    {{-- Mobile Drawer --}}
    <div x-cloak x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden border-t border-gray-100 py-4">
      <nav class="space-y-1">
        @foreach($navLinks as $link)
          <a href="{{ route($link['route']) }}"
             class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs($link['route']) ? 'bg-tosca-light text-tosca-dark' : 'text-text-main hover:bg-softbg' }}">
            <svg class="w-5 h-5 {{ request()->routeIs($link['route']) ? 'text-tosca' : 'text-text-soft' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
            {{ $link['label'] }}
          </a>
        @endforeach

        @auth
          <div class="border-t border-gray-100 mt-3 pt-3">
            <div class="px-4 py-2">
              <p class="text-xs text-text-soft">Akun</p>
              <p class="text-sm font-bold text-text-main truncate">{{ $u->email ?? '' }}</p>
            </div>
            <a href="{{ route('app.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-text-main hover:bg-softbg">
              <svg class="w-5 h-5 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
              Dashboard
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-text-main hover:bg-softbg">
              <svg class="w-5 h-5 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Edit Profile
            </a>
            @if($isAdmin)
              <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-tosca-dark hover:bg-tosca-light">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Admin Panel
              </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
              @csrf
              <button class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm text-red-600 hover:bg-red-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
              </button>
            </form>
          </div>
        @else
          <div class="border-t border-gray-100 mt-3 pt-3 flex gap-2 px-4">
            <a href="{{ route('login') }}" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-center text-sm font-semibold hover:bg-softbg transition-colors">Login</a>
            <a href="{{ route('register') }}" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-tosca to-tosca-dark text-white text-center text-sm font-bold">Daftar</a>
          </div>
        @endauth
      </nav>
    </div>
  </div>
</header>

{{-- ================= CONTENT ================= --}}
<main class="relative">
  @if (session('status'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
      <div class="p-4 rounded-2xl bg-tosca-light text-tosca-deep border border-tosca/15 flex items-center gap-3">
        <svg class="w-5 h-5 text-tosca shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-medium">{{ session('status') }}</span>
      </div>
    </div>
  @endif

  @isset($slot)
    {{ $slot }}
  @else
    @yield('content')
  @endisset
</main>

{{-- ================= FOOTER ================= --}}
<footer class="bg-gradient-to-b from-white to-softbg border-t border-gray-100 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid gap-12 md:grid-cols-12">

      {{-- Brand Column --}}
      <div class="md:col-span-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
          <div class="w-10 h-10 bg-gradient-to-br from-tosca to-tosca-dark rounded-xl flex items-center justify-center text-white shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <span class="text-xl font-extrabold text-text-main">BERKE<span class="text-tosca">MAH</span></span>
        </a>
        <p class="mt-4 text-sm text-text-soft leading-relaxed max-w-sm">
          Platform belajar teknologi & coding modern. Materi ringkas, interaktif, dan langsung praktik — bikin skillmu naik level. 🚀
        </p>
        <div class="mt-6 flex gap-2">
          <a href="#" class="w-9 h-9 rounded-xl bg-softbg border border-gray-100 flex items-center justify-center text-text-soft hover:bg-tosca hover:text-white hover:border-tosca transition-all">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-xl bg-softbg border border-gray-100 flex items-center justify-center text-text-soft hover:bg-tosca hover:text-white hover:border-tosca transition-all">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
          <a href="#" class="w-9 h-9 rounded-xl bg-softbg border border-gray-100 flex items-center justify-center text-text-soft hover:bg-tosca hover:text-white hover:border-tosca transition-all">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
          </a>
        </div>
      </div>

      {{-- Links Columns --}}
      <div class="md:col-span-5 grid grid-cols-2 gap-8">
        <div>
          <h4 class="text-sm font-bold text-text-main uppercase tracking-wider mb-4">Platform</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="{{ route('app.courses.index') }}" class="text-text-soft hover:text-tosca transition-colors">Courses</a></li>
            <li><a href="{{ route('app.memberships.index') }}" class="text-text-soft hover:text-tosca transition-colors">Memberships</a></li>
            <li><a href="{{ route('app.certificates.index') }}" class="text-text-soft hover:text-tosca transition-colors">Certificates</a></li>
            <li><a href="{{ route('app.qa-threads.index') }}" class="text-text-soft hover:text-tosca transition-colors">Community</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-bold text-text-main uppercase tracking-wider mb-4">Lainnya</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="#" class="text-text-soft hover:text-tosca transition-colors">Privacy Policy</a></li>
            <li><a href="#" class="text-text-soft hover:text-tosca transition-colors">Terms of Service</a></li>
            <li><a href="#" class="text-text-soft hover:text-tosca transition-colors">Contact Us</a></li>
            <li><a href="#" class="text-text-soft hover:text-tosca transition-colors">FAQ</a></li>
          </ul>
        </div>
      </div>

      {{-- Newsletter Column --}}
      <div class="md:col-span-3">
        <h4 class="text-sm font-bold text-text-main uppercase tracking-wider mb-4">Newsletter</h4>
        <p class="text-sm text-text-soft mb-4">Dapatkan update kelas baru & tips coding.</p>
        <form class="space-y-2">
          <input type="email" placeholder="Email kamu"
                 class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-tosca focus:ring-2 focus:ring-tosca/10 transition-all bg-white placeholder-gray-400">
          <button class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-tosca to-tosca-dark text-white text-sm font-bold hover:shadow-lg hover:shadow-tosca/20 transition-all">
            Langganan
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Copyright --}}
  <div class="border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-wrap items-center justify-between gap-3 text-sm text-text-soft">
      <p>© {{ date('Y') }} BERKEMAH. All rights reserved.</p>
      <div class="flex items-center gap-4">
        <a href="#" class="hover:text-tosca transition-colors">Changelog</a>
        <a href="#" class="hover:text-tosca transition-colors">Status</a>
      </div>
    </div>
  </div>

  {{-- Bottom accent --}}
  <div class="h-1 bg-gradient-to-r from-tosca-deep via-tosca to-tosca-dark"></div>
</footer>

@stack('scripts')
</body>
</html>
