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
    public function index()
    {
        $questions = Question::with('quiz')->latest()->paginate(15);
        return view('admin.questions.index', compact('questions'));
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
