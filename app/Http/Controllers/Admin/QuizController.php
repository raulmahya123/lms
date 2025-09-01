<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Quiz, Lesson};
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $r)
    {
        $quizzes = Quiz::with('lesson:id,title')
            ->when($r->filled('lesson_id'), fn($q)=>$q->where('lesson_id',$r->lesson_id))
            ->latest('id')->paginate(20);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $lessons = Lesson::select('id','title')->orderBy('id','desc')->get();
        return view('admin.quizzes.create', compact('lessons'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
        ]);

        $quiz = Quiz::create($data);
        return redirect()->route('admin.quizzes.edit', $quiz)->with('ok','Quiz dibuat');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options','lesson:id,title');
        $lessons = Lesson::select('id','title')->orderBy('id','desc')->get();
        return view('admin.quizzes.edit', compact('quiz','lessons'));
    }

    public function update(Request $r, Quiz $quiz)
    {
        $data = $r->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title'     => 'required|string|max:255',
        ]);

        $quiz->update($data);
        return back()->with('ok','Quiz diupdate');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('ok','Quiz dihapus');
    }
}
