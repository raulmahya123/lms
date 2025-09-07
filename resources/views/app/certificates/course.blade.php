{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    /* ===== A4 landscape, tanpa margin printer ===== */
    @page { size: A4 landscape; margin: 0; }
    html, body {
      margin: 0; padding: 0;
      width: 297mm; height: 210mm;
      font-family: DejaVu Sans, sans-serif;
      color: #0B1320;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }

    /* ===== Kanvas halaman (safe padding kecil agar muat) ===== */
    .page {
      position: relative;
      width: 297mm; height: 210mm;
      box-sizing: border-box;
      padding: 8mm 10mm;   /* <= diperkecil agar semua konten muat */
      background: #fff;
    }

    /* ===== Background opsional ===== */
    .bg { position:absolute; inset:0; z-index:-2; }
    .bg img { width:100%; height:100%; object-fit:cover; }
    .veil{ position:absolute; inset:0; background:rgba(255,255,255,.94); z-index:-1; }

    /* ===== Frame kartu ===== */
    .card{
      position: relative;
      height: 100%;
      border: 3pt solid #1E3A8A;
      border-radius: 6mm;
      box-sizing: border-box;
      padding: 10mm 12mm;       /* <= diperkecil */
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    /* ===== Tipografi kompak ===== */
    .title{ font-size: 13mm; font-weight: 800; color:#1E3A8A; margin: 0 0 1mm; }
    .sub{ font-size: 4.2mm; color:#2A3342; margin: 0 0 6mm; }
    .name{ font-size: 10mm; font-weight: 800; margin: 0 0 3mm; }
    .desc{ font-size: 4.2mm; color:#2A3342; margin: 0 0 2mm; }
    .course{ font-size: 7mm; font-weight: 700; margin: 0 0 6mm; quotes:"“" "”"; }
    .course::before{ content: open-quote; } .course::after{ content: close-quote; }

    /* ===== Metrik / nilai ===== */
    .metrics{ margin: 0 0 6mm; }
    .metric{ font-size: 4.2mm; margin: 1mm 0; }
    .metric small{ color:#5B6472; }

    .badge{
      display:inline-block;
      margin: 0 0 6mm;
      padding: 1.6mm 4mm;
      font-size: 3.9mm;
      border-radius: 3mm;
      color:#083A7A; background:#DBEAFE; border:.3mm solid #93C5FD;
    }

    .hr{ height:0; border:none; border-top:.3mm solid #BFDBFE; margin: 4mm 10mm 3mm; }

    /* ===== Area tanda tangan fixed-height supaya tidak memanjang ===== */
    .sign-area{
      width:100%;
      margin-top: auto;           /* dorong ke bawah */
    }
    .sig-grid{
      display: table; width:100%; table-layout: fixed;
    }
    .sig-cell{
      display: table-cell; vertical-align: bottom; text-align: center; padding: 0 8mm;
    }

    .sig-box{ height: 22mm; }     /* ruang gambar tanda tangan */
    .sig-img{ max-height: 18mm; max-width: 70mm; display:inline-block; }

    .sig-line{
      border-top: .4mm solid #1E3A8A;
      margin-top: 4mm; padding-top: 1.6mm;
    }
    .sig-name{ font-size: 4.2mm; font-weight: 700; }
    .sig-role{ font-size: 3.8mm; color:#2A3342; }
    .sig-org{  font-size: 3.6mm; color:#5B6472; }

    .footer{ text-align:center; font-size:3.8mm; color:#5B6472; margin-top: 3mm; }
    .serial{ margin-top:1.2mm; font-size:3.8mm; letter-spacing:.35mm; }
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
