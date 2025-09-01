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

<div class="grid md:grid-cols-2 gap-4">
  {{-- USER --}}
  <div>
    <label class="block text-sm font-medium mb-1">Pengguna <span class="text-red-500">*</span></label>
    <select name="user_id" required class="w-full border rounded-xl px-3 py-2">
      <option value="" disabled {{ $uVal ? '' : 'selected' }}>— pilih user —</option>
      @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ (string)$uVal === (string)$user->id ? 'selected' : '' }}>
          {{ $user->name ?? $user->email ?? ('User #'.$user->id) }}
        </option>
      @endforeach
    </select>
    @error('user_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- COURSE --}}
  <div>
    <label class="block text-sm font-medium mb-1">Course/Kelas <span class="text-red-500">*</span></label>
    <select name="course_id" required class="w-full border rounded-xl px-3 py-2">
      <option value="" disabled {{ $cVal ? '' : 'selected' }}>— pilih course —</option>
      @foreach ($courses as $course)
        <option value="{{ $course->id }}" {{ (string)$cVal === (string)$course->id ? 'selected' : '' }}>
          {{ $course->title ?? $course->name ?? ('Course #'.$course->id) }}
        </option>
      @endforeach
    </select>
    @error('course_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- MEMBERSHIP/PLAN (opsional) --}}
  <div>
    <label class="block text-sm font-medium mb-1">Membership/Plan (opsional)</label>
    <select name="membership_id" class="w-full border rounded-xl px-3 py-2">
      <option value="" {{ $mVal ? '' : 'selected' }}>— tanpa membership —</option>
      @foreach ($memberships as $plan)
        <option value="{{ $plan->id }}" {{ (string)$mVal === (string)$plan->id ? 'selected' : '' }}>
          {{ $plan->name ?? ('Plan #'.$plan->id) }}
        </option>
      @endforeach
    </select>
    @error('membership_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- STATUS ENROLLMENT --}}
  <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full border rounded-xl px-3 py-2">
      @foreach (['pending'=>'Pending','active'=>'Active','completed'=>'Completed','cancelled'=>'Cancelled','expired'=>'Expired'] as $k => $v)
        <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
      @endforeach
    </select>
    @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- PAYMENT STATUS --}}
  <div>
    <label class="block text-sm font-medium mb-1">Payment Status</label>
    <select name="payment_status" class="w-full border rounded-xl px-3 py-2">
      @foreach (['pending'=>'Pending','paid'=>'Paid','failed'=>'Failed','refunded'=>'Refunded'] as $k => $v)
        <option value="{{ $k }}" {{ $pay === $k ? 'selected' : '' }}>{{ $v }}</option>
      @endforeach
    </select>
    @error('payment_status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- START & END DATE --}}
  <div>
    <label class="block text-sm font-medium mb-1">Mulai</label>
    <input type="date" name="starts_at" value="{{ $start }}" class="w-full border rounded-xl px-3 py-2">
    @error('starts_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Berakhir</label>
    <input type="date" name="ends_at" value="{{ $end }}" class="w-full border rounded-xl px-3 py-2">
    @error('ends_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- REFERENCE / INVOICE --}}
  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">No. Referensi/Invoice (opsional)</label>
    <input type="text" name="reference" value="{{ $ref }}" class="w-full border rounded-xl px-3 py-2" placeholder="INV-2025-0001">
    @error('reference') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  {{-- NOTES --}}
  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
    <textarea name="notes" rows="4" class="w-full border rounded-xl px-3 py-2" placeholder="Catatan tambahan...">{{ $notes }}</textarea>
    @error('notes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>
</div>
