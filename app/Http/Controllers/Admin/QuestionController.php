<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Question, Quiz};
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'type'    => 'required|in:mcq,short,long',
            'prompt'  => 'required|string',
            'points'  => 'nullable|integer|min:1',
        ]);
        $data['points'] = $data['points'] ?? 1;

        $q = Question::create($data);
        return back()->with('ok','Pertanyaan dibuat');
    }

    public function update(Request $r, Question $question)
    {
        $data = $r->validate([
            'type'   => 'required|in:mcq,short,long',
            'prompt' => 'required|string',
            'points' => 'nullable|integer|min:1',
        ]);
        $question->update($data);
        return back()->with('ok','Pertanyaan diupdate');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('ok','Pertanyaan dihapus');
    }
}
