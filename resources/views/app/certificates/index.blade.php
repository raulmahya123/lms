@extends('layouts.app')
@section('title','Sertifikat Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
        Prestasi
      </div>
      <h1 class="text-3xl font-extrabold text-text-main">Sertifikat Saya</h1>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('app.courses.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Ikuti Kursus Baru
      </a>
    </div>
  </div>

  @if($issues->count())
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
      
      {{-- Mobile Cards --}}
      <div class="grid gap-4 sm:hidden p-4 bg-gray-50/50">
        @foreach ($issues as $issue)
          <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex justify-between items-start mb-3">
              <div class="font-bold text-text-main text-lg">{{ optional($issue->course)->title ?? 'Kursus Tidak Diketahui' }}</div>
            </div>
            
            <div class="space-y-2 mb-4 text-sm font-medium text-text-soft">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-mono text-xs">{{ $issue->serial }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>{{ optional($issue->issued_at)->format('d M Y, H:i') ?? '—' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                <span>Skor: <strong class="text-text-main">{{ is_numeric($issue->score) ? number_format($issue->score,0) : $issue->score }}%</strong></span>
              </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-50">
              <a href="{{ route('app.certificates.show', $issue) }}" class="flex-1 text-center py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-sm hover:bg-gray-50">Detail</a>
              <a href="{{ route('app.certificates.preview', $issue) }}" target="_blank" class="flex-1 text-center py-2 bg-tosca-light text-tosca-dark font-bold rounded-lg text-sm hover:bg-tosca-light/80">Preview</a>
              <a href="{{ route('app.certificates.download', $issue) }}" class="w-full text-center py-2 bg-tosca text-white font-bold rounded-lg text-sm hover:bg-tosca-dark">Download PDF</a>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Desktop Table --}}
      <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-text-soft">
              <th class="px-6 py-4 font-bold">Serial Number</th>
              <th class="px-6 py-4 font-bold">Kursus</th>
              <th class="px-6 py-4 font-bold">Skor</th>
              <th class="px-6 py-4 font-bold">Tanggal Terbit</th>
              <th class="px-6 py-4 font-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @foreach ($issues as $issue)
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                  <span class="font-mono text-sm font-bold text-text-main bg-gray-100 px-2 py-1 rounded">{{ $issue->serial }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-bold text-text-main">{{ optional($issue->course)->title ?? 'Kursus Tidak Diketahui' }}</div>
                  <div class="text-xs text-text-soft font-medium mt-0.5">ID: #{{ $issue->course_id }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-tosca-light text-tosca-dark font-extrabold text-sm">
                    {{ is_numeric($issue->score) ? number_format($issue->score,0) : $issue->score }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-text-soft whitespace-nowrap">
                  {{ optional($issue->issued_at)->format('d M Y, H:i') ?? '—' }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('app.certificates.show', $issue) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg text-xs hover:bg-gray-50 hover:border-gray-300 transition-colors" title="Detail">
                      Detail
                    </a>
                    <a href="{{ route('app.certificates.preview', $issue) }}" target="_blank" class="px-4 py-2 bg-tosca-light text-tosca-dark font-bold rounded-lg text-xs hover:bg-tosca-light/80 transition-colors" title="Preview">
                      Preview
                    </a>
                    <a href="{{ route('app.certificates.download', $issue) }}" class="px-4 py-2 bg-tosca text-white font-bold rounded-lg text-xs hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-1.5" title="Download">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                      PDF
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="px-6 py-4 border-t border-gray-50">
        {{ $issues->withQueryString()->links() }}
      </div>
    </div>
  @else
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-12 text-center flex flex-col items-center justify-center">
      <div class="w-24 h-24 rounded-full bg-softbg flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
      </div>
      <h3 class="text-2xl font-bold text-text-main mb-2">Belum Ada Sertifikat</h3>
      <p class="text-text-soft mb-8 max-w-md">Anda belum memiliki sertifikat. Selesaikan kursus dan kerjakan penilaian akhir untuk mendapatkan sertifikat kelulusan.</p>
      <a href="{{ route('app.courses.index') }}" class="px-8 py-3.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/20 flex items-center gap-2">
        Mulai Belajar Sekarang
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </a>
    </div>
  @endif

</div>
@endsection
