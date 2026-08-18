@extends('layouts.admin')
@section('title', 'Edit Opsi Jawaban — Admin')

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Opsi #{{ $option->id }}
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Opsi Jawaban</h1>
            <p class="text-sm text-text-soft mt-1">Perbarui teks atau status kebenaran opsi ini.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.options.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if (session('ok') || session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex gap-3 text-sm text-green-800 shadow-sm mb-6">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">{{ session('ok') ?? session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden mb-8">
        <form action="{{ route('admin.options.update', $option) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-8">
                {{-- Pemilihan Pertanyaan --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-tosca-light text-tosca-dark flex items-center justify-center text-sm">1</span>
                        Konteks Pertanyaan
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Pertanyaan Induk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="question_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-4 pr-10 py-3.5 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                                @foreach($questions as $q)
                                    <option value="{{ $q->id }}" {{ old('question_id', $option->question_id) == $q->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::limit($q->prompt, 100) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('question_id')
                            <p class="text-sm text-red-600 mt-2 font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Detail Opsi --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">2</span>
                        Teks & Status Opsi
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Teks Opsi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 pt-3.5 flex items-start pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                </div>
                                <textarea name="text" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main" required placeholder="Ketik isi opsi jawaban di sini...">{{ old('text', $option->text) }}</textarea>
                            </div>
                            @error('text')
                                <p class="text-sm text-red-600 mt-2 font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-text-soft uppercase tracking-wider mb-3">Status Kebenaran Jawaban</label>
                            
                            <label class="flex items-center gap-4 cursor-pointer group/toggle p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                                <div class="relative">
                                    <input type="checkbox" name="is_correct" id="is_correct" value="1" class="sr-only peer" {{ old('is_correct', $option->is_correct) ? 'checked' : '' }}>
                                    <div class="block bg-gray-200 w-14 h-8 rounded-full transition-colors duration-300 peer-checked:bg-green-500"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 flex items-center justify-center shadow-sm peer-checked:transform peer-checked:translate-x-6">
                                        <svg class="w-4 h-4 text-green-500 hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">Opsi ini adalah jawaban yang benar</div>
                                    <p class="text-sm text-gray-500">Aktifkan ini jika opsi ini merupakan jawaban yang tepat untuk pertanyaan di atas.</p>
                                </div>
                            </label>
                            @error('is_correct')
                                <p class="text-sm text-red-600 mt-2 font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Submit Footer --}}
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-end gap-3">
                <a href="{{ route('admin.options.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-red-50 border border-red-100 rounded-3xl overflow-hidden p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h3 class="text-lg font-extrabold text-red-800 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Zona Berbahaya
            </h3>
            <p class="text-sm text-red-600">Menghapus opsi ini akan menghilangkannya dari pertanyaan. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        
        <form action="{{ route('admin.options.destroy', $option) }}" method="POST" class="shrink-0 js-delete-form" data-title="opsi jawaban ini">
            @csrf @method('DELETE')
            <button type="button" class="js-delete-btn inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-red-200 text-red-600 font-bold hover:bg-red-600 hover:text-white transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus Permanen
            </button>
        </form>
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
                        title: 'Hapus Opsi Jawaban?',
                        html: `<b>${title}</b> akan dihapus permanen dari sistem.`,
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
