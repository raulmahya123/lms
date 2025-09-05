<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLessonAccessible
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $lesson = $request->route('lesson'); // ambil parameter {lesson} dari route (harus pakai implicit model binding)

        if (!$user || !$lesson) {
            abort(403, 'Unauthorized.');
        }

        // Kalau user admin → boleh akses semua
        if ($user->can('admin')) {
            return $next($request);
        }

        // Ambil course dari lesson (pastikan relasi lesson->module->course ada di model)
        $course = optional($lesson->module)->course;

        $isEnrolled = $course
            ? $course->enrollments()->where('user_id', $user->id)->exists()
            : false;

        if (!$isEnrolled) {
            abort(403, 'Anda belum memiliki akses ke pelajaran ini.');
        }

        return $next($request);
    }
}
