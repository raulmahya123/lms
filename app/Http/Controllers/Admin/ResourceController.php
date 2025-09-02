<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Resource, Lesson};
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with('lesson')->latest()->paginate(20);
        return view('admin.resources.index', compact('resources'));
    }

    public function create()
    {
        $lessons = Lesson::all(['id','title']);
        return view('admin.resources.create', compact('lessons'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
            'url'       => 'required|url',
            'type'      => 'nullable|string|max:50',
        ]);

        Resource::create($data);
        return redirect()->route('admin.resources.index')->with('ok','Resource ditambahkan');
    }

    public function show(Resource $resource)
    {
        return view('admin.resources.show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        $lessons = Lesson::all(['id','title']);
        return view('admin.resources.edit', compact('resource','lessons'));
    }

    public function update(Request $r, Resource $resource)
    {
        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
            'url'       => 'required|url',
            'type'      => 'nullable|string|max:50',
        ]);

        $resource->update($data);
        return redirect()->route('admin.resources.index')->with('ok','Resource diupdate');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('ok','Resource dihapus');
    }
}
