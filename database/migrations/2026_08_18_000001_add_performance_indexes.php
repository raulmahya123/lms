<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['status', 'paid_at'], 'payments_status_paid_at_index');
            $table->index(['user_id', 'status'], 'payments_user_status_index');
            $table->index(['provider', 'status'], 'payments_provider_status_index');
            $table->index(['membership_id', 'status'], 'payments_membership_status_index');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'expires_at'], 'memberships_user_status_expires_index');
            $table->index(['status', 'plan_id'], 'memberships_status_plan_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'enrollments_user_status_index');
            $table->index(['course_id', 'status'], 'enrollments_course_status_index');
            $table->index(['activated_at'], 'enrollments_activated_at_index');
        });

        Schema::table('lesson_progresses', function (Blueprint $table) {
            $table->index(['user_id', 'completed_at'], 'lesson_progresses_user_completed_index');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'quiz_attempts_user_created_index');
            $table->index(['user_id', 'submitted_at'], 'quiz_attempts_user_submitted_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['is_published', 'created_at'], 'courses_published_created_index');
        });

        Schema::table('qa_threads', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'qa_threads_user_created_index');
            $table->index(['status', 'created_at'], 'qa_threads_status_created_index');
        });

        Schema::table('qa_replies', function (Blueprint $table) {
            $table->index(['thread_id', 'is_answer', 'created_at'], 'qa_replies_thread_answer_created_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index(['valid_from', 'valid_until'], 'coupons_valid_range_index');
        });

        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->index(['user_id', 'coupon_id'], 'coupon_redemptions_user_coupon_index');
            $table->index(['coupon_id', 'course_id'], 'coupon_redemptions_coupon_course_index');
            $table->index(['coupon_id', 'plan_id'], 'coupon_redemptions_coupon_plan_index');
        });

        Schema::table('psy_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'submitted_at'], 'psy_attempts_user_submitted_index');
            $table->index(['user_id', 'test_id', 'submitted_at'], 'psy_attempts_user_test_submitted_index');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->index('lesson_id', 'quizzes_lesson_id_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['quiz_id', 'type'], 'questions_quiz_type_index');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->index(['question_id', 'is_correct'], 'options_question_correct_index');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->index(['attempt_id', 'question_id'], 'answers_attempt_question_index');
            $table->index(['question_id', 'is_correct'], 'answers_question_correct_index');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->index(['course_id', 'ordering'], 'modules_course_ordering_index');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['module_id', 'ordering'], 'lessons_module_ordering_index');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->index(['lesson_id', 'created_at'], 'resources_lesson_created_index');
        });

        Schema::table('certificate_issues', function (Blueprint $table) {
            $table->index(['user_id', 'issued_at'], 'certificate_issues_user_issued_index');
            $table->index(['course_id', 'assessment_type'], 'certificate_issues_course_type_index');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->index(['period', 'price'], 'plans_period_price_index');
        });

        Schema::table('psy_tests', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'psy_tests_active_created_index');
            $table->index(['track', 'type'], 'psy_tests_track_type_index');
        });

        Schema::table('test_iq', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'test_iq_active_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('qa_threads', function (Blueprint $table) {
            $table->dropIndex('qa_threads_user_created_index');
            $table->dropIndex('qa_threads_status_created_index');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('answers_attempt_question_index');
            $table->dropIndex('answers_question_correct_index');
        });

        Schema::table('test_iq', function (Blueprint $table) {
            $table->dropIndex('test_iq_active_created_index');
        });

        Schema::table('psy_tests', function (Blueprint $table) {
            $table->dropIndex('psy_tests_active_created_index');
            $table->dropIndex('psy_tests_track_type_index');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex('plans_period_price_index');
        });

        Schema::table('certificate_issues', function (Blueprint $table) {
            $table->dropIndex('certificate_issues_user_issued_index');
            $table->dropIndex('certificate_issues_course_type_index');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex('resources_lesson_created_index');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_module_ordering_index');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropIndex('modules_course_ordering_index');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropIndex('options_question_correct_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_quiz_type_index');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex('quizzes_lesson_id_index');
        });

        Schema::table('psy_attempts', function (Blueprint $table) {
            $table->dropIndex('psy_attempts_user_submitted_index');
            $table->dropIndex('psy_attempts_user_test_submitted_index');
        });

        Schema::table('coupon_redemptions', function (Blueprint $table) {
            $table->dropIndex('coupon_redemptions_user_coupon_index');
            $table->dropIndex('coupon_redemptions_coupon_course_index');
            $table->dropIndex('coupon_redemptions_coupon_plan_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('coupons_valid_range_index');
        });

        Schema::table('qa_replies', function (Blueprint $table) {
            $table->dropIndex('qa_replies_thread_answer_created_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_published_created_index');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('quiz_attempts_user_created_index');
            $table->dropIndex('quiz_attempts_user_submitted_index');
        });

        Schema::table('lesson_progresses', function (Blueprint $table) {
            $table->dropIndex('lesson_progresses_user_completed_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_user_status_index');
            $table->dropIndex('enrollments_course_status_index');
            $table->dropIndex('enrollments_activated_at_index');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropIndex('memberships_user_status_expires_index');
            $table->dropIndex('memberships_status_plan_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_paid_at_index');
            $table->dropIndex('payments_user_status_index');
            $table->dropIndex('payments_provider_status_index');
            $table->dropIndex('payments_membership_status_index');
        });
    }
};
