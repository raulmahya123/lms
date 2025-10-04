@extends('app.layouts.base')
@section('title', $course->title)

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\{Enrollment, Membership};

    $uid = Auth::id();

    // Enrollment user utk course ini (kalau ada)
    $enr = Enrollment::where('user_id', $uid)
        ->where('course_id', $course->id)
        ->first();

    // Status membership aktif saat ini?
    $hasMembership = isset($hasMembership)
        ? (bool) $hasMembership
        : Membership::where('user_id', $uid)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

    // Akses efektif?
    $effectiveAccess = false;
    $lockedByMembership = false;

    if ($enr && $enr->status === 'active') {
        if (in_array($enr->access_via, ['purchase','free'], true)) {
            $effectiveAccess = true; // beli / gratis = selalu efektif
        } elseif ($enr->access_via === 'membership') {
            $notExpired = is_null($enr->access_expires_at) || now()->lt($enr->access_expires_at);
            $effectiveAccess    = ($hasMembership && $notExpired);
            $lockedByMembership = !$effectiveAccess; // dulu via membership tapi sekarang tak aktif
        } else {
            // data lama tanpa access_via
            $effectiveAccess = true;
        }
    }

    // ==== COVER: pakai kolom 'cover' (relative path ke storage) ====
    $cover = $course->cover
        ? asset('storage/' . ltrim($course->cover, '/'))
        : asset('assets/images/placeholder-course.png');
@endphp

<div class="flex items-start gap-6">
  <div class="flex-1">

    {{-- Banner info kalau akses terkunci karena membership mati --}}
    @if($lockedByMembership)
      <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-3 py-2 text-sm">
        Akses course ini <strong>terkunci</strong> karena membership kamu tidak aktif/berakhir.
        Kamu tetap bisa membeli course ini per unit, atau perpanjang membership.
      </div>
    @endif

    <img src="{{ $cover }}" class="w-full rounded border object-cover" alt="{{ $course->title }}">
    <h1 class="text-2xl font-semibold mt-4">{{ $course->title }}</h1>
    <p class="mt-2 text-gray-700">{{ $course->description }}</p>

    <h2 class="mt-6 font-semibold">Kurikulum</h2>
    @foreach($course->modules as $m)
      <div class="mt-3">
        <div class="font-medium">{{ $m->title }}</div>
        <ul class="mt-2 space-y-1">
          @foreach($m->lessons as $l)
            <li>
              @if($lockedByMembership)
                {{-- kurikulum tetap terlihat, tapi non-klik kalau terkunci --}}
                <span class="inline-flex items-center text-gray-400">
                  {{ $l->ordering }}. {{ $l->title }}
                  <span class="ml-2 text-[10px] px-2 py-0.5 rounded bg-gray-200 text-gray-700">Terkunci</span>
                </span>
              @else
                <a class="text-blue-700 hover:underline" href="{{ route('app.lessons.show', $l) }}">
                  {{ $l->ordering }}. {{ $l->title }}
                  @if($l->is_free)
                    <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800">Free</span>
                  @endif
                </a>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    @endforeach
  </div>

  {{-- CTA card (kanan) --}}
  <div class="w-72">
    <div class="p-4 bg-white border rounded space-y-3">
      @if($effectiveAccess)
        <div class="text-emerald-700 font-medium">Kamu sudah terdaftar</div>
      @else
        @if($hasMembership)
          {{-- Membership aktif → enroll gratis via membership (POST ke enroll) --}}
          <form method="POST" action="{{ route('app.courses.enroll', $course) }}">
            @csrf
            <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
              Enroll via Membership (Gratis)
            </button>
          </form>
          <p class="text-xs text-gray-500">Selama membership aktif, akses course mengikuti masa aktif membership.</p>
        @else
          {{-- Membership tidak aktif → bayar per course / free --}}
          @if(($course->price ?? 0) > 0)
            <a href="{{ route('app.courses.checkout', $course) }}"
               class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Beli Course Ini (Checkout)
            </a>
          @else
            <form method="POST" action="{{ route('app.courses.enroll', $course) }}">
              @csrf
              <button class="w-full px-4 py-2 bg-blue-600 text-white rounded">
                Enroll Gratis
              </button>
            </form>
          @endif

          @if($enr && $enr->access_via === 'membership')
            <a href="{{ route('app.memberships.plans') }}"
               class="w-full block text-center px-4 py-2 bg-gray-200 text-gray-700 rounded">
              Perpanjang Membership
            </a>
          @endif
        @endif
      @endif
    </div>
  </div>
</div>
@endsection
