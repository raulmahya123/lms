<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Quiz, Lesson, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuizController extends Controller
{
    public function index(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        $quizzes = Quiz::query()
            ->with(['lesson:id,title'])
            ->when($r->filled('lesson_id'), fn($q)=>$q->where('lesson_id', $r->integer('lesson_id')))
            ->when($r->filled('q'), fn($q)=>$q->where('title','like','%'.$r->q.'%'))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        // tidak perlu filter created_by karena hanya admin/mentor yang bisa akses
        $lessons = Lesson::select('id','title')->orderByDesc('id')->get();

        return view('admin.quizzes.create', compact('lessons'));
    }

    public function store(Request $r)
    {
        $this->ensureAdminOrMentor($r->user());

        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
        ]);

        $quiz = Quiz::create($data);
        return redirect()->route('admin.quizzes.edit', $quiz)->with('ok','Quiz dibuat');
    }

    public function edit(Request $r, Quiz $quiz)
    {
        $this->ensureAdminOrMentor($r->user());

        $quiz->load('questions.options','lesson:id,title');

        $lessons = Lesson::select('id','title')->orderByDesc('id')->get();

        return view('admin.quizzes.edit', compact('quiz','lessons'));
    }

    public function update(Request $r, Quiz $quiz)
    {
        $this->ensureAdminOrMentor($r->user());

        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
        ]);

        $quiz->update($data);
        return back()->with('ok','Quiz diupdate');
    }

    public function destroy(Request $r, Quiz $quiz)
    {
        $this->ensureAdminOrMentor($r->user());

        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('ok','Quiz dihapus');
    }

    /* =========================
     * Helpers
     * ========================= */
    protected function ensureAdminOrMentor(?User $user): void
    {
        if (!$user || (!Gate::allows('admin') && !Gate::allows('mentor'))) {
            abort(403, 'Hanya admin/mentor yang boleh mengelola quiz.');
        }
    }
}
