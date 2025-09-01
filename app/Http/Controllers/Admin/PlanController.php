<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Plan, Course, PlanCourse};
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $r)
    {
        $plans = Plan::withCount(['planCourses','memberships'])->paginate(12);
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $courses = Course::select('id','title')->orderBy('title')->get();
        return view('admin.plans.create', compact('courses'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'     => 'required|string|max:100|unique:plans,name',
            'price'    => 'required|integer|min:0',
            'period'   => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $plan = Plan::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'period' => $data['period'],
            'features' => $data['features'] ?? [],
        ]);

        if (!empty($data['course_ids'])) {
            foreach ($data['course_ids'] as $cid) {
                PlanCourse::firstOrCreate(['plan_id'=>$plan->id,'course_id'=>$cid]);
            }
        }

        return redirect()->route('admin.plans.edit',$plan)->with('ok','Plan dibuat');
    }

    public function edit(Plan $plan)
    {
        $plan->load('planCourses.course:id,title');
        $courses = Course::select('id','title')->orderBy('title')->get();
        return view('admin.plans.edit', compact('plan','courses'));
    }

    public function update(Request $r, Plan $plan)
    {
        $data = $r->validate([
            'name'     => 'required|string|max:100|unique:plans,name,'.$plan->id,
            'price'    => 'required|integer|min:0',
            'period'   => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $plan->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'period' => $data['period'],
            'features' => $data['features'] ?? [],
        ]);

        // sinkronisasi akses course
        $new = collect($data['course_ids'] ?? []);
        $current = $plan->planCourses()->pluck('course_id');

        // remove yang tidak dipilih
        $plan->planCourses()->whereNotIn('course_id', $new)->delete();
        // tambahkan yang baru
        foreach ($new->diff($current) as $cid) {
            PlanCourse::firstOrCreate(['plan_id'=>$plan->id,'course_id'=>$cid]);
        }

        return back()->with('ok','Plan diupdate');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('ok','Plan dihapus');
    }
}
