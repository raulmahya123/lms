@extends('layouts.app')
@section('title','Detail Sertifikat')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
        Prestasi
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Detail Sertifikat</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('app.certificates.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
      <a href="{{ route('app.certificates.download', $issue) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Download PDF
      </a>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-8 items-start">
    {{-- Sidebar Kiri: Info Sertifikat --}}
    <div class="lg:col-span-1 space-y-6">
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-softbg/30">
          <h2 class="text-lg font-bold text-text-main flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-tosca-light text-tosca flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            Informasi Sertifikat
          </h2>
        </div>
        
        <div class="p-6 space-y-5">
          <div>
            <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Nomor Serial</div>
            <div class="flex items-center justify-between gap-2">
              <div id="serialText" class="font-mono text-sm font-bold text-text-main bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 break-all flex-1">{{ $issue->serial }}</div>
              <button type="button" id="btnCopySerial" class="shrink-0 p-2 text-gray-400 hover:text-tosca hover:bg-gray-50 rounded-lg transition-colors border border-transparent hover:border-gray-200" title="Salin Serial">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
              </button>
            </div>
          </div>

          <div>
            <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Nama Kursus</div>
            <div class="font-bold text-text-main leading-tight">{{ optional($issue->course)->title ?? '—' }}</div>
            <div class="text-xs text-text-soft font-medium mt-0.5">ID Kursus: #{{ $issue->course_id }}</div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Skor Kelulusan</div>
              <div class="text-2xl font-extrabold text-tosca-dark">{{ is_numeric($issue->score) ? number_format($issue->score,0) : $issue->score }}<span class="text-lg">%</span></div>
            </div>
            <div>
              <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Tanggal Terbit</div>
              <div class="font-bold text-text-main">{{ optional($issue->issued_at)->format('d M Y') ?? '—' }}</div>
              <div class="text-xs text-text-soft font-medium mt-0.5">{{ optional($issue->issued_at)->format('H:i') ?? '' }}</div>
            </div>
          </div>

          <div class="pt-5 border-t border-gray-50">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Jenis Penilaian</div>
                <div class="font-bold text-text-main">{{ ucfirst($issue->assessment_type) }}</div>
              </div>
              <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Template</div>
                <div class="font-bold text-text-main truncate" title="{{ optional($issue->template)->name ?? ('ID: '.$issue->template_id) }}">{{ optional($issue->template)->name ?? ('ID: '.$issue->template_id) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3 text-sm font-medium text-blue-800">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p>Gunakan tombol Download PDF untuk menyimpan dan mencetak sertifikat dengan kualitas terbaik. Preview di sebelah kanan bergantung pada pengaturan browser Anda.</p>
      </div>
    </div>

    {{-- Kolom Kanan: Preview Sertifikat --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden h-full flex flex-col">
        <div class="px-6 py-4 border-b border-gray-50 bg-softbg/30 flex items-center justify-between">
          <h2 class="text-lg font-bold text-text-main flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            Preview Sertifikat
          </h2>
          <a href="{{ route('app.certificates.preview', $issue) }}" target="_blank" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-xs hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Buka di Tab Baru
          </a>
        </div>
        
        <div class="flex-1 bg-gray-100 relative min-h-[500px] lg:min-h-[700px] p-4 flex items-center justify-center">
          <iframe
            src="{{ route('app.certificates.preview', $issue) }}"
            class="w-full h-full border-0 bg-white rounded-xl shadow-lg"
            title="Preview Sertifikat"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnCopySerial');
  const serialText = document.getElementById('serialText')?.textContent?.trim() || '';
  if (btn && serialText) {
    btn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(serialText);
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        btn.classList.add('bg-green-50', 'border-green-200');
        setTimeout(() => {
          btn.innerHTML = originalHtml;
          btn.classList.remove('bg-green-50', 'border-green-200');
        }, 1500);
      } catch (e) {
        console.error(e);
        alert('Gagal menyalin nomor serial.');
      }
    });
  }
});
</script>
@endpush
