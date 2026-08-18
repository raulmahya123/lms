{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    :root {
      --primary: #0F9D8A; /* Tosca */
      --primary-dark: #075E54; /* Deep Tosca */
      --text-main: #111827;
      --text-soft: #6b7280;
    }

    @page { size: letter landscape; margin: 0; }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: var(--text-main);
      font-size: .2in;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    .certificate {
      position: relative;
      width: 11in;
      height: 8.5in;
      overflow: hidden;
      background: #fff;
      background-image: url('file://{{ public_path("assets/images/certificate01.png") }}');
      background-size: 100% 100%;
      background-position: center;
      background-repeat: no-repeat;
    }

    /* Header */
    .certificate-head {
      position: absolute;
      top: .8in;
      right: 1.2in;
      text-align: right;
    }
    .certificate-head .title {
      font-size: .7in;
      margin: 0;
      font-weight: 900;
      letter-spacing: .08in;
      color: var(--primary-dark);
      text-transform: uppercase;
    }
    .certificate-head .subtitle {
      font-size: .25in;
      margin-top: -.05in;
      font-weight: 600;
      letter-spacing: .15in;
      color: var(--primary);
      text-transform: uppercase;
    }

    /* Body */
    .certificate-body {
      position: absolute;
      top: 2.8in;
      left: 1.2in;
      right: 1.2in;
      text-align: left;
    }
    .certificate-body .program {
      font-size: .45in;
      margin: 0;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.1;
      max-width: 8in;
    }
    .certificate-body .lead {
      font-size: .22in;
      margin-top: .4in;
      margin-bottom: 0;
      color: var(--text-soft);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: .05in;
    }
    .certificate-body .name {
      font-size: .55in;
      margin: .1in 0 0 0;
      font-weight: 900;
      text-transform: uppercase;
      color: var(--text-main);
      border-bottom: 3px solid var(--primary);
      display: inline-block;
      padding-bottom: .05in;
    }
    .certificate-body .desc,
    .certificate-body .issued {
      font-size: .22in;
      margin: .2in 0 0 0;
      color: var(--text-main);
      font-weight: 500;
    }

    /* Signature kiri bawah (absolute) */
    .sign {
      position: absolute;
      bottom: 1in;
      left: 1.2in;
      text-align: left;
    }
    .sign img {
      height: 1.2in;
      object-fit: contain;
      margin-left: -.2in;
    }
    .sign .sign-name {
      font-size: .22in;
      font-weight: 800;
      margin-top: .05in;
      color: var(--text-main);
      text-transform: uppercase;
      letter-spacing: .02in;
      border-top: 1px solid var(--text-soft);
      padding-top: .05in;
      width: 3in;
    }
    .sign .sign-role {
      font-size: .18in;
      margin-top: .02in;
      color: var(--primary);
      font-weight: 600;
    }

    /* Footer (serial kanan bawah) */
    .certificate-footer {
      position: absolute;
      bottom: 1in;
      right: 1.2in;
      text-align: right;
    }
    .certificate-footer .serial {
      font-size: .18in;
      color: var(--text-soft);
      font-family: monospace;
      font-weight: bold;
      background: #f3f4f6;
      padding: .05in .1in;
      border-radius: .05in;
    }
  </style>
</head>
<body>
  @php
    $orgName    = config('app.name');
    $issuedAt   = isset($issued_at) ? $issued_at->timezone('Asia/Jakarta') : now('Asia/Jakarta');
    $percentFmt = isset($percent) ? number_format((float)$percent, 0) : null;

    // file tanda tangan CEO
    $ceoSign = public_path('assets/images/sign_ceo.png');
  @endphp

  <div class="certificate">
    <!-- Header -->
    <div class="certificate-head">
      <h1 class="title">SERTIFIKAT</h1>
      <p class="subtitle">Pencapaian</p>
    </div>

    <!-- Body -->
    <div class="certificate-body">
      <h2 class="program">{{ $course->title }}</h2>
      <p class="lead">Diberikan Kepada</p>
      <p class="name">{{ $user->name }}</p>
      <p class="desc">Telah berhasil menyelesaikan seluruh rangkaian materi kursus dan ujian akhir dengan sangat baik.</p>

      @if($percentFmt !== null)
        <p class="desc">Skor Akhir Kelulusan: <strong style="color: var(--primary);">{{ $percentFmt }}%</strong></p>
      @endif

      <p class="issued">Tanggal Terbit: {{ $issuedAt->format('d F Y') }}</p>
    </div>

    <!-- Signature CEO di kiri bawah -->
    <div class="sign">
      <img src="{{ $ceoSign }}" alt="Tanda Tangan CEO">
      <div class="sign-name">RAUL MAHYA KOMARAN</div>
      <div class="sign-role">Chief Executive Officer</div>
    </div>

    <!-- Footer Serial di kanan bawah -->
    <div class="certificate-footer">
      <div class="serial">SN: {{ $serial }}</div>
    </div>
  </div>
</body>
</html>
