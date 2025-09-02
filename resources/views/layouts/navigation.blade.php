<nav x-data="{ open: false }"
     class="bg-white dark:bg-[#0f1a33] border-b border-gray-200 dark:border-white/10 shadow">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ubah h-20 jadi h-28 biar lebih tinggi -->
        <div class="flex justify-between h-28">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <!-- Logo lebih besar -->
                        <img src="{{ asset('assets/images/foto-berkemah.png') }}"
                             class="h-28 w-auto"> {{-- logo lebih tinggi --}}
                        <span class="text-3xl font-extrabold tracking-wide text-blue-600 dark:text-blue-400">
                          BERKEMAH
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ms-10 sm:flex sm:items-center sm:space-x-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="text-lg text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-5 py-4 border border-gray-300 dark:border-gray-700
                                   text-base font-medium rounded-xl
                                   text-gray-700 dark:text-gray-200 bg-white dark:bg-[#1a2337]
                                   hover:bg-blue-50 dark:hover:bg-white/10 hover:text-blue-600 dark:hover:text-blue-400
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-5 w-5"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')"
                            class="hover:bg-blue-50 dark:hover:bg-white/10">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-3 rounded-md
                               text-gray-500 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400
                               hover:bg-blue-50 dark:hover:bg-white/10
                               focus:outline-none focus:bg-blue-100 dark:focus:bg-white/20
                               focus:text-blue-700 dark:focus:text-blue-300 transition duration-150 ease-in-out">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
