<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LessonProgress;
use App\Models\Lesson;

class EnsureLessonAccessible
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lesson = $request->route('lesson');

        // kalau lesson bukan instance Lesson, resolve dari ID
        if (! $lesson instanceof Lesson) {
            $lesson = Lesson::find($lesson);
        }

        // jika lesson tidak ada
        if (! $lesson) {
            abort(404, 'Lesson not found');
        }

        $user = $request->user();

        // cek apakah user sudah punya progress / enroll
        $progress = LessonProgress::where('lesson_id', $lesson->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $progress) {
            abort(404, 'Lesson not accessible');
        }

        return $next($request);
    }
}
