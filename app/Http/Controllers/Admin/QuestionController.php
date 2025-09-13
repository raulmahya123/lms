<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Quiz, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * List semua pertanyaan.
     */
    public function index(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        $quizzes = Quiz::orderBy('title')->get(['id','title']);

        $questions = Question::query()
            ->with('quiz:id,title')
            ->when($r->filled('quiz_id'), fn($q) => $q->where('quiz_id', $r->quiz_id))
            ->when($r->filled('q'), fn($q2) => $q2->where('prompt','like','%'.$r->q.'%'))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.questions.index', compact('questions','quizzes'));
    }

    /**
     * Form buat pertanyaan baru.
     */
    public function create(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        $quizzes = Quiz::select('id','title')->orderBy('title')->get();
        return view('admin.questions.create', compact('quizzes'));
    }

    /**
     * Simpan pertanyaan baru.
     */
    public function store(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        $data = $r->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type'    => 'required|in:mcq,short,long',
            'prompt'  => 'required|string',
            'points'  => 'nullable|integer|min:1',
        ]);

        $data['points'] = $data['points'] ?? 1;

        Question::create($data);

        return redirect()->route('admin.questions.index')->with('ok','Pertanyaan dibuat');
    }

    public function show(Request $r, Question $question)
    {
        $this->ensureAdminOrMentor($r->user());
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Request $r, Question $question)
    {
        $this->ensureAdminOrMentor($r->user());

        $quizzes = Quiz::select('id','title')->orderBy('title')->get();
        return view('admin.questions.edit', compact('question','quizzes'));
    }

    public function update(Request $r, Question $question)
    {
        $this->ensureAdminOrMentor($r->user());

        $data = $r->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type'    => 'required|in:mcq,short,long',
            'prompt'  => 'required|string',
            'points'  => 'nullable|integer|min:1',
        ]);

        $data['points'] = $data['points'] ?? 1;
        $question->update($data);

        return redirect()->route('admin.questions.index')->with('ok','Pertanyaan diupdate');
    }

    public function destroy(Request $r, Question $question)
    {
        $this->ensureAdminOrMentor($r->user());
        $question->delete();

        return redirect()->route('admin.questions.index')->with('ok','Pertanyaan dihapus');
    }

    /** =========================
     * Helpers
     * ========================= */
    protected function ensureAdminOrMentor(?User $user): void
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin/mentor yang boleh mengakses pertanyaan.');
        }
    }
}
