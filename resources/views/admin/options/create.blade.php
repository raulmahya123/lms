@extends('layouts.admin')
@section('title','Create Options — BERKEMAH')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="optionForm()">
  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3.75a8.25 8.25 0 1 1 0 16.5 8.25 8.25 0 0 1 0-16.5Zm.75 3.75a.75.75 0 0 0-1.5 0v4.5H6.75a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5V7.5Z"/>
        </svg>
        Create Options (Bulk)
      </h1>
      <p class="text-sm opacity-70">Tambahkan banyak opsi untuk satu pertanyaan sekaligus.</p>
    </div>
    <a href="{{ route('admin.options.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50 transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.28 6.22a.75.75 0 0 1 0 1.06L6.56 11h11.19a.75.75 0 0 1 0 1.5H6.56l3.72 3.72a.75.75 0 1 1-1.06 1.06l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 1.06 0Z"/></svg>
      Back
    </a>
  </div>

  {{-- FLASH --}}
  @if(session('ok'))
    <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('ok') }}
    </div>
  @endif

  {{-- CARD FORM --}}
  <div class="rounded-2xl border bg-white p-6">
    <form action="{{ route('admin.options.bulk-store') }}" method="POST" class="space-y-6">
      @csrf

      {{-- Question --}}
      <div>
        <label class="block text-sm font-medium mb-1">Question <span class="text-red-500">*</span></label>
        <div class="relative">
          <select name="question_id" class="w-full border rounded-xl pl-10 pr-3 py-2" required>
            <option value="" disabled selected>— pilih pertanyaan —</option>
            @foreach($questions as $q)
              <option value="{{ $q->id }}" {{ old('question_id') == $q->id ? 'selected' : '' }}>
                {{ \Illuminate\Support\Str::limit($q->prompt, 80) }}
              </option>
            @endforeach
          </select>
          <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 7.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h12a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Zm0 4.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1 0-1.5Z"/>
          </svg>
        </div>
        @error('question_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Bulk paste helper --}}
      <div class="rounded-xl border p-4 bg-gray-50">
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="font-medium">Tempel daftar opsi</div>
            <p class="text-xs text-gray-600">Setiap baris = 1 opsi. Tanda <code>::true</code> di akhir baris untuk jawaban benar.</p>
            <p class="text-xs text-gray-600">Contoh: <em>Bilangan prima::true</em></p>
          </div>
          <button type="button" @click="fromBulkPaste()"
                  class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-100 text-sm">Isi ke daftar</button>
        </div>
        <textarea x-model="bulk" rows="3" class="mt-3 w-full border rounded-lg p-2 text-sm" placeholder="Opsi A
Opsi B::true
Opsi C"></textarea>
      </div>

      {{-- Options repeater --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label class="block text-sm font-medium">Options</label>
          <div class="flex items-center gap-2">
            <button type="button" @click="add()"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5.25a.75.75 0 0 1 .75.75v5.25H18a.75.75 0 0 1 0 1.5h-5.25V18a.75.75 0 0 1-1.5 0v-5.25H6a.75.75 0 0 1 0-1.5h5.25V6a.75.75 0 0 1 .75-.75Z"/></svg>
              Tambah baris
            </button>
            <button type="button" @click="clearAll()"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm">
              Bersihkan
            </button>
          </div>
        </div>

        <template x-for="(item, idx) in items" :key="item.key">
          <div class="border rounded-xl p-3 bg-white/50">
            <div class="flex items-start gap-3">
              <div class="grow">
                <div class="relative">
                  <textarea class="w-full border rounded-lg pl-10 pr-3 py-2 text-sm"
                            rows="2"
                            :name="`options[${idx}][text]`"
                            x-model="item.text"
                            placeholder="Tulis opsi..."></textarea>
                  <svg class="w-5 h-5 absolute left-3 top-2.5 opacity-60" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4.5 6.75A.75.75 0 0 1 5.25 6h13.5a.75.75 0 0 1 0 1.5H5.25A.75.75 0 0 1 4.5 6.75ZM5.25 10.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5Zm0 4.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z"/>
                  </svg>
                </div>
                @error('options.*.text') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
              </div>

              <div class="shrink-0 flex flex-col items-center gap-2 pt-1">
                <label class="text-xs">Benar?</label>
                <input type="checkbox" class="w-5 h-5"
                       :name="`options[${idx}][correct]`"
                       :checked="item.correct"
                       @change="item.correct = $event.target.checked"
                       value="1">
                <button type="button" @click="remove(idx)"
                        class="text-red-600 hover:bg-red-50 rounded-md px-2 py-1 text-xs">Hapus</button>
              </div>
            </div>
          </div>
        </template>

        @error('options') <p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center justify-end gap-2">
        <a href="{{ route('admin.options.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
          Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          Simpan Semua
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Alpine --}}
<script>
function optionForm() {
  return {
    items: [
      { key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false },
      { key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false },
    ],
    bulk: '',
    add() {
      this.items.push({ key: crypto.randomUUID?.() ?? String(Math.random()), text: '', correct: false });
    },
    remove(i) {
      this.items.splice(i,1);
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
          text: m ? l.replace(/::(true|1|yes)$/i,'').trim() : l,
          correct: !!m
        };
      });
    }
  }
}
</script>
@endsection
