<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Lesson, Quiz, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $filters = $request->validate([
            'lesson_id' => ['nullable', 'uuid', 'exists:lessons,id'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $quizzes = Quiz::query()
            ->select(['id', 'lesson_id', 'title', 'created_at'])
            ->with(['lesson:id,module_id,title', 'lesson.module:id,course_id', 'lesson.module.course:id,title,created_by'])
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ))
            ->when($filters['lesson_id'] ?? null, fn ($q, $lessonId) => $q->where('lesson_id', $lessonId))
            ->when($term !== '', fn ($q) => $q->where('title', 'like', "%{$term}%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $lessons = $this->allowedLessonsQuery($user)->orderByDesc('id')->get(['id', 'title']);

        return view('admin.quizzes.create', compact('lessons'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $data = $request->validate([
            'lesson_id' => ['required', 'uuid', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $lesson = Lesson::query()
            ->with('module.course:id,created_by')
            ->findOrFail($data['lesson_id']);
        $this->authorizeLessonOwner($lesson, $user);

        $quiz = Quiz::create($data);

        return redirect()->route('admin.quizzes.edit', $quiz)->with('ok', 'Quiz dibuat');
    }

    public function edit(Request $request, Quiz $quiz)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuizOwner($quiz, $user);

        $quiz->load([
            'questions:id,quiz_id,type,prompt,points',
            'questions.options:id,question_id,text,is_correct',
            'lesson:id,module_id,title',
        ]);

        $lessons = $this->allowedLessonsQuery($user)->orderByDesc('id')->get(['id', 'title']);

        return view('admin.quizzes.edit', compact('quiz', 'lessons'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuizOwner($quiz, $user);

        $data = $request->validate([
            'lesson_id' => ['required', 'uuid', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $targetLesson = Lesson::query()
            ->with('module.course:id,created_by')
            ->findOrFail($data['lesson_id']);
        $this->authorizeLessonOwner($targetLesson, $user);

        $quiz->update($data);

        return back()->with('ok', 'Quiz diupdate');
    }

    public function destroy(Request $request, Quiz $quiz)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuizOwner($quiz, $user);

        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('ok', 'Quiz dihapus');
    }

    private function authorizeContentManager(?User $user): User
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin/mentor yang boleh mengelola quiz.');
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

    private function authorizeQuizOwner(Quiz $quiz, User $user): void
    {
        $quiz->loadMissing('lesson.module.course:id,created_by');
        $this->authorizeLessonOwner($quiz->lesson, $user);
    }

    private function authorizeLessonOwner(Lesson $lesson, User $user): void
    {
        if (Gate::allows('admin')) {
            return;
        }

        if (!$lesson->module?->course?->manageableBy($user)) {
            abort(403, 'Anda tidak berhak mengelola quiz pada lesson ini.');
        }
    }
}
