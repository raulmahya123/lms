@extends('layouts.admin')
@section('title', 'Edit Lesson — BERKEMAH')

@section('content')
@php
  // Helper: pastikan selalu string saat ditampilkan dalam <textarea> atau input
  $toText = function ($v) {
      if (is_null($v)) return '';
      if (is_string($v)) return $v;
      // array / object -> JSON pretty agar terbaca & aman
      return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  };

  // Siapkan nilai string yang aman untuk textareas
  $aboutStr    = $toText(old('about',    $lesson->about    ?? null));
  $syllabusStr = $toText(old('syllabus', $lesson->syllabus ?? null));
  $reviewsStr  = $toText(old('reviews',  $lesson->reviews  ?? null));

  $contentOld  = old('content', is_array($lesson->content)
      ? json_encode($lesson->content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
      : ($lesson->content ?? '')
  );
  $contentStr  = $toText($contentOld); // kalau old('content') array, tetap jadi string
@endphp

<div class="max-w-3xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-wide flex items-center gap-2">
        {{-- Pencil/Edit icon --}}
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182l-9.57 9.569a4.5 4.5 0 0 1-1.78 1.11l-3.27 1.09a.75.75 0 0 1-.947-.948l1.09-3.269a4.5 4.5 0 0 1 1.11-1.78l9.57-9.57Z"/>
        </svg>
        Edit Lesson
      </h1>
      <p class="text-sm opacity-70">Perbarui judul, konten, meta, urutan, status, dan whitelist Drive.</p>
    </div>

    <a href="{{ route('admin.lessons.index') }}"
       class="px-3 py-2 rounded-xl border hover:bg-gray-50 transition"
       aria-label="Kembali ke daftar lesson">← Back</a>
  </div>

  {{-- FORM CARD --}}
  <div class="rounded-2xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="space-y-6" novalidate>
      @csrf
      @method('PUT')

      {{-- MODULE --}}
      <div>
        <label for="module_id" class="block text-sm font-medium mb-1">
          Module <span class="text-red-500">*</span>
        </label>
        <select id="module_id" name="module_id" class="w-full border rounded-xl px-3 py-2" required>
          @foreach ($modules as $m)
            <option value="{{ $m->id }}" @selected(old('module_id', $lesson->module_id) == $m->id)>
              {{ $m->course?->title }} — {{ $m->title }}
            </option>
          @endforeach
        </select>
        @error('module_id')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- TITLE --}}
      <div>
        <label for="title" class="block text-sm font-medium mb-1">
          Title <span class="text-red-500">*</span>
        </label>
        <input
          id="title"
          type="text"
          name="title"
          value="{{ old('title', $lesson->title) }}"
          class="w-full border rounded-xl px-3 py-2"
          required
        >
        @error('title')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- ABOUT / SYLLABUS / REVIEWS (BARU) --}}
      <div class="grid md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1">About</label>
          <textarea name="about" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Ringkasan singkat materi...">{{ $aboutStr }}</textarea>
          @error('about') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Syllabus</label>
          <textarea name="syllabus" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Garis besar topik...">{{ $syllabusStr }}</textarea>
          @error('syllabus') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Reviews</label>
          <textarea name="reviews" rows="3" class="w-full border rounded-xl px-3 py-2" placeholder="Testimoni / catatan review...">{{ $reviewsStr }}</textarea>
          @error('reviews') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- TOOLS / BENEFITS (BARU, chip input + fallback CSV) --}}
      <div class="grid md:grid-cols-2 gap-4">
        {{-- TOOLS --}}
        @php
          $toolsInit = old('tools');
          if (is_null($toolsInit)) { $toolsInit = $lesson->tools ?? []; }
          if (is_string($toolsInit)) {
              $decoded = json_decode($toolsInit, true);
              $toolsInit = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $toolsInit)));
          }
          if (!is_array($toolsInit)) $toolsInit = [];
        @endphp
        <div x-data="{
              items: @js(array_values($toolsInit)),
              input:'', add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
              remove(i){ this.items.splice(i,1); }
            }">
          <label class="block text-sm font-medium mb-1">Tools</label>
          <div class="flex gap-2">
            <input x-model="input" type="text" class="w-full border rounded-xl px-3 py-2" placeholder="Tulis lalu klik Add">
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
          <noscript>
            <input type="text" name="tools" value="{{ is_array($lesson->tools)? implode(',', $lesson->tools) : ($lesson->tools ?? '') }}" class="mt-2 w-full border rounded-xl px-3 py-2" placeholder="Pisahkan dengan koma">
          </noscript>
          @error('tools') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- BENEFITS --}}
        @php
          $benefitsInit = old('benefits');
          if (is_null($benefitsInit)) { $benefitsInit = $lesson->benefits ?? []; }
          if (is_string($benefitsInit)) {
              $decoded = json_decode($benefitsInit, true);
              $benefitsInit = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $benefitsInit)));
          }
          if (!is_array($benefitsInit)) $benefitsInit = [];
        @endphp
        <div x-data="{
              items: @js(array_values($benefitsInit)),
              input:'', add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
              remove(i){ this.items.splice(i,1); }
            }">
          <label class="block text-sm font-medium mb-1">Benefits</label>
          <div class="flex gap-2">
            <input x-model="input" type="text" class="w-full border rounded-xl px-3 py-2" placeholder="Tulis lalu klik Add">
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
          <noscript>
            <input type="text" name="benefits" value="{{ is_array($lesson->benefits)? implode(',', $lesson->benefits) : ($lesson->benefits ?? '') }}" class="mt-2 w-full border rounded-xl px-3 py-2" placeholder="Pisahkan dengan koma">
          </noscript>
          @error('benefits') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- CONTENT --}}
      <div>
        <label for="content" class="block text-sm font-medium mb-1">
          Content (HTML / Markdown / text)
        </label>
        <textarea id="content" name="content" rows="6" class="w-full border rounded-xl px-3 py-2">{{ $contentStr }}</textarea>
        @error('content')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- CONTENT URLs --}}
      <div x-data="{ urls: @js(old('content_url', $lesson->content_url ?? [])) }">
        <label class="block text-sm font-medium mb-1">Content URLs (video/file)</label>

        <template x-for="(item, index) in urls" :key="index">
          <div class="flex gap-2 mb-2">
            <input type="text" :name="`content_url[${index}][title]`" x-model="item.title" placeholder="Judul konten" class="w-1/3 border rounded-xl px-3 py-2">
            <input type="url"  :name="`content_url[${index}][url]`"   x-model="item.url"   placeholder="https://..." class="w-2/3 border rounded-xl px-3 py-2">
            <button type="button" @click="urls.splice(index, 1)" class="px-2 text-red-600" aria-label="Hapus URL">✕</button>
          </div>
        </template>

        <button type="button" @click="urls.push({ title: '', url: '' })" class="mt-2 px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600">+ Tambah URL</button>

        @error('content_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        @error('content_url.*.url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- GOOGLE DRIVE (link + status + whitelist) --}}
      <div class="rounded-xl border p-4 space-y-4">
        <h3 class="font-semibold">Google Drive</h3>

        {{-- Drive Link --}}
        <div>
          <label for="drive_link" class="block text-sm font-medium mb-1">Drive Link (opsional)</label>
          <input id="drive_link" type="url" name="drive_link" value="{{ old('drive_link', $lesson->drive_link ?? '') }}" placeholder="https://drive.google.com/..." class="w-full border rounded-xl px-3 py-2">
          @error('drive_link') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Drive Status (biarkan ada di UI; controller bisa abaikan bila kolom tak ada) --}}
        <div>
          <label for="drive_status" class="block text-sm font-medium mb-1">Drive Status</label>
          @php $currentStatus = old('drive_status', $lesson->drive_status ?? ''); @endphp
          <select id="drive_status" name="drive_status" class="w-full border rounded-xl px-3 py-2">
            <option value="">— pilih status —</option>
            <option value="pending"  @selected($currentStatus === 'pending')>Pending</option>
            <option value="approved" @selected($currentStatus === 'approved')>Approved</option>
            <option value="rejected" @selected($currentStatus === 'rejected')>Rejected</option>
          </select>
          @error('drive_status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Whitelist (maks 4 user) --}}
        @php
          $oldIds = collect(old('drive_user_ids', []))->map(fn($v) => (int) $v)->filter()->values();
          if ($oldIds->isEmpty()) {
              $usersByEmail = $users->keyBy(fn($u) => mb_strtolower($u->email));
              $derived = collect($lesson->driveWhitelists ?? [])
                  ->map(function ($w) use ($usersByEmail) {
                      if ($w->user_id) return (int) $w->user_id;
                      $match = $usersByEmail->get(mb_strtolower($w->email));
                      return $match ? (int) $match->id : null;
                  })
                  ->filter()->unique()->take(4)->values();
              $initialSelected = $derived;
          } else {
              $initialSelected = $oldIds->take(4)->values();
          }
        @endphp

        <div
          x-data="{
            users: @js($users->map(fn($u) => ['id'=>$u->id, 'name'=>$u->name, 'email'=>$u->email])->values()),
            selected: @js($initialSelected),
            pick: '',
            add() {
              const id = parseInt(this.pick);
              if (!id) return;
              if (this.selected.includes(id)) return;
              if (this.selected.length >= 4) return alert('Maksimal 4 user.');
              this.selected.push(id); this.pick = '';
            },
            remove(i) { this.selected.splice(i, 1); },
            available() { return this.users.filter(u => !this.selected.includes(u.id)); },
            label(u) { return `${u.name} — ${u.email}`; }
          }"
          class="space-y-2"
        >
          <label class="block text-sm font-medium">
            Drive Whitelist (maks 4)
            <span class="text-xs opacity-70" x-text="`— dipilih: ${selected.length}/4`"></span>
          </label>

          <div class="flex gap-2">
            <select x-model="pick" class="w-full border rounded-xl px-3 py-2" :disabled="selected.length >= 4">
              <option value="">— pilih user —</option>
              <template x-for="u in available()" :key="u.id">
                <option :value="u.id" x-text="label(u)"></option>
              </template>
            </select>
            <button type="button" class="px-3 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50" @click="add()" :disabled="selected.length >= 4 || !pick">Tambah</button>
          </div>
          <p class="text-xs opacity-70">Pilih user lalu klik “Tambah”. Bisa dihapus jika keliru.</p>

          <div class="mt-2 space-y-2">
            <template x-for="(id, i) in selected" :key="id">
              <div class="flex items-center justify-between rounded-lg border px-3 py-2">
                <div class="text-sm font-medium" x-text="label(users.find(u => u.id === id) ?? { name:'Unknown', email:'' })"></div>
                <div class="flex items-center gap-2">
                  <input type="hidden" :name="`drive_user_ids[${i}]`" :value="id">
                  <button type="button" class="px-2 text-red-600" @click="remove(i)" aria-label="Hapus dari whitelist">✕</button>
                </div>
              </div>
            </template>

            <template x-if="selected.length === 0">
              <div class="text-xs opacity-60">Belum ada user terpilih.</div>
            </template>
          </div>

          @error('drive_user_ids') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          @error('drive_user_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- KELOLA STATUS WHITELIST (jika ada) --}}
        @php $currentWhitelists = ($lesson->driveWhitelists ?? collect()); @endphp
        @if ($currentWhitelists->count())
          <div class="mt-4">
            <label class="block text-sm font-medium mb-1">Kelola Status Whitelist</label>
            <div class="overflow-hidden rounded-xl border">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="text-left px-3 py-2">Email</th>
                    <th class="text-left px-3 py-2">User</th>
                    <th class="text-left px-3 py-2">Ubah Status</th>
                    <th class="text-left px-3 py-2">Verified At</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($currentWhitelists as $w)
                    @php
                      $key = mb_strtolower($w->email);
                      $chosen = old("whitelist_status.$key", $w->status);
                    @endphp
                    <tr class="border-t">
                      <td class="px-3 py-2">{{ $w->email }}</td>
                      <td class="px-3 py-2">
                        @if ($w->user)
                          {{ $w->user->name }} <span class="opacity-60">({{ $w->user->email }})</span>
                        @else
                          <span class="opacity-60">—</span>
                        @endif
                      </td>
                      <td class="px-3 py-2">
                        <select name="whitelist_status[{{ $key }}]" class="border rounded px-2 py-1">
                          <option value="pending"  @selected($chosen === 'pending')>Pending</option>
                          <option value="approved" @selected($chosen === 'approved')>Approved</option>
                          <option value="rejected" @selected($chosen === 'rejected')>Rejected</option>
                        </select>
                      </td>
                      <td class="px-3 py-2">
                        {{ $w->verified_at ? $w->verified_at->format('Y-m-d H:i') : '—' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <p class="text-xs opacity-70 mt-1">
              Catatan: Jika kamu menghapus email dari pilihan user di atas, entri whitelist terkait akan ikut dihapus saat disimpan.
            </p>
          </div>
        @endif
      </div>

      {{-- WHITELIST TERSIMPAN (read-only) --}}
      @if (($lesson->driveWhitelists ?? collect())->count())
        <div class="mt-2">
          <label class="block text-sm font-medium mb-1">Whitelist Tersimpan</label>
          <div class="overflow-hidden rounded-xl border">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="text-left px-3 py-2">Email</th>
                  <th class="text-left px-3 py-2">User</th>
                  <th class="text-left px-3 py-2">Status</th>
                  <th class="text-left px-3 py-2">Verified At</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($lesson->driveWhitelists as $w)
                  @php
                    $badge = match ($w->status) {
                      'approved' => 'bg-green-100 text-green-700',
                      'rejected' => 'bg-red-100 text-red-700',
                      default    => 'bg-yellow-100 text-yellow-700',
                    };
                  @endphp
                  <tr class="border-t">
                    <td class="px-3 py-2">{{ $w->email }}</td>
                    <td class="px-3 py-2">
                      @if ($w->user)
                        {{ $w->user->name }} <span class="opacity-60">({{ $w->user->email }})</span>
                      @else
                        <span class="opacity-60">—</span>
                      @endif
                    </td>
                    <td class="px-3 py-2">
                      <span class="px-2 py-0.5 rounded {{ $badge }}">{{ ucfirst($w->status) }}</span>
                    </td>
                    <td class="px-3 py-2">
                      {{ $w->verified_at ? $w->verified_at->format('Y-m-d H:i') : '—' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <p class="text-xs opacity-70 mt-1">Perubahan whitelist mengikuti pilihan user di atas saat kamu menyimpan.</p>
        </div>
      @endif

      {{-- ORDERING + FREE --}}
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="ordering" class="block text-sm font-medium mb-1">Ordering</label>
          <input id="ordering" type="number" name="ordering" min="1" value="{{ old('ordering', $lesson->ordering) }}" class="w-full border rounded-xl px-3 py-2">
          <p class="text-xs opacity-70 mt-1">Urutan tampil (angka kecil muncul duluan).</p>
          @error('ordering') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-end">
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $lesson->is_free)) class="rounded">
            <span>Mark as Free</span>
          </label>
        </div>
      </div>

      {{-- ACTIONS --}}
      <div class="pt-2 flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow transition" aria-label="Simpan perubahan lesson">
          Update Lesson
        </button>
        <a href="{{ route('admin.lessons.index') }}" class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">Cancel</a>
      </div>

    </form>
  </div>
</div>
@endsection
