<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Admin')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-7xl mx-auto p-6">
    <header class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">@yield('title')</h1>
      <nav>
        <a href="{{ route('dashboard') }}" class="text-sm underline">Dashboard</a>
        <a href="{{ route('profile.edit') }}" class="ml-4 text-sm underline">Profile</a>
      </nav>
    </header>
    @if(session('ok'))
      <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
        {{ session('ok') }}
      </div>
    @endif
    @yield('content')
  </div>
</body>
</html>
