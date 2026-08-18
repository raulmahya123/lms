@extends('layouts.admin')
@section('title', 'Detail Riwayat Tes — Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Laporan Hasil
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Riwayat #{{ $attempt->id }}</h1>
      <p class="text-sm text-text-soft mt-1">Laporan pengerjaan tes psikologi peserta dan rincian jawabannya.</p>
    </div>
    
    <div class="shrink-0 flex items-center gap-3">
      <a href="{{ route('admin.psy-attempts.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    {{-- Kolom Kiri: Informasi Attempt & Laporan --}}
    <div class="lg:col-span-1 space-y-6">
      
      {{-- Card: Profil Peserta & Info Tes --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
            <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Peserta
            </h2>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Peserta</div>
                <div class="font-bold text-text-main text-lg">{{ $attempt->user?->name ?? '—' }}</div>
                <div class="text-sm text-text-soft">{{ $attempt->user?->email ?? 'Email tidak ditemukan' }}</div>
            </div>

            <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Tes Psikologi</div>
                <div class="font-bold text-blue-600 bg-blue-50 px-3 py-2 rounded-xl inline-block text-sm border border-blue-100">
                    {{ $attempt->test?->title ?? '—' }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Status</div>
                    @if($attempt->submitted_at)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Selesai
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> In Progress
                        </span>
                    @endif
                </div>
                <div>
                    <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-1">Kunci Profil</div>
                    <div class="font-mono text-sm font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200 inline-block">
                        {{ $attempt->result_key ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
      </div>

      {{-- Card: Waktu & Durasi --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
            <h2 class="text-base font-bold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Waktu Pengerjaan
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <div class="text-sm font-bold text-text-soft">Waktu Mulai</div>
                <div class="text-sm font-bold text-text-main text-right">
                    {{ $attempt->started_at?->format('d M Y, H:i:s') ?? '—' }}
                </div>
            </div>
            
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <div class="text-sm font-bold text-text-soft">Waktu Submit</div>
                <div class="text-sm font-bold text-text-main text-right">
                    {{ $attempt->submitted_at?->format('d M Y, H:i:s') ?? '—' }}
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-sm font-bold text-text-soft">Total Durasi</div>
                <div class="text-lg font-extrabold text-tosca tabular-nums">
                    @if(!is_null($durationSeconds))
                        @php
                        $h = floor($durationSeconds/3600);
                        $m = floor(($durationSeconds%3600)/60);
                        $s = $durationSeconds%60;
                        @endphp
                        {{ sprintf('%02d:%02d:%02d',$h,$m,$s) }}
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>
      </div>

      {{-- Card: Action Hapus --}}
      <div class="bg-white rounded-3xl border border-red-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-6">
            <h2 class="text-base font-bold text-red-600 mb-2">Hapus Riwayat</h2>
            <p class="text-xs text-text-soft mb-4">Tindakan ini akan menghapus riwayat tes beserta seluruh detail jawaban secara permanen.</p>
            <form method="POST" action="{{ route('admin.psy-attempts.destroy', $attempt) }}" class="js-delete-form" data-title="Riwayat #{{ $attempt->id }}">
                @csrf @method('DELETE')
                <button type="button" class="js-delete-btn w-full px-6 py-3 rounded-xl border border-red-200 bg-red-50 text-red-600 font-bold hover:bg-red-600 hover:text-white transition-colors flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Data
                </button>
            </form>
        </div>
      </div>
    </div>

    {{-- Kolom Kanan: Hasil & Jawaban --}}
    <div class="lg:col-span-2 space-y-6">
      
      {{-- Rekomendasi / Kesimpulan --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-tosca"></div>
        <div class="p-8">
            <h2 class="text-xl font-extrabold text-text-main flex items-center gap-2 mb-4">
                <svg class="w-6 h-6 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Kesimpulan & Rekomendasi
            </h2>
            
            <div class="prose max-w-none text-text-main bg-gray-50 p-5 rounded-2xl border border-gray-100 font-medium leading-relaxed">
                @if(!empty($attempt->recommendation_text))
                    {!! nl2br(e($attempt->recommendation_text)) !!}
                @else
                    <span class="text-gray-400 italic">Belum ada kesimpulan atau rekomendasi yang dihasilkan.</span>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-bold text-text-soft uppercase tracking-wider mb-3">Detail Skor (JSON)</h3>
                @if(is_array($attempt->score_json) && count($attempt->score_json))
                    <pre class="text-xs bg-gray-900 text-gray-100 rounded-xl p-4 overflow-x-auto font-mono leading-relaxed border border-gray-800">{{ json_encode($attempt->score_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm font-medium text-gray-400 italic">Tidak ada rincian skor.</p>
                @endif
            </div>
        </div>
      </div>

      {{-- Log Jawaban --}}
      <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gradient-to-r from-gray-50/50 to-white flex items-center justify-between">
            <h2 class="text-xl font-extrabold text-text-main flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                Log Jawaban Peserta
            </h2>
            <div class="text-sm font-bold text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                Total: {{ $attempt->answers->count() }} Soal
            </div>
        </div>
        
        <div class="p-8 bg-gray-50/30">
            @if($attempt->answers->count())
                <div class="space-y-4">
                    @foreach($attempt->answers as $ans)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:border-blue-200 transition-colors group">
                            
                            {{-- Pertanyaan --}}
                            <div class="flex items-start gap-4 mb-4">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold flex items-center justify-center text-sm border border-blue-100 mt-0.5">
                                    {{ $ans->question?->ordering ?? '-' }}
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-text-main leading-relaxed">
                                        {{ $ans->question?->prompt ?? '(Pertanyaan tidak ditemukan atau telah dihapus)' }}
                                    </h3>
                                </div>
                            </div>

                            {{-- Jawaban --}}
                            <div class="ml-12 pl-4 border-l-2 border-gray-100">
                                @if($ans->option)
                                    <div class="flex flex-col gap-2">
                                        <div class="inline-flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="font-bold text-text-main bg-green-50 px-3 py-1 rounded-lg border border-green-100">
                                                {{ $ans->option->label }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 mt-1">
                                            @if(!is_null($ans->option->value))
                                                <div class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded uppercase tracking-wider">
                                                    Nilai: <span class="text-gray-900">{{ $ans->option->value }}</span>
                                                </div>
                                            @endif
                                            @if(!is_null($ans->option->weight))
                                                <div class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded uppercase tracking-wider">
                                                    Bobot: <span class="text-gray-900">{{ $ans->option->weight }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif(isset($ans->value))
                                    <div class="inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span class="font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                                            {{ $ans->value }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-gray-400 italic flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Tidak ada jawaban atau terlewat.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-text-main mb-1">Belum Ada Jawaban</h3>
                    <p class="text-text-soft">Peserta ini belum menjawab pertanyaan apapun.</p>
                </div>
            @endif
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        function bindDeleteButtons() {
            document.querySelectorAll('.js-delete-btn').forEach(btn => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = '1';

                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form.js-delete-form');
                    const title = form?.dataset.title || 'data ini';

                    Swal.fire({
                        title: 'Hapus Riwayat Tes?',
                        html: `<b>${title}</b> beserta seluruh rincian jawabannya akan dihapus secara permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl font-bold px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white border-0',
                            cancelButton: 'rounded-xl font-bold px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border-0'
                        }
                    }).then((res) => {
                        if (res.isConfirmed) {
                            if (!form.dataset.submitting) {
                                form.dataset.submitting = '1';
                                const b = form.querySelector('.js-delete-btn');
                                if (b) {
                                    b.disabled = true;
                                    b.innerHTML = '<span class="animate-spin mr-2">⏳</span> Menghapus...';
                                }
                                form.submit();
                            }
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', bindDeleteButtons);
        document.addEventListener('turbo:load', bindDeleteButtons);
        document.addEventListener('livewire:navigated', bindDeleteButtons);
    })();
</script>
@endpush
@endsection
