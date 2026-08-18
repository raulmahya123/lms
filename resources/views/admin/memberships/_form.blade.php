{{-- _form.blade.php untuk Create/Edit Membership --}}
<div class="bg-white rounded-3xl border border-gray-50 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
    <div class="px-8 py-5 border-b border-gray-50 bg-gray-50/50">
        <h2 class="text-lg font-extrabold text-text-main flex items-center gap-2">
            <svg class="w-5 h-5 text-tosca" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Informasi Hak Akses
        </h2>
    </div>
    
    <div class="p-8 space-y-6">
        {{-- User --}}
        <div>
            <label class="block text-sm font-bold text-text-main mb-2">Pengguna (User) <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="user_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                    <option value="">— Pilih Pengguna —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id', $membership->user_id ?? null) == $u->id)>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <p class="text-xs text-text-soft mt-2">Pilih akun yang akan diberikan hak akses paket.</p>
        </div>

        {{-- Plan --}}
        <div>
            <label class="block text-sm font-bold text-text-main mb-2">Paket Langganan (Plan) <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="plan_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                    <option value="">— Pilih Paket —</option>
                    @foreach($plans as $pl)
                        <option value="{{ $pl->id }}" @selected(old('plan_id', $membership->plan_id ?? null) == $pl->id)>
                            {{ $pl->name }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <p class="text-xs text-text-soft mt-2">Pilih paket yang akan diaktifkan untuk pengguna ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Status --}}
            <div>
                <label class="block text-sm font-bold text-text-main mb-2">Status <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="status" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main appearance-none cursor-pointer" required>
                        <option value="active" @selected(old('status', $membership->status ?? 'active') == 'active')>Aktif (Active)</option>
                        <option value="expired" @selected(old('status', $membership->status ?? null) == 'expired')>Kadaluarsa (Expired)</option>
                        <option value="cancelled" @selected(old('status', $membership->status ?? null) == 'cancelled')>Dibatalkan (Cancelled)</option>
                        <option value="pending" @selected(old('status', $membership->status ?? null) == 'pending')>Menunggu (Pending)</option>
                        <option value="inactive" @selected(old('status', $membership->status ?? null) == 'inactive')>Tidak Aktif (Inactive)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Expired At --}}
            <div>
                <label class="block text-sm font-bold text-text-main mb-2">Tanggal Berakhir (Expired At)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    @php
                        // Format expired_at value for HTML date input (Y-m-d)
                        $expiredAtValue = old('expired_at');
                        if (!$expiredAtValue && isset($membership->expires_at)) {
                            // Cek jika field namanya expires_at di model
                            $expiredAtValue = $membership->expires_at instanceof \Carbon\Carbon 
                                ? $membership->expires_at->format('Y-m-d')
                                : (is_string($membership->expires_at) ? substr($membership->expires_at, 0, 10) : '');
                        } elseif (!$expiredAtValue && isset($membership->expired_at)) {
                            $expiredAtValue = $membership->expired_at instanceof \Carbon\Carbon 
                                ? $membership->expired_at->format('Y-m-d')
                                : (is_string($membership->expired_at) ? substr($membership->expired_at, 0, 10) : '');
                        }
                    @endphp
                    <input type="date" name="expired_at" value="{{ $expiredAtValue }}"
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-tosca focus:ring-4 focus:ring-tosca/10 outline-none transition-all font-medium text-text-main">
                </div>
                <p class="text-[10px] text-text-soft mt-2 leading-tight">Kosongkan jika masa berlaku mengikuti aturan paket (misal: "Selamanya").</p>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
    <a href="{{ route('admin.memberships.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors text-center">
        Batal
    </a>
    <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-tosca text-white font-bold hover:bg-tosca-dark transition-colors shadow-sm shadow-tosca/30 flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ $submit ?? 'Simpan' }}
    </button>
</div>
