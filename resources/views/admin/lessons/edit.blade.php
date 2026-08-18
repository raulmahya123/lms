@extends('layouts.admin')

@section('title', 'Edit Pelajaran — Admin')

@section('content')
@php
  $toText = function ($v) {
      if (is_null($v)) return '';
      if (is_string($v)) return $v;
      return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  };

  $aboutStr    = $toText(old('about',    $lesson->about    ?? null));
  $syllabusStr = $toText(old('syllabus', $lesson->syllabus ?? null));
  $reviewsStr  = $toText(old('reviews',  $lesson->reviews  ?? null));

  $contentOld  = old('content',
      is_array($lesson->content)
        ? json_encode($lesson->content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        : ($lesson->content ?? '')
  );
  $contentStr  = $toText($contentOld);
@endphp

<div class="max-w-4xl mx-auto space-y-8 pb-12">
  
  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-tosca-light text-tosca-dark text-xs font-bold uppercase tracking-wider mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Perbarui Data
      </div>
      <h1 class="text-3xl font-extrabold text-text-main tracking-tight">Edit Pelajaran</h1>
      <p class="text-sm text-text-soft mt-1">Ubah judul, konten materi, pengaturan Drive, atau akses pelajaran ini.</p>
    </div>
    <div class="shrink-0 flex gap-3">
      <a href="{{ route('admin.lessons.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors">
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
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    @csrf
    @method('PUT')

    <div class="p-8 space-y-10">
      
      {{-- Bagian 1: Struktur Dasar --}}
      <div>
        <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">1</span>
          Struktur Dasar
        </h3>
        
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-text-main mb-2">Penempatan Modul <span class="text-red-500">*</span></label>
              <div class="relative">
                <select name="module_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer" required>
                  @foreach ($modules as $m)
                    <option value="{{ $m->id }}" @selected(old('module_id', $lesson->module_id) == $m->id)>
                      {{ Str::limit($m->course?->title, 30) }} — {{ Str::limit($m->title, 30) }}
                    </option>
                  @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              @error('module_id') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-bold text-text-main mb-2">Urutan Tampil (Ordering)</label>
              <input type="number" name="ordering" min="1" value="{{ old('ordering', $lesson->ordering) }}" 
                     class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main text-center">
              @error('ordering') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Judul Pelajaran <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $lesson->title) }}" 
                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-bold text-text-main placeholder-gray-400" 
                   required>
            @error('title') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <hr class="border-gray-100">

      {{-- Bagian 2: Informasi Tambahan --}}
      <div>
        <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">2</span>
          Informasi Tambahan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-text-main mb-2">Ringkasan (About)</label>
            <textarea name="about" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y leading-relaxed">{{ $aboutStr }}</textarea>
            @error('about') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Silabus (Syllabus)</label>
            <textarea name="syllabus" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y leading-relaxed">{{ $syllabusStr }}</textarea>
            @error('syllabus') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Ulasan (Reviews)</label>
            <textarea name="reviews" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y leading-relaxed">{{ $reviewsStr }}</textarea>
            @error('reviews') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          {{-- Tools Input --}}
          @php
            $toolsInit = old('tools', $lesson->tools ?? []);
            if (is_string($toolsInit)) {
                $decoded = json_decode($toolsInit, true);
                $toolsInit = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $toolsInit)));
            }
            if (!is_array($toolsInit)) $toolsInit = [];
          @endphp
          <div x-data="{
                items: @js(array_values($toolsInit)),
                input:'',
                add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
                remove(i){ this.items.splice(i,1); }
              }">
            <label class="block text-sm font-bold text-text-main mb-2">Alat yang Dibutuhkan (Tools)</label>
            <div class="flex gap-2 mb-3">
              <input x-model="input" @keydown.enter.prevent="add()" type="text" class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all" placeholder="Tulis lalu klik Tambah...">
              <button type="button" @click="add()" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors shrink-0">Tambah</button>
            </div>
            <div class="flex flex-wrap gap-2">
              <template x-for="(t,i) in items" :key="i">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-sky-50 border border-sky-100 text-sky-700 text-sm font-bold">
                  <input type="hidden" :name="`tools[]`" :value="t">
                  <span x-text="t"></span>
                  <button type="button" @click="remove(i)" class="text-sky-500 hover:text-sky-800 transition-colors">✕</button>
                </span>
              </template>
              <template x-if="items.length===0">
                <span class="text-xs text-text-soft font-medium py-1">Belum ada tools yang ditambahkan.</span>
              </template>
            </div>
            @error('tools') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Benefits Input --}}
          @php
            $benefitsInit = old('benefits', $lesson->benefits ?? []);
            if (is_string($benefitsInit)) {
                $decoded = json_decode($benefitsInit, true);
                $benefitsInit = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $benefitsInit)));
            }
            if (!is_array($benefitsInit)) $benefitsInit = [];
          @endphp
          <div x-data="{
                items: @js(array_values($benefitsInit)),
                input:'',
                add(){ const v=this.input.trim(); if(!v) return; if(!this.items.includes(v)) this.items.push(v); this.input=''; },
                remove(i){ this.items.splice(i,1); }
              }">
            <label class="block text-sm font-bold text-text-main mb-2">Manfaat Pelajaran (Benefits)</label>
            <div class="flex gap-2 mb-3">
              <input x-model="input" @keydown.enter.prevent="add()" type="text" class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all" placeholder="Tulis lalu klik Tambah...">
              <button type="button" @click="add()" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors shrink-0">Tambah</button>
            </div>
            <div class="flex flex-wrap gap-2">
              <template x-for="(b,i) in items" :key="i">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold">
                  <input type="hidden" :name="`benefits[]`" :value="b">
                  <span x-text="b"></span>
                  <button type="button" @click="remove(i)" class="text-emerald-500 hover:text-emerald-800 transition-colors">✕</button>
                </span>
              </template>
              <template x-if="items.length===0">
                <span class="text-xs text-text-soft font-medium py-1">Belum ada manfaat yang ditambahkan.</span>
              </template>
            </div>
            @error('benefits') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <hr class="border-gray-100">

      {{-- Bagian 3: Konten Utama --}}
      <div>
        <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">3</span>
          Konten Pelajaran
        </h3>
        
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-text-main mb-2">Konten Teks (HTML / Markdown)</label>
            <textarea name="content" rows="6" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all placeholder-gray-400 resize-y font-mono text-sm leading-relaxed">{{ $contentStr }}</textarea>
            @error('content') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div x-data="{ urls: @js(old('content_url', $lesson->content_url ?? [])) }" class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
              <label class="block text-sm font-bold text-text-main">Media & Video (URL)</label>
              <button type="button" @click="urls.push({title:'',url:''})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah URL
              </button>
            </div>
            
            <div class="space-y-3">
              <template x-for="(item, index) in urls" :key="index">
                <div class="flex items-start gap-3 bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-3 flex-1">
                    <input type="text" :name="`content_url[${index}][title]`" x-model="item.title" placeholder="Judul Video/Media" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-bold">
                    <input type="url" :name="`content_url[${index}][url]`" x-model="item.url" placeholder="https://..." class="w-full md:col-span-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all">
                  </div>
                  <button type="button" @click="urls.splice(index,1)" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors shrink-0" title="Hapus URL">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </template>
              <template x-if="urls.length === 0">
                <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl bg-white">
                  <p class="text-sm text-text-soft font-medium">Belum ada media/video yang ditambahkan.</p>
                </div>
              </template>
            </div>
            @error('content_url') <p class="text-sm font-medium text-red-600 mt-2">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <hr class="border-gray-100">

      {{-- Bagian 4: Integrasi Google Drive --}}
      <div>
        <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">4</span>
          Integrasi File (Google Drive)
        </h3>
        
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold text-text-main mb-2">Link Drive (Opsional)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                <input type="url" name="drive_link" value="{{ old('drive_link', $lesson->drive_link ?? '') }}" placeholder="https://drive.google.com/..." 
                       class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-medium">
              </div>
              @error('drive_link') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-bold text-text-main mb-2">Status Drive</label>
              @php $currentStatus = old('drive_status', $lesson->drive_status ?? ''); @endphp
              <div class="relative">
                <select name="drive_status" class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-bold text-text-main appearance-none cursor-pointer">
                  <option value="">— Pilih status sinkronisasi —</option>
                  <option value="pending" @selected($currentStatus === 'pending')>Pending (Menunggu)</option>
                  <option value="approved" @selected($currentStatus === 'approved')>Approved (Disetujui)</option>
                  <option value="rejected" @selected($currentStatus === 'rejected')>Rejected (Ditolak)</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              @error('drive_status') <p class="text-sm font-medium text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          {{-- Whitelist Picker --}}
          @php
            $oldIds = collect(old('drive_user_ids', []))->map(fn($v) => (string) $v)->filter(fn($v) => $v !== '')->values();
            if ($oldIds->isEmpty()) {
                $usersByEmail = $users->keyBy(fn($u) => mb_strtolower($u->email));
                $derived = collect($lesson->driveWhitelists ?? [])->map(function ($w) use ($usersByEmail) {
                    if ($w->user_id) return (string) $w->user_id;
                    $match = $usersByEmail->get(mb_strtolower($w->email));
                    return $match ? (string) $match->id : null;
                })->filter()->unique()->take(4)->values();
                $initialSelected = $derived;
            } else {
                $initialSelected = $oldIds->take(4)->values();
            }
          @endphp

          <div x-data="{
                users: @js($users->map(fn($u)=>['id'=>(string)$u->id,'name'=>$u->name,'email'=>$u->email])->values()),
                selected: @js($initialSelected->map(fn($v)=>(string)$v)->values()),
                pick: '',
                add() {
                  const id = String(this.pick||'').trim();
                  if (!id) return;
                  if (this.selected.includes(id)) return;
                  if (this.selected.length >= 4) { 
                    Swal.fire({icon: 'warning', title: 'Batas Maksimal', text: 'Maksimal 4 user diperbolehkan.', confirmButtonColor: '#0F9D8A', customClass: {popup: 'rounded-2xl'}});
                    return; 
                  }
                  this.selected.push(id);
                  this.pick = '';
                },
                remove(i){ this.selected.splice(i,1); },
                available(){ return this.users.filter(u => !this.selected.includes(u.id)); },
                labelById(id){
                  const u = this.users.find(x => x.id === id);
                  return u ? `${u.name} — ${u.email}` : 'Unknown User';
                }
              }" class="border-t border-gray-200 pt-6">
            
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-bold text-text-main">Daftar Whitelist Pengguna (Akses Khusus)</label>
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white border border-gray-200 text-gray-700" x-text="`${selected.length} dari 4 Pengguna`"></span>
            </div>

            <div class="flex gap-2 mb-4">
              <div class="relative flex-1">
                <select x-model="pick" class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-medium appearance-none cursor-pointer disabled:bg-gray-100 disabled:cursor-not-allowed" :disabled="selected.length>=4">
                  <option value="">— Cari dan pilih user —</option>
                  <template x-for="u in available()" :key="u.id">
                    <option :value="u.id" x-text="`${u.name} — ${u.email}`"></option>
                  </template>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <button type="button" @click="add()" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-bold hover:bg-black transition-colors shrink-0 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="selected.length>=4 || !pick">
                Tambahkan
              </button>
            </div>

            <div class="space-y-2">
              <template x-for="(id, i) in selected" :key="id">
                <div class="flex items-center justify-between bg-white px-4 py-3 rounded-xl border border-gray-200 shadow-sm">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-tosca-light text-tosca flex items-center justify-center font-bold text-xs" x-text="labelById(id).charAt(0)"></div>
                    <div class="text-sm font-bold text-text-main" x-text="labelById(id)"></div>
                  </div>
                  <input type="hidden" :name="`drive_user_ids[${i}]`" :value="id">
                  <button type="button" class="p-1.5 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors" @click="remove(i)" title="Hapus dari Whitelist">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
                </div>
              </template>
              <template x-if="selected.length===0">
                <div class="text-center py-4 border-2 border-dashed border-gray-200 rounded-xl bg-white text-sm text-text-soft font-medium">
                  Belum ada pengguna yang diberikan akses khusus ke file Drive ini.
                </div>
              </template>
            </div>
            @error('drive_user_ids') <p class="text-sm font-medium text-red-600 mt-2">{{ $message }}</p> @enderror
          </div>

          {{-- Kelola Status Whitelist --}}
          @php $currentWhitelists = ($lesson->driveWhitelists ?? collect()); @endphp
          @if ($currentWhitelists->count())
            <div class="border-t border-gray-200 pt-6 mt-6">
              <label class="block text-sm font-bold text-text-main mb-3">Kelola Status Whitelist Tersimpan</label>
              <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                  <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100 text-text-soft uppercase tracking-wider font-bold text-xs">
                      <tr>
                        <th class="px-4 py-3">Email Pengguna</th>
                        <th class="px-4 py-3">Ubah Status</th>
                        <th class="px-4 py-3">Terakhir Verifikasi</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                      @foreach ($currentWhitelists as $w)
                        @php
                          $key = mb_strtolower($w->email);
                          $chosen = old("whitelist_status.$key", $w->status);
                        @endphp
                        <tr>
                          <td class="px-4 py-3">
                            <div class="font-bold text-text-main">{{ $w->email }}</div>
                            @if ($w->user)
                              <div class="text-xs text-text-soft mt-0.5">{{ $w->user->name }}</div>
                            @else
                              <div class="text-xs text-text-soft mt-0.5 italic">Belum terdaftar di sistem</div>
                            @endif
                          </td>
                          <td class="px-4 py-3">
                            <select name="whitelist_status[{{ $key }}]" class="w-32 bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 focus:bg-white focus:border-tosca focus:ring-2 focus:ring-tosca/20 outline-none transition-all font-bold text-text-main text-xs">
                              <option value="pending" @selected($chosen === 'pending')>Pending</option>
                              <option value="approved" @selected($chosen === 'approved')>Approved</option>
                              <option value="rejected" @selected($chosen === 'rejected')>Rejected</option>
                            </select>
                          </td>
                          <td class="px-4 py-3 text-text-soft text-xs font-medium">
                            {{ $w->verified_at ? $w->verified_at->format('d M Y, H:i') : 'Belum diverifikasi' }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <p class="text-xs text-text-soft mt-2 italic">Catatan: Jika Anda menghapus email dari daftar pengguna di atas, entri whitelist ini juga akan dihapus saat disimpan.</p>
            </div>
          @endif
        </div>
      </div>

      <hr class="border-gray-100">

      {{-- Bagian 5: Pengaturan Akses Premium --}}
      <div>
        <h3 class="text-lg font-bold text-text-main mb-6 flex items-center gap-2">
          <span class="w-8 h-8 rounded-lg bg-tosca-light text-tosca-dark flex items-center justify-center">5</span>
          Pengaturan Akses
        </h3>
        
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
          <div class="flex items-start justify-between">
            <div>
              <h4 class="font-bold text-text-main text-sm">Akses Gratis (Free Preview)</h4>
              <p class="text-xs text-text-soft mt-1 leading-relaxed max-w-md">Jika diaktifkan, pelajaran ini dapat diakses oleh semua pengguna meskipun mereka belum membeli atau mendaftar ke kursus ini. Cocok untuk materi pratinjau (trailer).</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer mt-1">
              <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $lesson->is_free)) class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tosca"></div>
            </label>
          </div>
        </div>
      </div>

    </div>

    <div class="p-6 border-t border-gray-50 bg-gray-50/30 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
      <a href="{{ route('admin.lessons.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
        Batal
      </a>
      <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
