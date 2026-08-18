<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $test->title ?? 'Tes IQ' }}</title>
  
  {{-- Tailwind CDN (Sesuai Foundation) --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            tosca: {
              DEFAULT: '#0F9D8A',
              dark: '#087A6C',
              light: '#DFF5F1',
            },
            softbg: '#F4FBF9',
            text: {
              main: '#1e293b',
              soft: '#64748b'
            }
          },
          fontFamily: {
            sans: ['Inter', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-softbg text-text-main antialiased relative">
  {{-- Background Decoration --}}
  <div class="absolute top-0 left-0 w-full h-96 bg-tosca-dark/5 overflow-hidden -z-10">
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-tosca-light rounded-full blur-3xl opacity-70"></div>
    <div class="absolute top-12 -left-12 w-64 h-64 bg-tosca-light rounded-full blur-3xl opacity-60"></div>
  </div>

  @php
    $questions = $test->questions ?? [];
    $total     = is_array($questions) ? count($questions) : 0;
    $dur       = $test->duration_minutes ?? null;
  @endphp

  <main class="mx-auto max-w-4xl px-4 sm:px-6 py-12 sm:py-20 relative z-10">
    <!-- Back / breadcrumb -->
    <div class="mb-8">
      <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm font-semibold text-text-soft hover:text-tosca transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
    </div>

    <!-- Card -->
    <section class="bg-white rounded-3xl border border-gray-50 shadow-[0_8px_30px_rgba(0,0,0,0.04)] overflow-hidden">
      <!-- Soft gradient strip -->
      <div class="h-2 w-full bg-gradient-to-r from-tosca-dark via-tosca to-tosca-light"></div>

      <div class="p-8 sm:p-10">
        <!-- Header -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
          <div class="flex-1">
            <span class="inline-flex items-center gap-2 rounded-lg bg-tosca-light px-3 py-1 text-xs font-bold text-tosca-dark uppercase tracking-wider">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
              Tes Kecerdasan (IQ)
            </span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-text-main leading-tight">{{ $test->title }}</h1>
            @if(!empty($test->description))
              <p class="mt-3 text-text-soft leading-relaxed max-w-2xl">{{ $test->description }}</p>
            @endif
          </div>

          <!-- Meta tiles -->
          <div class="flex sm:flex-col gap-3 shrink-0">
            <div class="flex-1 sm:w-48 rounded-2xl border border-gray-100 bg-softbg/50 p-4 text-center hover:border-tosca/20 hover:bg-softbg transition-colors">
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Jumlah Soal</div>
              <div class="mt-1 text-2xl font-extrabold text-tosca">{{ $total }}</div>
            </div>
            <div class="flex-1 sm:w-48 rounded-2xl border border-gray-100 bg-softbg/50 p-4 text-center hover:border-tosca/20 hover:bg-softbg transition-colors">
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider">Durasi Waktu</div>
              <div class="mt-1 text-2xl font-extrabold text-tosca">{{ $dur ? $dur.' Min' : '—' }}</div>
            </div>
          </div>
        </div>

        <hr class="border-gray-50 my-8">

        <!-- Rules -->
        <div>
          <h3 class="text-lg font-bold text-text-main mb-4">Peraturan Tes</h3>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
              <div class="w-10 h-10 rounded-full bg-tosca-light text-tosca flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              </div>
              <div>
                <h4 class="font-bold text-text-main text-sm">Sesi Terkunci</h4>
                <p class="text-sm text-text-soft mt-1">Setelah dimulai, sesi akan terkunci hingga tes diselesaikan.</p>
              </div>
            </div>

            <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
              <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div>
                <h4 class="font-bold text-text-main text-sm">Hitungan Mundur</h4>
                <p class="text-sm text-text-soft mt-1">Kelola waktu dengan cermat. Durasi akan tetap berjalan meski Anda keluar.</p>
              </div>
            </div>

            <div class="flex items-start gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:col-span-2">
              <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
              </div>
              <div>
                <h4 class="font-bold text-text-main text-sm">Persiapan Perangkat</h4>
                <p class="text-sm text-text-soft mt-1">Pastikan koneksi internet Anda stabil dan baterai perangkat memadai sebelum memulai tes.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div class="mt-10 flex flex-col items-center">
          <form method="GET" action="{{ route('user.test-iq.start', ['testIq' => $test]) }}" class="w-full sm:w-auto min-w-[300px]">
            <button type="submit"
              class="w-full flex items-center justify-center gap-2 rounded-xl bg-tosca px-8 py-4 text-lg font-bold text-white shadow-sm shadow-tosca/30 hover:bg-tosca-dark transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
              {{ $total <= 0 ? 'disabled' : '' }}>
              Mulai Tes Sekarang
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
          </form>

          @if($total <= 0)
            <div class="mt-4 px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-semibold flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              Belum ada soal pada tes ini.
            </div>
          @endif

          <p class="mt-4 text-sm text-text-soft text-center max-w-md">
            Dengan menekan <strong class="text-text-main">Mulai Tes</strong>, Anda menyetujui peraturan ujian dan siap untuk berkonsentrasi.
          </p>
        </div>
      </div>
    </section>

    <!-- Small tip -->
    <p class="mt-8 text-center text-sm font-medium text-text-soft flex items-center justify-center gap-2">
      <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
      Tips: Tutup aplikasi lain untuk meminimalkan distraksi.
    </p>
  </main>

</body>
</html>
