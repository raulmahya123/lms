@extends('layouts.admin')
@section('title', 'Buat Opsi Jawaban (Bulk) — Admin')

@section('content')
<div class="max-w-4xl mx-auto pb-12" x-data="optionForm()">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Pembuatan Masal
            </div>
            <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Buat Opsi Jawaban</h1>
            <p class="text-sm text-text-soft mt-1">Tambahkan banyak opsi untuk satu pertanyaan sekaligus dengan mudah.</p>
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

    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <form action="{{ route('admin.options.bulk-store') }}" method="POST">
            @csrf
            
            <div class="p-8 space-y-10">
                {{-- Pemilihan Pertanyaan --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-tosca-light text-tosca-dark flex items-center justify-center text-sm">1</span>
                        Pilih Pertanyaan
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-bold text-text-soft uppercase tracking-wider mb-2">Pertanyaan Induk <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="question_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-4 pr-10 py-3.5 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                                <option value="" disabled selected>— Pilih Pertanyaan Kuis —</option>
                                @foreach($questions as $q)
                                    <option value="{{ $q->id }}" {{ old('question_id') == $q->id ? 'selected' : '' }}>
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

                {{-- Mode Input Cepat (Bulk Paste) --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">2</span>
                        Input Cepat (Opsional)
                    </h2>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-4">
                            <div>
                                <div class="font-extrabold text-text-main mb-1">Tempel Daftar Opsi Sekaligus</div>
                                <div class="text-sm text-text-soft">Setiap baris akan menjadi 1 opsi. Tambahkan <code class="bg-white border border-gray-200 px-1.5 py-0.5 rounded text-purple-600 font-mono text-xs">::true</code> di akhir baris untuk opsi yang benar.</div>
                            </div>
                            <button type="button" @click="fromBulkPaste()" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-700 transition-colors shadow-sm shadow-purple-600/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                Generate Opsi
                            </button>
                        </div>
                        <textarea x-model="bulk" rows="4" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 outline-none transition-all font-medium text-text-main placeholder-gray-400" placeholder="Opsi A&#10;Opsi B::true&#10;Opsi C&#10;Opsi D"></textarea>
                    </div>
                </div>

                {{-- Opsi Editor Manual --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm">3</span>
                            Daftar Opsi
                        </h2>
                        
                        <div class="flex items-center gap-2">
                            <button type="button" @click="clearAll()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors text-sm">
                                Bersihkan
                            </button>
                            <button type="button" @click="add()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 border border-blue-200 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Baris
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4" id="options-container">
                        <template x-for="(item, idx) in items" :key="item.key">
                            <div class="group border border-gray-200 bg-white rounded-2xl p-5 shadow-sm hover:border-tosca/50 transition-colors relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1.5 h-full bg-gray-200 group-hover:bg-tosca transition-colors" :class="item.correct ? '!bg-green-500' : ''"></div>
                                
                                <div class="flex flex-col md:flex-row md:items-start gap-5 pl-2">
                                    <div class="grow w-full">
                                        <label class="block text-xs font-bold text-text-soft uppercase tracking-wider mb-2" x-text="'Teks Opsi ' + (idx + 1)"></label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 pt-3.5 flex items-start pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                            </div>
                                            <textarea class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main"
                                                      rows="2"
                                                      :name="`options[${idx}][text]`"
                                                      x-model="item.text"
                                                      placeholder="Tuliskan teks opsi di sini..."></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="shrink-0 flex md:flex-col items-center md:justify-center gap-4 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-5 min-w-[120px]">
                                        <label class="flex flex-col items-center gap-2 cursor-pointer group/toggle">
                                            <span class="text-xs font-bold uppercase tracking-wider text-text-soft transition-colors" :class="item.correct ? 'text-green-600' : ''">Jawaban Benar</span>
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only"
                                                       :name="`options[${idx}][correct]`"
                                                       :checked="item.correct"
                                                       @change="item.correct = $event.target.checked"
                                                       value="1">
                                                <div class="block bg-gray-200 w-14 h-8 rounded-full transition-colors duration-300" :class="item.correct ? 'bg-green-500' : ''"></div>
                                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 flex items-center justify-center shadow-sm" :class="item.correct ? 'transform translate-x-6' : ''">
                                                    <svg x-show="item.correct" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            </div>
                                        </label>
                                        
                                        <button type="button" @click="remove(idx)" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus Opsi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    @error('options') <p class="text-sm text-red-600 mt-2 font-medium"><svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $message }}</p>@enderror
                </div>
            </div>
            
            {{-- Submit Footer --}}
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-end gap-3">
                <a href="{{ route('admin.options.index') }}" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Semua Opsi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('optionForm', () => ({
        items: [
            { key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false },
            { key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false },
        ],
        bulk: '',
        add() {
            this.items.push({ key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false });
        },
        remove(i) {
            this.items.splice(i, 1);
            if (this.items.length === 0) this.add();
        },
        clearAll() {
            this.items = [];
            this.add();
        },
        fromBulkPaste() {
            if (!this.bulk.trim()) return;
            const lines = this.bulk.split('\n').map(l => l.trim()).filter(Boolean);
            if (lines.length === 0) return;
            this.items = lines.map(l => {
                const m = l.match(/::(true|1|yes)$/i);
                return {
                    key: crypto.randomUUID?.() ?? String(Math.random()),
                    text: m ? l.replace(/::(true|1|yes)$/i, '').trim() : l,
                    correct: !!m
                };
            });
        }
    }));
});
</script>
@endsection
