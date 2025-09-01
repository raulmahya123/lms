@extends('layouts.admin')
@section('title','Plans')

@section('content')
<div class="mb-4">
  <a href="{{ route('admin.plans.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">+ New Plan</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">#</th>
        <th class="p-2 text-left">Name</th>
        <th class="p-2 text-left">Price</th>
        <th class="p-2 text-left">Period</th>
        <th class="p-2 text-left">Courses</th>
        <th class="p-2 text-left">Members</th>
        <th class="p-2 text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($plans as $p)
        <tr class="border-t">
          <td class="p-2">{{ $p->id }}</td>
          <td class="p-2 font-medium">{{ $p->name }}</td>
          <td class="p-2">Rp {{ number_format($p->price,0,',','.') }}</td>
          <td class="p-2">{{ ucfirst($p->period) }}</td>
          <td class="p-2">{{ $p->plan_courses_count ?? $p->planCourses()->count() }}</td>
          <td class="p-2">{{ $p->memberships_count ?? $p->memberships()->count() }}</td>
          <td class="p-2 text-center">
            <a href="{{ route('admin.plans.edit',$p) }}" class="text-blue-600 underline">Edit</a>
            <form method="POST" action="{{ route('admin.plans.destroy',$p) }}" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 underline" onclick="return confirm('Delete this plan?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="7">No plans.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $plans->links() }}</div>
@endsection
