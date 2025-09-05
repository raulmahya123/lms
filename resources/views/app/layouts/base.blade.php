<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','App') — BERKEMAH</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('dashboard') }}" class="font-semibold">BERKEMAH</a>

      <nav class="flex gap-4 text-sm">
        <a href="{{ route('app.courses.index') }}">Courses</a>
        <a href="{{ route('app.my.courses') }}">My Courses</a>
        <a href="{{ route('app.memberships.index') }}">Memberships</a>
        <a href="{{ route('app.payments.index') }}">Payments</a>
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-8">
    @if (session('status'))
      <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-800">{{ session('status') }}</div>
    @endif
    @yield('content')
  </main>
</body>
</html>
