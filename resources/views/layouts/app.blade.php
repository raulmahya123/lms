<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name','LMS Enterprise') }}</title>

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

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-softbg text-text-main antialiased min-h-screen flex flex-col">

    {{-- Desktop Top Navbar & Mobile Bottom Nav are handled inside navigation --}}
    @include('layouts.navigation')

    {{-- Header untuk halaman yang pakai @section('header') --}}
    @hasSection('header')
        <header class="bg-white border-b border-gray-100 shadow-sm relative z-20">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>
    @endif

    {{-- Header component slot --}}
    @if (isset($header))
        <header class="bg-white border-b border-gray-100 shadow-sm relative z-20">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 mb-16 lg:mb-0">
        
        @if (session('status') || session('ok'))
            <div class="mb-6 p-4 rounded-xl bg-tosca-light text-tosca-deep border border-tosca/20 flex items-center gap-3">
                <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">{{ session('status') ?? session('ok') }}</span>
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-6 bg-red-50 text-red-800 px-4 py-3 rounded-xl border border-red-100 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Dukung component (slot) atau section --}}
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    @stack('scripts')
</body>
</html>
