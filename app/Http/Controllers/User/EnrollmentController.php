<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $uid = Auth::id();

        $enrollments = Enrollment::query()
            ->with(['course:id,title,cover_url'])
            ->where('user_id', $uid)
            ->select('enrollments.*')

            // ===== total lessons per course (join lessons -> modules -> courses) =====
            ->selectSub(function($q) {
                $q->from('lessons as l')
                  ->join('modules as m', 'm.id', '=', 'l.module_id')
                  ->whereColumn('m.course_id', 'enrollments.course_id')
                  ->selectRaw('COUNT(*)');
            }, 'total_lessons')

            // ===== lessons selesai oleh user ini (completed_at not null) =====
            ->selectSub(function($q) use ($uid) {
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

        // Tambahkan properti progress_percent agar langsung siap di Blade
        $enrollments->getCollection()->transform(function ($e) {
            $total = (int) ($e->total_lessons ?? 0);
            $done  = (int) ($e->done_lessons  ?? 0);
            $e->progress_percent = $total > 0 ? (int) round($done * 100 / $total) : 0;
            return $e;
        });

        return view('app.enrollments.index', compact('enrollments'));
    }

    public function store(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);
        $exists = Enrollment::where('user_id',Auth::id())->where('course_id',$course->id)->first();
        if ($exists) return back()->with('status','Kamu sudah terdaftar di kursus ini.');

        DB::transaction(function() use ($course) {
            Enrollment::create([
                'user_id'=>Auth::id(),
                'course_id'=>$course->id,
                'status'=>'active',
                'activated_at'=>now(),
            ]);
        });

        return redirect()->route('app.courses.show',$course)->with('status','Enroll berhasil.');
    }
}
