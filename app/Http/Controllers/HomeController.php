<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Membership;
use App\Models\Module;
use App\Models\Plan;
use App\Models\PsyTest;
use App\Models\QaThread;
use App\Models\Quiz;
use App\Models\TestIq;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'courses'     => Course::where('is_published', 1)->count(),
            'modules'     => Module::count(),
            'lessons'     => Lesson::count(),
            'enrollments' => Enrollment::count(),
            'quizzes'     => Quiz::count(),
        ];

        $latestCourses = Course::query()
            ->select(['id', 'title', 'description', 'cover', 'is_free', 'price', 'is_published', 'created_at'])
            ->where('is_published', 1)
            ->withCount(['modules', 'enrollments', 'lessons as lessons_count'])
            ->latest('created_at')
            ->take(3)
            ->get();

        $popularCourses = Course::query()
            ->select(['id', 'title', 'description', 'cover', 'is_free', 'price', 'is_published', 'created_at'])
            ->where('is_published', 1)
            ->withCount(['modules', 'enrollments', 'lessons as lessons_count'])
            ->orderByDesc('enrollments_count')
            ->latest('created_at')
            ->take(3)
            ->get();

        $psyTests = PsyTest::query()
            ->select(['id', 'name', 'slug', 'track', 'type', 'is_active', 'created_at'])
            ->where('is_active', true)
            ->withCount('questions')
            ->latest('created_at')
            ->take(4)
            ->get();

        $plans = Plan::query()
            ->select(['id', 'name', 'price', 'period', 'features'])
            ->withCount('planCourses')
            ->take(3)
            ->get();

        $today = Carbon::today();
        $activeCoupons = Coupon::query()
            ->select(['id', 'code', 'discount_percent', 'valid_from', 'valid_until', 'created_at'])
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
            })
            ->latest('created_at')
            ->take(3)
            ->get();

        $latestThreads = QaThread::with(['user:id,name', 'course:id,title', 'lesson:id,title'])
            ->select(['id', 'user_id', 'course_id', 'lesson_id', 'title', 'body', 'status', 'created_at'])
            ->withCount('replies')
            ->latest('created_at')
            ->take(3)
            ->get();

        $iqTests = TestIq::query()
            ->select(['id', 'title', 'description', 'questions', 'duration_minutes', 'is_active', 'created_at'])
            ->where('is_active', true)
            ->latest('created_at')
            ->take(2)
            ->get();

        $isMember = false;
        if (Auth::check()) {
            $isMember = Membership::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists();
        }

        [$latestCourses, $popularCourses] = $this->attachCourseProgress($latestCourses, $popularCourses);

        return view('welcome', compact(
            'stats',
            'latestCourses',
            'popularCourses',
            'plans',
            'activeCoupons',
            'psyTests',
            'latestThreads',
            'iqTests',
            'isMember',
        ));
    }

    private function attachCourseProgress($latestCourses, $popularCourses): array
    {
        $courseIds = $latestCourses->pluck('id')
            ->merge($popularCourses->pluck('id'))
            ->unique()
            ->values();

        $completedByCourse = collect();
        if (Auth::check() && $courseIds->isNotEmpty()) {
            $completedByCourse = DB::table('lesson_progresses as lp')
                ->join('lessons as l', 'l.id', '=', 'lp.lesson_id')
                ->join('modules as m', 'm.id', '=', 'l.module_id')
                ->where('lp.user_id', Auth::id())
                ->whereNotNull('lp.completed_at')
                ->whereIn('m.course_id', $courseIds)
                ->select('m.course_id', DB::raw('COUNT(DISTINCT lp.lesson_id) as done'))
                ->groupBy('m.course_id')
                ->pluck('done', 'course_id');
        }

        $applyProgress = function ($courses) use ($completedByCourse) {
            return $courses->map(function ($course) use ($completedByCourse) {
                $total = (int)($course->lessons_count ?? 0);
                $done = (int)($completedByCourse[$course->id] ?? 0);

                $course->progress_done = $done;
                $course->progress_total = $total;
                $course->progress_percent = $total > 0 ? round(($done / $total) * 100) : 0;

                return $course;
            });
        };

        return [$applyProgress($latestCourses), $applyProgress($popularCourses)];
    }
}
