<!doctype html>
<html lang="en" x-data="adminShell()">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'LMS Enterprise Dashboard')</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CDN & Config --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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
                            soft: '#475569'
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js for interactions --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="min-h-screen flex bg-softbg text-text-main antialiased">

    <!-- SIDEBAR -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-[4px_0_24px_rgba(0,0,0,0.02)] transform lg:transform-none lg:static z-40 transition-transform duration-300 flex flex-col"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <!-- BRAND -->
        <div class="h-16 flex items-center px-6 border-b border-gray-100 justify-between">
            <div class="flex items-center gap-3 font-bold text-xl text-text-main tracking-tight">
                <div class="w-8 h-8 bg-tosca rounded-lg flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                LMS<span class="text-tosca">Enterprise</span>
            </div>
            <button class="lg:hidden text-gray-500 hover:text-tosca" @click="sidebarOpen = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            
            <!-- 1. Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-tosca-light text-tosca-dark' : 'text-text-soft hover:bg-gray-50 hover:text-text-main' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>

            <!-- 2. Katalog & Edukasi -->
            <div x-data="{ open: {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.resources.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium text-text-soft hover:bg-gray-50 hover:text-text-main">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Katalog & Edukasi
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                    <a href="{{ route('admin.courses.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.courses.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Courses</a>
                </div>
            </div>

            <!-- 3. Assessment -->
            <div x-data="{ open: {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.test-iq.*') || request()->routeIs('admin.psy-tests.*') || request()->routeIs('admin.questions.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium text-text-soft hover:bg-gray-50 hover:text-text-main">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Assessment
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                    <a href="{{ route('admin.quizzes.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.quizzes.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Quizzes</a>
                    <a href="{{ route('admin.test-iq.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.test-iq.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">IQ Tests</a>
                    <a href="{{ route('admin.psy-tests.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.psy-tests.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Psychology Tests</a>
                    <a href="{{ route('admin.questions.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.questions.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Question Bank</a>
                </div>
            </div>

            <!-- 4. Sales & Membership -->
            <div x-data="{ open: {{ request()->routeIs('admin.plans.*') || request()->routeIs('admin.memberships.*') || request()->routeIs('admin.enrollments.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.coupons.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium text-text-soft hover:bg-gray-50 hover:text-text-main">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Sales & Membership
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                    <a href="{{ route('admin.plans.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.plans.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Plans</a>
                    <a href="{{ route('admin.memberships.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.memberships.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Memberships</a>
                    <a href="{{ route('admin.enrollments.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.enrollments.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Enrollments</a>
                    <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.payments.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Payments</a>
                    <a href="{{ route('admin.coupons.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.coupons.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Coupons</a>
                </div>
            </div>

            <!-- 5. Community & Achievement -->
            <div x-data="{ open: {{ request()->routeIs('admin.qa-threads.*') || request()->routeIs('admin.certificate-*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium text-text-soft hover:bg-gray-50 hover:text-text-main">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        Community & Awards
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                    <a href="{{ route('admin.qa-threads.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.qa-threads.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Q&A Moderation</a>
                    <a href="{{ route('admin.certificate-templates.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.certificate-templates.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Certificates</a>
                </div>
            </div>

            <!-- 6. Users & Settings -->
            <div x-data="{ open: {{ request()->routeIs('admin.psy-profiles.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg transition-colors font-medium text-text-soft hover:bg-gray-50 hover:text-text-main">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users & Settings
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse class="pl-11 pr-3 py-1 space-y-1">
                    <a href="{{ route('admin.psy-profiles.index') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.psy-profiles.*') ? 'text-tosca font-semibold' : 'text-text-soft hover:text-text-main hover:bg-gray-50' }}">Psych Profiles</a>
                </div>
            </div>

        </nav>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 flex items-center justify-between px-6 bg-white shadow-[0_4px_24px_rgba(0,0,0,0.02)] sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button class="lg:hidden text-gray-500 hover:text-tosca" @click="sidebarOpen = true">
                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <h1 class="text-xl font-semibold text-text-main">@yield('title', 'Dashboard')</h1>
            </div>

            {{-- RIGHT: Search, Notifications, Profile --}}
            <div class="flex items-center gap-4" x-data="{ openProfile: false }">
                
                <a href="{{ route('home') }}" class="text-sm font-medium text-text-soft hover:text-tosca flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-full transition-colors hidden sm:flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Go to LMS
                </a>

                <div class="relative">
                    <button @click="openProfile = !openProfile" @click.outside="openProfile = false" class="flex items-center gap-3 pl-2 pr-4 py-1.5 rounded-full border border-gray-100 hover:border-tosca/30 hover:bg-softbg transition-colors">
                        @php($u = \Illuminate\Support\Facades\Auth::user())
                        <div class="w-8 h-8 bg-tosca-light text-tosca-deep rounded-full flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(mb_substr($u?->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-text-main">{{ $u?->name ?? 'Admin User' }}</span>
                        <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openProfile" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-text-main hover:bg-softbg hover:text-tosca">Profile Settings</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}"> @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-y-auto min-w-0">
            @if (session('ok'))
                <div class="mb-6 bg-tosca-light text-tosca-deep px-4 py-3 rounded-xl border border-tosca/20 flex items-center gap-3">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium text-sm">{{ session('ok') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-50 text-red-800 px-4 py-3 rounded-xl border border-red-100 flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function adminShell() {
            return {
                sidebarOpen: false,
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
