<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(\Illuminate\Http\Request $r)
{
    $items = \App\Models\Enrollment::query()
        ->with(['user:id,name,email', 'course:id,title'])
        ->when($r->filled('q'), function($q) use ($r) {
            $q->whereHas('user', function($u) use ($r) {
                $u->where('name','like','%'.$r->q.'%')
                  ->orWhere('email','like','%'.$r->q.'%');
            })->orWhereHas('course', function($c) use ($r) {
                $c->where('title','like','%'.$r->q.'%');
            });
        })
        ->when($r->filled('status'), fn($q) => $q->where('status', $r->status))
        ->latest('id')
        ->paginate(12)
        ->withQueryString();

    return view('admin.enrollments.index', compact('items'));
}


    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['user:id,name,email','course:id,title']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function update(Request $r, Enrollment $enrollment)
    {
        $data = $r->validate([
            'status'       => ['required', Rule::in(['pending','active','inactive'])],
            'activated_at' => 'nullable|date',
        ]);

        $enrollment->update($data);
        return back()->with('ok','Enrollment diupdate');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')->with('ok','Enrollment dihapus');
    }
}
