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
    $filters = $r->validate([
        'q'      => ['nullable', 'string', 'max:100'],
        'status' => ['nullable', Rule::in(['pending','active','inactive'])],
    ]);
    $term = trim((string) ($filters['q'] ?? ''));

    $items = \App\Models\Enrollment::query()
        ->select(['id', 'user_id', 'course_id', 'status', 'activated_at', 'access_via', 'access_expires_at', 'created_at'])
        ->with(['user:id,name,email', 'course:id,title'])
        ->when($term !== '', function($q) use ($term) {
            $q->where(function ($w) use ($term) {
                $w->whereHas('user', function($u) use ($term) {
                    $u->where('name','like','%'.$term.'%')
                      ->orWhere('email','like','%'.$term.'%');
                })->orWhereHas('course', function($c) use ($term) {
                    $c->where('title','like','%'.$term.'%');
                });
            });
        })
        ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
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
