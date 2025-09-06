<!doctype html>
<html lang="en" x-data="adminShell()" x-init="init()" :class="theme">

<head>
  <meta charset="utf-8">
  <title>@yield('title','BERKEMAH Dashboard')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />

  {{-- Poppins --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    :root {
      --brand: #2563eb;
    }

    body {
      font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    }

    [x-cloak] {
      display: none !important;
    }
  </style>
</head>

<body class="min-h-screen flex"
  :class="theme==='navy' ? 'bg-[#0b1220] text-white' : 'bg-[#f3f7ff] text-[#102a43]'">

  <!-- SIDEBAR -->
  <aside class="fixed inset-y-0 left-0 w-64 transform lg:transform-none lg:static z-40 transition-transform duration-300"
    :class="[
           sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
           theme==='navy' ? 'bg-gradient-to-b from-[#0f1a33] to-[#0b1220] text-white' : 'bg-white text-[#102a43] shadow-lg'
         ]"
    aria-label="Main sidebar">

    <!-- BRAND (fix: img tidak lagi di dalam <svg>) -->
    <div class="p-4 flex items-center justify-between">
      <div class="flex items-center gap-3 select-none">
        <img src="{{ asset('assets/images/foto-berkemah.png') }}" alt="Berkemah Logo" class="h-14 w-auto shrink-0">
        <div class="leading-tight">
          <div class="text-[0.72rem] opacity-70 tracking-widest">BERKEMAH</div>
          <div class="text-2xl font-extrabold tracking-wide">
            <span :class="theme==='navy' ? 'text-white' : 'text-blue-700'">DASHBOARD</span>
          </div>
        </div>
      </div>
      <!-- Close (mobile) -->
      <button class="p-2 rounded lg:hidden focus:outline-none focus:ring-2 focus:ring-offset-2"
        :class="theme==='navy' ? 'hover:bg-white/10 ring-white/30' : 'hover:bg-blue-50 ring-blue-300'"
        @click="sidebarOpen=false" aria-label="Close sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 6l12 12M18 6L6 18" />
        </svg>
      </button>
    </div>

    <nav class="px-3 space-y-1" aria-label="Primary">
      @php
      $nav = [
      ['Dashboard','admin.dashboard'],
      ['Courses','admin.courses.index'],
      ['Modules','admin.modules.index'],
      ['Lessons','admin.lessons.index'],
      ['Quizzes','admin.quizzes.index'],
      ['Questions','admin.questions.index'],
      ['Options','admin.options.index'],
      ['Memberships','admin.memberships.index'],
      ['Enrollments','admin.enrollments.index'],
      ['Payments','admin.payments.index'],
      ['Plans','admin.plans.index'],
      ['Coupons','admin.coupons.index'],
      ['Resources','admin.resources.index'],
      ['Certificate Templates','admin.certificate-templates.index'],
      ['Certificate Issues','admin.certificate-issues.index'],
      ['Psych Tests','admin.psy-tests.index'],
      ['Psych Questions','admin.psy-questions.index'],
      ['Psych Attempts','admin.psy-attempts.index'],
      ['Qa_Threads','admin.qa-threads.index'],
      ['Lihat Situs','home'],
      ];
      $icon = function($label) {
      $base = 'class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
      return match($label) {
      'Dashboard' => "<svg $base>
        <path d='M3 13h8V3H3v10zM13 21h8V11h-8v10z' />
      </svg>",
      'Courses' => "<svg $base>
        <path d='M4 19l8 3 8-3V5l-8-3-8 3v14z' />
        <path d='M12 22V8' />
      </svg>",
      'Modules' => "<svg $base>
        <rect x='3' y='3' width='7' height='7' />
        <rect x='14' y='3' width='7' height='7' />
        <rect x='3' y='14' width='7' height='7' />
        <rect x='14' y='14' width='7' height='7' />
      </svg>",
      'Lessons' => "<svg $base>
        <path d='M4 19.5V5a2 2 0 0 1 2-2h9l5 5v11.5a2.5 2.5 0 0 1-2.5 2.5H6.5A2.5 2.5 0 0 1 4 19.5z' />
        <path d='M14 3v5h5' />
      </svg>",
      'Quizzes' => "<svg $base>
        <path d='M9 9h6' />
        <path d='M9 13h6' />
        <path d='M5 7l-2 2 2 2' />
        <path d='M19 15l2 2-2 2' />
        <rect x='3' y='3' width='18' height='18' rx='2' />
      </svg>",
      'Questions' => "<svg $base>
        <path d='M9 9a3 3 0 1 1 6 0c0 2-3 2-3 4' />
        <path d='M12 17h.01' />
        <rect x='3' y='3' width='18' height='18' rx='2' />
      </svg>",
      'Options' => "<svg $base>
        <circle cx='12' cy='12' r='3' />
        <path d='M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V22a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 7.04 4.3l.06.06c.47.47 1.13.68 1.82.33A1.65 1.65 0 0 0 10 3.09V3a2 2 0 1 1 4 0v.09c0 .66.39 1.26 1 1.51.69.35 1.35.14 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06c-.47.47-.68 1.13-.33 1.82.35.69 1.15 1 1.51 1H22a2 2 0 1 1 0 4h-.09c-.66 0-1.26.39-1.51 1z' />
      </svg>",
      'Memberships' => "<svg $base>
        <path d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2' />
        <circle cx='12' cy='7' r='4' />
      </svg>",
      'Enrollments' => "<svg $base>
        <path d='M4 4h16v12H4z' />
        <path d='M8 20h8M12 16v4' />
      </svg>",
      'Payments' => "<svg $base>
        <rect x='2' y='5' width='20' height='14' rx='2' />
        <path d='M2 10h20' />
      </svg>",
      'Plans' => "<svg $base>
        <path d='M4 6h16M6 10h12M8 14h8M10 18h4' />
      </svg>",
      'Coupons' => "<svg $base>
        <path d='M21 12a3 3 0 1 1 0-6H3v12h18a3 3 0 1 1 0-6z' />
        <path d='M7 6v12' />
      </svg>",
      'Resources' => "<svg $base>
        <path d='M4 4h16v16H4z' />
        <path d='M8 4v16' />
        <path d='M4 8h16' />
      </svg>",
      'Lihat Situs' => "<svg $base>
        <circle cx='12' cy='12' r='10' />
        <path d='M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z' />
      </svg>",
      'Certificate Templates' => "<svg $base>
        <path d='M4 4h16v12H5.17L4 17.17V4z' />
        <path d='M8 8h8v2H8V8Zm0 3h5v2H8v-2Z' />
      </svg>",
      'Certificate Issues' => "<svg $base>
        <path d='M4 4h16v12H5.17L4 17.17V4Z' />
        <path d='M8 8h8v2H8V8Zm0 3h5v2H8v-2Z' />
      </svg>",
      'Psych Tests' => "<svg $base>
        <path d='M12 20' />
        <path d='M16.24 18.24A9 9 0 1 0 5.76 5.76a9 9 0 0 0 10.48 12.48z' />
        <line x1='12' y1='2' x2='12' y2='22' />
        <line x1='2' y1='12' x2='22' y2='12' />
      </svg>",
      'Pysch Questions' => "<svg $base>
        <path d='M6 2h12v20l-6-3-6 3V2Z' />
      </svg>",
      'Qa_Threads' => "<svg $base>
        <path d='M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z' />
        <line x1='11' y1='10' x2='11' y2='10' />
        <line x1='15' y1='10' x2='15' y2='10' />
        <line x1='19' y1='10' x2='19' y2='10' />
      </svg>",
      default => "<svg $base>
        <circle cx='12' cy='12' r='9' />
      </svg>",
      };
      };

      $badges = $badges ?? [];
      @endphp

      @foreach($nav as [$label, $route])
      @php
      $active = request()->routeIs($route);
      $count = (int)($badges[$label] ?? 0);
      $badgeText = $count > 99 ? '99+' : $count;
      @endphp

      <a href="{{ route($route) }}"
        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition justify-between focus:outline-none focus:ring-2 focus:ring-offset-2"
        :class="theme==='navy'
             ? '{{ $active ? 'bg-blue-600 text-white ring-white/30' : 'hover:bg-white/10 text-blue-100 ring-white/20' }}'
             : '{{ $active ? 'bg-blue-600 text-white ring-blue-300' : 'hover:bg-blue-50 text-[#102a43] ring-blue-200' }}'"
        aria-current="{{ $active ? 'page' : 'false' }}">

        <span class="flex items-center gap-3 min-w-0">
          <span class="shrink-0 inline-flex items-center justify-center w-6 h-6"
            :class="theme==='navy' ? '{{ $active ? '' : 'opacity-90' }}' : '{{ $active ? '' : 'opacity-80' }}'">
            {!! $icon($label) !!}
          </span>
          <span class="truncate">{{ $label }}</span>
        </span>

        @if($count > 0)
        <span class="ml-3 shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full
              {{ $active ? 'bg-white text-blue-700' : 'bg-blue-600 text-white' }}">
          {{ $badgeText }}
        </span>
        @endif
      </a>
      @endforeach
      {{-- NOTE: tombol Logout dihapus dari sidebar sesuai permintaan --}}
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="flex-1 flex flex-col min-w-0">
    <!-- TOPBAR -->
    <header class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 shadow sticky top-0 z-30"
      :class="theme==='navy' ? 'bg-[#0f1a33] text-white' : 'bg-white text-[#102a43]'">
      <div class="flex items-center gap-2">
        <button class="p-2 rounded lg:hidden focus:outline-none focus:ring-2 focus:ring-offset-2"
          :class="theme==='navy' ? 'hover:bg-white/10 ring-white/30' : 'hover:bg-blue-50 ring-blue-300'"
          @click="sidebarOpen=true" aria-label="Open sidebar">
          <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 6h18M3 12h18M3 18h18" />
          </svg>
        </button>
        <h1 class="text-lg sm:text-xl font-semibold">@yield('title','Dashboard')</h1>
      </div>

      {{-- RIGHT: Home + Profile dropdown + Theme toggle --}}
      @php($u = \Illuminate\Support\Facades\Auth::user())
      <div class="flex items-center gap-2 sm:gap-3" x-data="{open:false}">
        <a href="{{ route('home') }}" class="px-3 py-1.5 rounded hover:bg-blue-200/30">Home</a>

        <!-- Profile dropdown (fix: opsi Logout dihapus) -->
        <div class="relative">
          <button @click="open=!open" @keydown.escape.window="open=false"
            class="flex items-center gap-2 px-3 py-1.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="theme==='navy' ? 'border-white/10 hover:bg-white/10 ring-white/30' : 'border-blue-200 hover:bg-blue-50 ring-blue-300'">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold bg-blue-600 text-white">
              {{ strtoupper(mb_substr($u?->name ?? 'U',0,1)) }}
            </span>
            <span class="hidden sm:block max-w-[160px] truncate">{{ $u?->name ?? 'User' }}</span>
            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </button>

          <div x-cloak x-show="open" @click.outside="open=false"
            class="absolute right-0 mt-2 w-48 rounded-xl border shadow-lg overflow-hidden z-40"
            :class="theme==='navy' ? 'bg-[#0f1a33] text-white border-white/10' : 'bg-white text-[#102a43] border-blue-100'">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5"
              :class="theme==='navy' ? 'hover:bg-white/10' : 'hover:bg-blue-50'">
              Profile
            </a>
            <form method="POST" action="{{ route('logout') }}"> @csrf <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-red-500/10 text-red-600"> Logout </button> </form>
          </div>
        </div>

        <!-- THEME TOGGLE -->
        <button @click="toggleTheme()" class="p-2 rounded-xl border flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-offset-2"
          :class="theme==='navy' ? 'border-white/10 hover:bg-white/10 ring-white/30' : 'border-blue-200 hover:bg-blue-50 ring-blue-300'"
          aria-label="Toggle theme">
          <svg x-show="theme==='navy'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
          </svg>
          <svg x-show="theme!=='navy'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
          </svg>
        </button>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 p-4 sm:p-6 overflow-y-auto min-w-0">
      @if(session('ok'))
      <div class="p-3 rounded mb-4"
        :class="theme==='navy' ? 'bg-emerald-600/15 text-emerald-300' : 'bg-emerald-50 text-emerald-700'">
        {{ session('ok') }}
      </div>
      @endif
      @yield('content')
    </main>
  </div>

  <script>
    function adminShell() {
      return {
        theme: 'navy',
        sidebarOpen: false,
        init() {
          const saved = localStorage.getItem('admin-theme');
          if (saved) this.theme = saved;
        },
        toggleTheme() {
          this.theme = this.theme === 'navy' ? 'sky' : 'navy';
          localStorage.setItem('admin-theme', this.theme);
        }
      }
    }
  </script>
</body>

</html>