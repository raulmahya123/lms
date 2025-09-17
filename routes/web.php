<?php

use App\Http\Controllers\User\PsyDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// =====================
// Public
// =====================
use App\Http\Controllers\HomeController;

// Throttle untuk quiz/psy-tests
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
// Global Route Param Patterns (UUID untuk berbagai resource)
// =====================
$uuidRegex = '[0-9a-fA-F-]{36}';
Route::pattern('course', $uuidRegex);
Route::pattern('module', $uuidRegex);
Route::pattern('lesson', $uuidRegex);
Route::pattern('resource', $uuidRegex);
Route::pattern('quiz', $uuidRegex);
Route::pattern('question', $uuidRegex);
Route::pattern('option', $uuidRegex);
Route::pattern('attempt', $uuidRegex);
Route::pattern('payment', $uuidRegex);
Route::pattern('plan', $uuidRegex);
Route::pattern('membership', $uuidRegex);
Route::pattern('issue', $uuidRegex);
Route::pattern('certificate', $uuidRegex);
Route::pattern('psy_test', $uuidRegex);
Route::pattern('psy_question', $uuidRegex);
Route::pattern('psy_attempt', $uuidRegex);
Route::pattern('qa_thread', $uuidRegex);
Route::pattern('qa_reply', $uuidRegex);
Route::pattern('testIq', $uuidRegex);

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
    PsyTestController        as AdminPsyTestController,
    PsyAttemptController     as AdminPsyAttemptController,
    TestIqController         as AdminTestIqController,
};
use App\Http\Controllers\MidtransWebhookController;
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
    CourseCheckoutController as UserCourseCheckoutController,
    PaymentController        as UserPaymentController,
    ResourceController       as UserResourceController,
    PlanController           as UserPlanController,
    CertificateController,
    // Psy (USER)
    PsyTestController        as UserPsyTestController,
    PsyQuestionController    as UserPsyQuestionController,
    PsyAttemptController     as UserPsyAttemptController,
    QaThreadController       as UserQaThreadController,
    QaReplyController        as UserQaReplyController,
    TestIqController         as UserTestIqController,
    PsyDashboardController   as UserPysDashController,
};

// =====================
// User Area (login diperlukan)
// =====================
// GET ping (boleh pakai web biasa)
// Route::get('/midtrans/webhook', [MidtransWebhookController::class, 'ping'])->middleware('web');

// // POST notif Midtrans — TANPA session/auth/CSRF
// Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])
//     ->middleware('web') // pastikan masuk pipeline web dulu
//     ->withoutMiddleware([
//         Authenticate::class,
//         VerifyCsrfToken::class,
//         StartSession::class,
//         AddQueuedCookiesToResponse::class,
//         ShareErrorsFromSession::class,
//     ]);
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (USER)
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/app/dashboard', [UserDashboardController::class, 'index'])->name('app.dashboard'); // alias

    // Membership (USER)
    Route::get('/memberships', [UserMembershipController::class, 'index'])
        ->name('app.memberships.index');

    // daftar paket membership versi user (dipakai UI saya: route('app.memberships.plans'))
    Route::get('/memberships/plans', [UserMembershipController::class, 'plans'])
        ->name('app.memberships.plans');

    // mulai subscribe (buat membership status "pending" untuk plan terpilih)
    Route::post('/memberships/subscribe/{plan}', [UserMembershipController::class, 'subscribe'])
        ->whereUuid('plan')->name('app.memberships.subscribe');

     Route::get('/memberships/finish', [UserMembershipController::class, 'finish'])->name('app.memberships.finish');

    // halaman checkout membership pending
    Route::get('/memberships/checkout/{membership}', [UserMembershipController::class, 'checkout'])
        ->whereUuid('membership')->name('app.memberships.checkout');

    // aktivasi membership (dipanggil setelah pembayaran sukses / simulasi dev)
    Route::post('/memberships/activate/{membership}', [UserMembershipController::class, 'activate'])
        ->whereUuid('membership')->name('app.memberships.activate');

    // batalkan/nonaktifkan membership user
    Route::post('/memberships/cancel/{membership}', [UserMembershipController::class, 'cancel'])
        ->whereUuid('membership')->name('app.memberships.cancel');

    Route::post('/memberships/update/{membership}', [UserMembershipController::class, 'update'])->name('app.memberships.update');
    // update membership oleh user (opsional)

    Route::patch('/memberships/{membership}', [UserMembershipController::class, 'update'])
        ->whereUuid('membership')->name('app.memberships.update');

    Route::post(
        '/memberships/{membership}/midtrans/snap',
        [UserMembershipController::class, 'startSnap']
    )->whereUuid('membership')->name('app.memberships.snap');

    Route::post('/memberships/snap/{membership}', [UserMembershipController::class, 'startSnap'])->name('app.memberships.snap');


    // Katalog & detail kursus
    Route::get('/courses', [CourseBrowseController::class, 'index'])->name('app.courses.index');
    Route::get('/courses/{course}', [CourseBrowseController::class, 'show'])
        ->whereUuid('course')->name('app.courses.show');

    // Kursus saya & enroll
    Route::get('/my/courses', [UserEnrollmentController::class, 'index'])->name('app.my.courses');
    Route::post('/courses/{course}/enroll', [UserEnrollmentController::class, 'store'])
        ->whereUuid('course')->name('app.courses.enroll');

    // Pelajaran & progress
    Route::get('/lessons/{lesson}', [UserLessonController::class, 'show'])
        ->whereUuid('lesson')
        ->middleware('app.ensure.lesson.accessible')->name('app.lessons.show');

    Route::post('/lessons/{lesson}/progress', [UserLessonController::class, 'updateProgress'])
        ->whereUuid('lesson')
        ->middleware('app.ensure.lesson.accessible')->name('app.lessons.progress');

    // Resource per lesson
    Route::get('/resources/{resource}', [UserResourceController::class, 'show'])
        ->whereUuid('resource')->name('app.resources.show');

    // Quiz (rate-limit submit)
    Route::post('/lessons/{lesson}/quiz/start', [UserQuizController::class, 'start'])
        ->whereUuid('lesson')
        ->middleware('app.ensure.lesson.accessible')->name('app.quiz.start');

    Route::post('/quizzes/{quiz}/submit', [UserQuizController::class, 'submit'])
        ->whereUuid('quiz')
        ->middleware('throttle:quiz')->name('app.quiz.submit');

    Route::get('/attempts/{attempt}', [UserQuizController::class, 'result'])
        ->whereUuid('attempt')
        ->middleware('ensure.attempt.owner')->name('app.quiz.result');

    Route::post('/lessons/{lesson}/drive/request', [UserLessonController::class, 'requestDriveAccess'])
        ->whereUuid('lesson')->name('lessons.drive.request');

    // Kupon
    Route::post('/coupons/validate', [UserCouponController::class, 'validateCode'])->name('app.coupons.validate');

    // Checkout plan & course + confirm
    Route::post('/checkout/plan/{plan}', [CheckoutController::class, 'checkoutPlan'])
        ->whereUuid('plan')->name('app.checkout.plan');

    Route::post('/checkout/course/{course}', [CheckoutController::class, 'checkoutCourse'])
        ->whereUuid('course')->name('app.checkout.course');

    Route::post('/checkout/{payment}/confirm', [CheckoutController::class, 'confirm'])
        ->whereUuid('payment')->name('app.checkout.confirm');

    // Halaman checkout course
    Route::get('/courses/{course}/checkout', [UserCourseCheckoutController::class, 'checkout'])
        ->whereUuid('course')->name('app.courses.checkout');

    // Ambil Snap token (dipanggil dari tombol Bayar)
    Route::post('/courses/{course}/midtrans/snap', [UserCourseCheckoutController::class, 'startSnap'])
        ->whereUuid('course')->name('app.courses.snap');

    // Sertifikat (PDF)
    Route::get('/courses/{course}/certificate', [CertificateController::class, 'course'])
        ->whereUuid('course')->name('app.certificate.course');

    Route::get('/certificates', [CertificateController::class, 'index'])
        ->name('app.certificates.index');

    // (DUPLIKASI DIPERTAHANKAN SESUAI PUNYAMU)
    Route::get('/memberships', [UserMembershipController::class, 'index'])->name('app.memberships.index');
    Route::get('/plans', [UserPlanController::class, 'index'])->name('app.plans.index');

    // Payments (USER)
    Route::get('/payments', [UserPaymentController::class, 'index'])->name('app.payments.index');
    Route::get('/payments/{payment}', [UserPaymentController::class, 'show'])
        ->whereUuid('payment')->name('app.payments.show');

    // =====================
    // Psy Tests (USER)
    // =====================
    // =====================
    // Psy Tests (USER) — simple & jelas
    // =====================
    Route::prefix('psy-tests')->group(function () {
        // List & detail tes
        Route::get('/', [UserPsyTestController::class, 'index'])->name('app.psytests.index');
        Route::get('/{slugOrId}', [UserPsyTestController::class, 'show'])->name('app.psytests.show');

        // Tampilkan 1 soal (pakai UUID question)
        Route::get('/{slugOrId}/questions/{question}', [UserPsyQuestionController::class, 'show'])
            ->whereUuid('question')
            ->name('app.psytests.questions.show');

        // Mulai / lanjut attempt (GET/POST)
        Route::match(['GET', 'POST'], '/{slugOrId}/start', [UserPsyAttemptController::class, 'start'])
            ->middleware('throttle:quiz')
            ->name('app.psy.attempts.start');

        // Simpan jawaban (POST)
        Route::post('/{slugOrId}/q/{question}/answer', [UserPsyAttemptController::class, 'answer'])
            ->whereUuid('question')
            ->name('app.psy.attempts.answer');

        // Kalau ada yang buka via GET, balikin ke halaman soal (biar gak 419)
        Route::get('/{slugOrId}/q/{question}/answer', function ($slugOrId, $question) {
            return redirect()->route('app.psytests.questions.show', [$slugOrId, $question]);
        })->whereUuid('question');

        // Submit & hasil
        Route::get('/{slugOrId}/submit', [UserPsyAttemptController::class, 'submit'])
            ->name('app.psy.attempts.submit');

        Route::get('/{slugOrId}/result/{attempt}', [UserPsyAttemptController::class, 'result'])
            ->whereUuid('attempt')
            ->name('app.psy.attempts.result');
    });

    // ⬇️ Sudah ada sebelumnya, dipertahankan
    Route::get('/certificates/{issue}', [CertificateController::class, 'show'])
        ->whereUuid('issue')->name('app.certificates.show');

    Route::get('/certificates/{issue}/preview', [CertificateController::class, 'preview'])
        ->whereUuid('issue')->name('app.certificates.preview');

    Route::get('/certificates/{issue}/download', [CertificateController::class, 'download'])
        ->whereUuid('issue')->name('app.certificates.download');

    // Q&A (USER)
    Route::resource('qa-threads', UserQaThreadController::class)
        ->names('app.qa-threads')
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::post('qa-threads/{thread}/replies', [UserQaReplyController::class, 'store'])
        ->whereUuid('thread')->name('app.qa-threads.replies.store');

    // Tandai sebagai jawaban (PATCH)
    Route::patch('qa-replies/{reply}/answer', [UserQaReplyController::class, 'markAnswer'])
        ->whereUuid('reply')->name('app.qa-replies.answer');

    // Hapus reply (DELETE), JANGAN pakai PATCH
    Route::delete('qa-replies/{reply}', [UserQaReplyController::class, 'destroy'])
        ->whereUuid('reply')->name('app.qa-replies.destroy');

    // =====================
    // IQ Test (USER) — EXISTING milikmu (biarin)
    // =====================
    Route::get('/iq/{testIq}', [UserTestIqController::class, 'show'])
        ->whereUuid('testIq')->name('user.test-iq.show');

    Route::post('/iq/{testIq}/submit', [UserTestIqController::class, 'submit'])
        ->whereUuid('testIq')->name('user.test-iq.submit');

    Route::get('/iq/{testIq}/result', [UserTestIqController::class, 'result'])
        ->whereUuid('testIq')->name('user.test-iq.result');

    // =====================
    // IQ Test (USER) — TAMBAHAN: versi app.* (URI berbeda biar gak tabrakan)
    // =====================
    Route::get('/app/iq/{testIq}', [UserTestIqController::class, 'show'])
        ->whereUuid('testIq')->name('app.test-iq.show');

    Route::post('/app/iq/{testIq}/submit', [UserTestIqController::class, 'submit'])
        ->whereUuid('testIq')->name('app.test-iq.submit');

    Route::get('/app/iq/{testIq}/result', [UserTestIqController::class, 'result'])
        ->whereUuid('testIq')->name('app.test-iq.result');

    // =====================
    // IQ Test (USER) — STEP ROUTES (yang kamu minta tampil 1-per-1)
    // =====================
    // Start (opsional) -> redirect ke step 1
    Route::get('/iq/{testIq}/start', [UserTestIqController::class, 'start'])
        ->whereUuid('testIq')->name('user.test-iq.start');

    Route::get('/app/iq/{testIq}/start', [UserTestIqController::class, 'start'])
        ->whereUuid('testIq')->name('app.test-iq.start');

    // Versi user.* (tanpa /app)
    Route::get('/iq/{testIq}/q/{step}', [UserTestIqController::class, 'showStep'])
        ->whereUuid('testIq')->whereNumber('step')->name('user.test-iq.question');

    Route::post('/iq/{testIq}/q/{step}', [UserTestIqController::class, 'answer'])
        ->whereUuid('testIq')->whereNumber('step')->name('user.test-iq.answer');

    // Versi app.* (dengan /app prefix di path)
    Route::get('/app/iq/{testIq}/q/{step}', [UserTestIqController::class, 'showStep'])
        ->whereUuid('testIq')->whereNumber('step')->name('app.test-iq.question');

    Route::post('/app/iq/{testIq}/q/{step}', [UserTestIqController::class, 'answer'])
        ->whereUuid('testIq')->whereNumber('step')->name('app.test-iq.answer');

    // =====================
    // IQ Test (USER) — ROUTES custom kamu (dipertahankan)
    // =====================
    Route::get('/test-iq/{testIq}', [UserTestIqController::class, 'start'])
        ->whereUuid('testIq')->name('test-iq.start');

    // Tampilkan 1 soal (step)
    Route::get('/test-iq/{testIq}/q/{step}', [UserTestIqController::class, 'showStep'])
        ->whereUuid('testIq')->whereNumber('step')->name('test-iq.show');

    // Submit 1 jawaban (lanjut step berikutnya / finish)
    Route::post('/test-iq/{testIq}/q/{step}', [UserTestIqController::class, 'answer'])
        ->whereUuid('testIq')->whereNumber('step')->name('test-iq.answer');

    // Hasil
    Route::get('/test-iq/{testIq}/result', [UserTestIqController::class, 'result'])
        ->whereUuid('testIq')->name('test-iq.result');

    // =====================
    // Profile
    // =====================
    Route::middleware('auth')->group(function () {
        Route::get('/profile', fn() => view('profile.index'))->name('profile.edit');

        Route::get(
            '/profile/updateinformation',
            fn(\Illuminate\Http\Request $r) =>
            view('profile.updateinformation', [
                'user' => $r->user(),
                'mustVerifyEmail' => $r->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
                'status' => session('status'),
            ])
        )->name('profile.info.edit');

        Route::get('/profile/updatepass', fn() => view('profile.updatepass', ['status' => session('status')]))
            ->name('profile.pass.edit');

        Route::get('/profile/delacc', fn() => view('profile.delacc'))
            ->name('profile.delete.confirm');

        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// =====================
// Admin Area (role admin)
// =====================
Route::middleware(['auth', 'can:backoffice'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Admin Dashboard (/admin/dashboard)
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ✅ perbaiki pemetaan ke AdminTestIqController
        Route::get('test-iq',                 [AdminTestIqController::class, 'index'])->name('test-iq.index');
        Route::get('test-iq/create',          [AdminTestIqController::class, 'create'])->name('test-iq.create');
        Route::post('test-iq',                [AdminTestIqController::class, 'store'])->name('test-iq.store');
        Route::get('test-iq/{testIq}/edit',   [AdminTestIqController::class, 'edit'])
            ->whereUuid('testIq')->name('test-iq.edit');
        Route::put('test-iq/{testIq}',        [AdminTestIqController::class, 'update'])
            ->whereUuid('testIq')->name('test-iq.update');
        Route::delete('test-iq/{testIq}',     [AdminTestIqController::class, 'destroy'])
            ->whereUuid('testIq')->name('test-iq.destroy');
        Route::post('test-iq/{testIq}/toggle', [AdminTestIqController::class, 'toggle'])
            ->whereUuid('testIq')->name('test-iq.toggle');

        // Resource khusus
        Route::resource('dashboard_admin', AdminDashboardController::class);

        // === Courses ===
        Route::resource('courses', AdminCourseController::class);
        Route::get('courses/{course}/modules', [AdminCourseController::class, 'modules'])
            ->whereUuid('course')->name('courses.modules');

        // === Modules ===
        Route::resource('modules', AdminModuleController::class);

        // === Lessons ===
        Route::resource('lessons', AdminLessonController::class);

        // === Resources ===
        Route::resource('resources', AdminResourceController::class)
            ->only(['index', 'create', 'store', 'update', 'destroy', 'show', 'edit']);

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
        Route::post('qa-threads/{thread}/replies', [\App\Http\Controllers\Admin\QaReplyController::class, 'store'])
            ->whereUuid('thread')->name('qa-threads.replies.store');
        Route::patch('qa-replies/{reply}/answer', [\App\Http\Controllers\Admin\QaReplyController::class, 'markAnswer'])
            ->whereUuid('reply')->name('qa-replies.answer');

        // === Certificates ===
        Route::resource('certificate-templates', \App\Http\Controllers\Admin\CertificateTemplateController::class);
        Route::resource('certificate-issues', \App\Http\Controllers\Admin\CertificateIssueController::class)->only(['index', 'show', 'destroy']);

        // === Psych Tests ===
        Route::resource('psy-tests', \App\Http\Controllers\Admin\PsyTestController::class);

        // Nested: psy-tests/{psy_test}/questions
        // (aktifkan SEMUA method shg admin.psy-tests.questions.index dkk tersedia)
        Route::resource('psy-tests.questions', \App\Http\Controllers\Admin\PsyQuestionController::class)
            ->shallow();

        // GLOBAL (opsional, untuk semua soal lintas tes)
        Route::get('psy-questions', [\App\Http\Controllers\Admin\PsyQuestionController::class, 'globalIndex'])
            ->name('psy-questions.index');
        Route::get('psy-questions/create', [\App\Http\Controllers\Admin\PsyQuestionController::class, 'globalCreate'])
            ->name('psy-questions.create');
        Route::post('psy-questions', [\App\Http\Controllers\Admin\PsyQuestionController::class, 'globalStore'])
            ->name('psy-questions.store');

        Route::resource('psy-attempts', AdminPsyAttemptController::class)
            ->only(['index', 'show', 'destroy']);
    });

/* ================== USER AREA ================== */
Route::middleware(['auth'])
    ->prefix('app')->name('app.')->group(function () {
        // Dashboard Psikologi (USER)
        Route::get('psychology', UserPysDashController::class)->name('psychology');
    });
