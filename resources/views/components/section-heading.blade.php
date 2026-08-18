@props(['kicker', 'title', 'subtitle' => null])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
  <div class="max-w-2xl">
    <span class="section-kicker">{{ $kicker }}</span>
    <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $title }}</h2>
    @if ($subtitle)
      <p class="mt-3 text-base leading-7 text-slate-600">{{ $subtitle }}</p>
    @endif
  </div>
  {{ $slot ?? '' }}
</div>
