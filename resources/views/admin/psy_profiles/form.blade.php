@csrf
<div class="grid md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium">Test</label>
    <select name="test_id" class="mt-1 w-full rounded-lg border-gray-300" required>
      <option value="">— pilih —</option>
      @foreach($tests as $t)
        <option value="{{ $t->id }}" @selected(old('test_id', $profile->test_id ?? '')==$t->id)>
          {{ $t->name }} ({{ $t->track }})
        </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium">Key</label>
    <input type="text" name="key" value="{{ old('key', $profile->key ?? '') }}"
           class="mt-1 w-full rounded-lg border-gray-300" placeholder="BACKEND_LOW" required>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm font-medium">Name</label>
    <input type="text" name="name" value="{{ old('name', $profile->name ?? '') }}"
           class="mt-1 w-full rounded-lg border-gray-300" placeholder="Backend Fit — Low" required>
  </div>

  <div>
    <label class="block text-sm font-medium">Min Total</label>
    <input type="number" name="min_total" value="{{ old('min_total', $profile->min_total ?? 0) }}"
           class="mt-1 w-full rounded-lg border-gray-300" min="0" required>
  </div>
  <div>
    <label class="block text-sm font-medium">Max Total</label>
    <input type="number" name="max_total" value="{{ old('max_total', $profile->max_total ?? 0) }}"
           class="mt-1 w-full rounded-lg border-gray-300" min="0" required>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm font-medium">Description (opsional)</label>
    <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border-gray-300"
      placeholder="Narasi rekomendasi/cocoknya apa…">{{ old('description', $profile->description ?? '') }}</textarea>
  </div>
</div>

<div class="mt-4 flex items-center gap-3">
  <button class="px-4 py-2 rounded-xl bg-blue-600 text-white">Save</button>
  <a href="{{ route('admin.psy-profiles.index', ['test_id'=>old('test_id',$profile->test_id ?? null)]) }}">Cancel</a>
</div>
