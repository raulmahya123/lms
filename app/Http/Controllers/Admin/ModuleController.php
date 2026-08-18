<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Module, Course, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    public function index(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW
        $filters = $r->validate([
            'course_id' => ['nullable', 'uuid', 'exists:courses,id'],
        ]);

        $modules = Module::query()
            ->select(['id', 'course_id', 'title', 'ordering', 'created_at'])
            ->with('course:id,title,created_by')
            ->when(!$this->isAdmin(), function ($q) use ($user) {
                $q->whereHas('course', fn($qc) => $qc->manageableBy($user));
            })
            // === Jika ingin mentor hanya lihat course yang ditugaskan, aktifkan ini:
            // ->when($this->isMentorOnly(), function ($q) use ($user) {
            //     $q->where(function ($qq) use ($user) {
            //         $qq->whereHas('course.mentors', fn($qm) => $qm->whereKey($user->id))
            //            ->orWhereHas('course', fn($qc) => $qc->where('created_by', $user->id));
            //     });
            // })
            ->when($filters['course_id'] ?? null, fn($q, $courseId) => $q->where('course_id', $courseId))
            ->orderBy('course_id')->orderBy('ordering')
            ->paginate(20)
            ->withQueryString();

        return view('admin.modules.index', compact('modules'));
    }

    public function create(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $courses = Course::select('id','title','created_by')
            ->when(!$this->isAdmin(), function ($q) use ($user) {
                $q->manageableBy($user);
            })
            // ->when($this->isMentorOnly(), function ($q) use ($user) {
            //     $q->where(function ($qq) use ($user) {
            //         $qq->whereHas('mentors', fn($qm) => $qm->whereKey($user->id))
            //            ->orWhere('created_by', $user->id);
            //     });
            // })
            ->orderBy('title')
            ->get();

        return view('admin.modules.create', compact('courses'));
    }

    public function store(Request $r)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $data = $r->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'ordering'  => 'nullable|integer|min:1',
        ]);

        $data['ordering'] = $data['ordering'] ?? 1;

        $course = Course::select('id','created_by')->findOrFail($data['course_id']);

        if (!$course->manageableBy($user)) {
            abort(403, 'Anda tidak berhak menambah module di course ini.');
        }

        // if ($this->isMentorOnly()) {
        //     $assigned = $course->mentors()->whereKey($user->id)->exists()
        //               || $course->created_by === $user->id;
        //     if (!$assigned) abort(403, 'Course ini tidak berada dalam tanggung jawab Anda.');
        // }

        Module::create($data);

        return redirect()->route('admin.modules.index')->with('ok', 'Module berhasil dibuat');
    }

    public function edit(Request $r, Module $module)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $this->authorizeModule($module, $user);

        $courses = Course::select('id','title','created_by')
            ->when(!$this->isAdmin(), function ($q) use ($user) {
                $q->manageableBy($user);
            })
            // ->when($this->isMentorOnly(), function ($q) use ($user) {
            //     $q->where(function ($qq) use ($user) {
            //         $qq->whereHas('mentors', fn($qm) => $qm->whereKey($user->id))
            //            ->orWhere('created_by', $user->id);
            //     });
            // })
            ->orderBy('title')
            ->get();

        return view('admin.modules.edit', compact('module','courses'));
    }

    public function update(Request $r, Module $module)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $this->authorizeModule($module, $user);

        $data = $r->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'ordering'  => 'nullable|integer|min:1',
        ]);

        if (!$this->isAdmin()) {
            $target = Course::select('id','created_by')->findOrFail($data['course_id']);
            if (!$target->manageableBy($user)) {
                abort(403, 'Anda tidak berhak memindahkan module ke course ini.');
            }
        }
        // if ($this->isMentorOnly()) {
        //     $target = Course::select('id','created_by')->with('mentors:id')->findOrFail($data['course_id']);
        //     $assigned = $target->mentors->contains('id', $user->id) || $target->created_by === $user->id;
        //     if (!$assigned) abort(403, 'Course tujuan bukan tanggung jawab Anda.');
        // }

        $module->update($data);

        return redirect()->route('admin.modules.index')->with('ok', 'Module berhasil diupdate');
    }

    public function destroy(Request $r, Module $module)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $this->authorizeModule($module, $user);

        $module->delete();

        return redirect()->route('admin.modules.index')->with('ok', 'Module dihapus');
    }

    public function lessons(Request $r, Module $module)
    {
        $user = $r->user();
        if (!$user) abort(403); // NEW

        $this->authorizeModule($module, $user);

        return response()->json(
            $module->lessons()
                ->select('id','title','ordering','is_free')
                ->orderBy('ordering')
                ->get()
        );
    }

    /** =========================
     * Helpers
     * ========================= */
    protected function authorizeModule(Module $module, User $user): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $module->loadMissing('course:id,created_by');
        if (!$module->course->manageableBy($user)) {
            abort(403, 'Anda tidak berhak mengelola module ini.');
        }
    }

    protected function isAdmin(): bool
    {
        return Gate::allows('admin');
    }

    protected function isMentorOnly(): bool
    {
        return Gate::allows('mentor') && !Gate::allows('admin'); // NEW (opsional)
    }
}
