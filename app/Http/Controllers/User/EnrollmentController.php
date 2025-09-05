<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['course:id,title,cover_url'])
            ->where('user_id', Auth::id())
            ->latest('activated_at')->paginate(20);
        return view('app.enrollments.index', compact('enrollments'));
    }

    public function store(Request $r, Course $course)
    {
        abort_unless($course->is_published, 404);
        $exists = Enrollment::where('user_id',Auth::id())->where('course_id',$course->id)->first();
        if ($exists) return back()->with('status','Kamu sudah terdaftar di kursus ini.');

        DB::transaction(function() use ($course) {
            Enrollment::create([
                'user_id'=>Auth::id(),
                'course_id'=>$course->id,
                'status'=>'active',
                'activated_at'=>now(),
            ]);
        });

        return redirect()->route('app.courses.show',$course)->with('status','Enroll berhasil.');
    }
}
