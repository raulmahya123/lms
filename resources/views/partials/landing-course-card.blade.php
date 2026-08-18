@php
  $cover = $course->cover ? asset('storage/' . ltrim($course->cover, '/')) : $fallbackCover;
  $courseUrl = auth()->check() ? route('app.courses.show', $course) : route('register');
  $modules = (int)($course->modules_count ?? 0);
  $students = (int)($course->enrollments_count ?? 0);
  $progress = (int)($course->progress_percent ?? 0);
  $done = (int)($course->progress_done ?? 0);
  $total = max(1, (int)($course->progress_total ?? $course->lessons_count ?? 0));
  $level = trim($course->level ?? '') ?: 'All Levels';
@endphp

<a href="{{ $courseUrl }}" class="group soft-card lift flex h-full flex-col overflow-hidden rounded-3xl">
  <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
    <img src="{{ $cover }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/55 to-transparent"></div>
    <div class="absolute left-4 top-4 flex flex-wrap gap-2">
      @if ($popular)
        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-[#6D28D9] shadow-sm">Populer</span>
      @endif
      <span class="rounded-full bg-slate-950/80 px-3 py-1 text-xs font-black text-white backdrop-blur">{{ $level }}</span>
    </div>
  </div>
  <div class="flex flex-1 flex-col p-6">
    <h3 class="line-clamp-2 text-lg font-black text-slate-950 group-hover:text-[#6D28D9]">{{ $course->title }}</h3>
    <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $course->description ?? 'Kelas praktis untuk membangun skill digital.' }}</p>
    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-bold text-slate-500">
      <span>{{ $modules }} modul</span>
      <span class="h-1 w-1 rounded-full bg-slate-300"></span>
      <span>{{ $students }} siswa</span>
    </div>
    <div class="mt-auto pt-5">
      <div class="flex items-center justify-between text-xs font-bold text-slate-500">
        <span>Progress</span>
        <span>{{ $progress }}%</span>
      </div>
      <div class="progress-track mt-2">
        <div class="progress-fill" style="width: {{ $progress }}%"></div>
      </div>
      @auth
        <div class="mt-2 text-xs font-semibold text-slate-400">{{ $done }}/{{ $total }} pelajaran selesai</div>
      @else
        <div class="mt-2 text-xs font-semibold text-slate-400">Login untuk menyimpan progres</div>
      @endauth
    </div>
  </div>
</a>
