@extends('app.layouts.base')
@section('title', $course->title)

@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
        use App\Models\{Enrollment, Membership};

        $uid = Auth::id();

        // Ambil enrollment user utk course ini
        $enr = Enrollment::where('user_id', $uid)->where('course_id', $course->id)->first();

        // Flag terdaftar
        $isEnrolled = isset($isEnrolled) ? $isEnrolled : (bool) $enr;

        // Membership aktif?
        $hasMembership = isset($hasMembership)
            ? $hasMembership
            : Membership::where('user_id', $uid)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists();

        // Hitung locked (kalau enrollment via membership & membership nonaktif/expired)
        $locked = false;
        if ($enr && $enr->status === 'active' && $enr->access_via === 'membership') {
            $notExpired = is_null($enr->access_expires_at) || now()->lt($enr->access_expires_at);
            $locked = !($hasMembership && $notExpired);
        }
    @endphp

    <div class="flex items-start gap-6">
        <div class="flex-1">
            {{-- Banner terkunci --}}
            @if ($locked)
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-3 py-2 text-sm">
                    Akses course ini <strong>terkunci</strong> karena membership kamu tidak aktif/berakhir.
                    Perpanjang untuk membuka semua pelajaran.
                </div>
            @endif

            <img src="{{ $course->cover_url }}" class="w-full rounded border">
            <h1 class="text-2xl font-semibold mt-4">{{ $course->title }}</h1>
            <p class="mt-2 text-gray-700">{{ $course->description }}</p>

            <h2 class="mt-6 font-semibold">Kurikulum</h2>
            @foreach ($course->modules as $m)
                <div class="mt-3">
                    <div class="font-medium">{{ $m->title }}</div>
                    <ul class="mt-2 space-y-1">
                        @foreach ($m->lessons as $l)
                            <li>
                                @if ($locked)
                                    <span class="inline-flex items-center text-gray-400">
                                        {{ $l->ordering }}. {{ $l->title }}
                                        <span
                                            class="ml-2 text-[10px] px-2 py-0.5 rounded bg-gray-200 text-gray-700">Terkunci</span>
                                    </span>
                                @else
                                    <a class="text-blue-700 hover:underline" href="{{ route('app.lessons.show', $l) }}">
                                        {{ $l->ordering }}. {{ $l->title }}
                                        @if ($l->is_free)
                                            <span
                                                class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-800">Free</span>
                                        @endif
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="w-72">
            <div class="p-4 bg-white border rounded">
                @if ($isEnrolled)
                    @if ($locked)
                        <div class="text-gray-600 text-sm mb-3">
                            Kamu sudah terdaftar via membership, tapi aksesnya <strong>terkunci</strong>.
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('app.memberships.plans') }}"
                                class="w-full block text-center px-4 py-2 bg-gray-200 text-gray-700 rounded">
                                Perpanjang Membership
                            </a>
                            <a href="{{ route('app.courses.checkout', $course) }}"
                                class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Beli Course Ini (Checkout)
                            </a>
                        </div>
                    @else
                        <div class="text-emerald-700 font-medium">Kamu sudah terdaftar</div>
                    @endif
                @else
                    @if (($course->price ?? 0) > 0)
                        {{-- tombol checkout --}}
                        <a href="{{ route('app.courses.checkout', $course) }}"
                            class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Enroll
                        </a>
                    @else
                        {{-- enroll gratis --}}
                        <form method="POST" action="{{ route('app.courses.enroll', $course) }}">
                            @csrf
                            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded">Enroll Gratis</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

    </div>
@endsection
