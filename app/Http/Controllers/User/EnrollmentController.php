<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment, Membership};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $uid = Auth::id();

        $enrollments = Enrollment::query()
            ->with(['course:id,title,cover'])
            ->where('user_id', $uid)
            ->select('enrollments.*')

            // ===== total lessons per course (join lessons -> modules -> courses) =====
            ->selectSub(function ($q) {
                $q->from('lessons as l')
                  ->join('modules as m', 'm.id', '=', 'l.module_id')
                  ->whereColumn('m.course_id', 'enrollments.course_id')
                  ->selectRaw('COUNT(*)');
            }, 'total_lessons')

            // ===== lessons selesai oleh user ini (completed_at not null) =====
            ->selectSub(function ($q) use ($uid) {
                $q->from('lesson_progresses as lp')
                  ->join('lessons as l', 'l.id', '=', 'lp.lesson_id')
                  ->join('modules as m', 'm.id', '=', 'l.module_id')
                  ->where('lp.user_id', $uid)
                  ->whereNotNull('lp.completed_at')
                  ->whereColumn('m.course_id', 'enrollments.course_id')
                  ->selectRaw('COUNT(DISTINCT lp.lesson_id)');
            }, 'done_lessons')

            ->latest('activated_at')
            ->paginate(20);

        // Tambahkan progress_percent utk langsung dipakai di Blade
        $enrollments->getCollection()->transform(function ($e) {
            $total = (int) ($e->total_lessons ?? 0);
            $done  = (int) ($e->done_lessons  ?? 0);
            $e->progress_percent = $total > 0 ? (int) round($done * 100 / $total) : 0;
            return $e;
        });

        return view('app.enrollments.index', compact('enrollments'));
    }

    public function store(Request $request, Course $course)
    {
        abort_unless($course->is_published, 404);

        // Sudah aktif?
        $already = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($already) {
            return redirect()->route('app.my.courses')->with('ok', 'Kamu sudah ter-enroll.');
        }

        // Membership aktif? (ambil satu yang masih valid)
        $activeMembership = Membership::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('expires_at')
            ->first();

        $hasActiveMembership = (bool) $activeMembership;

        // Kursus gratis jika: punya membership ATAU is_free true ATAU price <= 0/null
        $isCourseFree = $course->is_free
            || is_null($course->price)
            || (float) $course->price <= 0;

        if ($hasActiveMembership || $isCourseFree) {
            $via = $hasActiveMembership ? 'membership' : 'free';
            $exp = $hasActiveMembership ? ($activeMembership?->expires_at) : null;

            $enr = Enrollment::firstOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $course->id],
                [
                    'status'            => 'active',
                    'activated_at'      => now(),
                    'access_via'        => $via,
                    'access_expires_at' => $exp,
                ]
            );

            if ($enr->wasRecentlyCreated === false) {
                $enr->update([
                    'status'            => 'active',
                    'access_via'        => $via,
                    'access_expires_at' => $exp,
                ]);
            }

            return redirect()->route('app.my.courses')->with('ok', 'Berhasil enroll.');
        }

        // Tidak gratis → ke checkout
        return redirect()
            ->route('app.courses.checkout', $course)
            ->with('info', 'Silakan lanjutkan pembayaran untuk course ini.');
    }
}
