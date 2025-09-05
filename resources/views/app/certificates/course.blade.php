<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* A4 landscape handled by controller, here we keep margins tight */
    @page { margin: 0; }
    html, body {
      margin: 0; padding: 0;
      font-family: DejaVu Sans, sans-serif; color: #111;
      width: 100%; height: 100%;
    }

    /* Wrapper full page */
    .wrap {
      position: relative;
      width: 100%;
      height: 100vh; /* DomPDF treats vh as page height */
      box-sizing: border-box;
      padding: 28mm 22mm; /* nyaman untuk border double */
    }

    /* Background image (template) spans full page */
    .bg {
      position: absolute;
      inset: 0;
      z-index: -1;
    }
    .bg img {
      width: 100%; height: 100%;
      object-fit: cover;
      display: block;
    }

    .card {
      border: 8px double #1f2937;
      border-radius: 24px;
      padding: 20mm 18mm;
      height: calc(100% - 56mm); /* kompensasi padding .wrap */
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      background: rgba(255,255,255,.88); /* agar teks tetap kebaca di atas background */
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }

    .title { font-size: 40px; font-weight: 700; letter-spacing: .03em; margin-bottom: 8px; }
    .sub { font-size: 18px; color: #374151; margin-bottom: 22px; }
    .name { font-size: 36px; font-weight: 700; margin: 8px 0 4px; }
    .course { font-size: 22px; font-weight: 600; color: #111827; margin-bottom: 16px; }

    .score, .percent { font-size: 16px; color: #111827; margin-top: 6px; }
    .percent small { color: #6b7280; }

    .badge {
      margin-top: 14px; font-size: 12px; color: #065f46;
      background: #d1fae5; display:inline-block; padding:6px 10px; border-radius: 8px;
    }

    .sign-row { display: flex; gap: 48px; margin-top: 28px; justify-content: center; }
    .sign { width: 260px; border-top: 1px solid #9ca3af; padding-top: 8px; font-size: 12px; color: #374151; }

    .meta { font-size: 12px; color: #6b7280; margin-top: 18px; }
    .serial { font-size: 12px; letter-spacing: .08em; color: #4b5563; margin-top: 8px; }
  </style>
</head>
<body>
  @php
    // Tentukan sumber background:
    // - jika background_url absolut (http/https/data), pakai langsung
    // - jika relatif, asumsikan relatif ke public_path()
    $bgUrl = $template?->background_url ?? null;
    $isAbsolute = false;
    if ($bgUrl) {
        $parts = parse_url($bgUrl);
        $isAbsolute = isset($parts['scheme']); // http/https/data
        if (!$isAbsolute) {
            // kalau relatif: ex '/certificates/default-bg.png'
            $bgUrl = public_path($bgUrl);
            // Catatan: untuk DomPDF, path lokal (absolute filesystem path) OK.
        }
    }
  @endphp

  <div class="wrap">
    @if($bgUrl)
      <div class="bg">
        {{-- DomPDF menerima path file lokal atau URL absolut --}}
        <img src="{{ $bgUrl }}">
      </div>
    @endif

    <div class="card">
      <div class="title">SERTIFIKAT KELULUSAN</div>
      <div class="sub">Dengan ini menyatakan bahwa</div>

      <div class="name">{{ $user->name }}</div>

      <div class="sub">telah berhasil menyelesaikan kursus</div>
      <div class="course">“{{ $course->title }}”</div>

      @if(isset($percent))
        <div class="percent">
          Ketuntasan kuis otomatis: <strong>{{ number_format($percent, 2) }}%</strong>
          @if(isset($correct, $total))
            <small>({{ $correct }} benar dari {{ $total }} soal MCQ)</small>
          @endif
        </div>
      @endif

      @if($bestAttempt)
        <div class="score">Skor terbaik kuis: <strong>{{ $bestAttempt->score }}</strong></div>
      @endif

      <div class="badge">
        Diterbitkan pada: {{ $issued_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
      </div>

      <div class="sign-row">
        <div class="sign">Instruktur / Admin</div>
        <div class="sign">{{ config('app.name') }}</div>
      </div>

      <div class="meta">Dokumen ini sah tanpa tanda tangan basah.</div>
      <div class="serial">No. Serial: {{ $serial }}</div>
    </div>
  </div>
</body>
</html>
