<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\{
    Course, Module, Lesson, Quiz, Question, Option,
    Membership, Enrollment, Payment, Plan, Coupon, Resource,
    CertificateTemplate, CertificateIssue,
    PsyTest, PsyQuestion, PsyAttempt,
    QaThread,
    TestIq,
};
use Illuminate\Support\Facades\URL;
use PHPUnit\Event\Code\Test;

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
        if (str_starts_with(config('app.url'), 'https://')) {
        URL::forceScheme('https');
    }
        View::composer('*', function ($view) {
            $user = Auth::user();

            $badges = [
                'Dashboard'             => 1, // contoh static
                'Courses'               => Course::count(),
                'Modules'               => Module::count(),
                'Lessons'               => Lesson::count(),
                'Quizzes'               => Quiz::count(),
                'Questions'             => Question::count(),
                'Options'               => Option::count(),
                'Memberships'           => Membership::count(),
                'Enrollments'           => Enrollment::count(),
                'Payments'              => Payment::where('status','pending')->count(),
                'Plans'                 => Plan::count(),
                'Coupons'               => Coupon::count(),
                'Resources'             => Resource::count(),

                // === tambahan ===
                'Certificate Templates' => CertificateTemplate::count(),
                'Certificate Issues'    => CertificateIssue::where(function($q){
                                                $q->whereNull('pdf_path')
                                                  ->orWhereNull('issued_at');
                                            })->count(),
                'Psych Tests'           => PsyTest::where('is_active', 1)->count(),
                'Psych Questions'       => PsyQuestion::count(),
                'Psych Attempts'        => PsyAttempt::count(), // <— DITAMBAHKAN
                'Test Iq'               =>  TestIq::count(),
                'Qa_Threads'            => QaThread::count(),
            ];
            $view->with('badges', $badges);
        });
    }
}
