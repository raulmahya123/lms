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
        $lesson->load(['module.course','resources','quiz.questions.options']);
        $course = $lesson->module->course;

        // prev/next
        $siblings = $lesson->module->lessons()->orderBy('ordering')->pluck('id')->all();
        $idx = array_search($lesson->id, $siblings);
        $prev = $idx>0 ? $siblings[$idx-1] : null;
        $next = ($idx!==false && $idx<count($siblings)-1) ? $siblings[$idx+1] : null;

        $isEnrolled = Enrollment::where('user_id',Auth::id())->where('course_id',$course->id)->exists();
        $progress = $lesson->progresses()->where('user_id',Auth::id())->first();

        return view('app.lessons.show', compact('lesson','course','isEnrolled','progress','prev','next'));
    }

    public function updateProgress(UpdateProgressRequest $r, Lesson $lesson)
    {
        $payload = [
            'lesson_id'=>$lesson->id,
            'user_id'=>Auth::id(),
            'progress'=>$r->input('progress', []),
        ];
        if ($r->boolean('completed')) $payload['completed_at']=now();

        $lesson->progresses()->updateOrCreate(
            ['lesson_id'=>$lesson->id,'user_id'=>Auth::id()],
            $payload
        );

        return back()->with('status','Progress tersimpan.');
    }
}
