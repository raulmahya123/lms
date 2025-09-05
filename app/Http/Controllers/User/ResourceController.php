<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function show(Resource $resource)
    {
        $resource->load('lesson.module.course');
        $course = $resource->lesson->module->course;

        $isEnrolled = Enrollment::where('user_id',Auth::id())->where('course_id',$course->id)->exists();
        if (!$resource->lesson->is_free && !$isEnrolled) abort(403);

        return view('app.resources.show', compact('resource','course'));
    }
}
