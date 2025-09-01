@extends('layouts.admin')
@section('title','Create Plan')

@section('content')
<form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-5 bg-white p-6 rounded shadow max-w-3xl">
  @csrf
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
      @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Price (Rp)</label>
      <input type="number" name="price" class="w-full border rounded px-3 py-2" value="{{ old('price',0) }}" min="0" required>
      @error('price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Period</label>
    <select name="period" class="w-full border rounded px-3 py-2" required>
      <option value="monthly" @selected(old('period')==='monthly')>Monthly</option>
      <option value="yearly"  @selected(old('period')==='yearly')>Yearly</option>
    </select>
    @error('period') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div id="features-wrap">
    <label class="block text-sm font-medium mb-1">Features</label>
    <div class="space-y-2">
      <div><input name="features[]" class="w-full border rounded px-3 py-2" placeholder="e.g. Access all beginner courses"></div>
      <div><input name="features[]" class="w-full border rounded px-3 py-2" placeholder="e.g. Certificate"></div>
    </div>
    <button type="button" onclick="addFeature()" class="mt-2 text-sm underline">+ add feature</button>
    @error('features') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Courses included</label>
    <select name="course_ids[]" multiple size="8" class="w-full border rounded px-3 py-2">
      @foreach($courses as $c)
        <option value="{{ $c->id }}">{{ $c->title }}</option>
      @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">Tahan CTRL/⌘ untuk pilih banyak.</p>
    @error('course_ids.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 rounded border">Cancel</a>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
  </div>
</form>

<script>
function addFeature(){
  const wrap = document.querySelector('#features-wrap .space-y-2');
  const div = document.createElement('div');
  div.innerHTML = '<input name="features[]" class="w-full border rounded px-3 py-2" placeholder="Feature">';
  wrap.appendChild(div);
}
</script>
@endsection
