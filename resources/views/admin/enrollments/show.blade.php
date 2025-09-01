@extends('layouts.admin')
@section('title','Enrollment Detail')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-3xl">
  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <div class="text-xs text-gray-500">User</div>
      <div class="font-medium">{{ $enrollment->user?->name }}</div>
      <div class="text-sm text-gray-600">{{ $enrollment->user?->email }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Course</div>
      <div class="font-medium">{{ $enrollment->course?->title }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Status</div>
      <div>{{ ucfirst($enrollment->status) }}</div>
    </div>
    <div>
      <div class="text-xs text-gray-500">Activated</div>
      <div>{{ $enrollment->activated_at }}</div>
    </div>
  </div>

  <hr class="my-6">

  <form method="POST" action="{{ route('admin.enrollments.update',$enrollment) }}" class="grid md:grid-cols-2 gap-4">
    @csrf @method('PUT')
    <div>
      <label class="block text-sm">Status</label>
      <select name="status" class="w-full border rounded px-3 py-2">
        @foreach(['pending','active','inactive'] as $st)
          <option value="{{ $st }}" @selected(old('status',$enrollment->status)===$st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm">Activated At</label>
      <input type="datetime-local" name="activated_at" class="w-full border rounded px-3 py-2"
             value="{{ old('activated_at', optional($enrollment->activated_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="md:col-span-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
      <a href="{{ route('admin.enrollments.index') }}" class="px-4 py-2 rounded border ml-2">Back</a>
    </div>
  </form>
</div>
@endsection
