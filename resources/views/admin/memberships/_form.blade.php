@csrf
<div class="rounded-2xl border bg-white shadow p-6 space-y-5">
  {{-- User --}}
  <div>
    <label class="block text-sm font-medium mb-1">User</label>
    <select name="user_id" class="w-full border rounded-xl px-3 py-2 focus:ring-blue-600 focus:border-blue-600">
      <option value="">— pilih user —</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" @selected(old('user_id', $membership->user_id ?? null)==$u->id)>
          {{ $u->name }} ({{ $u->email }})
        </option>
      @endforeach
    </select>
  </div>

  {{-- Plan --}}
  <div>
    <label class="block text-sm font-medium mb-1">Plan</label>
    <select name="plan_id" class="w-full border rounded-xl px-3 py-2 focus:ring-blue-600 focus:border-blue-600">
      <option value="">— pilih plan —</option>
      @foreach($plans as $pl)
        <option value="{{ $pl->id }}" @selected(old('plan_id', $membership->plan_id ?? null)==$pl->id)>
          {{ $pl->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Status --}}
  <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full border rounded-xl px-3 py-2 focus:ring-blue-600 focus:border-blue-600">
      @foreach(['active','expired','cancelled'] as $st)
        <option value="{{ $st }}" @selected(old('status', $membership->status ?? null)==$st)>
          {{ ucfirst($st) }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Expired At --}}
  <div>
    <label class="block text-sm font-medium mb-1">Expired At</label>
    <input type="date" name="expired_at" value="{{ old('expired_at', $membership->expired_at ?? '') }}"
           class="w-full border rounded-xl px-3 py-2 focus:ring-blue-600 focus:border-blue-600">
  </div>
</div>

{{-- ACTIONS --}}
<div class="flex items-center gap-3 mt-6">
  <a href="{{ route('admin.memberships.index') }}"
     class="inline-flex items-center gap-2 px-4 py-2 border rounded-xl hover:bg-gray-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Cancel
  </a>
  <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-500">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ $submit ?? 'Save' }}
  </button>
</div>
