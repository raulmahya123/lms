<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Course, Module, Lesson, Enrollment, Plan, Coupon, Quiz, PsyTest}; // + PsyTest
use Carbon\Carbon;

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
            ->withCount(['modules','enrollments'])
            ->latest('id')
            ->take(6)
            ->get();

        // --- KELAS POPULER (banyak enrollment) ---
        $popularCourses = Course::query()
            ->where('is_published', 1)
            ->withCount(['modules','enrollments'])
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

        // --- PLANS (dengan jumlah course di setiap plan) ---
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
            ['key' => 'frontend','name' => 'Frontend'],
            ['key' => 'mobile',  'name' => 'Mobile'],
            ['key' => 'data',    'name' => 'Data & AI'],
            ['key' => 'devops',  'name' => 'DevOps'],
            ['key' => 'uiux',    'name' => 'UI/UX'],
        ];

        return view('welcome', compact(
            'stats',
            'latestCourses',
            'popularCourses',
            'plans',
            'activeCoupons',
            'categories',
            'psyTests' // <— kirim ke view (dipakai di section PSI)
        ));
    }
}
