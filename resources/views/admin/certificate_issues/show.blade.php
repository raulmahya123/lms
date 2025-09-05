{{-- resources/views/admin/certificate_issues/show.blade.php --}}
@extends('layouts.admin')
@section('title','Certificate Issue — BERKEMAH')

@section('content')
@php($issue = $issue ?? $certificate_issue ?? null)
@if(!$issue)
  <div class="p-4 rounded-xl border bg-white text-red-600">
    Certificate Issue data not provided.
  </div>
@else
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Certificate Issue</h1>
      <p class="text-sm opacity-70">Detail penerbitan sertifikat</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.certificate-issues.index') }}"
         class="px-4 py-2 rounded-xl border hover:bg-gray-50">Back</a>
      <form method="POST" action="{{ route('admin.certificate-issues.destroy',$issue) }}"
            onsubmit="return confirm('Delete this issue?')">
        @csrf @method('DELETE')
        <button class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50">
          Delete
        </button>
      </form>
    </div>
  </div>

  <div class="bg-white rounded-xl border p-6 space-y-5 shadow-sm">
    <div>
      <h2 class="text-lg font-semibold">ID</h2>
      <p>#{{ $issue->id }}</p>
    </div>

    <div>
      <h2 class="text-lg font-semibold">Template</h2>
      @if($issue->template)
        <a href="{{ route('admin.certificate-templates.show',$issue->template) }}"
           class="text-blue-600 underline">
          {{ $issue->template->name }}
        </a>
      @else
        <p class="text-sm opacity-60">—</p>
      @endif
    </div>

    <div>
      <h2 class="text-lg font-semibold">User</h2>
      @if($issue->user)
        <div>{{ $issue->user->name }} <span class="text-xs text-gray-500">({{ $issue->user->email }})</span></div>
      @else
        <p class="text-sm opacity-60">—</p>
      @endif
    </div>

    <div>
      <h2 class="text-lg font-semibold">Course / Context</h2>
      <p>{{ $issue->course->title ?? $issue->context ?? '—' }}</p>
    </div>

    <div>
      <h2 class="text-lg font-semibold">Certificate No</h2>
      <p>{{ $issue->cert_no ?? '—' }}</p>
    </div>

    <div>
      <h2 class="text-lg font-semibold">Meta JSON</h2>
      <pre class="text-xs bg-gray-50 border rounded-lg p-3 overflow-auto">
{{ json_encode(is_array($issue->meta) ? $issue->meta : (json_decode($issue->meta ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}
      </pre>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="text-lg font-semibold">Issued At</h2>
        <p>{{ optional($issue->issued_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
      <div>
        <h2 class="text-lg font-semibold">Expired At</h2>
        <p>{{ optional($issue->expired_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="text-lg font-semibold">Created At</h2>
        <p>{{ optional($issue->created_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
      <div>
        <h2 class="text-lg font-semibold">Updated At</h2>
        <p>{{ optional($issue->updated_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
    </div>
  </div>

</div>
@endif
@endsection
