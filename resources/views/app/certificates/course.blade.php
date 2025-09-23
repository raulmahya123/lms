{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* ===== KERTAS: US Letter Landscape, tanpa margin ===== */
    @page { size: letter landscape; margin: 0; }

    html, body {
      margin: 0; padding: 0;
      width: 11in;   /* 279.4 mm */
      height: 8.5in; /* 215.9 mm */
      font-family: DejaVu Sans, sans-serif;
      color: #0B1320;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    * { box-sizing: border-box; }

    /* ===== Kanvas halaman (tanpa padding agar tidak overflow) ===== */
    .page {
      position: relative;
      width: 11in; height: 8.5in;
      padding: 0;                 /* <= nolkan padding halaman */
      background: #fff;
      overflow: hidden;           /* cegah spill ke halaman 2 */
    }

    /* ===== Background opsional ===== */
    .bg { position:absolute; inset:0; z-index:-2; }
    .bg img { width:100%; height:100%; object-fit:cover; }
    .veil{ position:absolute; inset:0; background:rgba(255,255,255,.94); z-index:-1; }

    /* ===== Frame kartu: dipasang absolute dengan inset (safe margin) ===== */
    .card{
      position: absolute;
      /* inset: <top/bottom> <left/right>  → aman & tidak butuh calc() */
      inset: 0.35in 0.45in;       /* ruang tepi “halaman” */
      border: 3pt solid #1E3A8A;
      border-radius: 0.22in;
      padding: 0.45in 0.55in;     /* inner padding konten */
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      page-break-inside: avoid;   /* jangan pecah halaman */
    }

    /* ===== Tipografi (pas untuk Letter) ===== */
    .title{ font-size: 0.55in; font-weight: 800; color:#1E3A8A; margin: 0 0 0.06in; line-height:1.05; }
    .sub{ font-size: 0.17in; color:#2A3342; margin: 0 0 0.22in; }
    .name{ font-size: 0.42in; font-weight: 800; margin: 0 0 0.12in; }
    .desc{ font-size: 0.17in; color:#2A3342; margin: 0 0 0.08in; }
    .course{ font-size: 0.28in; font-weight: 700; margin: 0 0 0.25in; quotes:"“" "”"; }
    .course::before{ content: open-quote; } .course::after{ content: close-quote; }

    /* ===== Metrik / nilai ===== */
    .metrics{ margin: 0 0 0.25in; }
    .metric{ font-size: 0.17in; margin: 0.04in 0; }
    .metric small{ color:#5B6472; }

    .badge{
      display:inline-block;
      margin: 0 0 0.25in;
      padding: 0.07in 0.16in;
      font-size: 0.16in;
      border-radius: 0.12in;
      color:#083A7A; background:#DBEAFE; border:0.013in solid #93C5FD;
    }

    .hr{ height:0; border:none; border-top:0.012in solid #BFDBFE; margin: 0.18in 0.4in 0.14in; }

    /* ===== Area tanda tangan (tinggi tetap) ===== */
    .sign-area{ width:100%; margin-top: auto; }
    .sig-grid{ display: table; width:100%; table-layout: fixed; }
    .sig-cell{ display: table-cell; vertical-align: bottom; text-align: center; padding: 0 0.3in; }

    .sig-box{ height: 0.85in; }           /* ruang gambar tanda tangan */
    .sig-img{ max-height: 0.7in; max-width: 2.7in; display:inline-block; }

    .sig-line{
      border-top: 0.016in solid #1E3A8A;
      margin-top: 0.16in; padding-top: 0.06in;
    }
    .sig-name{ font-size: 0.17in; font-weight: 700; }
    .sig-role{ font-size: 0.15in; color:#2A3342; }
    .sig-org{  font-size: 0.14in; color:#5B6472; }

    .footer{ text-align:center; font-size:0.15in; color:#5B6472; margin-top: 0.12in; }
    .serial{ margin-top:0.05in; font-size:0.15in; letter-spacing:0.014in; }
  </style>
</head>
<body>
@php
  // ===== path gambar untuk DomPDF =====
  $bgUrl = $template?->background_url ?? null;
  if ($bgUrl && !preg_match('#^https?://#',$bgUrl)) $bgUrl = public_path($bgUrl);

  $leftSign  = $template->sign_left_image_url  ?? null;
  $rightSign = $template->sign_right_image_url ?? null;
  if ($leftSign && !preg_match('#^https?://#',$leftSign))  $leftSign  = public_path($leftSign);
  if ($rightSign && !preg_match('#^https?://#',$rightSign)) $rightSign = public_path($rightSign);

  $leftName = $template->sign_left_name   ?? 'Instruktur';
  $leftRole = $template->sign_left_title  ?? 'Penguji';
  $rightName= $template->sign_right_name  ?? 'Admin';
  $rightRole= $template->sign_right_title ?? 'Penyelenggara';
  $orgName  = config('app.name');

  $issuedAt = isset($issued_at) ? $issued_at->timezone('Asia/Jakarta') : now('Asia/Jakarta');
  $percentFmt = isset($percent) ? number_format((float)$percent, 2) : null;
@endphp

<div class="page">
  @if($bgUrl)
    <div class="bg"><img src="{{ $bgUrl }}" alt=""></div>
    <div class="veil"></div>
  @endif

  <div class="card">
    <h1 class="title">SERTIFIKAT KELULUSAN</h1>
    <p class="sub">Diberikan kepada</p>

    <div class="name">{{ $user->name }}</div>

    <p class="desc">telah berhasil menyelesaikan kursus</p>
    <div class="course">{{ $course->title }}</div>

    <div class="metrics">
      @if($percentFmt !== null)
        <div class="metric">
          Ketuntasan otomatis: <strong>{{ $percentFmt }}%</strong>
          @if(isset($correct,$total) && $correct!==null && $total!==null)
            <small>({{ $correct }} benar dari {{ $total }} soal MCQ)</small>
          @endif
        </div>
      @endif
      @isset($bestAttempt)
        <div class="metric">Skor terbaik kuis: <strong>{{ $bestAttempt->score }}</strong></div>
      @endisset
    </div>

    <span class="badge">Diterbitkan: {{ $issuedAt->format('d M Y, H:i') }} WIB</span>

    <hr class="hr">

    {{-- ===== Dua tanda tangan (tinggi fixed) ===== --}}
    <div class="sign-area">
      <div class="sig-grid">
        <div class="sig-cell">
          <div class="sig-box">
            @if($leftSign)
              <img src="{{ $leftSign }}" class="sig-img" alt="Ttd kiri">
            @endif
          </div>
          <div class="sig-line">
            <div class="sig-name">{{ $leftName }}</div>
            <div class="sig-role">{{ $leftRole }}</div>
            <div class="sig-org">{{ $orgName }}</div>
          </div>
        </div>

        <div class="sig-cell">
          <div class="sig-box">
            @if($rightSign)
              <img src="{{ $rightSign }}" class="sig-img" alt="Ttd kanan">
            @endif
          </div>
          <div class="sig-line">
            <div class="sig-name">{{ $rightName }}</div>
            <div class="sig-role">{{ $rightRole }}</div>
            <div class="sig-org">{{ $orgName }}</div>
          </div>
        </div>
      </div>

      <div class="footer">
        Dokumen ini sah tanpa tanda tangan basah.
        <div class="serial">No. Serial: {{ $serial }}</div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
