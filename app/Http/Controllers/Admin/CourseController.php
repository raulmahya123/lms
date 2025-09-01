<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Course, Module};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        $courses = Course::query()
            ->withCount('modules')
            ->when($r->filled('q'), fn($q)=>$q->where('title','like','%'.$r->q.'%'))
            ->latest('id')->paginate(12);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover_url'    => 'nullable|url',
            'is_published' => 'boolean',
        ]);
        $data['created_by'] = Auth::id();
        $data['is_published'] = $r->boolean('is_published');

        $course = Course::create($data);
        return redirect()->route('admin.courses.edit', $course)->with('ok','Course dibuat');
    }

    public function edit(Course $course)
    {
        $course->load('modules');
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $r, Course $course)
    {
        $data = $r->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'cover_url'    => 'nullable|url',
            'is_published' => 'boolean',
        ]);
        $data['is_published'] = $r->boolean('is_published');

        $course->update($data);
        return back()->with('ok','Course diupdate');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('ok','Course dihapus');
    }

    // (opsional) daftar module untuk course tertentu (JSON)
    public function modules(Course $course)
    {
        return response()->json(
            $course->modules()->select('id','title','ordering')->orderBy('ordering')->get()
        );
    }
}
