@extends('app.layouts.base')

@section('title','Sertifikat Saya')

@push('styles')
<style>
  .hover-lift{transition:transform .2s ease, box-shadow .2s ease}
  .hover-lift:hover{transform:translateY(-2px); box-shadow:0 12px 36px rgba(2,6,23,.12)}
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Sertifikat Saya</h1>
  </div>

  <div class="bg-white shadow-sm rounded-xl overflow-hidden soft-border">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-left text-gray-600">
            <th class="px-4 py-3">Serial</th>
            <th class="px-4 py-3">Kursus</th>
            <th class="px-4 py-3">Skor</th>
            <th class="px-4 py-3">Tanggal Terbit</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse ($issues as $issue)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 font-mono">{{ $issue->serial }}</td>
              <td class="px-4 py-3">
                {{ optional($issue->course)->title ?? '—' }}
                <div class="text-xs text-gray-500">#{{ $issue->course_id }}</div>
              </td>
              <td class="px-4 py-3">
                {{ is_numeric($issue->score) ? number_format($issue->score,2) : $issue->score }}%
              </td>
              <td class="px-4 py-3">
                {{ optional($issue->issued_at)->format('d M Y, H:i') ?? '—' }}
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2 justify-end">
                  <a href="{{ route('app.certificates.show', $issue) }}"
                     class="px-3 py-1.5 rounded-lg border hover-lift">Lihat</a>
                  <a href="{{ route('app.certificates.preview', $issue) }}"
                     target="_blank"
                     class="px-3 py-1.5 rounded-lg border hover-lift">Preview</a>
                  <a href="{{ route('app.certificates.download', $issue) }}"
                     class="px-3 py-1.5 rounded-lg bg-gray-900 text-white hover-lift">Download</a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                Belum ada sertifikat yang terbit.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t">
      {{ $issues->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
