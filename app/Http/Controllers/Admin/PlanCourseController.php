<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PlanCourse, Plan, Course};
use Illuminate\Http\Request;

class PlanCourseController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'plan_id'   => 'required|exists:plans,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        PlanCourse::firstOrCreate($data);
        return back()->with('ok','Course ditambahkan ke Plan');
    }

    public function destroy(PlanCourse $planCourse)
    {
        $planCourse->delete();
        return back()->with('ok','Course dihapus dari Plan');
    }
}
