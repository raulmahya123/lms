<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{
    Course,
    Enrollment,
    Membership,
    QuizAttempt,
    Coupon,
    PsyTest,
    TestIq,
    QaThread
};

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        // === 1) Stats ringkas milik user ===
        $stats = [
            'courses_count'     => Enrollment::where('user_id', $user->id)->count(),
            'active_membership' => Membership::where('user_id', $user->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->first(),
            'last_attempt'      => QuizAttempt::where('user_id', $user->id)->latest('id')->first(),
        ];

        // === 2) Courses yang diambil user + progress ===
        $myCourseIds = Enrollment::where('user_id', $user->id)->pluck('course_id');

        $myCourses = Course::query()
            ->whereIn('id', $myCourseIds)
            ->withCount([
                'modules',
                'enrollments',
                'lessons as lessons_count',
            ])
            ->latest('id')
            ->take(12)
            ->get();

        // Hitung lesson completed per course (join modules -> m.course_id)
        $completedByCourse = collect();
        if ($myCourses->isNotEmpty()) {
            $completedByCourse = DB::table('lesson_progresses as lp')
                ->join('lessons as l', 'l.id', '=', 'lp.lesson_id')
                ->join('modules as m', 'm.id', '=', 'l.module_id')
                ->where('lp.user_id', $user->id)
                ->whereNotNull('lp.completed_at')
                ->whereIn('m.course_id', $myCourses->pluck('id'))
                ->select('m.course_id', DB::raw('COUNT(DISTINCT lp.lesson_id) as done'))
                ->groupBy('m.course_id')
                ->pluck('done', 'course_id');
        }

        $myCourses = $myCourses->map(function ($c) use ($completedByCourse) {
            $total = (int) ($c->lessons_count ?? 0);
            $done  = (int) ($completedByCourse[$c->id] ?? 0);
            $c->progress_done    = $done;
            $c->progress_total   = $total;
            $c->progress_percent = $total > 0 ? round(($done / $total) * 100) : 0;
            return $c;
        });

        // === 3) Rekomendasi course (belum diambil user) ===
        $recommendedCourses = Course::query()
            ->where('is_published', 1)
            ->when($myCourseIds->isNotEmpty(), fn($q) => $q->whereNotIn('id', $myCourseIds))
            ->withCount([
                'modules',
                'enrollments',
                'lessons as lessons_count',
            ])
            ->orderByDesc('enrollments_count')
            ->take(6)
            ->get()
            ->map(function ($c) {
                $c->progress_done    = 0;
                $c->progress_total   = (int) ($c->lessons_count ?? 0);
                $c->progress_percent = 0;
                return $c;
            });

        // === 4) Kupon aktif hari ini ===
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

        // === 5) Tes Psikologi & IQ aktif ===
        $psyTests = PsyTest::where('is_active', true)
            ->withCount('questions')
            ->latest('id')
            ->take(6)
            ->get();

        $iqTests = TestIq::where('is_active', true)
            ->latest('id')
            ->take(6)
            ->get();

        // === 6) Threads Q&A ===
        $latestThreads = QaThread::with(['user:id,name', 'course:id,title', 'lesson:id,title'])
            ->withCount('replies')
            ->latest('id')
            ->take(5)
            ->get();

        $myThreads = QaThread::where('user_id', $user->id)
            ->with(['course:id,title', 'lesson:id,title'])
            ->withCount('replies')
            ->latest('id')
            ->take(5)
            ->get();

        // === 7) Flag membership ===
        $isMember = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhereDate('expires_at', '>=', now()->toDateString());
            })
            ->exists();

        // === 8) Dataset untuk Grafik ===

        // 8.1 Progress per course
        $progressLabels = $myCourses->pluck('title')->values();
        $progressPerc   = $myCourses->pluck('progress_percent')->values();
        $progressDone   = $myCourses->pluck('progress_done')->values();
        $progressTotal  = $myCourses->pluck('progress_total')->values();

        // 8.2 Enrollments per course (courses yg diambil user)
        $enrollLabels = $myCourses->pluck('title')->values();
        $enrollCounts = $myCourses->pluck('enrollments_count')->values();

        // 8.3 Distribusi progress (bucket)
        $bucket       = ['0%', '1–25%', '26–50%', '51–75%', '76–99%', '100%'];
        $bucketCount  = [0, 0, 0, 0, 0, 0];
        foreach ($myCourses as $c) {
            $p = (int) $c->progress_percent;
            if ($p === 0)           $bucketCount[0]++;
            elseif ($p <= 25)       $bucketCount[1]++;
            elseif ($p <= 50)       $bucketCount[2]++;
            elseif ($p <= 75)       $bucketCount[3]++;
            elseif ($p < 100)       $bucketCount[4]++;
            else /* 100 */          $bucketCount[5]++;
        }

        // 8.4 Riwayat skor quiz (score % = score / total_answers * 100)
        $quizSeries = QuizAttempt::where('user_id', $user->id)
            ->withCount(['answers as total_answers'])
            ->orderBy('created_at')
            ->get(['score', 'created_at'])
            ->map(function ($qa) {
                $den = max(1, (int) $qa->total_answers);
                return [
                    't' => $qa->created_at->format('Y-m-d'),
                    'y' => round(($qa->score / $den) * 100, 2),
                ];
            })
            ->values();

        // 8.5 Completion by month (6 bulan terakhir) — lesson_progresses.completed_at
        $fromMonth = now()->startOfMonth()->subMonths(5); // termasuk bulan ini
        $completionByMonth = DB::table('lesson_progresses as lp')
            ->where('lp.user_id', $user->id)
            ->whereNotNull('lp.completed_at')
            ->whereBetween('lp.completed_at', [$fromMonth, now()])
            ->selectRaw("DATE_FORMAT(lp.completed_at, '%Y-%m') as ym, COUNT(*) as cnt")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('cnt', 'ym');

        // siapkan 6 label bulanan berurutan
        $monthLabels = [];
        $monthCounts = [];
        for ($i = 0; $i < 6; $i++) {
            $label = now()->startOfMonth()->subMonths(5 - $i)->format('Y-m');
            $monthLabels[] = $label;
            $monthCounts[] = (int) ($completionByMonth[$label] ?? 0);
        }

        // 8.6 Quiz attempts by month (6 bulan terakhir) — quiz_attempts.created_at
        $attemptsByMonthRaw = QuizAttempt::where('user_id', $user->id)
            ->whereBetween('created_at', [$fromMonth, now()])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('cnt', 'ym');

        $attemptCounts = [];
        foreach ($monthLabels as $ym) {
            $attemptCounts[] = (int) ($attemptsByMonthRaw[$ym] ?? 0);
        }

        $charts = [
            'progress' => [
                'labels'  => $progressLabels->all(),
                'percent' => $progressPerc->all(),
                'done'    => $progressDone->all(),
                'total'   => $progressTotal->all(),
            ],
            'enroll' => [
                'labels' => $enrollLabels->all(),
                'counts' => $enrollCounts->all(),
            ],
            'distribution' => [
                'labels' => $bucket,
                'counts' => $bucketCount,
            ],
            'quiz' => $quizSeries->toArray(),
            'completion_monthly' => [
                'labels' => $monthLabels,   // 'YYYY-MM'
                'counts' => $monthCounts,   // lesson selesai per bulan
            ],
            'attempts_monthly' => [
                'labels' => $monthLabels,   // sinkron dengan completion_monthly
                'counts' => $attemptCounts, // attempts per bulan
            ],
        ];

        return view('app.dashboard', compact(
            'user',
            'stats',
            'myCourses',
            'recommendedCourses',
            'activeCoupons',
            'psyTests',
            'iqTests',
            'latestThreads',
            'myThreads',
            'isMember',
            'charts',
        ));
    }
}
