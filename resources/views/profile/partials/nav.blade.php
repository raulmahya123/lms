@php
$active = fn($name) => request()->routeIs($name)
    ? 'bg-blue-600 text-white'
    : 'bg-white text-ink-700 hover:bg-ivory-100 dark:bg-ink-800 dark:text-ivory-100';
@endphp

<div class="flex flex-wrap gap-2 mb-6">
  <a href="{{ route('profile.info.edit') }}" class="px-3 py-2 rounded-lg {{ $active('profile.info.edit') }}">
    <i class="fa-solid fa-id-badge mr-2"></i>Profile Info
  </a>
  <a href="{{ route('profile.pass.edit') }}" class="px-3 py-2 rounded-lg {{ $active('profile.pass.edit') }}">
    <i class="fa-solid fa-key mr-2"></i>Update Password
  </a>
  <a href="{{ route('profile.delete.confirm') }}" class="px-3 py-2 rounded-lg {{ $active('profile.delete.confirm') }}">
    <i class="fa-solid fa-user-slash mr-2"></i>Delete Account
  </a>
</div>
