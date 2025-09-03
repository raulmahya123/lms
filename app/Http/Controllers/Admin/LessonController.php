<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Module};
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $r)
    {
        $lessons = Lesson::query()
            ->with([
                'module' => fn($q) => $q->select(['id', 'course_id', 'title']),
                'module.course' => fn($q) => $q->select(['id', 'title']),
            ])
            ->when($r->filled('module_id'), fn($q) => $q->where('module_id', $r->integer('module_id')))
            ->when($r->filled('q'), fn($q) => $q->where('title', 'like', '%' . $r->q . '%'))
            ->orderBy('module_id')->orderBy('ordering')
            ->paginate(20)
            ->withQueryString();

        return view('admin.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $modules = Module::with('course:id,title')
            ->orderBy('course_id')->orderBy('ordering')->get();

        return view('admin.lessons.create', compact('modules'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',
            'content'                => 'nullable|string',
            'content_url'            => 'array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',
            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',
        ]);

        $data['ordering'] = $data['ordering'] ?? 1;
        $data['is_free']  = $r->boolean('is_free');

        $lesson = Lesson::create($data);

        return redirect()->route('admin.lessons.edit', $lesson)->with('ok', 'Lesson dibuat');
    }

    public function edit(Lesson $lesson)
    {
        $modules = Module::orderBy('course_id')->orderBy('ordering')->get();
        $lesson->load('resources', 'quiz');

        return view('admin.lessons.edit', compact('lesson', 'modules'));
    }

    public function update(Request $r, Lesson $lesson)
    {
        $data = $r->validate([
            'module_id'              => 'required|exists:modules,id',
            'title'                  => 'required|string|max:255',
            'content'                => 'nullable|string',
            'content_url'            => 'array',
            'content_url.*.title'    => 'required_with:content_url|string|max:255',
            'content_url.*.url'      => 'required_with:content_url|url',
            'ordering'               => 'nullable|integer|min:1',
            'is_free'                => 'boolean',
        ]);

        $data['is_free'] = $r->boolean('is_free');

        $lesson->update($data);

        return back()->with('ok', 'Lesson diupdate');
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.lessons.index')->with('ok', 'Lesson dihapus');
    }
    // app/Http/Controllers/Admin/LessonController.php

    public function show(\App\Models\Lesson $lesson)
    {
        $lesson->load('module.course');

        // pastikan content_url array
        $videos = $lesson->content_url;
        if (is_string($videos)) {
            $decoded = json_decode($videos, true);
            $videos = is_array($decoded) ? $decoded : [];
        }

        // index video aktif dari query ?v=
        $active = request()->integer('v', 0);
        if ($active < 0 || $active >= count($videos)) $active = 0;

        return view('admin.lessons.show', [
            'lesson' => $lesson,
            'videos' => $videos,
            'active' => $active,
        ]);
    }
}
