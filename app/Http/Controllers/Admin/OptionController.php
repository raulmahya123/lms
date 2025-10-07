<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Option, Question, Quiz};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class OptionController extends Controller
{
    protected function authorizeAdminOrMentor(): void
    {
        if (! Gate::allows('admin') && ! Gate::allows('mentor')) {
            abort(403, 'Hanya admin atau mentor yang boleh mengelola opsi.');
        }
    }
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'question_id'        => ['required', 'uuid', 'exists:questions,id'],
            'options'            => ['required', 'array', 'min:2'], // minimal 2 opsi
            'options.*.text'     => ['required', 'string', 'min:1'],
            'options.*.correct'  => ['nullable', 'boolean'],
        ]);

        // pastikan minimal satu jawaban benar
        $hasTrue = collect($data['options'])->contains(fn($o) => !empty($o['correct']));
        if (!$hasTrue) {
            throw ValidationException::withMessages([
                'options' => 'Minimal satu opsi harus ditandai sebagai jawaban benar.',
            ]);
        }

        DB::transaction(function () use ($data) {
            // (opsional) hapus opsi lama untuk question ini – kalau mau bersih dulu
            // Option::where('question_id', $data['question_id'])->delete();

            $payload = [];
            foreach ($data['options'] as $o) {
                $payload[] = [
                    'id'          => (string) \Illuminate\Support\Str::uuid(),
                    'question_id' => $data['question_id'],
                    'text'        => trim($o['text']),
                    'is_correct'  => !empty($o['correct']),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            Option::insert($payload);
        });

        return redirect()
            ->route('admin.options.index')
            ->with('ok', 'Berhasil menambahkan ' . count($data['options']) . ' opsi sekaligus.');
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

        $quizzes = Quiz::orderBy('title')->get(['id', 'title']);

        return view('admin.options.index', compact('options', 'quizzes'));
    }

    public function create()
    {
        $this->authorizeAdminOrMentor();

        $questions = Question::all(['id', 'prompt']);
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

        $questions = Question::all(['id', 'prompt']);
        return view('admin.options.edit', compact('option', 'questions'));
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
