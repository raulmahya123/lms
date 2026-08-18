@php
  $uVal   = old('user_id',        optional($enrollment)->user_id);
  $cVal   = old('course_id',      optional($enrollment)->course_id);
  $mVal   = old('membership_id',  optional($enrollment)->membership_id);
  $status = old('status',         optional($enrollment)->status ?? 'pending');
  $pay    = old('payment_status', optional($enrollment)->payment_status ?? 'pending');
  $start  = old('starts_at',      optional(optional($enrollment)->starts_at)->format('Y-m-d'));
  $end    = old('ends_at',        optional(optional($enrollment)->ends_at)->format('Y-m-d'));
  $ref    = old('reference',      optional($enrollment)->reference);
  $notes  = old('notes',          optional($enrollment)->notes);
@endphp

<div class="space-y-8">
    {{-- Hubungan Data --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                Hubungan Data
            </h2>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- USER --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Pengguna <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="user_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="" disabled {{ $uVal ? '' : 'selected' }}>— Pilih Pengguna —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ (string)$uVal === (string)$user->id ? 'selected' : '' }}>
                                    {{ $user->name ?? $user->email ?? ('User #'.$user->id) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- COURSE --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Course / Kelas <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="course_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="" disabled {{ $cVal ? '' : 'selected' }}>— Pilih Course —</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ (string)$cVal === (string)$course->id ? 'selected' : '' }}>
                                    {{ $course->title ?? $course->name ?? ('Course #'.$course->id) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- MEMBERSHIP --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-text-main mb-2">Membership / Plan Terkait (Opsional)</label>
                    <div class="relative">
                        <select name="membership_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            <option value="" {{ $mVal ? '' : 'selected' }}>— Tanpa Membership (Pembelian Langsung) —</option>
                            @foreach ($memberships as $plan)
                                <option value="{{ $plan->id }}" {{ (string)$mVal === (string)$plan->id ? 'selected' : '' }}>
                                    {{ $plan->name ?? ('Plan #'.$plan->id) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs text-text-soft mt-2">Pilih ini jika pendaftaran diberikan otomatis karena pengguna berlangganan suatu membership.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status & Periode --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Status & Periode
            </h2>
        </div>
        
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- STATUS ENROLLMENT --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Status Pendaftaran</label>
                    <div class="relative">
                        <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            @foreach (['pending'=>'Menunggu (Pending)','active'=>'Aktif (Active)','completed'=>'Selesai (Completed)','cancelled'=>'Dibatalkan (Cancelled)','expired'=>'Kadaluarsa (Expired)'] as $k => $v)
                                <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- PAYMENT STATUS --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Status Pembayaran</label>
                    <div class="relative">
                        <select name="payment_status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer">
                            @foreach (['pending'=>'Menunggu (Pending)','paid'=>'Berhasil (Paid)','failed'=>'Gagal (Failed)','refunded'=>'Dikembalikan (Refunded)'] as $k => $v)
                                <option value="{{ $k }}" {{ $pay === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- START DATE --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Tanggal Mulai</label>
                    <input type="date" name="starts_at" value="{{ $start }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                </div>

                {{-- END DATE --}}
                <div>
                    <label class="block text-sm font-bold text-text-main mb-2">Tanggal Berakhir</label>
                    <input type="date" name="ends_at" value="{{ $end }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Tambahan --}}
    <div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Informasi Tambahan
            </h2>
        </div>
        
        <div class="p-8 space-y-6">
            {{-- REFERENCE --}}
            <div>
                <label class="block text-sm font-bold text-text-main mb-2">No. Referensi / Invoice (Opsional)</label>
                <input type="text" name="reference" value="{{ $ref }}" placeholder="Misal: INV-2024-0001"
                       class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400">
            </div>

            {{-- NOTES --}}
            <div>
                <label class="block text-sm font-bold text-text-main mb-2">Catatan Internal (Opsional)</label>
                <textarea name="notes" rows="4" placeholder="Tambahkan catatan khusus untuk pendaftaran ini..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main placeholder-gray-400 resize-none">{{ $notes }}</textarea>
            </div>
        </div>
    </div>
</div>
