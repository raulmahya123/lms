<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 0; }
    body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; color: #111; }
    .wrap { width: 100%; height: 100vh; padding: 40px; box-sizing: border-box;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); }
    .card {
      border: 8px double #1f2937; border-radius: 24px; padding: 40px;
      height: calc(100vh - 80px); box-sizing: border-box;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      text-align: center;
    }
    .title { font-size: 40px; font-weight: 700; letter-spacing: .03em; margin-bottom: 8px; }
    .sub { font-size: 18px; color: #374151; margin-bottom: 36px; }
    .name { font-size: 36px; font-weight: 700; margin: 8px 0 4px; }
    .course { font-size: 22px; font-weight: 600; color: #111827; margin-bottom: 20px; }
    .meta { font-size: 12px; color: #6b7280; margin-top: 24px; }
    .serial { font-size: 12px; letter-spacing: .08em; color: #4b5563; margin-top: 8px; }
    .score { font-size: 16px; color: #111827; margin-top: 6px; }
    .sign-row { display: flex; gap: 48px; margin-top: 36px; justify-content: center; }
    .sign { width: 260px; border-top: 1px solid #9ca3af; padding-top: 8px; font-size: 12px; color: #374151; }
    .badge { margin-top: 18px; font-size: 12px; color: #065f46; background: #d1fae5; display:inline-block; padding:6px 10px; border-radius: 8px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="title">SERTIFIKAT KELULUSAN</div>
      <div class="sub">Dengan ini menyatakan bahwa</div>
      <div class="name">{{ $user->name }}</div>
      <div class="sub">telah berhasil menyelesaikan kursus</div>
      <div class="course">“{{ $course->title }}”</div>

      @if($bestAttempt)
        <div class="score">Skor terbaik kuis: <strong>{{ $bestAttempt->score }}</strong></div>
      @endif

      <div class="badge">Diterbitkan pada: {{ $issued_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</div>

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
