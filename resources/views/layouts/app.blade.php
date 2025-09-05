<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name','Laravel'))</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  {{-- Assets --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    body { font-family: 'Poppins', sans-serif; }
    [x-cloak]{ display:none !important; }
  </style>

  @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-[#0f1a33] text-[#102a43] dark:text-gray-200">
  <div class="min-h-screen flex flex-col">
    {{-- Navbar --}}
    @include('layouts.navigation')

    {{-- Optional page header --}}
    @hasSection('header')
      <header class="bg-white dark:bg-[#1a2337] shadow border-b border-gray-200 dark:border-white/10">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
          @yield('header')
        </div>
      </header>
    @endif

    {{-- Page content --}}
    <main class="flex-1">
      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>
