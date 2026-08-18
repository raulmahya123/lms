<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment, Membership};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseBrowseController extends Controller
{
    public function index(Request $r)
    {
        $courses = Course::query()
            ->select(['id', 'title', 'description', 'cover', 'is_free', 'price', 'is_published', 'created_at'])
            ->where('is_published', 1)
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%' . trim((string) $r->q) . '%'))
            ->withCount(['modules', 'enrollments'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $uid   = Auth::id();
        $my    = $uid
            ? Enrollment::where('user_id', $uid)->get(['course_id', 'status', 'access_via', 'access_expires_at'])
            : collect();

        $myIds = $my->pluck('course_id')->all();

        $hasMembership = $uid
            ? Membership::where('user_id', $uid)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists()
            : false;

        $lockedIds = $my->filter(function ($e) use ($hasMembership) {
            // pakai helper manual jika model accessor belum di-load:
            if (($e->status ?? '') !== 'active') return false;
            if (($e->access_via ?? null) === 'membership') {
                $notExpired = is_null($e->access_expires_at) || now()->lt($e->access_expires_at);
                return !($hasMembership && $notExpired);
            }
            return false; // purchase / free = tidak terkunci
        })->pluck('course_id')->all();

        return view('app.courses.index', compact('courses', 'myIds', 'lockedIds'));
    }


    public function show(Course $course)
    {
        abort_unless($course->is_published, 404);

        $course->load([
            'modules' => fn($q) => $q
                ->select(['id', 'course_id', 'title', 'ordering'])
                ->with(['lessons' => fn($qq) => $qq
                    ->select(['id', 'module_id', 'title', 'ordering', 'is_free'])
                    ->orderBy('ordering')])
                ->orderBy('ordering'),
            'creator:id,name',
        ]);

        $userId     = Auth::id();

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        $isEnrolled = (bool) $enrollment;

        $hasMembership = Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        // butuh helper di model Enrollment: hasEffectiveAccess()
        $locked = $enrollment ? !$enrollment->hasEffectiveAccess($hasMembership) : false;

        return view('app.courses.show', compact('course', 'isEnrolled', 'hasMembership', 'locked'));
    }
}
