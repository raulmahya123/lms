@extends('layouts.admin')
@section('title', 'Edit Q&A Thread — Admin')

@section('content')
<div class="max-w-4xl mx-auto pb-12 space-y-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Diskusi
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Perbarui Thread #{{ $thread->id }}</h1>
            <p class="text-sm text-text-soft mt-1">Ubah konten diskusi, pengguna terkait, atau status penyelesaiannya.</p>
        </div>
        
        <div class="shrink-0 flex items-center gap-3">
            <a href="{{ route('admin.qa-threads.show', $thread) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex gap-3 text-sm text-red-800 shadow-sm">
            <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold">Gagal menyimpan perubahan! Silakan periksa isian Anda:</p>
                <ul class="mt-2 list-disc pl-4 text-red-700 font-medium space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.qa-threads.update', $thread) }}" class="space-y-8">
        @csrf @method('PUT')

        {{-- Meta Info --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Penulis & Kaitan
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                {{-- Penulis --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Penulis Diskusi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="user_id" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="">— Pilih Pengguna —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $thread->user_id) == $u->id)>
                                    {{ $u->name }} ({{ Str::limit($u->id, 8, '') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                    {{-- Course --}}
                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Pilih Course (Opsional)</label>
                        <div class="relative">
                            <select name="course_id"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                                <option value="">— Tidak Berkaitan dengan Course —</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" @selected(old('course_id', $thread->course_id) == $c->id)>{{ $c->title }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Lesson --}}
                    <div>
                        <label class="block text-sm font-bold text-text-main mb-2">Pilih Lesson (Opsional)</label>
                        <div class="relative">
                            <select name="lesson_id"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                                <option value="">— Tidak Berkaitan dengan Lesson —</option>
                                @foreach($lessons as $l)
                                    <option value="{{ $l->id }}" @selected(old('lesson_id', $thread->lesson_id) == $l->id)>{{ $l->title }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten Diskusi --}}
        <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Konten Diskusi
                </h2>
            </div>
            
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Judul Diskusi <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $thread->title) }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main text-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Isi Pesan <span class="text-red-500">*</span></label>
                    <textarea name="body" rows="6" required
                              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main leading-relaxed resize-y">{{ old('body', $thread->body) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Status Saat Ini</label>
                    <div class="relative w-full md:w-1/3">
                        @php($st = old('status', $thread->status))
                        <select name="status"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="open" @selected($st === 'open')>Buka (Open)</option>
                            <option value="resolved" @selected($st === 'resolved')>Terselesaikan (Resolved)</option>
                            <option value="closed" @selected($st === 'closed')>Ditutup (Closed)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
            <a href="{{ route('admin.qa-threads.show', $thread) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
