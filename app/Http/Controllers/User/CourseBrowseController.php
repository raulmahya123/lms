<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseBrowseController extends Controller
{
public function index(Request $r)
{
    $courses = Course::query()
        ->where('is_published', 1)
        ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%' . $r->q . '%'))
        ->withCount(['modules', 'enrollments'])
        ->latest() // pakai created_at, bukan id
        ->paginate(12)
        ->withQueryString();

    $myIds = Enrollment::where('user_id', Auth::id())
        ->pluck('course_id')
        ->all();

    return view('app.courses.index', compact('courses', 'myIds'));
}

public function show(Course $course)
{
    abort_unless($course->is_published, 404);

    $course->load([
        'modules' => fn($q) => $q->with(['lessons' => fn($qq) => $qq->orderBy('ordering')])->orderBy('ordering'),
        'creator:id,name',
    ]);

    $isEnrolled = Enrollment::where('user_id', Auth::id())
        ->where('course_id', $course->id)
        ->exists();

    return view('app.courses.show', compact('course', 'isEnrolled'));
}

}
