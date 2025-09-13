<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question, Quiz};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OptionController extends Controller
{
protected function authorizeAdminOrMentor(): void
{
    if (! Gate::allows('admin') && ! Gate::allows('mentor')) {
        abort(403, 'Hanya admin atau mentor yang boleh mengelola opsi.');
    }
}

public function index(Request $r)
{
    $this->authorizeAdminOrMentor();

    $options = Option::query()
        ->with(['question:id,quiz_id,prompt', 'question.quiz:id,title'])
        ->when($r->filled('quiz_id'), fn($q) => $q->whereHas('question', fn($qq) => $qq->where('quiz_id', $r->quiz_id)))
        ->when($r->filled('q'), fn($q) => $q->where('text', 'like', '%' . $r->q . '%'))
        ->when($r->filled('is_correct'), function ($q) use ($r) {
            if ($r->is_correct === '1') $q->where('is_correct', true);
            if ($r->is_correct === '0') $q->where('is_correct', false);
        })
        ->latest('id')
        ->paginate(12)
        ->withQueryString();

    $quizzes = Quiz::orderBy('title')->get(['id','title']);

    return view('admin.options.index', compact('options', 'quizzes'));
}

public function create()
{
    $this->authorizeAdminOrMentor();

    $questions = Question::all(['id','prompt']);
    return view('admin.options.create', compact('questions'));
}

public function store(Request $r)
{
    $this->authorizeAdminOrMentor();

    $data = $r->validate([
        'question_id' => 'required|exists:questions,id',
        'text'        => 'required|string|max:500',
        'is_correct'  => 'boolean',
    ]);
    $data['is_correct'] = $r->boolean('is_correct');

    Option::create($data);

    return redirect()->route('admin.options.index')->with('ok', 'Opsi ditambahkan');
}

public function show(Option $option)
{
    $this->authorizeAdminOrMentor();

    return view('admin.options.show', compact('option'));
}

public function edit(Option $option)
{
    $this->authorizeAdminOrMentor();

    $questions = Question::all(['id','prompt']);
    return view('admin.options.edit', compact('option','questions'));
}

public function update(Request $r, Option $option)
{
    $this->authorizeAdminOrMentor();

    $data = $r->validate([
        'question_id' => 'required|exists:questions,id',
        'text'        => 'required|string|max:500',
        'is_correct'  => 'boolean',
    ]);
    $data['is_correct'] = $r->boolean('is_correct');

    $option->update($data);

    return redirect()->route('admin.options.index')->with('ok', 'Opsi diupdate');
}

public function destroy(Option $option)
{
    $this->authorizeAdminOrMentor();

    $option->delete();
    return redirect()->route('admin.options.index')->with('ok', 'Opsi dihapus');
}
}
