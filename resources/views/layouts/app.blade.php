<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name','BERKEMAH') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
  @include('layouts.navigation')

  {{-- Header untuk halaman yang pakai @section('header') --}}
  @hasSection('header')
    <header class="border-b bg-white">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('header')
      </div>
    </header>
  @endif

  {{-- Header untuk halaman yang pakai <x-slot name="header"> --}}
  @if (isset($header))
    <div class="max-w-7xl mx-auto px-4 pt-6">
      {{ $header }}
    </div>
  @endif

  <main class="min-h-screen max-w-7xl mx-auto px-4 py-8">
    @if (session('status'))
      <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">
        {{ session('status') }}
      </div>
    @endif

    {{-- Dukung dua gaya: component (slot) atau section --}}
    @isset($slot)
      {{ $slot }}
    @else
      @yield('content')
    @endisset
  </main>

  @stack('scripts')
</body>
</html>
