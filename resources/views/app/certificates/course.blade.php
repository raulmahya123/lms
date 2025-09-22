{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    @page { size: letter landscape; margin: 0; }

    html, body {
      margin: 0; padding: 0;
      width: 11in; height: 8.5in;
      font-family: DejaVu Sans, sans-serif;
      color: #0B1320;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    * { box-sizing: border-box; }

    :root{
      --primary:#0EA5E9;       /* biru terang (aksen)  */
      --primary-dark:#0D74C7;  /* judul "SERTIFIKAT"   */
      --ink:#0B1320;           /* teks gelap utama     */
      --muted:#64748B;         /* teks sekunder        */
      --frame:#111111;         /* garis frame hitam    */
    }

    .page{
      position:relative; width:11in; height:8.5in; background:#fff; overflow:hidden;
      /* frame hitam tipis mengelilingi kertas */
      outline: .06in solid var(--frame);
      outline-offset: -.06in;
      border-radius: .04in;
    }

    /* ==== aksen garis sudut mirip contoh ==== */
    .accent-top   { position:absolute; left:.45in;  top:.45in;  width:2.6in; height:.1in; background:var(--primary); border-radius:.08in; }
    .accent-left  { position:absolute; left:.30in;  top:.45in;  width:.09in; height:1.55in; background:var(--primary); border-radius:.08in; }
    .accent-bottom{ position:absolute; right:.45in; bottom:.45in; width:3.1in; height:.1in; background:var(--primary); border-radius:.08in; }
    .accent-right { position:absolute; right:.30in; bottom:.45in; width:.09in; height:1.15in; background:var(--primary); border-radius:.08in; }

    /* ==== watermark kanan (pakai foto sama) ==== */
    .bg-photo{
      position:absolute; inset:0 0 0 auto; width:6.2in; display:flex; align-items:center; justify-content:flex-end;
      opacity:.08;              /* lembut agar teks tetap jelas */
    }
    .bg-photo img{ max-height:100%; max-width:100%; object-fit:contain; }

    /* ==== header kanan: SERTIFIKAT / Kursus Online ==== */
    .right-head{ position:absolute; top:.6in; right:.6in; text-align:right; }
    .right-head .title{ margin:0; font-weight:900; font-size:.66in; letter-spacing:.5px; color:var(--primary-dark); line-height:1; }
    .right-head .sub  { margin:.06in 0 0; font-weight:700; font-size:.22in; color:var(--primary); }

    /* ==== logo kiri atas (pakai foto juga, versi kecil) ==== */
    .logo{ position:absolute; top:.55in; left:.55in; width:.72in; height:.72in; object-fit:contain; }

    /* ==== blok konten kiri ==== */
    .content{ position:absolute; left:.55in; top:1.55in; width:6.8in; }
    .program{
      margin:0 0 .12in 0; color:var(--ink);
      font-size:.26in; font-weight:900; letter-spacing:.3px; text-transform:uppercase;
    }
    .lead   { margin:.10in 0 .06in; font-size:.18in; color:var(--muted); }
    .name   { margin:0 0 .06in; font-size:.34in; font-weight:900; color:var(--ink); }
    .desc   { margin:.02in 0; font-size:.18in; color:#1F2937; }
    .muted  { color:var(--muted); }

    /* ==== tanda tangan kiri bawah ==== */
    .sign{ position:absolute; left:.55in; bottom:1.18in; width:3.9in; }
    .sign img{ display:block; max-width:2.9in; max-height:1.1in; }
    .sign .nm{ margin:.06in 0 0; font-weight:800; color:var(--ink); }
    .sign .rl{ margin:.02in 0 0; font-size:.16in; color:var(--muted); }

    /* ==== footer nomor serial (tengah bawah) ==== */
    .footer{ position:absolute; left:.55in; right:.55in; bottom:.6in; text-align:center; color:var(--muted); font-size:.15in; }
  </style>
</head>
<body>
@php
  // Pakai gambar yang sama untuk logo & watermark
  $brandRel  = 'assets/images/foto-berkemah.png';
  $brandPath = public_path($brandRel);    // aman untuk DomPDF tanpa remote
  $brandUrl  = asset($brandRel);          // jika dompdf.isRemoteEnabled = true

  // Tentukan sumber <img>: prioritas file path (lebih stabil di DomPDF)
  $brandImg  = file_exists($brandPath) ? $brandPath : $brandUrl;

  // TTD opsional dari template
  $signUrl = $template->sign_left_image_url ?? null;
  if ($signUrl && !preg_match('#^https?://#',$signUrl)) $signUrl = public_path($signUrl);

  $signName = $template->sign_left_name  ?? 'Raul Mahya';
  $signRole = $template->sign_left_title ?? 'CEO Berkemah';

  $issuedAt = isset($issued_at) ? $issued_at->timezone('Asia/Jakarta') : now('Asia/Jakarta');
  $orgName  = config('app.name', 'Berkemah');
@endphp

<div class="page">
  {{-- aksen dekoratif pojok --}}
  <div class="accent-top"></div>
  <div class="accent-left"></div>
  <div class="accent-bottom"></div>
  <div class="accent-right"></div>

  {{-- watermark kanan --}}
  <div class="bg-photo">
    <img src="{{ $brandImg }}" alt="Watermark">
  </div>

  {{-- header kanan --}}
  <div class="right-head">
    <h1 class="title">SERTIFIKAT</h1>
    <div class="sub">Kursus Online</div>
  </div>

  {{-- logo kiri atas --}}
  <img src="{{ $brandImg }}" class="logo" alt="Logo BERKEMAH">

  {{-- konten kiri --}}
  <div class="content">
    <h2 class="program">{{ mb_strtoupper($course->title) }}</h2>

    <div class="lead">Sertifikat ini diberikan kepada</div>
    <div class="name">{{ $user->name }}</div>

    <p class="desc">Sebagai apresiasi atas penyelesaian program dengan hasil memuaskan.</p>
    <p class="desc muted" style="margin-top:.05in;">Pada tanggal {{ $issuedAt->format('d F Y') }}</p>
    <p class="desc muted" style="margin-top:.02in;">Program {{ $orgName }}</p>
  </div>

  {{-- tanda tangan --}}
  <div class="sign">
    @if($signUrl)
      <img src="{{ $signUrl }}" alt="Tanda Tangan">
    @endif
    <div class="nm">{{ $signName }}</div>
    <div class="rl">{{ $signRole }}</div>
  </div>

  {{-- nomor serial --}}
  <div class="footer">No. Serial: {{ $serial }}</div>
</div>
</body>
</html>
