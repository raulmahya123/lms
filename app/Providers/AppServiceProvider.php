<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\{
    Course, Module, Lesson, Quiz, Question, Option,
    Membership, Enrollment, Payment, Plan, Coupon, Resource
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    View::composer('*', function ($view) {
        $user = Auth::user();

        $now = now();

        $badges = [
            'Dashboard'             => 1, // contoh static
            'Courses'               => \App\Models\Course::count(),
            'Modules'               => \App\Models\Module::count(),
            'Lessons'               => \App\Models\Lesson::count(),
            'Quizzes'               => \App\Models\Quiz::count(),
            'Questions'             => \App\Models\Question::count(),
            'Options'               => \App\Models\Option::count(),
            'Memberships'           => \App\Models\Membership::count(),
            'Enrollments'           => \App\Models\Enrollment::count(),
            'Payments'              => \App\Models\Payment::where('status','pending')->count(),
            'Plans'                 => \App\Models\Plan::count(),
            'Coupons'               => \App\Models\Coupon::count(),
            'Resources'             => \App\Models\Resource::count(),

            // === tambahan ===
            'Certificate Templates' => \App\Models\CertificateTemplate::count(),
            'Certificate Issues'    => \App\Models\CertificateIssue::where(function($q){
                                            $q->whereNull('pdf_path')->orWhereNull('issued_at');
                                        })->count(),
            'Psych Tests'           => \App\Models\PsyTest::where('is_active',1)->count(),
            'Psych Questions'       => \App\Models\PsyQuestion::count(),
            'Qa_Threads'            => \App\Models\QaThread::whereDoesntHave('replies', function($q){
                                            $q->where('is_answer',1);
                                        })->count(),
        ];

        $view->with('badges', $badges);
    });
}

}
