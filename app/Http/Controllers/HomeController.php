<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ perlu untuk query agregasi
use App\Models\{Course, Module, Lesson, Enrollment, Plan, Coupon, Quiz, PsyTest, TestIq, Membership};
use Carbon\Carbon;
use App\Models\QaThread;

class HomeController extends Controller
{
    public function index(Request $r)
    {
        // --- STAT RINGKAS ---
        $stats = [
            'courses'     => Course::where('is_published', 1)->count(),
            'modules'     => Module::count(),
            'lessons'     => Lesson::count(),
            'enrollments' => Enrollment::count(),
            'quizzes'     => Quiz::count(),
        ];

        // --- KELAS TERBARU (publish) ---
        $latestCourses = Course::query()
            ->where('is_published', 1)
            ->withCount([
                'modules',
                'enrollments',
                'lessons as lessons_count', // ✅ total lesson / course
            ])
            ->latest('id')
            ->take(6)
            ->get();

        // --- KELAS POPULER (banyak enrollment) ---
        $popularCourses = Course::query()
            ->where('is_published', 1)
            ->withCount([
                'modules',
                'enrollments',
                'lessons as lessons_count', // ✅ total lesson / course
            ])
            ->orderByDesc('enrollments_count')
            ->take(6)
            ->get();

        // --- TES PSIKOLOGI (aktif) ---
        $psyTests = PsyTest::query()
            ->where('is_active', true)
            ->withCount('questions')
            ->latest('id')
            ->take(6)
            ->get();

        // --- PLANS ---
        $plans = Plan::query()
            ->withCount('planCourses')
            ->get();

        // --- COUPON aktif (valid hari ini) ---
        $today = Carbon::today();
        $activeCoupons = Coupon::query()
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
            })
            ->latest('id')
            ->take(3)
            ->get();

        // --- Kategori (statis) ---
        $categories = [
            ['key' => 'backend', 'name' => 'Backend'],
            ['key' => 'frontend', 'name' => 'Frontend'],
            ['key' => 'mobile',  'name' => 'Mobile'],
            ['key' => 'data',    'name' => 'Data & AI'],
            ['key' => 'devops',  'name' => 'DevOps'],
            ['key' => 'uiux',    'name' => 'UI/UX'],
        ];

        $latestThreads = QaThread::with(['user:id,name', 'course:id,title', 'lesson:id,title'])
            ->withCount('replies')
            ->latest('id')
            ->take(3)
            ->get();

        // --- Test IQ aktif ---
        $iqTests = TestIq::query()
            ->where('is_active', true)
            ->latest('id')
            ->take(6)
            ->get();

        // --- Membership gating ---
        $isMember = false;
        if (Auth::check()) {
            $isMember = Membership::where('user_id', Auth::id())
                ->where('status', 'active')
                ->whereDate('expires_at', '>=', now()->toDateString())
                ->exists();
        }

        // --- Progress per course (untuk user yang login) ---
        if (Auth::check()) {
            $userId = Auth::id();

            // Kumpulkan semua course id yang tampil
            $courseIds = $latestCourses->pluck('id')
                ->merge($popularCourses->pluck('id'))
                ->unique()
                ->values();

            if ($courseIds->isNotEmpty()) {
                // ✅ PERBAIKAN: join ke modules, bukan ambil l.course_id
                $completedByCourse = DB::table('lesson_progresses as lp')
                    ->join('lessons as l', 'l.id', '=', 'lp.lesson_id')
                    ->join('modules as m', 'm.id', '=', 'l.module_id')   // <-- tambahkan join ini
                    ->where('lp.user_id', $userId)
                    ->whereNotNull('lp.completed_at')
                    ->whereIn('m.course_id', $courseIds)                // <-- ambil dari m.course_id
                    ->select('m.course_id', DB::raw('COUNT(DISTINCT lp.lesson_id) as done'))
                    ->groupBy('m.course_id')
                    ->pluck('done', 'course_id');

                // Helper untuk menyisipkan progress_percent ke setiap course
                $applyProgress = function ($courses) use ($completedByCourse) {
                    return $courses->map(function ($c) use ($completedByCourse) {
                        $total = (int)($c->lessons_count ?? 0);
                        $done  = (int)($completedByCourse[$c->id] ?? 0);
                        $c->progress_done     = $done;
                        $c->progress_total    = $total;
                        $c->progress_percent  = $total > 0 ? round(($done / $total) * 100) : 0;
                        return $c;
                    });
                };

                $latestCourses  = $applyProgress($latestCourses);
                $popularCourses = $applyProgress($popularCourses);
            }
        } else {
            // Jika belum login: set default 0 supaya view aman dipakai
            $latestCourses = $latestCourses->map(function ($c) {
                $c->progress_percent = 0;
                $c->progress_done = 0;
                $c->progress_total = (int)($c->lessons_count ?? 0);
                return $c;
            });
            $popularCourses = $popularCourses->map(function ($c) {
                $c->progress_percent = 0;
                $c->progress_done = 0;
                $c->progress_total = (int)($c->lessons_count ?? 0);
                return $c;
            });
        }


        return view('welcome', compact(
            'stats',
            'latestCourses',
            'popularCourses',
            'plans',
            'activeCoupons',
            'categories',
            'psyTests',
            'latestThreads',
            'iqTests',
            'isMember',
        ));
    }
}
