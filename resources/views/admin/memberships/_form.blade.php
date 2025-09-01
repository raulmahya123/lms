@php
  $userVal   = old('user_id', optional($membership)->user_id);
  $planVal   = old('plan_id', optional($membership)->plan_id);
  $statusVal = old('status', optional($membership)->status ?? 'active');
  $actVal    = old('activated_at', optional(optional($membership)->activated_at)->format('Y-m-d\TH:i'));
  $expVal    = old('expires_at', optional(optional($membership)->expires_at)->format('Y-m-d\TH:i'));
@endphp

<div class="grid md:grid-cols-2 gap-4">
  {{-- USER --}}
  <div>
    <label class="block text-sm font-medium mb-1">User</label>
    <select name="user_id" class="w-full border rounded-xl px-3 py-2" required>
      <option value="" disabled {{ $userVal ? '' : 'selected' }}>— pilih user —</option>
      @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ (string)$userVal === (string)$user->id ? 'selected' : '' }}>
          {{ $user->name ?? $user->email }}
        </option>
      @endforeach
    </select>
    @error('user_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- PLAN --}}
  <div>
    <label class="block text-sm font-medium mb-1">Plan</label>
    <select name="plan_id" class="w-full border rounded-xl px-3 py-2" required>
      <option value="" disabled {{ $planVal ? '' : 'selected' }}>— pilih plan —</option>
      @foreach ($plans as $plan)
        <option value="{{ $plan->id }}" {{ (string)$planVal === (string)$plan->id ? 'selected' : '' }}>
          {{ $plan->name ?? 'Plan #'.$plan->id }}
        </option>
      @endforeach
    </select>
    @error('plan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- STATUS --}}
  <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="w-full border rounded-xl px-3 py-2">
      @foreach (['active'=>'Active','inactive'=>'Inactive','expired'=>'Expired'] as $k => $v)
        <option value="{{ $k }}" {{ $statusVal === $k ? 'selected' : '' }}>{{ $v }}</option>
      @endforeach
    </select>
    @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- ACTIVATED AT --}}
  <div>
    <label class="block text-sm font-medium mb-1">Activated At</label>
    <input type="datetime-local" name="activated_at" value="{{ $actVal }}" class="w-full border rounded-xl px-3 py-2">
    @error('activated_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- EXPIRES AT --}}
  <div>
    <label class="block text-sm font-medium mb-1">Expires At</label>
    <input type="datetime-local" name="expires_at" value="{{ $expVal }}" class="w-full border rounded-xl px-3 py-2">
    @error('expires_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
</div>
