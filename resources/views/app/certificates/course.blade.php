{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* ====== Halaman A4 Landscape (paper sudah di-set di controller) ====== */
    @page { margin: 0; }
    html, body {
      margin: 0; padding: 0;
      font-family: DejaVu Sans, sans-serif;
      color: #0b0f19;
    }

    /* ====== Kanvas penuh halaman ====== */
    .page {
      position: relative;
      width: 297mm;      /* A4 width landscape */
      height: 210mm;     /* A4 height landscape */
      box-sizing: border-box;
      padding: 18mm 18mm;
    }

    /* ====== Background template (opsional) ====== */
    .bg {
      position: absolute;
      left: 0; top: 0; right: 0; bottom: 0;
      z-index: -2;
    }
    .bg img {
      width: 297mm; height: 210mm; display: block; object-fit: cover;
    }

    /* Lapisan putih transparan supaya teks tetap terbaca di atas background */
    .overlay {
      position: absolute; inset: 0;
      background: rgba(255,255,255,.92);
      z-index: -1;
    }

    /* ====== Kartu sertifikat ====== */
    .card {
      height: 100%;
      border: 6px double #1f2937;       /* slate-800 */
      border-radius: 10mm;
      padding: 16mm 20mm;
      box-sizing: border-box;

      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }

    /* ====== Tipografi utama ====== */
    .title {
      font-weight: 800;
      font-size: 18mm;                   /* ~51px */
      letter-spacing: .4mm;
      margin: 0 0 2mm;
      text-transform: uppercase;
    }
    .subtitle {
      font-size: 5mm;                    /* ~14px */
      color: #4b5563;                    /* gray-600 */
      margin: 0 0 6mm;
    }
    .name {
      font-size: 13mm;                   /* ~37px */
      font-weight: 800;
      margin: 2mm 0 1mm;
    }
    .desc {
      font-size: 5mm; color:#374151; margin: 0 0 3mm;
    }
    .course {
      font-size: 7mm;                    /* ~20px */
      font-weight: 700;
      margin: 0 0 6mm;
      quotes: "“" "”" "‘" "’";
    }
    .course::before { content: open-quote; }
    .course::after  { content: close-quote; }

    /* ====== Info Nilai ====== */
    .metrics { margin-top: 2mm; }
    .metric {
      font-size: 4.5mm;
      color: #111827;
      margin: 1mm 0;
    }
    .metric small { color:#6b7280; }

    .badge {
      margin-top: 6mm;
      display: inline-block;
      padding: 2.2mm 4.5mm;
      font-size: 3.6mm;
      border-radius: 2.8mm;
      color: #065f46;                    /* emerald-800 */
      background: #d1fae5;               /* emerald-100 */
    }

    /* ====== Tanda tangan / footer ====== */
    .signs {
      display: flex;
      gap: 18mm;
      justify-content: center;
      margin-top: auto;                  /* dorong ke bawah */
      margin-bottom: 8mm;
    }
    .sign {
      width: 60mm;
      padding-top: 3mm;
      border-top: .4mm solid #9ca3af;
      font-size: 3.8mm;
      color: #374151;
    }

    .footer {
      font-size: 3.6mm;
      color: #6b7280;
      line-height: 1.6;
    }
    .serial {
      font-size: 3.6mm;
      letter-spacing: .35mm;
      color: #4b5563;
      margin-top: 2mm;
    }

    /* ====== Ornamen sudut (garis tipis) – aman untuk DomPDF ====== */
    .corners::before,
    .corners::after {
      content: "";
      position: absolute;
      width: 50mm; height: 50mm;
      border: .6mm solid #cbd5e1;        /* slate-300 */
      border-radius: 6mm;
      z-index: -1;
    }
    .corners::before { left: 10mm; top: 10mm; border-right: none; border-bottom: none; }
    .corners::after  { right: 10mm; bottom: 10mm; border-left: none; border-top: none; }
  </style>
</head>
<body>
@php
  // Tentukan sumber background:
  $bgUrl = $template?->background_url ?? null;
  if ($bgUrl) {
      $parts = parse_url($bgUrl);
      // Jika relatif (mis. '/certificates/default-bg.png'), ubah ke absolute filesystem path untuk DomPDF
      if (!isset($parts['scheme'])) {
          $bgUrl = public_path($bgUrl);
      }
  }

  // Tanggal terbitan (fallback now)
  $issuedAt = isset($issued_at) ? $issued_at->timezone('Asia/Jakarta') : now('Asia/Jakarta');
@endphp

<div class="page">
  @if($bgUrl)
    <div class="bg"><img src="{{ $bgUrl }}" alt=""></div>
    <div class="overlay"></div>
  @endif

  <div class="card corners">
    <h1 class="title">Sertifikat Kelulusan</h1>
    <p class="subtitle">Dengan ini menyatakan bahwa</p>

    <div class="name">{{ $user->name }}</div>

    <p class="desc">telah berhasil menyelesaikan kursus</p>
    <div class="course">{{ $course->title }}</div>

    {{-- Metrics / Nilai --}}
    <div class="metrics">
      @isset($percent)
        <div class="metric">
          Ketuntasan kuis otomatis: <strong>{{ number_format($percent, 2) }}%</strong>
          @isset($correct, $total)
            <small>({{ $correct }} benar dari {{ $total }} soal MCQ)</small>
          @endisset
        </div>
      @endisset

      @isset($bestAttempt)
        <div class="metric">Skor terbaik kuis: <strong>{{ $bestAttempt->score }}</strong></div>
      @endisset
    </div>

    <span class="badge">Diterbitkan pada: {{ $issuedAt->format('d M Y, H:i') }} WIB</span>

    <div class="signs">
      <div class="sign">Instruktur / Admin</div>
      <div class="sign">{{ config('app.name') }}</div>
    </div>

    <div class="footer">
      Dokumen ini sah tanpa tanda tangan basah.
      <div class="serial">No. Serial: {{ $serial }}</div>
    </div>
  </div>
</div>
</body>
</html>
