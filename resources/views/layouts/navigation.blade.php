<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Left: Logo & Brand -->
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-tosca rounded-lg flex items-center justify-center text-white font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-xl font-bold text-text-main tracking-tight hidden sm:block">
                        LMS<span class="text-tosca">Enterprise</span>
                    </span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'bg-tosca-light text-tosca-dark' : 'text-text-soft hover:bg-gray-50 hover:text-text-main' }}">
                        Home
                    </a>
                    <a href="{{ route('app.my.courses') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('app.my.courses') ? 'bg-tosca-light text-tosca-dark' : 'text-text-soft hover:bg-gray-50 hover:text-text-main' }}">
                        My Learning
                    </a>
                    <a href="{{ route('app.psychology') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('app.psychology') || request()->routeIs('app.quizzes.*') ? 'bg-tosca-light text-tosca-dark' : 'text-text-soft hover:bg-gray-50 hover:text-text-main' }}">
                        Assessment
                    </a>
                    <a href="{{ route('app.courses.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('app.courses.index') ? 'bg-tosca-light text-tosca-dark' : 'text-text-soft hover:bg-gray-50 hover:text-text-main' }}">
                        Explore
                    </a>
                </div>
            </div>

            <!-- Right: Actions & Profile -->
            <div class="flex items-center gap-3">
                
                @auth
                    @php
                        $u = auth()->user();
                        $isAdmin = \Illuminate\Support\Facades\Gate::allows('admin');
                    @endphp

                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:flex items-center gap-1 px-3 py-1.5 rounded-full bg-softbg border border-gray-100 text-sm font-medium text-text-main hover:border-tosca/30 hover:text-tosca transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Admin Panel
                        </a>
                    @endif

                    <!-- Notification Bell -->
                    <div class="relative hidden sm:block" x-data="{ notifOpen: false }">
                        @php
                            $unreadCount = $u->unreadNotifications->count();
                            $latestNotifs = $u->notifications()->take(5)->get();
                        @endphp
                        <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false" class="relative p-2 rounded-full border border-gray-100 hover:bg-gray-50 transition-colors focus:outline-none text-text-soft hover:text-text-main">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </button>
                        
                        <div x-show="notifOpen" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50 overflow-hidden">
                            <div class="px-4 py-2 border-b border-gray-50 flex items-center justify-between">
                                <span class="font-bold text-text-main text-sm">Notifications</span>
                                @if($unreadCount > 0)
                                    <form action="{{ route('app.notifications.readAll') }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-tosca hover:text-tosca-dark font-medium">Mark all as read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse($latestNotifs as $notif)
                                    <div class="px-4 py-3 hover:bg-softbg transition-colors {{ is_null($notif->read_at) ? 'bg-tosca-light/30' : '' }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="text-sm text-text-main {{ is_null($notif->read_at) ? 'font-semibold' : '' }}">
                                                {{ $notif->data['message'] ?? 'Kamu punya notifikasi baru.' }}
                                            </div>
                                            @if(is_null($notif->read_at))
                                                <span class="w-2 h-2 rounded-full bg-tosca shrink-0 mt-1.5"></span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-text-soft mt-1 flex items-center justify-between">
                                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                                            @if(is_null($notif->read_at))
                                                <form action="{{ route('app.notifications.read', $notif->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-[10px] text-tosca hover:underline">Tandai dibaca</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-text-soft">
                                        Belum ada notifikasi.
                                    </div>
                                @endforelse
                            </div>
                            <a href="{{ route('app.notifications.index') }}" class="block text-center px-4 py-2 text-xs font-bold text-tosca bg-gray-50 hover:bg-gray-100 border-t border-gray-50">
                                View all notifications
                            </a>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative hidden sm:block" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full border border-gray-100 hover:bg-gray-50 transition-colors focus:outline-none">
                            <img src="{{ $u?->avatar_url ?: 'https://ui-avatars.com/api/?background=DFF5F1&color=075E54&name='.urlencode($u?->name ?? 'Guest') }}" alt="avatar" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-medium text-text-main hidden md:block">{{ $u?->name }}</span>
                            <svg class="w-4 h-4 text-text-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="profileOpen" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-50 mb-1 lg:hidden">
                                <p class="text-sm font-semibold text-text-main truncate">{{ $u?->name }}</p>
                                <p class="text-xs text-text-soft truncate">{{ $u?->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-text-main hover:bg-softbg hover:text-tosca">Account Settings</a>
                            <a href="{{ route('app.my.courses') }}" class="block px-4 py-2 text-sm text-text-main hover:bg-softbg hover:text-tosca">My Certificates</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}"> @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>

                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-text-soft hover:text-text-main transition-colors">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2 rounded-xl bg-tosca text-white text-sm font-medium hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20">Sign Up</a>
                    @endif
                @endauth

            </div>
        </div>
    </div>
</nav>

<!-- Mobile Bottom Navigation (Fixed at bottom on small screens) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-[0_-4px_24px_rgba(0,0,0,0.02)] z-50 pb-safe">
    <div class="flex justify-around items-center h-16">
        
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('home') ? 'text-tosca' : 'text-text-soft hover:text-tosca' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('home') ? '2.5' : '1.5' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>

        <a href="{{ route('app.my.courses') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('app.my.courses') ? 'text-tosca' : 'text-text-soft hover:text-tosca' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('app.my.courses') ? '2.5' : '1.5' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <span class="text-[10px] font-medium">Learning</span>
        </a>

        <a href="{{ route('app.psychology') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('app.psychology') ? 'text-tosca' : 'text-text-soft hover:text-tosca' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('app.psychology') ? '2.5' : '1.5' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            <span class="text-[10px] font-medium">Assess</span>
        </a>

        <a href="{{ route('app.courses.index') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('app.courses.index') ? 'text-tosca' : 'text-text-soft hover:text-tosca' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('app.courses.index') ? '2.5' : '1.5' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span class="text-[10px] font-medium">Explore</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('profile.edit') ? 'text-tosca' : 'text-text-soft hover:text-tosca' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('profile.edit') ? '2.5' : '1.5' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-medium">Account</span>
        </a>

    </div>
</div>

<style>
    /* Support safe area for modern mobile browsers with bottom bar */
    .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
</style>
