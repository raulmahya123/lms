{{-- resources/views/app/certificates/course.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    :root {
      --primary: #0098DA;
      --text: #333;
    }

    @page { size: letter landscape; margin: 0; }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Times New Roman', serif;
      color: var(--text);
      font-size: .2in;
      line-height: 1.5;
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
      top: .6in;
      right: 1in;
      text-align: right;
    }
    .certificate-head .title {
      font-size: .7in;
      margin: 0;
      font-weight: bold;
      letter-spacing: .03in;
      color: var(--primary);
    }
    .certificate-head .subtitle {
      font-size: .3in;
      margin-top: -.1in;
      font-style: italic;
      color: var(--primary);
    }

    /* Body */
    .certificate-body {
      position: absolute;
      top: 2.5in;
      left: 1in;
      right: 1in;
      text-align: left;
    }
    .certificate-body .program {
      font-size: .45in;
      margin: 0;
      font-weight: bold;
      text-transform: uppercase;
      color: var(--text);
    }
    .certificate-body .lead {
      font-size: .25in;
      margin-top: .1in;
      margin-bottom: 0;
      color: var(--text);
    }
    .certificate-body .name {
      font-size: .4in;
      margin: .1in 0 0 0;
      font-weight: bold;
      text-transform: uppercase;
      color: var(--text);
    }
    .certificate-body .desc,
    .certificate-body .issued {
      font-size: .2in;
      margin: .1in 0 0 0;
      color: var(--text);
    }

    /* Signature kiri bawah (absolute) */
    .sign {
      position: absolute;
      bottom: 1in;
      left: 1in;
      text-align: left;
    }
    .sign img {
      height: 1in;
      object-fit: contain;
    }
    .sign .sign-name {
      font-size: .22in;
      font-weight: bold;
      margin-top: .05in;
      color: var(--text);
      text-transform: uppercase;
    }
    .sign .sign-role {
      font-size: .18in;
      margin-top: .02in;
      color: var(--text);
    }

    /* Footer (serial kanan bawah) */
    .certificate-footer {
      position: absolute;
      bottom: .8in;
      right: 1in;
      text-align: right;
    }
    .certificate-footer .serial {
      font-size: .16in;
      color: var(--text);
    }
  </style>
</head>
<body>
  @php
    $orgName    = config('app.name');
    $issuedAt   = isset($issued_at) ? $issued_at->timezone('Asia/Jakarta') : now('Asia/Jakarta');
    $percentFmt = isset($percent) ? number_format((float)$percent, 2) : null;

    // file tanda tangan CEO
    $ceoSign = public_path('assets/images/sign_ceo.png');
  @endphp

  <div class="certificate">
    <!-- Header -->
    <div class="certificate-head">
      <h1 class="title">SERTIFIKAT</h1>
      <p class="subtitle">Kelulusan</p>
    </div>

    <!-- Body -->
    <div class="certificate-body">
      <h2 class="program">{{ $course->title }}</h2>
      <p class="lead">Diberikan kepada</p>
      <p class="name">{{ $user->name }}</p>
      <p class="desc">atas keberhasilan menyelesaikan program tersebut.</p>

      @if($percentFmt !== null)
        <p class="desc">Ketuntasan otomatis: <strong>{{ $percentFmt }}%</strong></p>
      @endif

      <p class="issued">Diterbitkan: {{ $issuedAt->format('d M Y, H:i') }} WIB</p>
    </div>

    <!-- Signature CEO di kiri bawah -->
    <div class="sign">
      <img src="{{ $ceoSign }}" alt="Tanda tangan CEO">
      <div class="sign-name">RAUL MAHYA KOMARAN</div>
      <div class="sign-role">Chief Executive Officer</div>
    </div>

    <!-- Footer Serial di kanan bawah -->
    <div class="certificate-footer">
      <div class="serial">No. Serial: {{ $serial }}</div>
    </div>
  </div>
</body>
</html>
