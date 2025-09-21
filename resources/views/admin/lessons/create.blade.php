@extends('layouts.admin')
@section('title','Create Lesson — BERKEMAH')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
          <path d="M4.5 5.75A2.75 2.75 0 0 1 7.25 3h9.5A2.75 2.75 0 0 1 19.5 5.75v12.5A2.75 2.75 0 0 1 16.75 21h-9.5A2.75 2.75 0 0 1 4.5 18.25V5.75Zm5 1.25a.75.75 0 0 0-.75.75v8.5a.75.75 0 0 0 1.14.64l6.5-4.25a.75.75 0 0 0 0-1.28l-6.5-4.25a.75.75 0 0 0-.39-.11Z"/>
        </svg>
        Create Lesson
      </h1>
      <p class="text-sm opacity-70">Tambahkan pelajaran baru ke salah satu modul.</p>
    </div>
    <a href="{{ route('admin.lessons.index') }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition">← Back</a>
  </div>

  {{-- FORM CARD --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.lessons.store') }}" class="space-y-6">
      @csrf

      {{-- Module --}}
      <div>
        <label class="block text-sm font-medium mb-1">Module <span class="text-red-500">*</span></label>
        <select name="module_id" class="w-full border rounded-xl px-3 py-2" required>
          <option value="">— Select Module —</option>
          @foreach($modules as $m)
            <option value="{{ $m->id }}" @selected(old('module_id')==$m->id)>
              {{ $m->course?->title }} — {{ $m->title }}
            </option>
          @endforeach
        </select>
        @error('module_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title"
               value="{{ old('title') }}"
               placeholder="Contoh: Pengenalan Variabel"
               class="w-full border rounded-xl px-3 py-2" required>
        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- ABOUT / SYLLABUS / REVIEWS --}}
      <div class="grid md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">About</label>
          <textarea name="about" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Ringkasan singkat materi...">{{ old('about') }}</textarea>
          @error('about') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Syllabus</label>
          <textarea name="syllabus" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Garis besar topik...">{{ old('syllabus') }}</textarea>
          @error('syllabus') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Reviews</label>
          <textarea name="reviews" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Testimoni / catatan review...">{{ old('reviews') }}</textarea>
          @error('reviews') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- TOOLS / BENEFITS (chip input + CSV friendly) --}}
      <div class="grid md:grid-cols-2 gap-4">
        {{-- Tools --}}
        <div x-data="{
              items: (()=>{ const raw = @js(old('tools')); if (!raw) return [];
                try { const j=JSON.parse(raw); return Array.isArray(j)?j:[]; } catch(e) {}
                return String(raw).split(',').map(s=>s.trim()).filter(Boolean); })(),
              input:'',
              add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
              remove(i){ this.items.splice(i,1); }
            }">
          <label class="block text-sm font-medium mb-1">Tools</label>
          <div class="flex gap-2">
            <input x-model="input" type="text" class="w-full border rounded-xl px-3 py-2" placeholder="Tulis lalu Enter">
            <button type="button" @click="add()" class="px-3 py-2 rounded-xl bg-blue-600 text-white">Add</button>
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            <template x-for="(t,i) in items" :key="i">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs">
                <input type="hidden" :name="`tools[]`" :value="t">
                <span x-text="t"></span>
                <button type="button" @click="remove(i)" class="text-sky-700/70 hover:text-sky-900">×</button>
              </span>
            </template>
            <template x-if="items.length===0">
              <span class="text-xs opacity-60">Kosong (opsional)</span>
            </template>
          </div>
          {{-- fallback CSV (kalau JS mati) --}}
          <noscript>
            @php
              $oldTools = old('tools');
              $oldToolsCsv = is_array($oldTools) ? implode(',', $oldTools) : (string) $oldTools;
            @endphp
            <input type="text" name="tools" value="{{ $oldToolsCsv }}" class="mt-2 w-full border rounded-xl px-3 py-2" placeholder="Pisahkan dengan koma">
          </noscript>
          @error('tools') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Benefits --}}
        <div x-data="{
              items: (()=>{ const raw = @js(old('benefits')); if (!raw) return [];
                try { const j=JSON.parse(raw); return Array.isArray(j)?j:[]; } catch(e) {}
                return String(raw).split(',').map(s=>s.trim()).filter(Boolean); })(),
              input:'',
              add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
              remove(i){ this.items.splice(i,1); }
            }">
          <label class="block text-sm font-medium mb-1">Benefits</label>
          <div class="flex gap-2">
            <input x-model="input" type="text" class="w-full border rounded-xl px-3 py-2" placeholder="Tulis lalu Enter">
            <button type="button" @click="add()" class="px-3 py-2 rounded-xl bg-blue-600 text-white">Add</button>
          </div>
          <div class="flex flex-wrap gap-2 mt-2">
            <template x-for="(b,i) in items" :key="i">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                <input type="hidden" :name="`benefits[]`" :value="b">
                <span x-text="b"></span>
                <button type="button" @click="remove(i)" class="text-emerald-700/70 hover:text-emerald-900">×</button>
              </span>
            </template>
            <template x-if="items.length===0">
              <span class="text-xs opacity-60">Kosong (opsional)</span>
            </template>
          </div>
          {{-- fallback CSV --}}
          <noscript>
            @php
              $oldBenefits = old('benefits');
              $oldBenefitsCsv = is_array($oldBenefits) ? implode(',', $oldBenefits) : (string) $oldBenefits;
            @endphp
            <input type="text" name="benefits" value="{{ $oldBenefitsCsv }}" class="mt-2 w-full border rounded-xl px-3 py-2" placeholder="Pisahkan dengan koma">
          </noscript>
          @error('benefits') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Content --}}
      <div>
        <label class="block text-sm font-medium mb-1">Content (HTML / Markdown / text)</label>
        <textarea name="content" rows="6"
                  placeholder="Tulis materi di sini..."
                  class="w-full border rounded-xl px-3 py-2">{{ old('content') }}</textarea>
        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Content URLs (Array) --}}
      <div x-data="{ urls: @js(old('content_url', [])) }">
        <label class="block text-sm font-medium mb-1">Content URLs (video/file)</label>

        <template x-for="(item, index) in urls" :key="index">
          <div class="flex gap-2 mb-2">
            <input type="text" :name="`content_url[${index}][title]`"
                   x-model="item.title"
                   placeholder="Judul konten"
                   class="w-1/3 border rounded-xl px-3 py-2">
            <input type="url" :name="`content_url[${index}][url]`"
                   x-model="item.url"
                   placeholder="https://..."
                   class="w-2/3 border rounded-xl px-3 py-2">
            <button type="button" @click="urls.splice(index,1)"
                    class="px-2 text-red-600">✕</button>
          </div>
        </template>

        <button type="button" @click="urls.push({title:'',url:''})"
                class="mt-2 px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600">
          + Tambah URL
        </button>

        @error('content_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- GOOGLE DRIVE (link + status + whitelist by user_id) --}}
      <div class="rounded-xl border p-4 space-y-4">
        <h3 class="font-semibold">Google Drive</h3>

        {{-- Drive link (opsional) --}}
        <div>
          <label class="block text-sm font-medium mb-1">Drive Link (opsional)</label>
          <input type="url" name="drive_link"
                 value="{{ old('drive_link') }}"
                 placeholder="https://drive.google.com/..."
                 class="w-full border rounded-xl px-3 py-2">
          @error('drive_link') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Drive status (opsional) --}}
        <div>
          <label class="block text-sm font-medium mb-1">Drive Status</label>
          <select name="drive_status" class="w-full border rounded-xl px-3 py-2">
            <option value="">— pilih status —</option>
            <option value="pending"  @selected(old('drive_status')==='pending')>Pending</option>
            <option value="approved" @selected(old('drive_status')==='approved')>Approved</option>
            <option value="rejected" @selected(old('drive_status')==='rejected')>Rejected</option>
          </select>
          @error('drive_status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Whitelist picker (add/remove) --}}
        <div
          x-data="{
            users: @js($users->map(fn($u)=>['id'=>(string)$u->id,'name'=>$u->name,'email'=>$u->email])->values()),
            selected: @js(collect(old('drive_user_ids', []))->map(fn($v)=>(string)$v)->values()),
            pick: '',
            add() {
              const id = String(this.pick||'').trim();
              if (!id) return;
              if (this.selected.includes(id)) return;
              if (this.selected.length >= 4) { alert('Maksimal 4 user.'); return; }
              this.selected.push(id);
              this.pick = '';
            },
            remove(i){ this.selected.splice(i,1); },
            available(){ return this.users.filter(u => !this.selected.includes(u.id)); },
            labelById(id){
              const u = this.users.find(x => x.id === id);
              return u ? `${u.name} — ${u.email}` : '(user tidak ditemukan)';
            }
          }"
          class="space-y-2"
        >
          <label class="block text-sm font-medium">
            Drive Whitelist (maks 4)
            <span class="text-xs opacity-70" x-text="`— dipilih: ${selected.length}/4`"></span>
          </label>

          <div class="flex gap-2">
            <select x-model="pick" class="w-full border rounded-xl px-3 py-2" :disabled="selected.length>=4">
              <option value="">— pilih user —</option>
              <template x-for="u in available()" :key="u.id">
                <option :value="u.id" x-text="`${u.name} — ${u.email}`"></option>
              </template>
            </select>
            <button type="button"
                    class="px-3 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                    @click="add()"
                    :disabled="selected.length>=4 || !pick">
              Tambah
            </button>
          </div>
          <p class="text-xs opacity-70">Pilih user lalu klik “Tambah”. Bisa dihapus jika keliru.</p>

          <div class="mt-2 space-y-2">
            <template x-for="(id, i) in selected" :key="id">
              <div class="flex items-center justify-between rounded-lg border px-3 py-2">
                <div class="text-sm font-medium" x-text="labelById(id)"></div>
                <div class="flex items-center gap-2">
                  <input type="hidden" :name="`drive_user_ids[${i}]`" :value="id">
                  <button type="button" class="px-2 text-red-600" @click="remove(i)">✕</button>
                </div>
              </div>
            </template>
            <template x-if="selected.length===0">
              <div class="text-xs opacity-60">Belum ada user terpilih.</div>
            </template>
          </div>

          @error('drive_user_ids') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          @error('drive_user_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Ordering + Free --}}
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Ordering</label>
          <input type="number" name="ordering" min="1"
                 value="{{ old('ordering',1) }}"
                 class="w-full border rounded-xl px-3 py-2">
          <p class="text-xs opacity-70 mt-1">Urutan tampil (angka kecil muncul duluan).</p>
          @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_free" value="1" @checked(old('is_free'))
                   class="rounded">
            <span>Mark as Free</span>
          </label>
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition">
          Save Lesson
        </button>
        <a href="{{ route('admin.lessons.index') }}"
           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
