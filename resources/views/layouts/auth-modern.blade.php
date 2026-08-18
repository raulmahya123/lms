<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Auth') - BERKEMAH</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif']
          },
          colors: {
            tosca: {
              light: '#F3E8FF',
              DEFAULT: '#7C3AED',
              dark: '#6D28D9',
              deep: '#4C1D95'
            },
            ink: '#171026'
          },
          boxShadow: {
            auth: '0 24px 80px rgba(15, 157, 138, .14)'
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    [x-cloak]{display:none!important}
    body{font-family:'Poppins',ui-sans-serif,system-ui,sans-serif}
    .auth-input{
      height:52px;width:100%;border-radius:16px;border:1px solid rgba(124,58,237,.16);
      background:rgba(255,255,255,.88);padding:0 44px 0 44px;font-size:.925rem;
      color:#171026;outline:none;transition:border-color .2s,box-shadow .2s,background .2s;
    }
    .auth-input:hover{border-color:rgba(124,58,237,.32)}
    .auth-input:focus{border-color:#7C3AED;box-shadow:0 0 0 4px rgba(124,58,237,.12);background:#fff}
    .auth-input-error{border-color:#ef4444!important;box-shadow:0 0 0 4px rgba(239,68,68,.08)}
    .dark .auth-input{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.12);color:#f8fffd}
    .dark .auth-input:focus{background:rgba(255,255,255,.12);border-color:#7C3AED}
    .auth-panel{animation:authIn .45s ease both}
    @keyframes authIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
  </style>
  @stack('styles')
</head>
<body
  x-data="{ darkMode: localStorage.getItem('berkemah-dark') === '1' }"
  x-init="$watch('darkMode', value => { localStorage.setItem('berkemah-dark', value ? '1' : '0'); document.documentElement.classList.toggle('dark', value); }); document.documentElement.classList.toggle('dark', darkMode)"
  class="min-h-screen bg-[#faf7ff] text-ink antialiased dark:bg-[#12091F] dark:text-white">

  <main class="min-h-screen lg:grid lg:grid-cols-[55%_45%]">
    <section class="@yield('visual_order', '') hidden min-h-screen overflow-hidden bg-[radial-gradient(circle_at_18%_18%,rgba(124,58,237,.24),transparent_28%),linear-gradient(135deg,#4C1D95,#171026)] p-8 text-white lg:flex">
      <div class="relative flex w-full flex-col justify-between overflow-hidden rounded-[2rem] border border-white/12 bg-white/8 p-8 shadow-2xl backdrop-blur">
        <div class="absolute -right-20 -top-20 h-60 w-60 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-24 left-16 h-72 w-72 rounded-full bg-[#7C3AED]/20 blur-3xl"></div>

        <div class="relative z-10 flex items-center justify-between">
          <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-tosca-deep shadow-lg">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </span>
            <span class="text-xl font-black tracking-tight">BERKE<span class="text-[#DDD6FE]">MAH</span></span>
          </a>
          <span class="rounded-full border border-white/12 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white/75">LMS Premium</span>
        </div>

        <div class="relative z-10 my-10 max-w-xl">
          <p class="text-sm font-bold uppercase tracking-[.2em] text-[#DDD6FE]">@yield('visual_kicker', 'Belajar online')</p>
          <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight xl:text-5xl">@yield('visual_title')</h1>
          <p class="mt-5 text-base leading-8 text-white/74">@yield('visual_text')</p>
        </div>

        <div class="relative z-10 grid gap-4 xl:grid-cols-3">
          @yield('visual_cards')
        </div>
      </div>
    </section>

    <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-10">
      <div class="auth-panel w-full max-w-[480px]">
        <div class="mb-6 flex items-center justify-between">
          <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-tosca/15 bg-white px-4 py-2 text-xs font-bold text-tosca-deep shadow-sm transition hover:bg-tosca-light dark:border-white/10 dark:bg-white/8 dark:text-white">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
          </a>
          <button type="button" @click="darkMode=!darkMode" class="grid h-10 w-10 place-items-center rounded-full border border-tosca/15 bg-white text-tosca-deep shadow-sm transition hover:bg-tosca-light dark:border-white/10 dark:bg-white/8 dark:text-white" aria-label="Toggle dark mode">
            <svg x-show="!darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            <svg x-cloak x-show="darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
          </button>
        </div>

        <div class="rounded-[2rem] border border-tosca/12 bg-white/86 p-6 shadow-auth backdrop-blur dark:border-white/10 dark:bg-white/8 sm:p-8">
          <div class="mb-8">
            <div class="mb-5 flex items-center gap-3 lg:hidden">
              <span class="grid h-11 w-11 place-items-center rounded-2xl bg-tosca text-white shadow-lg shadow-tosca/20">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
              </span>
              <span class="text-xl font-black">BERKE<span class="text-tosca">MAH</span></span>
            </div>
            <h2 class="text-3xl font-black tracking-tight text-ink dark:text-white">@yield('heading')</h2>
            <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-white/62">@yield('subheading')</p>
          </div>

          @if (session('status'))
            <div class="mb-5 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-semibold text-violet-800 dark:border-violet-400/20 dark:bg-violet-400/10 dark:text-violet-100">
              {{ session('status') }}
            </div>
          @endif

          @yield('content')
        </div>
      </div>
    </section>
  </main>

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @stack('scripts')
</body>
</html>
