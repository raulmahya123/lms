{{-- resources/views/admin/certificate_templates/show.blade.php --}}
@extends('layouts.admin')
@section('title','Certificate Template — BERKEMAH')

@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Certificate Template</h1>
      <p class="text-sm opacity-70">Detail template sertifikat</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.certificate-templates.edit', $template) }}"
         class="px-4 py-2 rounded-xl border hover:bg-gray-50">Edit</a>
      <a href="{{ route('admin.certificate-templates.index') }}"
         class="px-4 py-2 rounded-xl border hover:bg-gray-50">Back</a>
    </div>
  </div>

  <div class="bg-white rounded-xl border p-6 space-y-5 shadow-sm">
    <div>
      <h2 class="text-lg font-semibold">Name</h2>
      <p>{{ $template->name }}</p>
    </div>

    <div>
      <h2 class="text-lg font-semibold">Active</h2>
      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
        {{ $template->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
        {{ $template->is_active ? 'Yes' : 'No' }}
      </span>
    </div>

    <div>
      <h2 class="text-lg font-semibold">Background URL</h2>
      @if($template->background_url)
        <div class="space-y-2">
          <a href="{{ $template->background_url }}" target="_blank" class="text-blue-600 underline">{{ $template->background_url }}</a>
          <img src="{{ $template->background_url }}" alt="Background preview" class="rounded-lg border max-w-full">
        </div>
      @else
        <p class="text-sm opacity-60">—</p>
      @endif
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="text-lg font-semibold">Fields JSON</h2>
        <pre class="text-xs bg-gray-50 border rounded-lg p-3 overflow-auto">
{{ json_encode(is_array($template->fields_json) ? $template->fields_json : (json_decode($template->fields_json ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}
        </pre>
      </div>
      <div>
        <h2 class="text-lg font-semibold">SVG JSON</h2>
        <pre class="text-xs bg-gray-50 border rounded-lg p-3 overflow-auto">
{{ json_encode(is_array($template->svg_json) ? $template->svg_json : (json_decode($template->svg_json ?? '[]', true) ?: []), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}
        </pre>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="text-lg font-semibold">Created At</h2>
        <p>{{ optional($template->created_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
      <div>
        <h2 class="text-lg font-semibold">Updated At</h2>
        <p>{{ optional($template->updated_at)->format('d M Y H:i') ?: '—' }}</p>
      </div>
    </div>
  </div>

</div>
@endsection
