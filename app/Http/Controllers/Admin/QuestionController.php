<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Quiz};
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * List semua pertanyaan.
     */
    public function index(Request $r)
{
    $quizzes   = \App\Models\Quiz::orderBy('title')->get(['id','title']);
    $questions = \App\Models\Question::query()
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
    public function create()
    {
        $quizzes = Quiz::all(['id','title']);
        return view('admin.questions.create', compact('quizzes'));
    }

    /**
     * Simpan pertanyaan baru.
     */
    public function store(Request $r)
    {
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

    /**
     * Tampilkan detail pertanyaan.
     */
    public function show(Question $question)
    {
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Form edit pertanyaan.
     */
    public function edit(Question $question)
    {
        $quizzes = Quiz::all(['id','title']);
        return view('admin.questions.edit', compact('question','quizzes'));
    }

    /**
     * Update pertanyaan.
     */
    public function update(Request $r, Question $question)
    {
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

    /**
     * Hapus pertanyaan.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')->with('ok','Pertanyaan dihapus');
    }
}
