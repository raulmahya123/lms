{{-- resources/views/layouts/app.blade.php (Gen Z Remix) --}}
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
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto',
                            'Helvetica Neue', 'Arial'
                        ]
                    },
                    colors: {
                        ivory: {
                            50: '#F8FBFF',
                            100: '#F7FAFC',
                            200: '#EFF6FF',
                            300: '#DBEAFE',
                            400: '#BFDBFE'
                        },
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
                        ink: {
                            900: '#0B1320',
                            800: '#101827',
                            700: '#1D2430',
                            600: '#2A3342'
                        },
                        neon: {
                            pink: '#10b3f4ff',
                            purple: '#1524f4ff',
                            blue: '#60A5FA',
                            green: '#02293dff',
                            yellow: '#0e385dff'
                        }
                    },
                    boxShadow: {
                        glow: '0 0 0 3px rgba(59,130,246,0.25)',
                        card: '0 10px 30px rgba(16,24,39,.08)'
                    },
                    dropShadow: {
                        brand: '0 10px 24px rgba(59,130,246,.25)'
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.25rem',
                        '4xl': '2rem'
                    },
                    backgroundImage: {
                        grid: 'radial-gradient(circle at 1px 1px, rgba(0,0,0,.05) 1px, transparent 0)',
                        'noise-light': 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.9\' numOctaves=\'2\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23n)\' opacity=\'0.02\'/%3E%3C/svg%3E")'
                    },
                    keyframes: {
                        floaty: {
                            '0%,100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-6px)'
                            }
                        },
                        shimmer: {
                            '0%': {
                                backgroundPosition: '0% 50%'
                            },
                            '100%': {
                                backgroundPosition: '200% 50%'
                            }
                        },
                    },
                    animation: {
                        floaty: 'floaty 3s ease-in-out infinite',
                        shimmer: 'shimmer 2.5s linear infinite'
                    }
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
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial
        }

        [x-cloak] {
            display: none !important
        }

        .size-9 {
            width: 2.25rem;
            height: 2.25rem
        }

        .nav-icon {
            display: inline-block;
            width: 1rem;
            text-align: center;
            font-size: 14px;
            line-height: 1;
        }

        /* Fancy underline on hover */
        .link-underline {
            position: relative
        }

        .link-underline:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            height: 2px;
            width: 0;
            background: linear-gradient(90deg, #3B82F6, #8B5CF6, #FF53C0);
            transition: width .25s ease;
        }

        .link-underline:hover:after {
            width: 100%
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen bg-ivory-50 text-ink-900 dark:bg-ink-900 dark:text-ivory-100 bg-noise-light"
    x-data="{
        mobileOpen: false,
        userMenu: false,
        isDark: false,
        toggleDark() { this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.setItem('berkemah_dark', this.isDark ? '1' : '0'); },
        init() { this.isDark = localStorage.getItem('berkemah_dark') === '1';
            document.documentElement.classList.toggle('dark', this.isDark); }
    }" x-init="init()" @keydown.escape="mobileOpen=false; userMenu=false">

    @php use Illuminate\Support\Str; @endphp

    {{-- ================= HEADER ================= --}}
    <header
        class="sticky top-0 z-40 border-b border-bluecamp-200/50 bg-white/70 backdrop-blur-xl dark:bg-ink-900/70 dark:border-ink-700">
        <div class="relative">
            {{-- neon gradient bar (thin) --}}
            <div
                class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-neon-pink via-neon-purple to-neon-blue opacity-70">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between gap-3">

                {{-- Brand (logo) --}}
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group">
                    <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Logo BERKEMAH"
                        class="h-9 w-auto rounded-xl ring-1 ring-bluecamp-200/40 dark:ring-ink-700 object-contain bg-white/80 dark:bg-ink-900/80 animate-floaty" />
                    <span
                        class="text-lg font-semibold tracking-tight bg-gradient-to-r from-bluecamp-600 via-neon-purple to-neon-pink bg-clip-text text-transparent group-hover:opacity-90">
                        BERKEMAH
                    </span>
                    <span
                        class="ml-1 px-2 py-0.5 text-[10px] leading-none rounded-full bg-bluecamp-600/10 text-bluecamp-700 border border-bluecamp-300/40 dark:text-bluecamp-200 dark:border-ink-700">beta</span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex items-center gap-2 text-sm">
                    @php
                        $isActive = fn($names) => request()->routeIs($names)
                            ? 'text-bluecamp-700 bg-bluecamp-100 dark:text-bluecamp-200 dark:bg-bluecamp-800/30 shadow-sm'
                            : 'text-ink-700 hover:text-bluecamp-700 hover:bg-bluecamp-50 dark:text-ivory-100/80 dark:hover:text-bluecamp-200 dark:hover:bg-ink-700';
                        $u = Auth::user();
                        $isAdmin = $u
                            ? (method_exists($u, 'isAdmin')
                                ? $u->isAdmin()
                                : isset($u->is_admin) && $u->is_admin)
                            : false;
                    @endphp

                    <a href="{{ route('home') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('home') }} link-underline">
                        <i class="fa-solid fa-house nav-icon"></i><span>Home</span>
                    </a>
                    <a href="{{ route('app.psychology') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('psychology') }} link-underline">
                        <i class="fa-solid fa-brain nav-icon"></i><span>Psikologi</span>
                    </a>
                    <a href="{{ route('app.courses.index') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('app.courses.index') }} link-underline">
                        <i class="fa-solid fa-graduation-cap nav-icon"></i><span>Courses</span>
                    </a>
                    <a href="{{ route('app.my.courses') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('app.my.courses') }} link-underline">
                        <i class="fa-solid fa-book-open nav-icon"></i><span>My Courses</span>
                    </a>
                    <a href="{{ route('app.memberships.index') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('app.memberships.index') }} link-underline">
                        <i class="fa-solid fa-id-card nav-icon"></i><span>Memberships</span>
                    </a>
                    <a href="{{ route('app.payments.index') }}"
                        class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('app.payments.index') }} link-underline">
                        <i class="fa-solid fa-wallet nav-icon"></i><span>Payments</span>
                    </a>

                    @auth
                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 ring-1 ring-bluecamp-300 text-bluecamp-700 hover:bg-bluecamp-50 hover:text-bluecamp-800 {{ request()->routeIs('admin.*') ? 'bg-bluecamp-100 dark:bg-bluecamp-800/30 dark:text-bluecamp-200' : '' }} dark:ring-ink-600 dark:text-bluecamp-200 dark:hover:bg-ink-700">
                                <i class="fa-solid fa-shield-halved nav-icon"></i><span>Admin</span>
                            </a>
                        @endif

                        {{-- User dropdown --}}
                        <div class="relative ml-1" @click.outside="userMenu=false">
                            <button @click="userMenu=!userMenu"
                                class="flex items-center gap-2 px-2 py-1 rounded-full hover:bg-bluecamp-50 focus:outline-none focus:shadow-glow dark:hover:bg-ink-700">
                                <span
                                    class="inline-flex size-9 items-center justify-center rounded-full bg-gradient-to-br from-bluecamp-500/15 via-neon-purple/15 to-neon-pink/15 text-bluecamp-700 dark:text-bluecamp-200">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </span>
                                <span
                                    class="hidden lg:inline text-ink-700 dark:text-ivory-100/90">{{ Str::limit(Auth::user()->name ?? 'User', 18) }}</span>
                                <i class="fa-solid fa-chevron-down text-ink-700 text-xs dark:text-ivory-100/70"></i>
                            </button>

                            <div x-cloak x-show="userMenu" x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-64 bg-white border border-bluecamp-200 rounded-2xl shadow-2xl overflow-hidden dark:bg-ink-900 dark:border-ink-700">
                                <div class="px-4 py-3">
                                    <p class="text-xs text-ink-600/70 dark:text-ivory-100/60">Masuk sebagai</p>
                                    <p class="text-sm font-medium text-ink-900 dark:text-ivory-100 truncate">
                                        {{ Auth::user()->email }}</p>
                                </div>
                                <div class="border-t border-ivory-200 dark:border-ink-700">
                                    <a href="{{ route('app.dashboard') }}"
                                        class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800">
                                        <i class="fa-solid fa-house mr-2"></i>Dashboard User
                                    </a>
                                    <a href="{{ route('app.certificates.index') }}"
                                        class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800">
                                        <i class="fa-solid fa-certificate mr-2"></i>Certificates
                                    </a>
                                    @if ($isAdmin)
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800">
                                            <i class="fa-solid fa-shield-halved mr-2"></i>Dashboard Admin
                                        </a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800">
                                        <i class="fa-solid fa-user-pen mr-2"></i>Edit Profile
                                    </a>
                                    <a href="{{ route('app.my.courses') }}"
                                        class="block px-4 py-2 text-sm hover:bg-ivory-100 dark:hover:bg-ink-800">
                                        <i class="fa-solid fa-book-open mr-2"></i>Kursus Saya
                                    </a>
                                </div>
                                <div class="border-t border-ivory-200 dark:border-ink-700">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-ink-800">
                                            <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                            class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 {{ $isActive('login') }} link-underline">
                            <i class="fa-solid fa-right-to-bracket nav-icon"></i><span>Login</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-white bg-gradient-to-r from-bluecamp-600 via-neon-purple to-neon-pink hover:opacity-95 drop-shadow-brand">
                            <i class="fa-solid fa-user-plus nav-icon"></i><span>Register</span>
                        </a>
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
                        <a href="{{ route('app.dashboard') }}"
                            class="inline-flex items-center justify-center size-9 rounded-full bg-bluecamp-500/10 text-bluecamp-700 hover:bg-bluecamp-500/20 dark:text-bluecamp-200 dark:hover:bg-ink-700">
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
            <div x-cloak x-show="mobileOpen" x-transition
                class="md:hidden mt-3 border-t border-ivory-200 pt-3 dark:border-ink-700">
                <nav class="grid gap-2 text-sm">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('home') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                        <i class="fa-solid fa-house nav-icon"></i>Home
                    </a>
                    <a href="{{ route('app.courses.index') }}"
                        class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.courses.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                        <i class="fa-solid fa-graduation-cap nav-icon"></i>Courses
                    </a>
                    <a href="{{ route('app.my.courses') }}"
                        class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.my.courses') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                        <i class="fa-solid fa-book-open nav-icon"></i>My Courses
                    </a>
                    <a href="{{ route('app.memberships.index') }}"
                        class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.memberships.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                        <i class="fa-solid fa-id-card nav-icon"></i>Memberships
                    </a>
                    <a href="{{ route('app.payments.index') }}"
                        class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.payments.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                        <i class="fa-solid fa-wallet nav-icon"></i>Payments
                    </a>

                    @auth
                        <a href="{{ route('app.certificates.index') }}"
                            class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.certificates.index') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                            <i class="fa-solid fa-certificate nav-icon"></i>Certificates
                        </a>
                    @endauth

                    @auth
                        <a href="{{ route('app.dashboard') }}"
                            class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('app.dashboard') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                            <i class="fa-solid fa-user nav-icon"></i>Dashboard User
                        </a>
                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('admin.dashboard') ? 'bg-ivory-100 text-bluecamp-700' : 'text-bluecamp-700 dark:text-bluecamp-200 dark:hover:bg-ink-800' }}">
                                <i class="fa-solid fa-shield-halved nav-icon"></i>Dashboard Admin
                            </a>
                        @endif

                        <div class="border-t border-ivory-200 my-2 dark:border-ink-700"></div>
                        <div class="px-3 py-1 text-xs text-ink-600/70 dark:text-ivory-100/60">Akun</div>
                        <div class="px-3 py-2 text-sm text-ink-900 dark:text-ivory-100 truncate">{{ Auth::user()->email }}
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button
                                class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-ink-800">
                                <i class="fa-solid fa-right-from-bracket nav-icon"></i>Logout
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                            class="px-3 py-2 rounded-lg hover:bg-ivory-100 {{ request()->routeIs('login') ? 'bg-ivory-100 text-bluecamp-700' : 'text-ink-700 dark:text-ivory-100/90 dark:hover:bg-ink-800' }}">
                            <i class="fa-solid fa-right-to-bracket nav-icon"></i>Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-3 py-2 rounded-lg bg-gradient-to-r from-bluecamp-600 via-neon-purple to-neon-pink text-white hover:opacity-95">
                            <i class="fa-solid fa-user-plus nav-icon"></i>Register
                        </a>
                    @endguest
                </nav>
            </div>
        </div>
    </header>

    {{-- Decorative stripe --}}
    <div
        class="h-1 bg-gradient-to-r from-bluecamp-300 via-neon-purple to-neon-pink dark:from-ink-700 dark:via-ink-600 dark:to-ink-700">
    </div>

    {{-- Subtle grid bg layer --}}
    <div class="pointer-events-none select-none fixed inset-0 opacity-[.035] dark:opacity-[.06]" aria-hidden="true"
        style="background-size:20px 20px; background-image:radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0);">
    </div>

    {{-- ================= CONTENT ================= --}}
    <main class="relative max-w-7xl mx-auto px-4 py-8">
        @if (session('status'))
            <div
                class="mb-4 p-3 rounded-2xl bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-600/30">
                {{ session('status') }}
            </div>
        @endif

        @isset($slot)
            {{ $slot }} {{-- untuk halaman yang pakai <x-app-layout> --}}
        @else
            @yield('content') {{-- untuk halaman yang pakai @extends/@section --}}
        @endisset
    </main>

    {{-- ================= FOOTER (Gen Z, bold & pretty) ================= --}}
    <footer class="relative overflow-hidden border-t border-ivory-200/70 dark:border-ink-700">
        {{-- wavy top separator --}}
        <div class="absolute -top-10 left-1/2 -translate-x-1/2 w-[140%] h-24 opacity-40 blur-2xl pointer-events-none"
            style="background: radial-gradient(closest-side, rgba(59,130,246,.35), transparent 70%), radial-gradient(closest-side, rgba(139,92,246,.35), transparent 70%), radial-gradient(closest-side, rgba(255,83,192,.28), transparent 70%); filter: saturate(120%);">
        </div>

        <div
            class="bg-gradient-to-b from-white via-bluecamp-50/60 to-bluecamp-100/60 dark:from-ink-900 dark:via-ink-900 dark:to-ink-800">
            <div class="max-w-7xl mx-auto px-4 py-12 grid gap-10 md:grid-cols-12">
                <div class="md:col-span-5">
                    <div class="inline-flex items-center gap-2">
                        <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Logo BERKEMAH"
                            class="h-8 w-auto object-contain rounded-md ring-1 ring-bluecamp-200/40 dark:ring-ink-700" />
                        <span class="font-semibold text-lg">BERKEMAH</span>
                    </div>
                    <p class="mt-3 text-sm text-ink-600/80 dark:text-ivory-100/70">Belajar teknologi & coding vibes
                        alam. Biru muda + putih tulang biar adem 👌</p>

                    {{-- Socials pill row --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="#"
                            class="px-3 py-1.5 rounded-full border border-ivory-200 bg-white hover:border-bluecamp-300 hover:drop-shadow-sm text-sm dark:bg-ink-900 dark:border-ink-700 dark:hover:bg-ink-800"><i
                                class="fa-brands fa-x-twitter mr-1"></i> Twitter</a>
                        <a href="#"
                            class="px-3 py-1.5 rounded-full border border-ivory-200 bg-white hover:border-bluecamp-300 hover:drop-shadow-sm text-sm dark:bg-ink-900 dark:border-ink-700 dark:hover:bg-ink-800"><i
                                class="fa-brands fa-facebook mr-1"></i> Facebook</a>
                        <a href="#"
                            class="px-3 py-1.5 rounded-full border border-ivory-200 bg-white hover:border-bluecamp-300 hover:drop-shadow-sm text-sm dark:bg-ink-900 dark:border-ink-700 dark:hover:bg-ink-800"><i
                                class="fa-brands fa-instagram mr-1"></i> Instagram</a>
                        <a href="#"
                            class="px-3 py-1.5 rounded-full border border-ivory-200 bg-white hover:border-bluecamp-300 hover:drop-shadow-sm text-sm dark:bg-ink-900 dark:border-ink-700 dark:hover:bg-ink-800"><i
                                class="fa-brands fa-youtube mr-1"></i> YouTube</a>
                    </div>
                </div>

                <div class="md:col-span-4 grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold mb-2">Produk</h4>
                        <ul class="text-sm text-ink-700 space-y-1 dark:text-ivory-100/90">
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Courses</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Memberships</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Certificates</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Community</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Legal</h4>
                        <ul class="text-sm text-ink-700 space-y-1 dark:text-ivory-100/90">
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Privacy</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Terms</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Cookies</a></li>
                            <li><a href="#"
                                    class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <h4 class="font-semibold mb-2">Newsletter</h4>
                    <form class="flex gap-2">
                        <input type="email"
                            class="flex-1 border border-ivory-200 rounded-2xl px-4 py-2 focus:outline-none focus:shadow-glow bg-white/90 dark:bg-ink-900 dark:border-ink-700 dark:text-ivory-100"
                            placeholder="Email kamu">
                        <button
                            class="px-4 py-2 rounded-2xl bg-gradient-to-r from-bluecamp-600 via-neon-purple to-neon-pink text-white hover:opacity-95">Langganan</button>
                    </form>
                    <p class="mt-2 text-[12px] text-ink-600/70 dark:text-ivory-100/60">Dapatkan update kelas baru &
                        tips coding yang fun.</p>
                </div>
            </div>

            <div class="border-t border-ivory-200/70 dark:border-ink-700">
                <div
                    class="max-w-7xl mx-auto px-4 py-5 text-sm text-ink-600/80 flex flex-wrap items-center justify-between gap-3 dark:text-ivory-100/70">
                    <p>© {{ date('Y') }} BERKEMAH — stay curious ✨</p>
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1 text-[12px] px-2 py-1 rounded-full bg-bluecamp-600/10 text-bluecamp-700 dark:text-bluecamp-200"><i
                                class="fa-solid fa-circle-half-stroke"></i> <button type="button"
                                @click="toggleDark()" class="underline underline-offset-2">Toggle
                                theme</button></span>
                        <a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Changelog</a>
                        <a href="#" class="hover:text-bluecamp-700 dark:hover:text-bluecamp-300">Status</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- bottom neon gradient line --}}
        <div class="h-0.5 bg-gradient-to-r from-neon-pink via-neon-purple to-neon-blue"></div>
    </footer>

    @stack('scripts')
</body>

</html>
