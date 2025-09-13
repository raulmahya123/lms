<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Lesson;
use App\Models\Enrollment;

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

        // cek apakah user terdaftar di course
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (! $enrolled) {
            abort(403, 'Anda belum terdaftar pada kursus ini');
        }

        return $next($request);
    }
}
