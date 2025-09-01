<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Module};
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $r)
    {
        $lessons = Lesson::with(['module:id,title','module.course:id,title'])
            ->when($r->filled('module_id'), fn($q)=>$q->where('module_id',$r->module_id))
            ->orderBy('module_id')->orderBy('ordering')->paginate(20);

        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $modules = Module::with('course:id,title')->orderBy('course_id')->orderBy('ordering')->get();
        return view('admin.lessons.create', compact('modules'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'module_id'   => 'required|exists:modules,id',
            'title'       => 'required|string|max:255',
            'content'     => 'nullable|string',
            'content_url' => 'nullable|url',
            'ordering'    => 'nullable|integer|min:1',
            'is_free'     => 'boolean',
        ]);
        $data['ordering'] = $data['ordering'] ?? 1;
        $data['is_free']  = $r->boolean('is_free');

        $lesson = Lesson::create($data);
        return redirect()->route('admin.lessons.edit', $lesson)->with('ok','Lesson dibuat');
    }

    public function edit(Lesson $lesson)
    {
        $modules = Module::orderBy('course_id')->orderBy('ordering')->get();
        $lesson->load('resources','quiz');
        return view('admin.lessons.edit', compact('lesson','modules'));
    }

    public function update(Request $r, Lesson $lesson)
    {
        $data = $r->validate([
            'module_id'   => 'required|exists:modules,id',
            'title'       => 'required|string|max:255',
            'content'     => 'nullable|string',
            'content_url' => 'nullable|url',
            'ordering'    => 'nullable|integer|min:1',
            'is_free'     => 'boolean',
        ]);
        $data['is_free'] = $r->boolean('is_free');

        $lesson->update($data);
        return back()->with('ok','Lesson diupdate');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('ok','Lesson dihapus');
    }
}
