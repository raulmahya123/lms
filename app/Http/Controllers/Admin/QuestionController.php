<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Quiz, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $filters = $request->validate([
            'quiz_id' => ['nullable', 'uuid', 'exists:quizzes,id'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $quizzes = $this->allowedQuizzesQuery($user)->orderBy('title')->get(['id', 'title']);

        $questions = Question::query()
            ->select(['id', 'quiz_id', 'type', 'prompt', 'points', 'created_at'])
            ->with('quiz:id,title')
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'quiz.lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ))
            ->when($filters['quiz_id'] ?? null, fn ($q, $quizId) => $q->where('quiz_id', $quizId))
            ->when($term !== '', fn ($q) => $q->where('prompt', 'like', "%{$term}%"))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.questions.index', compact('questions', 'quizzes'));
    }

    public function create(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $quizzes = $this->allowedQuizzesQuery($user)->orderBy('title')->get(['id', 'title']);

        return view('admin.questions.create', compact('quizzes'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $data = $request->validate([
            'quiz_id' => ['required', 'uuid', 'exists:quizzes,id'],
            'type' => ['required', 'in:mcq,short,long'],
            'prompt' => ['required', 'string'],
            'points' => ['nullable', 'integer', 'min:1'],
        ]);

        $quiz = Quiz::query()->with('lesson.module.course:id,created_by')->findOrFail($data['quiz_id']);
        $this->authorizeQuizOwner($quiz, $user);

        $data['points'] = $data['points'] ?? 1;
        Question::create($data);

        return redirect()->route('admin.questions.index')->with('ok', 'Pertanyaan dibuat');
    }

    public function show(Request $request, Question $question)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuestionOwner($question, $user);

        return view('admin.questions.show', compact('question'));
    }

    public function edit(Request $request, Question $question)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuestionOwner($question, $user);

        $quizzes = $this->allowedQuizzesQuery($user)->orderBy('title')->get(['id', 'title']);

        return view('admin.questions.edit', compact('question', 'quizzes'));
    }

    public function update(Request $request, Question $question)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuestionOwner($question, $user);

        $data = $request->validate([
            'quiz_id' => ['required', 'uuid', 'exists:quizzes,id'],
            'type' => ['required', 'in:mcq,short,long'],
            'prompt' => ['required', 'string'],
            'points' => ['nullable', 'integer', 'min:1'],
        ]);

        $targetQuiz = Quiz::query()->with('lesson.module.course:id,created_by')->findOrFail($data['quiz_id']);
        $this->authorizeQuizOwner($targetQuiz, $user);

        $data['points'] = $data['points'] ?? 1;
        $question->update($data);

        return redirect()->route('admin.questions.index')->with('ok', 'Pertanyaan diupdate');
    }

    public function destroy(Request $request, Question $question)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeQuestionOwner($question, $user);

        $question->delete();

        return redirect()->route('admin.questions.index')->with('ok', 'Pertanyaan dihapus');
    }

    private function authorizeContentManager(?User $user): User
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin/mentor yang boleh mengakses pertanyaan.');
        }

        return $user;
    }

    private function allowedQuizzesQuery(User $user)
    {
        return Quiz::query()
            ->select(['id', 'lesson_id', 'title'])
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ));
    }

    private function authorizeQuestionOwner(Question $question, User $user): void
    {
        $question->loadMissing('quiz.lesson.module.course:id,created_by');
        $this->authorizeQuizOwner($question->quiz, $user);
    }

    private function authorizeQuizOwner(Quiz $quiz, User $user): void
    {
        if (Gate::allows('admin')) {
            return;
        }

        if (!$quiz->lesson?->module?->course?->manageableBy($user)) {
            abort(403, 'Anda tidak berhak mengelola pertanyaan pada quiz ini.');
        }
    }
}
