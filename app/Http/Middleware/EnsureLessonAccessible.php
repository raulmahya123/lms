<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Lesson;
use App\Models\{Enrollment, Membership};

class EnsureLessonAccessible
{
    public function handle(Request $request, Closure $next): Response
    {
        $lesson = $request->route('lesson');

        if (! $lesson instanceof Lesson) {
            $lesson = Lesson::where('id', $lesson)->orWhere('slug', $lesson)->first();
        }

        if (! $lesson) {
            abort(404, 'Lesson not found');
        }

        $lesson->loadMissing('module.course');
        $course = $lesson->module?->course;

        if (! $course) {
            abort(404, 'Course not found for this lesson');
        }

        $user = $request->user();

        // Admin & Mentor selalu boleh
        if ($user->can('admin') || $user->can('mentor')) {
            return $next($request);
        }

        // cek akses efektif, bukan hanya pernah terdaftar
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (! $enrollment) {
            abort(403, 'Anda belum terdaftar pada kursus ini');
        }

        $hasActiveMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if (! $enrollment->hasEffectiveAccess($hasActiveMembership)) {
            abort(403, 'Akses kursus sudah tidak aktif');
        }

        return $next($request);
    }
}
