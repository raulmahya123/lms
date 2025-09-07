@extends('app.layouts.base')

@section('title','Detail Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Detail Sertifikat</h1>
    <div class="flex items-center gap-2">
      <a href="{{ route('app.certificates.preview', $issue) }}" target="_blank"
         class="px-3 py-1.5 rounded-lg border">Preview</a>
      <a href="{{ route('app.certificates.download', $issue) }}"
         class="px-3 py-1.5 rounded-lg bg-gray-900 text-white">Download</a>
    </div>
  </div>

  <div class="bg-white rounded-xl shadow-sm p-6">
    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="font-medium mb-3">Informasi</h2>
        <table class="w-full text-sm">
          <tr>
            <td class="py-2 text-gray-500 w-40">Serial</td>
            <td class="py-2 font-mono">{{ $issue->serial }}</td>
          </tr>
          <tr>
            <td class="py-2 text-gray-500">Kursus</td>
            <td class="py-2">{{ optional($issue->course)->title ?? '—' }} (ID: {{ $issue->course_id }})</td>
          </tr>
          <tr>
            <td class="py-2 text-gray-500">Skor</td>
            <td class="py-2">{{ is_numeric($issue->score) ? number_format($issue->score,2) : $issue->score }}%</td>
          </tr>
          <tr>
            <td class="py-2 text-gray-500">Tanggal Terbit</td>
            <td class="py-2">{{ optional($issue->issued_at)->format('d M Y, H:i') ?? '—' }}</td>
          </tr>
          <tr>
            <td class="py-2 text-gray-500">Template</td>
            <td class="py-2">{{ optional($issue->template)->name ?? ('ID: '.$issue->template_id) }}</td>
          </tr>
          <tr>
            <td class="py-2 text-gray-500">Jenis</td>
            <td class="py-2">{{ ucfirst($issue->assessment_type) }}</td>
          </tr>
        </table>
      </div>

      <div>
        <h2 class="font-medium mb-3">Aksi</h2>
        <div class="space-y-2">
          <a href="{{ route('app.certificates.preview', $issue) }}" target="_blank"
             class="inline-block px-3 py-2 rounded-lg border">Buka Preview</a>
          <a href="{{ route('app.certificates.download', $issue) }}"
             class="inline-block px-3 py-2 rounded-lg bg-gray-900 text-white">Download PDF</a>
        </div>
        <p class="text-xs text-gray-500 mt-4">
          Jika preview tidak muncul (karena blokir pop-up), gunakan tombol “Download PDF”.
        </p>
      </div>
    </div>
  </div>

  {{-- Opsional: sematkan preview dalam iframe jika ingin inline --}}
  <div class="bg-white rounded-xl shadow-sm p-4">
    <h2 class="font-medium mb-3">Preview Inline</h2>
    <iframe
      src="{{ route('app.certificates.preview', $issue) }}"
      class="w-full h-[70vh] border rounded-lg"
    ></iframe>
  </div>
</div>
@endsection
