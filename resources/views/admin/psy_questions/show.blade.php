{{-- resources/views/admin/psy_questions/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Soal — Admin')

@section('content')
@php
  /** @var \App\Models\PsyQuestion $question */
@endphp

<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        Detail Informasi
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Detail Pertanyaan</h1>
      <p class="text-sm text-text-soft mt-1">Lihat teks pertanyaan dan daftar opsi jawaban yang tersedia.</p>
    </div>
    
    <div class="shrink-0 flex items-center gap-3">
      <a href="{{ route('admin.psy-questions.index', request()->only(['psy_test_id','q','qtype','trait','page'])) }}"
         class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
      </a>
      <a href="{{ route('admin.psy-questions.edit', $question) }}"
         class="inline-flex items-center gap-2 px-5 py-2.5 bg-tosca text-white font-bold rounded-xl hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Edit Soal
      </a>
    </div>
  </div>

  <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    
    {{-- Meta Badges --}}
    <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
            Tes: {{ Str::limit($question->test->name ?? '— Tidak Ada Tes —', 30) }}
        </span>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-violet-50 text-violet-700 border border-violet-100">
            Tipe: {{ $question->qtype ?? '—' }}
        </span>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
            Trait: {{ $question->trait_key ?? '—' }}
        </span>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
            Urutan: {{ $question->ordering ?? 0 }}
        </span>
    </div>

    <div class="p-8 space-y-8">
        {{-- Prompt Section --}}
        <div>
            <h2 class="text-sm font-bold text-text-soft uppercase tracking-wider mb-3">Teks Pertanyaan</h2>
            <div class="p-6 bg-gray-50 border border-gray-100 rounded-2xl text-lg font-medium text-text-main leading-relaxed">
                {{ $question->prompt }}
            </div>
        </div>

        {{-- Options Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-text-soft uppercase tracking-wider">Opsi Jawaban & Skor</h2>
            </div>

            @php
                $options = $question->relationLoaded('options') ? $question->options : $question->options()->get();
                $options = $options->sortBy('ordering')->values();
            @endphp

            @if($options->isEmpty())
                <div class="p-6 bg-gray-50 border border-gray-100 rounded-2xl text-center text-gray-500 font-medium">
                    Belum ada opsi jawaban untuk soal ini.
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                            <tr class="bg-gray-100/50 border-b border-gray-200 text-xs font-bold text-text-soft uppercase tracking-wider">
                                <th class="px-6 py-4 w-16 text-center">#</th>
                                <th class="px-6 py-4">Teks Pilihan</th>
                                <th class="px-6 py-4 w-32 text-center">Nilai (Value)</th>
                                <th class="px-6 py-4 w-32 text-center">Skor Tambahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($options as $opt)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-center font-bold text-gray-400">
                                        {{ $opt->ordering ?? $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-text-main">
                                            {{ $opt->label ?? $opt->text ?? $opt->value ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[3.5rem] h-8 rounded-lg bg-blue-50 border border-blue-100 font-bold text-blue-700 text-sm px-2">
                                            {{ $opt->value ?? '0' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[3.5rem] h-8 rounded-lg bg-gray-50 border border-gray-200 font-bold text-text-main text-sm px-2">
                                            {{ $opt->score ?? $opt->weight ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Meta Information --}}
        <div class="pt-8 border-t border-gray-100 grid sm:grid-cols-2 gap-6">
            <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">ID Soal (UUID)</div>
                <div class="text-sm font-mono font-medium text-gray-600 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100 break-all">
                    {{ $question->id }}
                </div>
            </div>
            <div>
                <div class="text-xs font-bold text-text-soft uppercase tracking-wider mb-2">Waktu Perubahan</div>
                <div class="text-sm font-medium text-text-main space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-text-soft">Dibuat:</span>
                        <span>{{ $question->created_at?->format('d M Y, H:i') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-text-soft">Diperbarui:</span>
                        <span>{{ $question->updated_at?->format('d M Y, H:i') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="p-6 sm:p-8 bg-gray-50/50 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 text-sm font-medium text-text-soft">
            <svg class="w-12 h-12 text-red-200 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
            <p>Hati-hati: Menghapus soal juga akan menghapus secara permanen semua opsi jawaban yang terkait dengan soal ini.</p>
        </div>

        <div class="shrink-0 w-full sm:w-auto">
            @php
                $delRoute = isset($question->test_id)
                    ? route('admin.psy-tests.questions.destroy', [$question->test_id, $question->id])
                    : route('admin.psy-questions.destroy', $question->id);
            @endphp
            <form method="POST" action="{{ $delRoute }}" class="js-delete-form w-full sm:w-auto" data-title="soal ini">
                @csrf @method('DELETE')
                <button type="button" class="js-delete-btn w-full sm:w-auto px-6 py-3 rounded-xl border border-red-200 bg-white text-red-600 font-bold hover:bg-red-50 hover:border-red-300 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Soal
                </button>
            </form>
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
                    const title = form?.dataset.title || 'soal ini';

                    Swal.fire({
                        title: 'Hapus Soal Psikologi?',
                        html: `<b>${title}</b> beserta opsi jawabannya akan dihapus permanen.`,
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
                                    b.innerHTML = '<span class="animate-spin mr-2">⏳</span>';
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
