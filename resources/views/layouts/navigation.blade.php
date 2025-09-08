<nav x-data="{ open:false }"
     class="sticky top-0 z-50
            bg-white/80 dark:bg-[#0f1a33]/70
            backdrop-blur-md
            border-b border-gray-200/70 dark:border-white/10
            shadow-sm">
  <!-- Gradient hairline -->
  <div class="h-[2px] w-full bg-gradient-to-r from-blue-500/30 via-cyan-400/30 to-violet-500/30"></div>

  <!-- Primary Navigation -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- nav tinggi 28 -->
    <div class="flex items-center justify-between h-28">

      <!-- Left: Logo + Links -->
      <div class="flex items-center gap-8">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
          <img src="{{ asset('assets/images/foto-berkemah.png') }}" class="h-24 w-auto"
               alt="Berkemah Logo">
          <span class="text-2xl sm:text-3xl font-extrabold tracking-wide
                       text-blue-700 dark:text-blue-400
                       group-hover:tracking-wider transition-all duration-200">
            BERKEMAH
          </span>
        </a>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-2">
          <!-- Home -->
          <x-nav-link :href="route('home')" :active="request()->routeIs('home')"
            class="relative px-3 py-2 text-[15px] text-gray-700 dark:text-gray-200
                   hover:text-blue-700 dark:hover:text-blue-300 transition">
            <span class="flex items-center gap-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                   fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 9.75L12 3l9 6.75M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
              </svg>
              {{ __('Home') }}
              <span class="absolute left-0 -bottom-1 h-[2px] w-0 bg-current
                           group-hover:w-full transition-all duration-300"></span>
            </span>
          </x-nav-link>

          <!-- Dashboard -->
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
            class="relative px-3 py-2 text-[15px] text-gray-700 dark:text-gray-200
                   hover:text-blue-700 dark:hover:text-blue-300 transition">
            <span class="flex items-center gap-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                   fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12h18M3 6h18M3 18h18"/>
              </svg>
              {{ __('Dashboard') }}
              <span class="absolute left-0 -bottom-1 h-[2px] w-0 bg-current
                           group-hover:w-full transition-all duration-300"></span>
            </span>
          </x-nav-link>

          <!-- Courses -->
          <x-nav-link :href="route('app.courses.index')" :active="request()->routeIs('courses.*')"
            class="relative px-3 py-2 text-[15px] text-gray-700 dark:text-gray-200
                   hover:text-blue-700 dark:hover:text-blue-300 transition">
            <span class="flex items-center gap-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                   fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6V4m0 2v2m0-2h8.25M12 6H3.75M21 12H3m18 6H3"/>
              </svg>
              {{ __('Courses') }}
              <span class="absolute left-0 -bottom-1 h-[2px] w-0 bg-current
                           group-hover:w-full transition-all duration-300"></span>
            </span>
          </x-nav-link>
        </div>
      </div>

      <!-- Right: Actions -->
      <div class="hidden sm:flex items-center gap-4">

        <!-- Bell -->
        <button class="relative p-2 rounded-xl
                       text-gray-600 dark:text-gray-300
                       hover:bg-blue-50 dark:hover:bg-white/10
                       hover:text-blue-700 dark:hover:text-blue-300 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-red-500"></span>
        </button>

        <!-- Profile Dropdown -->
        <x-dropdown align="right" width="56">
          <x-slot name="trigger">
            <button
              class="inline-flex items-center gap-3 ps-3 pe-4 py-2.5 border border-gray-200 dark:border-white/10
                     rounded-2xl bg-white/70 dark:bg-[#1a2337]/70
                     text-gray-800 dark:text-gray-100
                     hover:bg-blue-50/80 dark:hover:bg-white/10
                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/60 transition">
              <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}"
                   alt="avatar" class="h-8 w-8 rounded-full object-cover">
              <span class="hidden lg:block font-medium">{{ Auth::user()->name }}</span>
              <svg class="h-5 w-5 opacity-80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill="currentColor" fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 11.188l3.71-3.96a.75.75 0 111.08 1.04l-4.24 4.53a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                      clip-rule="evenodd"/>
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-blue-50 dark:hover:bg-white/10">
              {{ __('Profile') }}
            </x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link :href="route('logout')"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                {{ __('Log Out') }}
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>

      <!-- Mobile: Hamburger -->
      <div class="flex md:hidden">
        <button @click="open = !open"
                class="inline-flex items-center justify-center p-3 rounded-xl
                       text-gray-600 dark:text-gray-300
                       hover:bg-blue-50 dark:hover:bg-white/10
                       focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
          <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Panel -->
  <div x-show="open" x-transition.origin.top
       class="md:hidden border-t border-gray-200 dark:border-white/10 bg-white/90 dark:bg-[#0f1a33]/90 backdrop-blur">
    <div class="px-4 pt-3 pb-6 space-y-3">
      <!-- Home -->
      <a href="{{ route('home') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-800 dark:text-gray-100
                hover:bg-blue-50 dark:hover:bg-white/10 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 9.75L12 3l9 6.75M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
        </svg>
        {{ __('Home') }}
      </a>

      <!-- Dashboard -->
      <a href="{{ route('dashboard') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-800 dark:text-gray-100
                hover:bg-blue-50 dark:hover:bg-white/10 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
        {{ __('Dashboard') }}
      </a>

      <!-- Courses -->
      <a href="{{ route('app.courses.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-800 dark:text-gray-100
                hover:bg-blue-50 dark:hover:bg-white/10 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6V4m0 2v2m0-2h8.25M12 6H3.75M21 12H3m18 6H3"/>
        </svg>
        {{ __('Courses') }}
      </a>

      <div class="pt-3 border-t border-gray-200 dark:border-white/10">
        <a href="{{ route('profile.edit') }}"
           class="block px-3 py-2 rounded-lg text-gray-800 dark:text-gray-100
                  hover:bg-blue-50 dark:hover:bg-white/10 transition">
          {{ __('Profile') }}
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
          @csrf
          <button type="submit"
                  class="w-full text-left px-3 py-2 rounded-lg
                         text-red-600 dark:text-red-400
                         hover:bg-red-50 dark:hover:bg-red-900/30 transition">
            {{ __('Log Out') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</nav>
