<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Resource, Lesson};
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
            'url'       => 'required|url',
            'type'      => 'nullable|string|max:50',
        ]);

        Resource::create($data);
        return back()->with('ok','Resource ditambahkan');
    }

    public function update(Request $r, Resource $resource)
    {
        $data = $r->validate([
            'title' => 'required|string|max:255',
            'url'   => 'required|url',
            'type'  => 'nullable|string|max:50',
        ]);

        $resource->update($data);
        return back()->with('ok','Resource diupdate');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return back()->with('ok','Resource dihapus');
    }
}
