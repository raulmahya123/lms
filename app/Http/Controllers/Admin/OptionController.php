<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question};
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * List semua opsi (dengan relasi question).
     */
    public function index(\Illuminate\Http\Request $r)
    {
        $options = \App\Models\Option::query()
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

        // kalau mau kirim daftar quiz ke view (untuk select):
        $quizzes = \App\Models\Quiz::orderBy('title')->get(['id', 'title']);

        return view('admin.options.index', compact('options', 'quizzes'));
    }


    /**
     * Form tambah opsi baru.
     */
    public function create()
    {
        $questions = Question::all(['id', 'prompt']);
        return view('admin.options.create', compact('questions'));
    }

    /**
     * Simpan opsi baru.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'question_id' => 'required|exists:questions,id',
            'text'        => 'required|string|max:500',
            'is_correct'  => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        Option::create($data);

        return redirect()->route('admin.options.index')->with('ok', 'Opsi ditambahkan');
    }

    /**
     * Detail opsi.
     */
    public function show(Option $option)
    {
        return view('admin.options.show', compact('option'));
    }

    /**
     * Form edit opsi.
     */
    public function edit(Option $option)
    {
        $questions = Question::all(['id', 'prompt']);
        return view('admin.options.edit', compact('option', 'questions'));
    }

    /**
     * Update opsi.
     */
    public function update(Request $r, Option $option)
    {
        $data = $r->validate([
            'question_id' => 'required|exists:questions,id',
            'text'        => 'required|string|max:500',
            'is_correct'  => 'boolean',
        ]);
        $data['is_correct'] = $r->boolean('is_correct');

        $option->update($data);

        return redirect()->route('admin.options.index')->with('ok', 'Opsi diupdate');
    }

    /**
     * Hapus opsi.
     */
    public function destroy(Option $option)
    {
        $option->delete();
        return redirect()->route('admin.options.index')->with('ok', 'Opsi dihapus');
    }
}
