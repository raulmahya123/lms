<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Resource, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());

        $resources = Resource::query()
            ->select(['id', 'lesson_id', 'title', 'url', 'type', 'created_at'])
            ->with('lesson:id,module_id,title')
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ))
            ->latest()
            ->paginate(20);

        return view('admin.resources.index', compact('resources'));
    }

    public function create(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $lessons = $this->allowedLessonsQuery($user)->orderBy('title')->get(['id', 'title']);

        return view('admin.resources.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $data = $request->validate([
            'lesson_id' => ['required', 'uuid', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $lesson = Lesson::query()->with('module.course:id,created_by')->findOrFail($data['lesson_id']);
        $this->authorizeLessonOwner($lesson, $user);

        Resource::create($data);

        return redirect()->route('admin.resources.index')->with('ok', 'Resource ditambahkan');
    }

    public function show(Request $request, Resource $resource)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeResourceOwner($resource, $user);

        $resource->load('lesson:id,title');

        return view('admin.resources.show', compact('resource'));
    }

    public function edit(Request $request, Resource $resource)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeResourceOwner($resource, $user);

        $lessons = $this->allowedLessonsQuery($user)->orderBy('title')->get(['id', 'title']);

        return view('admin.resources.edit', compact('resource', 'lessons'));
    }

    public function update(Request $request, Resource $resource)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeResourceOwner($resource, $user);

        $data = $request->validate([
            'lesson_id' => ['required', 'uuid', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $targetLesson = Lesson::query()->with('module.course:id,created_by')->findOrFail($data['lesson_id']);
        $this->authorizeLessonOwner($targetLesson, $user);

        $resource->update($data);

        return redirect()->route('admin.resources.index')->with('ok', 'Resource diupdate');
    }

    public function destroy(Request $request, Resource $resource)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeResourceOwner($resource, $user);

        $resource->delete();

        return redirect()->route('admin.resources.index')->with('ok', 'Resource dihapus');
    }

    private function authorizeContentManager(?User $user): User
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin atau mentor yang boleh mengelola resource.');
        }

        return $user;
    }

    private function allowedLessonsQuery(User $user)
    {
        return Lesson::query()
            ->select(['id', 'module_id', 'title'])
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'module.course',
                fn ($course) => $course->manageableBy($user)
            ));
    }

    private function authorizeResourceOwner(Resource $resource, User $user): void
    {
        $resource->loadMissing('lesson.module.course:id,created_by');
        $this->authorizeLessonOwner($resource->lesson, $user);
    }

    private function authorizeLessonOwner(Lesson $lesson, User $user): void
    {
        if (Gate::allows('admin')) {
            return;
        }

        if (!$lesson->module?->course?->manageableBy($user)) {
            abort(403, 'Anda tidak berhak mengelola resource pada lesson ini.');
        }
    }
}
