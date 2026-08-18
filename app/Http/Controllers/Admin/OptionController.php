<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question, Quiz, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OptionController extends Controller
{
    public function bulkStore(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $data = $request->validate([
            'question_id' => ['required', 'uuid', 'exists:questions,id'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.text' => ['required', 'string', 'min:1'],
            'options.*.correct' => ['nullable', 'boolean'],
        ]);

        $question = Question::query()
            ->with('quiz.lesson.module.course:id,created_by')
            ->findOrFail($data['question_id']);
        $this->authorizeQuestionOwner($question, $user);

        if (!collect($data['options'])->contains(fn ($option) => !empty($option['correct']))) {
            throw ValidationException::withMessages([
                'options' => 'Minimal satu opsi harus ditandai sebagai jawaban benar.',
            ]);
        }

        DB::transaction(function () use ($data) {
            $payload = [];
            foreach ($data['options'] as $option) {
                $payload[] = [
                    'id' => (string) Str::uuid(),
                    'question_id' => $data['question_id'],
                    'text' => trim($option['text']),
                    'is_correct' => !empty($option['correct']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Option::insert($payload);
        });

        return redirect()
            ->route('admin.options.index')
            ->with('ok', 'Berhasil menambahkan ' . count($data['options']) . ' opsi sekaligus.');
    }

    public function index(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $filters = $request->validate([
            'quiz_id' => ['nullable', 'uuid', 'exists:quizzes,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'is_correct' => ['nullable', Rule::in(['0', '1'])],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $options = Option::query()
            ->select(['id', 'question_id', 'text', 'is_correct', 'created_at'])
            ->with(['question:id,quiz_id,prompt', 'question.quiz:id,title'])
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'question.quiz.lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ))
            ->when($filters['quiz_id'] ?? null, fn ($q, $quizId) => $q->whereHas('question', fn ($question) => $question->where('quiz_id', $quizId)))
            ->when($term !== '', fn ($q) => $q->where('text', 'like', "%{$term}%"))
            ->when(isset($filters['is_correct']), fn ($q) => $q->where('is_correct', $filters['is_correct'] === '1'))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $quizzes = $this->allowedQuizzesQuery($user)->orderBy('title')->get(['id', 'title']);

        return view('admin.options.index', compact('options', 'quizzes'));
    }

    public function create(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $questions = $this->allowedQuestionsQuery($user)->orderByDesc('created_at')->get(['id', 'prompt']);

        return view('admin.options.create', compact('questions'));
    }

    public function store(Request $request)
    {
        $user = $this->authorizeContentManager($request->user());
        $data = $request->validate([
            'question_id' => ['required', 'uuid', 'exists:questions,id'],
            'text' => ['required', 'string', 'max:500'],
            'is_correct' => ['boolean'],
        ]);

        $question = Question::query()
            ->with('quiz.lesson.module.course:id,created_by')
            ->findOrFail($data['question_id']);
        $this->authorizeQuestionOwner($question, $user);

        $data['is_correct'] = $request->boolean('is_correct');
        Option::create($data);

        return redirect()->route('admin.options.index')->with('ok', 'Opsi ditambahkan');
    }

    public function show(Request $request, Option $option)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeOptionOwner($option, $user);

        return view('admin.options.show', compact('option'));
    }

    public function edit(Request $request, Option $option)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeOptionOwner($option, $user);

        $questions = $this->allowedQuestionsQuery($user)->orderByDesc('created_at')->get(['id', 'prompt']);

        return view('admin.options.edit', compact('option', 'questions'));
    }

    public function update(Request $request, Option $option)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeOptionOwner($option, $user);

        $data = $request->validate([
            'question_id' => ['required', 'uuid', 'exists:questions,id'],
            'text' => ['required', 'string', 'max:500'],
            'is_correct' => ['boolean'],
        ]);

        $targetQuestion = Question::query()
            ->with('quiz.lesson.module.course:id,created_by')
            ->findOrFail($data['question_id']);
        $this->authorizeQuestionOwner($targetQuestion, $user);

        $data['is_correct'] = $request->boolean('is_correct');
        $option->update($data);

        return redirect()->route('admin.options.index')->with('ok', 'Opsi diupdate');
    }

    public function destroy(Request $request, Option $option)
    {
        $user = $this->authorizeContentManager($request->user());
        $this->authorizeOptionOwner($option, $user);

        $option->delete();

        return redirect()->route('admin.options.index')->with('ok', 'Opsi dihapus');
    }

    private function authorizeContentManager(?User $user): User
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin atau mentor yang boleh mengelola opsi.');
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

    private function allowedQuestionsQuery(User $user)
    {
        return Question::query()
            ->select(['id', 'quiz_id', 'prompt', 'created_at'])
            ->when(!Gate::allows('admin'), fn ($q) => $q->whereHas(
                'quiz.lesson.module.course',
                fn ($course) => $course->manageableBy($user)
            ));
    }

    private function authorizeOptionOwner(Option $option, User $user): void
    {
        $option->loadMissing('question.quiz.lesson.module.course:id,created_by');
        $this->authorizeQuestionOwner($option->question, $user);
    }

    private function authorizeQuestionOwner(Question $question, User $user): void
    {
        if (Gate::allows('admin')) {
            return;
        }

        if (!$question->quiz?->lesson?->module?->course?->manageableBy($user)) {
            abort(403, 'Anda tidak berhak mengelola opsi pada pertanyaan ini.');
        }
    }
}
