<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ theme: localStorage.getItem('theme') || 'light' }"
      :class="theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak]{ display:none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-[#0f1a33] text-[#102a43] dark:text-gray-200">
    <div class="min-h-screen flex flex-col">
        {{-- Navbar --}}
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-[#1a2337] shadow border-b border-gray-200 dark:border-white/10">
                <!-- <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                    {{ $header }}

                    <button @click="theme = theme === 'light' ? 'dark' : 'light'; localStorage.setItem('theme', theme)"
                            class="p-2 rounded-xl border text-sm flex items-center gap-1
                                   border-gray-300 dark:border-gray-700
                                   hover:bg-gray-100 dark:hover:bg-white/10 transition">
                        <svg x-show="theme==='light'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                        <svg x-show="theme==='dark'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                        </svg>
                    </button>
                </div> -->
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
