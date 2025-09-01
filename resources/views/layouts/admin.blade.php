<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Admin')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 flex h-screen">

  {{-- SIDEBAR --}}
  <aside class="w-64 bg-blue-900 text-white flex flex-col">
    <div class="p-4 text-center text-2xl font-extrabold tracking-wide">
      AdminZone
    </div>
    <nav class="flex-1 px-2 space-y-1">
      <a href="{{ route('admin.dashboard') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
        Dashboard
      </a>
      <a href="{{ route('admin.courses.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/courses*') ? 'bg-blue-700' : '' }}">
        Courses
      </a>
      <a href="{{ route('admin.modules.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/modules*') ? 'bg-blue-700' : '' }}">
        Modules
      </a>
      <a href="{{ route('admin.lessons.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/lessons*') ? 'bg-blue-700' : '' }}">
        Lessons
      </a>
      <a href="{{ route('admin.quizzes.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/quizzes*') ? 'bg-blue-700' : '' }}">
        Quizzes
      </a>
      <a href="{{ route('admin.questions.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/questions*') ? 'bg-blue-700' : '' }}">
        Questions
      </a>
      <a href="{{ route('admin.options.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/options*') ? 'bg-blue-700' : '' }}">
        Options
      </a>
      <a href="{{ route('admin.memberships.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/memberships*') ? 'bg-blue-700' : '' }}">
        Memberships
      </a>
      <a href="{{ route('admin.enrollments.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/enrollments*') ? 'bg-blue-700' : '' }}">
        Enrollments
      </a>
      <a href="{{ route('admin.payments.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/payments*') ? 'bg-blue-700' : '' }}">
        Payments
      </a>
      <a href="{{ route('admin.plans.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/plans*') ? 'bg-blue-700' : '' }}">
        Plans
      </a>
      <a href="{{ route('admin.coupons.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/coupons*') ? 'bg-blue-700' : '' }}">
        Coupons
      </a>
      <a href="{{ route('admin.resources.index') }}"
         class="block px-3 py-2 rounded-lg hover:bg-blue-700 {{ request()->is('admin/resources*') ? 'bg-blue-700' : '' }}">
        Resources
      </a>
    </nav>
    <div class="p-4 border-t border-blue-700">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full px-3 py-2 rounded-lg bg-blue-700 hover:bg-blue-600">
          Logout
        </button>
      </form>
    </div>
  </aside>

  {{-- MAIN --}}
  <div class="flex-1 flex flex-col">
    {{-- TOP NAV --}}
    <header class="bg-blue-800 text-white shadow flex items-center justify-between px-6 py-4">
      <h1 class="text-xl font-semibold">@yield('title','Dashboard')</h1>
      <nav class="space-x-4 text-sm">
        <a href="{{ route('profile.edit') }}" class="hover:text-blue-200">Profile</a>
        <a href="{{ route('home') }}" class="hover:text-blue-200">Home</a>
      </nav>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 bg-white p-6 overflow-y-auto">
      @if(session('ok'))
        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
          {{ session('ok') }}
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</body>
</html>
