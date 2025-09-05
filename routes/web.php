<?php

use Illuminate\Support\Facades\Route;

// =====================
// Public
// =====================
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

// Definisikan limiter untuk throttle:quiz
RateLimiter::for('quiz', function ($request) {
    return [
        Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip()),
    ];
});

Route::get('/', [HomeController::class, 'index'])->name('home');

// =====================
// Auth scaffolding
// =====================
require __DIR__ . '/auth.php';

// =====================
// Admin Controllers (alias agar tak tabrakan)
// =====================
use App\Http\Controllers\Admin\{
    CouponController         as AdminCouponController,
    CourseController         as AdminCourseController,
    EnrollmentController     as AdminEnrollmentController,
    LessonController         as AdminLessonController,
    MembershipController     as AdminMembershipController,
    ModuleController         as AdminModuleController,
    OptionController         as AdminOptionController,
    PaymentController        as AdminPaymentController,
    PlanController           as AdminPlanController,
    PlanCourseController     as AdminPlanCourseController,
    QuestionController       as AdminQuestionController,
    QuizController           as AdminQuizController,
    ResourceController       as AdminResourceController,
    DashboardController      as AdminDashboardController,
};

// =====================
// User Controllers
// =====================
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\{
    DashboardController      as UserDashboardController,
    CourseBrowseController,
    EnrollmentController     as UserEnrollmentController,
    LessonController         as UserLessonController,
    QuizController           as UserQuizController,
    CouponController         as UserCouponController,
    CheckoutController,
    MembershipController     as UserMembershipController,
    PaymentController        as UserPaymentController,
    ResourceController       as UserResourceController,
    PlanController           as UserPlanController,
    CertificateController,
};

// =====================
// User Area (login diperlukan)
// =====================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (USER)
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/app/dashboard', [UserDashboardController::class, 'index'])->name('app.dashboard'); // alias

    // Katalog & detail kursus
    Route::get('/courses', [CourseBrowseController::class, 'index'])->name('app.courses.index');
    Route::get('/courses/{course}', [CourseBrowseController::class, 'show'])->name('app.courses.show');

    // Kursus saya & enroll
    Route::get('/my/courses', [UserEnrollmentController::class, 'index'])->name('app.my.courses');
    Route::post('/courses/{course}/enroll', [UserEnrollmentController::class, 'store'])->name('app.courses.enroll');

    // Pelajaran & progress
    Route::get('/lessons/{lesson}', [UserLessonController::class, 'show'])
        ->middleware('app.ensure.lesson.accessible')->name('app.lessons.show');
    Route::post('/lessons/{lesson}/progress', [UserLessonController::class, 'updateProgress'])
        ->middleware('app.ensure.lesson.accessible')->name('app.lessons.progress');

    // Resource per lesson
    Route::get('/resources/{resource}', [UserResourceController::class, 'show'])->name('app.resources.show');

    // Quiz (rate-limit submit)
    Route::post('/lessons/{lesson}/quiz/start', [UserQuizController::class, 'start'])
        ->middleware('app.ensure.lesson.accessible')->name('app.quiz.start');
    Route::post('/quizzes/{quiz}/submit', [UserQuizController::class, 'submit'])
        ->middleware('throttle:quiz')->name('app.quiz.submit');
    Route::get('/attempts/{attempt}', [UserQuizController::class, 'result'])
        ->middleware('ensure.attempt.owner')->name('app.quiz.result');

    // Kupon
    Route::post('/coupons/validate', [UserCouponController::class, 'validateCode'])->name('app.coupons.validate');

    // Checkout plan & course + confirm
    Route::post('/checkout/plan/{plan}', [CheckoutController::class, 'checkoutPlan'])->name('app.checkout.plan');
    Route::post('/checkout/course/{course}', [CheckoutController::class, 'checkoutCourse'])->name('app.checkout.course');
    Route::post('/checkout/{payment}/confirm', [CheckoutController::class, 'confirm'])->name('app.checkout.confirm');

    // Sertifikat (PDF)
    Route::get('/courses/{course}/certificate', [CertificateController::class, 'course'])->name('app.certificate.course');

    // Membership & Plan (USER)
    Route::get('/memberships', [UserMembershipController::class, 'index'])->name('app.memberships.index');
    Route::get('/plans', [UserPlanController::class, 'index'])->name('app.plans.index');

    // Payments (USER)
    Route::get('/payments', [UserPaymentController::class, 'index'])->name('app.payments.index');
    Route::get('/payments/{payment}', [UserPaymentController::class, 'show'])->name('app.payments.show');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================
// Admin Area (role admin)
// =====================
Route::middleware(['auth', 'can:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Admin Dashboard (/admin/dashboard)
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource khusus
        Route::resource('dashboard_admin', AdminDashboardController::class);

        // === Courses ===
        Route::resource('courses', AdminCourseController::class);
        Route::get('courses/{course}/modules', [AdminCourseController::class, 'modules'])->name('courses.modules');

        // === Modules ===
        Route::resource('modules', AdminModuleController::class);
        Route::get('modules/{module}/lessons', [AdminModuleController::class, 'lessons'])->name('modules.lessons');

        // === Lessons ===
        Route::resource('lessons', AdminLessonController::class);

        // === Resources ===
        Route::resource('resources', AdminResourceController::class)->only(['index', 'create', 'store', 'update', 'destroy', 'show', 'edit']);

        // === Quizzes / Questions / Options ===
        Route::resource('quizzes',   AdminQuizController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('options',   AdminOptionController::class);

       // === Plans & Plan-Course ===
        Route::resource('plans', AdminPlanController::class);
        Route::resource('plan-courses', AdminPlanCourseController::class)->only(['store', 'destroy']);

        // === Memberships ===
        Route::resource('memberships', AdminMembershipController::class);

        // === Payments ===
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show', 'update']);

        // === Enrollments ===
        Route::resource('enrollments', AdminEnrollmentController::class);

        // === Coupons ===
        Route::resource('coupons', AdminCouponController::class);

        // === Q&A ===
        Route::resource('qa-threads', \App\Http\Controllers\Admin\QaThreadController::class);
        Route::post('qa-threads/{thread}/replies', [\App\Http\Controllers\Admin\QaReplyController::class, 'store'])->name('qa-threads.replies.store');
        Route::patch('qa-replies/{reply}/answer', [\App\Http\Controllers\Admin\QaReplyController::class, 'markAnswer'])->name('qa-replies.answer');

        // === Certificates ===
        Route::resource('certificate-templates', \App\Http\Controllers\Admin\CertificateTemplateController::class);
        Route::resource('certificate-issues', \App\Http\Controllers\Admin\CertificateIssueController::class)->only(['index', 'show', 'destroy']);

        // === Psych Tests ===
        Route::resource('psy-tests', \App\Http\Controllers\Admin\PsyTestController::class);
        Route::resource('psy-tests.questions', \App\Http\Controllers\Admin\PsyQuestionController::class)->shallow(); // <— opsional tapi enak
    });
