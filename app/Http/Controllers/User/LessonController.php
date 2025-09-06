<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProgressRequest;
use App\Models\{Lesson, Enrollment};
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $lesson->load(['module.course', 'quiz.questions.options', 'resources']);

        $course     = $lesson->module->course;
        $blocks     = $lesson->content ?? [];       // array dari JSON
        $links      = $lesson->content_url ?? [];   // array dari JSON
        $resources  = $lesson->resources()->orderBy('id')->get();

        $siblings = $lesson->module->lessons()->orderBy('ordering')->orderBy('id')->pluck('id')->values();
        $idx  = $siblings->search($lesson->id);
        $prev = ($idx !== false && $idx > 0) ? $siblings[$idx - 1] : null;
        $next = ($idx !== false && $idx < $siblings->count() - 1) ? $siblings[$idx + 1] : null;

        $isEnrolled = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->exists();
        $progress   = $lesson->progresses()->where('user_id', Auth::id())->first();

        return view('app.lessons.show', compact(
            'lesson',
            'course',
            'isEnrolled',
            'progress',
            'prev',
            'next',
            'resources',
            'blocks',
            'links'
        ));
    }



    public function updateProgress(UpdateProgressRequest $r, Lesson $lesson)
    {
        $payload = [
            'lesson_id' => $lesson->id,
            'user_id' => Auth::id(),
            'progress' => $r->input('progress', []),
        ];
        if ($r->boolean('completed')) $payload['completed_at'] = now();

        $lesson->progresses()->updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => Auth::id()],
            $payload
        );

        return back()->with('status', 'Progress tersimpan.');
    }
}
