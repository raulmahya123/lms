<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Module, Course};
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $r)
    {
        $modules = Module::with('course:id,title')
            ->when($r->filled('course_id'), fn($q) => $q->where('course_id', $r->integer('course_id')))
            ->orderBy('course_id')
            ->orderBy('ordering')
            ->paginate(20)
            ->withQueryString();

        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $courses = Course::select('id','title')->orderBy('title')->get();
        return view('admin.modules.create', compact('courses'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'ordering'  => 'nullable|integer|min:1',
        ]);

        $data['ordering'] = $data['ordering'] ?? 1;

        Module::create($data);

        return redirect()->route('admin.modules.index')
            ->with('ok', 'Module berhasil dibuat');
    }

    public function edit(Module $module)
    {
        $courses = Course::select('id','title')->orderBy('title')->get();
        return view('admin.modules.edit', compact('module','courses'));
    }

    public function update(Request $r, Module $module)
    {
        $data = $r->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'ordering'  => 'nullable|integer|min:1',
        ]);

        $module->update($data);

        return redirect()->route('admin.modules.index')
            ->with('ok', 'Module berhasil diupdate');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('ok', 'Module dihapus');
    }

    // Helper: list lessons by module (JSON)
    public function lessons(Module $module)
    {
        return response()->json(
            $module->lessons()
                ->select('id','title','ordering','is_free')
                ->orderBy('ordering')
                ->get()
        );
    }
}
