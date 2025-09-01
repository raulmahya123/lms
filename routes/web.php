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
    ResourceController
};

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| Dashboard (user login)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile (user login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
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

        // === Courses ===
        Route::resource('courses', CourseController::class);
        // helper JSON: daftar modules milik course
        Route::get('courses/{course}/modules', [CourseController::class, 'modules'])
            ->name('courses.modules');

        // === Modules ===
        Route::resource('modules', ModuleController::class);
        // helper JSON: daftar lessons milik module
        Route::get('modules/{module}/lessons', [ModuleController::class, 'lessons'])
            ->name('modules.lessons');

        // === Lessons ===
        Route::resource('lessons', LessonController::class);

        // === Lesson Resources (file/link pendukung) ===
        Route::resource('resources', ResourceController::class)
            ->only(['store', 'update', 'destroy']);

        // === Quizzes / Questions / Options ===
        Route::resource('quizzes', QuizController::class);
        Route::resource('questions', QuestionController::class)
            ->only(['store', 'update', 'destroy']);
        Route::resource('options', OptionController::class)
            ->only(['store', 'update', 'destroy']);

        // === Plans (paket berlangganan) ===
        Route::resource('plans', PlanController::class);

        // === PlanCourse (relasi plan ↔ course) ===
        Route::resource('plan-courses', PlanCourseController::class)
            ->only(['store', 'destroy']);

        // === Memberships (keanggotaan user pada plan) ===
        Route::resource('memberships', MembershipController::class)
            ->only(['index', 'show', 'update', 'destroy']);

        // === Payments ===
        Route::resource('payments', PaymentController::class)
            ->only(['index', 'show', 'update']);

        // === Enrollments (pendaftaran user ke course) ===
        Route::resource('enrollments', EnrollmentController::class)
            ->only(['index', 'show', 'update', 'destroy']);

        // === Coupons ===
        Route::resource('coupons', CouponController::class);
    });

/*
|--------------------------------------------------------------------------
| Auth scaffolding (Breeze/Fortify/etc)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
