<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    CouponController,
    CourseController,
    EnrollmentController,
    LessonController,
    MembershipController,
    ModuleController,
    OptionController,
    PaymentController,
    PlanController,
    PlanCourseController,
    QuestionController,
    QuizController,
    ResourceController,
    DashboardController,
};

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard (user login)
| - Kalau kamu tetap pakai halaman /dashboard untuk user, biarkan ini.
| - Redirect setelah login diatur di AuthenticatedSessionController (admin → admin.dashboard, user → home)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile (user login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Area (hanya role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Admin Dashboard → gunakan layout admin
        Route::get('/dashboard', fn() => view('layouts.admin'))->name('dashboard');

        // === Courses ===
        Route::resource('courses', CourseController::class);
        Route::get('courses/{course}/modules', [CourseController::class, 'modules'])
            ->name('courses.modules');

        // === Modules ===
        Route::resource('modules', ModuleController::class);
        Route::get('modules/{module}/lessons', [ModuleController::class, 'lessons'])
            ->name('modules.lessons');

        // === Lessons ===
        Route::resource('lessons', LessonController::class);

        // === Lesson Resources (file/link pendukung) ===
        Route::resource('resources', ResourceController::class)
            ->only(['index', 'create', 'store', 'update', 'destroy', 'show', 'edit']);
        // === Quizzes / Questions / Options ===
        Route::resource('quizzes', QuizController::class);
        Route::resource('questions', QuestionController::class); // full (index/create/store/show/edit/update/destroy)
        Route::resource('options',   OptionController::class);   // full
        Route::resource('dashboard',   DashboardController::class);   // full

        // === Plans (paket berlangganan) ===
        Route::resource('plans', PlanController::class);

        // === PlanCourse (relasi plan ↔ course) ===
        Route::resource('plan-courses', PlanCourseController::class)
            ->only(['store', 'destroy']);

        // === Memberships (keanggotaan user pada plan) ===
        Route::resource('memberships', MembershipController::class); // full

        // === Payments ===
        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show', 'update']);

        // === Enrollments (pendaftaran user ke course) ===
        Route::resource('enrollments', EnrollmentController::class); // full

        // === Coupons ===
        Route::resource('coupons', CouponController::class);
    });

/*
|--------------------------------------------------------------------------
| Auth scaffolding (Breeze/Fortify/etc)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
