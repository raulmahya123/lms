<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Enrollment, Membership, Resource};
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function show(Resource $resource)
    {
        $resource->load('lesson.module.course');
        $course = $resource->lesson->module->course;

        if (!$resource->lesson->is_free) {
            $enrollment = Enrollment::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->first();

            $hasActiveMembership = Membership::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->exists();

            if (!$enrollment || !$enrollment->hasEffectiveAccess($hasActiveMembership)) {
                abort(403);
            }
        }

        return view('app.resources.show', compact('resource','course'));
    }
}
