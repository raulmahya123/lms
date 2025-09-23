<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $test->title ?? 'Tes IQ' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-indigo-50 via-white to-slate-50 text-slate-800 antialiased">

  @php
    $questions = $test->questions ?? [];
    $total     = is_array($questions) ? count($questions) : 0;
    $dur       = $test->duration_minutes ?? null;
  @endphp

  <main class="mx-auto max-w-4xl px-4 sm:px-6 py-6 sm:py-10">
    <!-- Back / breadcrumb -->
    <div class="mb-6">
      <a href="{{ url()->previous() }}"
         class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M15.78 5.22a.75.75 0 0 1 0 1.06L9.06 13l6.72 6.72a.75.75 0 1 1-1.06 1.06l-7.25-7.25a.75.75 0 0 1 0-1.06l7.25-7.25a.75.75 0 0 1 1.06 0z" clip-rule="evenodd"/>
        </svg>
        Kembali
      </a>
    </div>

    <!-- Card -->
    <section class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <!-- Soft gradient strip -->
      <div class="h-1 w-full bg-gradient-to-r from-indigo-500/60 via-indigo-400 to-indigo-600/70"></div>

      <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-[12px] font-medium text-indigo-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.7 2.07a.75.75 0 0 1 .6 0l8.25 3.75a.75.75 0 0 1 .45.68v5.25a8.25 8.25 0 1 1-16.5 0V6.5a.75.75 0 0 1 .45-.68l6.75-3.07Z"/>
              </svg>
              Tes IQ
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">{{ $test->title }}</h1>
            @if(!empty($test->description))
              <p class="mt-2 text-slate-600">{{ $test->description }}</p>
            @endif
          </div>

          <!-- Meta tiles -->
          <div class="grid grid-cols-2 gap-3 sm:w-[280px]">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
              <div class="text-xs text-slate-500">Jumlah Soal</div>
              <div class="mt-0.5 text-xl font-semibold text-slate-900">{{ $total }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
              <div class="text-xs text-slate-500">Durasi</div>
              <div class="mt-0.5 text-xl font-semibold text-slate-900">{{ $dur ? $dur.' menit' : '—' }}</div>
            </div>
          </div>
        </div>

        <!-- Rules -->
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
          <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-emerald-600 ring-1 ring-emerald-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M9 12.75 5.75 9.5a.75.75 0 1 0-1.06 1.06l4 4a.75.75 0 0 0 1.06 0l9-9a.75.75 0 1 0-1.06-1.06L9 12.75Z" clip-rule="evenodd"/>
              </svg>
            </span>
            <p class="text-sm text-emerald-800">
              Setelah dimulai, sesi terkunci hingga selesai.
            </p>
          </div>
          <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-amber-600 ring-1 ring-amber-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 3.75A8.25 8.25 0 1 0 20.25 12 8.26 8.26 0 0 0 12 3.75Zm.75 3a.75.75 0 0 0-1.5 0v4.5c0 .414.336.75.75.75h3a.75.75 0 0 0 0-1.5H12.75V6.75Z"/>
              </svg>
            </span>
            <p class="text-sm text-amber-800">
              Kelola waktu dengan cermat; hitungan durasi tetap berjalan.
            </p>
          </div>
          <div class="flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 sm:col-span-2">
            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white text-sky-600 ring-1 ring-sky-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75S6.615 21.75 12 21.75 21.75 17.385 21.75 12 17.385 2.25 12 2.25Zm-4.03 9.47a.75.75 0 0 1 1.06-1.06L12 13.69l2.97-3.03a.75.75 0 1 1 1.06 1.06l-3.5 3.57a.75.75 0 0 1-1.06 0l-3.5-3.57Z"/>
              </svg>
            </span>
            <p class="text-sm text-sky-800">
              Pastikan koneksi stabil dan baterai perangkat memadai.
            </p>
          </div>
        </div>

        <!-- CTA -->
        <div class="mt-6">
          <form method="GET" action="{{ route('user.test-iq.start', ['testIq' => $test]) }}">
            <button type="submit"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3.5 text-base font-semibold text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600/70 disabled:opacity-60"
              {{ $total <= 0 ? 'disabled' : '' }}>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.25 12a9.75 9.75 0 1 1 19.5 0 9.75 9.75 0 0 1-19.5 0Zm7.53-3.53a.75.75 0 0 0 0 1.06L12.19 12l-2.41 2.47a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 0 0-1.06 0Z"/>
              </svg>
              Mulai Tes
            </button>
          </form>

          @if($total <= 0)
            <p class="mt-3 text-xs text-amber-600">
              Belum ada soal pada tes ini. Silakan hubungi admin.
            </p>
          @endif

          <p class="mt-3 text-[12px] leading-relaxed text-slate-500">
            Dengan menekan <em>Mulai Tes</em>, Anda menyetujui ketentuan ujian di atas.
          </p>
        </div>
      </div>
    </section>

    <!-- Small tip -->
    <p class="mt-6 text-center text-[12px] text-slate-500">
      Tips: tutup aplikasi lain untuk meminimalkan distraksi.
    </p>
  </main>

</body>
</html>
